define(
	[
		'jquery',
		'ko',
		'uiComponent',
		'underscore',
		'googlemaps!',

		'HubBox_HubBox/js/action/confirm-collectpoint',
		'HubBox_HubBox/js/action/unselect-collectpoint',
		'HubBox_HubBox/js/action/view-explore',
		'HubBox_HubBox/js/action/hide-widget',

		'HubBox_HubBox/js/model/collectpoints',
		'HubBox_HubBox/js/model/hubbox'
	],
	function (
		$,
		ko,
		Component,
		_,
		googleMaps,

		action_confirmCollectPoint,
		action_unSelectCollectPoint,
		action_viewExplore,
		action_hideWidget,

		CollectPoints,
		hubBox
	) {
		'use strict';

		return Component.extend({
			defaults: {
				template: 'HubBox_HubBox/widget/confirm',
			},

			collectPoint: hubBox.selectedCollectPoint,

			translucent: ko.observable(true),

			destination: {
				point: null,
				marker: null
			},

            home: {
                point: null,
                marker: null
            },

            directions: null,

			show:  ko.computed(function(){
				return hubBox.view() === 'confirm';
			}),

			mapDefaults : {
				noPoi : [{
					featureType: "poi",
					stylers: [{ visibility: "off" }]
				}],
				mapStyles : [{ featureType: 'water', stylers: [{ saturation: 43 }, { lightness: -11 }, { hue: '#0088ff' }] }, { featureType: 'road', elementType: 'geometry.fill', stylers: [{ hue: '#ff0000' }, { saturation: -100 }, { lightness: 99 }] }, { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#808080' }, { lightness: 54 }] }, { featureType: 'landscape.man_made', elementType: 'geometry.fill', stylers: [{ color: '#ece2d9' }] }, { featureType: 'poi.park', elementType: 'geometry.fill', stylers: [{ color: '#ccdca1' }] }, { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#767676' }] }, { featureType: 'road', elementType: 'labels.text.stroke', stylers: [{ color: '#ffffff' }] }, { featureType: 'poi', stylers: [{ visibility: 'off' }] }, { featureType: 'landscape.natural', elementType: 'geometry.fill', stylers: [{ visibility: 'on' }, { color: '#b8cb93' }] }, { featureType: 'poi.park', stylers: [{ visibility: 'on' }] }, { featureType: 'poi.sports_complex', stylers: [{ visibility: 'on' }] }, { featureType: 'poi.medical', stylers: [{ visibility: 'on' }] }, { featureType: 'poi.business', stylers: [{ visibility: 'simplified' }] }]
			},

			map: {
				object: null,
				bounds: ko.observable(null)
			},

			bootMap: function() {
				var self = this;
			     //var myLatlng = new google.maps.LatLng(39.51728, 34.765211);
			     var nearest = CollectPoints.nearestCollectPoint().address.location;
			     var tmpCcenter = {lat: nearest.y, lng: nearest.x};

				this.map.object = new googleMaps.Map(
					document.getElementById('hubbox-map-confirm'),
					{
						center: tmpCcenter,
						draggable: true,
						zoomControl: true,
						zoomControlOptions: {
							position: google.maps.ControlPosition.RIGHT_CENTER
						},
						mapTypeControl: false,
						scaleControl: false,
						streetViewControl: false,
						fullscreenControl: false
					}
				);

				this.map.object.setOptions({ styles: this.mapDefaults.noPoi });
				this.map.object.setOptions({ styles: this.mapDefaults.mapStyles });

				self.mapTileRender();

			},

			isTranslucent: function() {
				var self = this;
				return self.translucent();
			},

			confirmBack: function() {
				action_unSelectCollectPoint();
				action_viewExplore();
			},

			confirmCollectPoint: function() {
				var self = this;
				action_confirmCollectPoint(this.collectPoint());
                action_hideWidget();
				hubBox.setHubBox()
                    .then(function () {
                        hubBox.setHubBoxAddressToOrder(self.collectPoint());
                    }
                );
			},

			clearMap: function() {
				var self = this;
                // unset previous things
                if (self.destination.marker) {
                    self.destination.marker.setMap(null);
                }
                if (self.home.marker) {
                    self.home.marker.setMap(null);
                }

                if (self.directions) {
                    self.directions.setMap(null);
                }
			},

			destinationMarkerToMap: function(point) {
				var self = this;
				var cpData = this.collectPoint();
                var pinUrl = 'https://s3-eu-west-1.amazonaws.com/hub-box-assets/production/widget/images/pins/droplet/hubbox-pin.svg';
                var pinImage = new google.maps.MarkerImage(pinUrl, null, null, null, new google.maps.Size(36,45));

				// private cp pins
				if (cpData.type !== 'hubbox') {
					var pinImageIconImage = (window.checkoutConfig.hubBox.privatePinUrl
						&& window.checkoutConfig.hubBox.privatePinUrl.trim().length > 0) ?
						window.checkoutConfig.hubBox.privatePinUrl :
						require.toUrl('') + 'HubBox_HubBox/images/privatepin.png';

					pinImage = new google.maps.MarkerImage(pinImageIconImage, null, null, null, new google.maps.Size(44,45));
				}

				if (point && point.y) {
					var position = {lat: point.y, lng: point.x};
					self.destination.marker = new googleMaps.Marker({
						position: position,
						map: self.map.object,
						icon: pinImage
					});
				}
			},

			homeMarkerToMap: function(point) {
				var self = this;
                var homePinUrl = 'https://s3-eu-west-1.amazonaws.com/hub-box-assets/production/widget/images/pins/droplet/search-pin.svg';
                var homeMarkerImage = new google.maps.MarkerImage(homePinUrl, null, null, null, new google.maps.Size(36,36));

				if (point && point.y) {
					var position = {lat: point.y, lng: point.x};
					self.home.marker = new google.maps.Marker({
						position: position,
						map: self.map.object,
						icon: homeMarkerImage
					});
				}
			},

            directionsToMap: function() {
                var self = this;
                var polylineOptions = new google.maps.Polyline({
                    strokeColor: '#ef286b',
                    strokeOpacity: 1.0,
                    strokeWeight: 4
                });
                self.directions = new google.maps.DirectionsRenderer({
                    suppressMarkers: true,
                    suppressInfoWindows: true,
                    polylineOptions: polylineOptions
                });
                var directionsService = new google.maps.DirectionsService;

                directionsService.route({
                    origin: self.destination.marker.getPosition(),
                    destination: self.home.marker.getPosition(),
                    travelMode: 'WALKING'
                }, function(response, status) {
                    if (status === 'OK') {
                        self.directions.setMap(null);
                        self.directions.setMap(self.map.object);
                        self.directions.setDirections(response);
                    } else {
                        console.log('Directions request failed due to ' + status);
                    }
                });

                self.mapTileRender();
            },


			mapTileRender: function() {
				if (this.map.object) {
					googleMaps.event.trigger(this.map.object, "resize");
				}
			},

			initialize: function () {
				this._super();
				var self = this;


				self.show.subscribe(function(inView) {

					if (inView) {

                        self.map.bounds = new googleMaps.LatLngBounds();
                        var homePoint = CollectPoints.homePoint();

                        if (!self.map.object || $('#hubbox-map-confirm').html().trim() === '') {
                            self.bootMap(homePoint);
                        }

                        self.clearMap();

                        var position = {lat: homePoint.y, lng: homePoint.x};
                        self.map.bounds.extend(position);
                        self.homeMarkerToMap(homePoint);

                        // destination pin
                        var destPoint = self.collectPoint().address.location;
                        position = {lat: destPoint.y, lng: destPoint.x};
                        self.map.bounds.extend(position);
                        self.destinationMarkerToMap(destPoint);

                        self.directionsToMap();

                        setTimeout(function () {
                        	self.mapTileRender();
                        	self.map.object.fitBounds(self.map.bounds);
                        }, 300);


                        setTimeout(function () {
                        	self.translucent(false);
                        }, 800);

                    } else {
						// not in view, hide the info
                        self.translucent(true);
					}
				});


				return this;
			}

		});
	}
);
