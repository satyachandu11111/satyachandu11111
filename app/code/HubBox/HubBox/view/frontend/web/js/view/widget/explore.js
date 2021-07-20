define(
	[
		'jquery',
		'ko',
		'uiComponent',
		'underscore',
		'googlemaps!',

		'HubBox_HubBox/js/action/set-loading',
		'HubBox_HubBox/js/action/unset-loading',

		'HubBox_HubBox/js/action/select-collectpoint',

		'HubBox_HubBox/js/action/view-confirm',
		'HubBox_HubBox/js/action/view-info',

		'HubBox_HubBox/js/model/collectpoints',
		'HubBox_HubBox/js/model/hubbox'
	],
	function (
		$,
		ko,
		Component,
		_,
		googleMaps,

		action_setLoading,
		action_unSetLoading,

		action_selectCollectPoint,

		action_viewConfirm,
		action_viewInfo,

		CollectPoints,
		hubBox
	) {
		'use strict';

		return Component.extend({
			defaults: {
				template: 'HubBox_HubBox/widget/explore',
			},

			show:  ko.computed(function(){
				return hubBox.view() === 'explore';
			}),

			translucent : ko.observable(false),

			map: {
				object: null,
				bounds: null,
				markers:  [],
				homePoint: CollectPoints.homePoint,
				homeMarker: null,
                infoWindow: null,
                defaults : '[{"featureType":"administrative","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ece2d9"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"color":"#b8cb93"},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","stylers":[{"visibility":"on"}]},{"featureType":"poi.park","stylers":[{"visibility":"on"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#ccdca1"}]},{"featureType":"poi.sports_complex","stylers":[{"visibility":"on"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":99}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#808080"},{"lightness":54}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#767676"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"hue":"#0088ff"},{"saturation":43},{"lightness":-11}]}]'
            },

			message: {
				showing: ko.observable(),
				type: ko.observable(),
				text: ko.observable("Please enter your postcode or location to search for Collect Points")
			},

			drawerOpen: ko.observable(false),

			collectPoints: CollectPoints.collectPoints,
			searchQuery: hubBox.postCode,
			queryType: {
				within: 'within',
				nearest: 'nearest'
			},

			timer: null,

			// toggle drawer open
			drawerClick: function() {
				var self = this;
				self.drawerOpen(!self.drawerOpen());
				self.hideMessage();
			},

			searchClick: function() {
				action_setLoading();
				if (hubBox.postCode().length > 0) {
                    CollectPoints.getNearest(hubBox.postCode());
				}
			},

			bootMap: function() {
				var self = this;
				var center = new googleMaps.LatLng(
					parseFloat(51.457),
					parseFloat(-0.1844)
				);

				action_setLoading();

				this.map.object = new googleMaps.Map(
					document.getElementById('hubbox-map-exploration'),
					{
						center: center,
						draggable: true,
						zoomControl: true,
						zoomControlOptions: {
							position: google.maps.ControlPosition.RIGHT_CENTER
						},
						mapTypeControl: false,
						streetViewControl: false,
						fullscreenControl: false,
						// for mobile to disable two fingers scroll,
						// we are full screen anyway so no need
						gestureHandling: "greedy",
						// limit max zoom to limit when 'within'
						// results are more than their max size
						minZoom: 14,
                        styles: JSON.parse(self.map.defaults)
					}
				);

				// kick the map
				self.map.object.setZoom(15);

				// hook drag for 'within' search
				googleMaps.event.addListener(this.map.object, 'bounds_changed',  function() {
                    clearTimeout(self.timer);
                    self.timer = setTimeout(function() {
                        var map = self.map.object;
                        if (map.getBounds()) {
                        	var center = map.getCenter();
                            var coordinates = {
                                n: map.getBounds().getNorthEast().lat(),
                                e: map.getBounds().getNorthEast().lng(),
                                s: map.getBounds().getSouthWest().lat(),
                                w: map.getBounds().getSouthWest().lng(),
                            }
                            CollectPoints.getWithin(coordinates, center);
                        }
                    }, 500);
                });

				self.map.object.addListener('dragstart', function() {
                    CollectPoints.lastQueryType('within');
					window.setTimeout(function() {
						self.translucent(true);
					}, 100);
				});
				self.map.object.addListener('dragend', function() {
					window.setTimeout(function() {
						self.translucent(false);
					}, 100);
				});

				// preload the tooltip html
                self.renderTooltip('HubBox_HubBox/widget/tooltip/nearest', {name:'test', distanceRounded: 'test'});
                self.renderTooltip('HubBox_HubBox/widget/tooltip/default', {name:'test', distanceRounded: 'test'});

                self.mapTileRender();

                setTimeout(function() {
					action_unSetLoading();
				}, 500);

			},

			messageShowing: function() {
				return this.message.showing();
			},

            messageTypeWarning: function() {
                return this.message.type() === 'warning';
            },

            messageTypeInfo: function() {
                return this.message.type() === 'info';
            },

            messageText: function() {
                return this.message.text();
            },

			showMessage: function(msg, type) {
				var self = this;
				self.message.text(msg);
				self.message.type(type);
				self.message.showing(true);
			},

			hideMessage: function() {
                var self = this;
                self.message.showing(false);
			},

			viewInfo: function() {
				action_viewInfo();
			},

			clearMapMarkers: function() {
				var self = this;
				if (self.markers) {
					for (var i = 0; i < self.markers.length; i++) {
						self.markers[i].setMap(null);
					}
				}
				self.map.markers = [];
			},


			cpSelect: function (collectPoint){
                action_selectCollectPoint(collectPoint);
                action_viewConfirm();
			},

			clearHomeMarker: function() {
				var self = this;
				if (self.map.homeMarker) {
					self.map.homeMarker.setMap(null);
				}
				self.map.homeMarker = null;
			},

			homeMarkerToMap: function(point) {
				var self = this;
                var homePinUrl = 'https://s3-eu-west-1.amazonaws.com/hub-box-assets/production/widget/images/pins/droplet/search-pin.svg';
                var homeMarkerImage = new google.maps.MarkerImage(homePinUrl, null, null, null, new google.maps.Size(36,36));
				if (point && point.y) {
					var position = {lat: point.y, lng: point.x};
					self.map.homeMarker = new google.maps.Marker({
						position: position,
						map: self.map.object,
						icon: homeMarkerImage,
                        zIndex: google.maps.Marker.MIN_ZINDEX - 1
					});
				}
			},

			collectPointsToMap: function() {
				var self = this;
				var bounds = new googleMaps.LatLngBounds();
				var cps = this.collectPoints();

				// clear any messages
                self.hideMessage();

                var pinUrl = 'https://s3-eu-west-1.amazonaws.com/hub-box-assets/production/widget/images/pins/droplet/hubbox-pin.svg';

                // Pins
                var pinImage = new google.maps.MarkerImage(pinUrl, null, null, null, new google.maps.Size(36,45));
                var pinImageLarge = new google.maps.MarkerImage(pinUrl, null, null, null, new google.maps.Size(48,60));


				for (var i = 0, len = cps.length; i < len; i++) {
                    var cp = cps[i];

                    // only put a marker on the map if there isn't one already there
					if (!self.map.markers[cp.id]) {

                        var position = {lat: cp.address.location.y, lng: cp.address.location.x};

                        var pinImageIcon = pinImage;
                        var pinImageIconLarge = pinImageLarge;

                        // private cp pins
                        if (cp.type !== 'hubbox') {
                        	if(cp.meta && cp.meta.network && cp.meta.network.toLowerCase() === "ups") {
														var pinImageIconImage = require.toUrl('')
																+ 'HubBox_HubBox/images/ups_brown_pin.png';
														pinImageIcon=new google.maps.MarkerImage(
																pinImageIconImage, null, null, null,
																new google.maps.Size(44, 45));
														pinImageIconLarge = new google.maps.MarkerImage(
																pinImageIconImage, null, null, null,
																new google.maps.Size(44, 45));
													} else {
														var pinImageIconImage = (window.checkoutConfig.hubBox.privatePinUrl
																&& window.checkoutConfig.hubBox.privatePinUrl.trim().length
																> 0) ?
																window.checkoutConfig.hubBox.privatePinUrl :
																require.toUrl('')
																+ 'HubBox_HubBox/images/privatepin.png';

														pinImageIcon = new google.maps.MarkerImage(
																pinImageIconImage, null, null, null,
																new google.maps.Size(44, 45));
														pinImageIconLarge = new google.maps.MarkerImage(
																pinImageIconImage, null, null, null,
																new google.maps.Size(44, 45));
													}
                        }

						self.map.markers[cp.id] = new googleMaps.Marker({
							position: position,
							map: self.map.object,
							title: cp.shortName,
							collectPoint: cp,
							icon: pinImageIcon
						});

						var marker = self.map.markers[cp.id];
						marker.addListener('click',
							self.cpSelect.bind(self, cp)
						);
						marker.set('cp', cp);

						var tipHTML  = '';
						if ((i === 0) && CollectPoints.lastQueryType() === self.queryType.nearest) {
							tipHTML = self.renderTooltip('HubBox_HubBox/widget/tooltip/nearest', cp);
						} else {
							tipHTML = self.renderTooltip('HubBox_HubBox/widget/tooltip/default', cp);
						}
						marker.set('tipHTML', tipHTML);

						marker.set('iconLarge', pinImageIconLarge);
						marker.set('iconNormal', pinImageIcon);

						marker.addListener('mouseover', function() {
							var cp = this.get('cp');
							var iconLarge = this.get('iconLarge');

							// set infowindow position
							self.map.infoWindow.setPosition(
								{
									lng: cp.address.location.x,
									lat: cp.address.location.y
								});
							// inject tip html
							self.map.infoWindow.setContent(this.get('tipHTML'));
							// open the infowindow on the marker.
							self.map.infoWindow.open(self.map.object, self.map.markers[cp.id]);
							this.setIcon(iconLarge);
						});

						marker.addListener('mouseout', function() {
							var icon = this.get('iconNormal');
							self.map.infoWindow.close();
							this.setIcon(icon);
						});

                        bounds.extend(position);
					} else {
						//console.log(cp)
					}
				}

			},

			handleNearestSearch: function() {
				var self = this;

				// show informative message
                self.showMessage('Click on a pin to view more details and checkout', 'info');

                //close the drawer
                self.drawerOpen(false);

				setTimeout(function(){
					var nearestCp = CollectPoints.nearestCollectPoint();
					if (nearestCp) {
						var nearestCenter = new googleMaps.LatLng(
							nearestCp.address.location.y,
							nearestCp.address.location.x
						);
						var lowCenter = self.offsetCenter(self.map.object, nearestCenter, 0, -150);
						self.map.object.panTo(lowCenter);
                        self.map.object.setZoom(15);
					}

                    self.mapTileRender();
				}, 700);

                // mimic mouse over to open up the tooltip
                setTimeout(function() {
                    var nearestCp = CollectPoints.nearestCollectPoint();
                    googleMaps.event.trigger(self.map.markers[nearestCp.id], 'mouseover');
                }, 1000);

			},

			// simple html rendering function for google maps hover tooltips
            renderTooltip: function(name, data){
                var temp = $("<div>");
                ko.applyBindingsToNode(temp[0], { template: { name: name, data: data } });
                var html = temp.html();
                temp.remove();
                return html;
			},


			offsetCenter: function (theMap, latlng, offsetx, offsety) {
				// latlng is the apparent centre-point
				// offsetx is the distance you want that point to move to the right, in pixels
				// offsety is the distance you want that point to move upwards, in pixels
				// offset can be negative
				// offsetx and offsety are both optional

				var scale = Math.pow(2, theMap.getZoom());

				var worldCoordinateCenter = theMap.getProjection().fromLatLngToPoint(latlng);
				var pixelOffset = new google.maps.Point((offsetx/scale) || 0,(offsety/scale) ||0);

				var worldCoordinateNewCenter = new google.maps.Point(
					worldCoordinateCenter.x - pixelOffset.x,
					worldCoordinateCenter.y + pixelOffset.y
				);

				return theMap.getProjection().fromPointToLatLng(worldCoordinateNewCenter);
			},

			mapTileRender: function() {
				if (this.map.object) {
					googleMaps.event.trigger(this.map.object, "resize");
				}
			},

			initialize: function () {
				this._super();
				var self = this;

				this.map.infoWindow = new google.maps.InfoWindow({map: self.map.object, disableAutoPan: true});

                self.show.subscribe(function(inView) {
                    if (inView) {
                    	if(self.map.object == null || $('#hubbox-map-exploration').html().trim() === '') {
                    		self.clearMapMarkers();
                    		self.bootMap();
                        }
                    }
                });

                CollectPoints.requestRunning.subscribe(function(running) {

                    var lastQueryType = CollectPoints.lastQueryType();
                	if (running && lastQueryType === self.queryType.nearest) {
                		action_setLoading();
                    }
				});

				CollectPoints.collectPoints.subscribe(function(arr){

                    var lastQueryType = CollectPoints.lastQueryType();

                    action_unSetLoading();

					if (arr.length > 0) {
						self.collectPointsToMap();
					}

                    if (lastQueryType === self.queryType.nearest) {
                        self.handleNearestSearch();
                    }
				});

				CollectPoints.feedback.subscribe(function(msg){

					if (msg !== "") {
						action_unSetLoading();

						self.showMessage(msg, 'warning');

						// if there are no collect points, reboot map
						if (CollectPoints.collectPoints.length === 0) {
							// check if map booted ok, better to show a map that a grey square
                            if (!self.map.object.getCenter()) {
                                var center = {
                                    lat: 51.457,
                                    lng: -0.1844
                                };
                                self.map.object.setCenter(center);
                                self.map.object.setZoom(13);
                                self.mapTileRender();
							}
						}
					}

                }),

				CollectPoints.homePoint.subscribe(function(pin){
					self.clearHomeMarker();
					self.homeMarkerToMap(pin);
				});

				return this;
			}
		});
	}
);
