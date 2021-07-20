/**
 * Created by labnotes on 28/04/2017.
 */
define(
		[
			'ko',
			'jquery'
		],
		function (ko, $) {
			'use strict';

			var requestRunning = ko.observable(false);
			var collectPoints = ko.observableArray();
			var nearestCollectPoint = ko.observable(null);
			var homePoint = ko.observable(null);
			var feedback = ko.observable(null);

			/**
			 *
			 * @param a
			 * @param b
			 * @returns {boolean}
			 */
			var sortCollectPointsByDistance = function(a, b) {
				return a.distance - b.distance;
			};

			/**
			 * 'within' / 'nearest', we need to know when laying out the frontend
			 */
			var lastQueryType = ko.observable(null);

			return {
				collectPoints: collectPoints,
				homePoint: homePoint,
				lastQueryType: lastQueryType,
				nearestCollectPoint: nearestCollectPoint,
				requestRunning : requestRunning,
				feedback: feedback,
				daysOfTheWeek : {
					'monday': 'Mon',
					'tuesday': 'Tue',
					'wednesday': 'Weds',
					'thursday': 'Thu',
					'friday': 'Fri',
					'saturday': 'Sat',
					'sunday': 'Sun'
				},

				/**
				 * Inital 'Nearest' search sets up the map nearest to the query
				 * @param query
				 */
				getNearest: function(query) {
					var self = this;
					self.feedback('');

					if (query) {

						// remember the query, we can use it later
						sessionStorage.setItem("hubBox_postCode", JSON.stringify(query));
						var boost = window.checkoutConfig.hubBox.privateBoost;
						var size = 10;
						var distance = 10;
						var boostDistance = parseFloat(window.checkoutConfig.hubBox.privateDistance);
						var countryCode = 'GB';
						var data = {
							query: query,
							size: size,
							privateDist: boostDistance,
							dist: distance,
							countryCode: countryCode,
						};
						if(boost) data.separate = true;
						self.requestRunning(true);
						$.ajax({
							method: 'GET',
							url: window.checkoutConfig.hubBox.searchUrlNearest,
							data: data,
							cache: false,
							contentType: 'application/json'
						}).done(function (response) {
							if (!response || !response.point || !response.page) {
								self.feedback('There is a problem, please try again');
								return null;
							}
							var data = response;
							if (data._embedded && data._embedded.collectpoints) {
								self.lastQueryType('nearest');
								var cps = [];
								if(boost) {
									var boostArrays = [];
									var fill = [];
									// Add private to head
									boostArrays.push([data._embedded.private[0]]);

									// Prep fill
									var fillNumber = size - 1 - (data._embedded.private.length - 1);
									fill.push(data._embedded.hubbox.slice(0, fillNumber));
									fill.push(data._embedded.private.slice(1, data._embedded.private.length));
									fill.sort(sortCollectPointsByDistance);
									boostArrays.push([].concat.apply([], fill));

									// Finish collection
									var flatList = [].concat.apply([], boostArrays);
									cps = self.processCollectPoints(flatList, true);
								} else {
									cps = self.processCollectPoints(data._embedded.collectpoints, true);
								}
								self.nearestCollectPoint(cps[0]);
								homePoint(data.point);
								collectPoints.removeAll();
								ko.utils.arrayPushAll(collectPoints, cps);
							} else {
								self.feedback('We’re sorry but there are no Collect Points in this area');
							}
						}).fail(function() {
							self.feedback('There is a problem, please try again');
						}).always(function () {
							setTimeout(function () {
								self.requestRunning(false);
							}, 3000)
						});
					}
				},

				/**
				 * within search, on drag of the map
				 * @param coordinates
				 * @param center
				 * @returns {null}
				 */
				getWithin: function(coordinates, center) {
					var self = this;
					self.feedback('');

					if(!self.requestRunning()) {

						// deal with the possibility there is no center point,
						// shouldn't happen but whatever
						if(!homePoint()) {
							var point = {x: center.lng(), y: center.lat()};
							homePoint(point);
						}

						self.requestRunning(true);
						var data = Object.assign(coordinates, {size: 50});
						return $.ajax({
							method: "GET",
							url: window.checkoutConfig.hubBox.searchUrlWithin,
							data: data
						}).done(function(response) {
							if (!response || !response.point || !response.page) {
								self.feedback('There is a problem, please try again');
							} else {
								var data = response;
								if (data.page.totalElements && data._embedded.collectpoints) {
									self.lastQueryType('within');
									var cps = self.processCollectPoints(data._embedded.collectpoints, false);
									self.nearestCollectPoint(cps[0]);
									collectPoints.removeAll();
									ko.utils.arrayPushAll(collectPoints, cps);
								} else {
									self.feedback('We’re sorry but there are no Collect Points in this area');
								}
							}
						}).fail(function() {
							self.feedback('There is a problem, please try again');
						}).always(function() {
							self.requestRunning(false);
						});
					}
					return null;
				},

				/**
				 * @param collectPoints
				 * @param nearestSearch
				 * @param shouldResort
				 * @returns enhancedCollectPoints enhanced and sorted
				 */
				processCollectPoints : function(collectPoints, nearestSearch, shouldResort) {
					var self = this;
					var enhancedCollectPoints = collectPoints;
					for (var i = 0; i < enhancedCollectPoints.length; i++) {
						// Extend with new hours format
						var reducedOpenings = self.reduceOpenTimesSeparate(enhancedCollectPoints[i].openingHours);
						$.extend(enhancedCollectPoints[i], {openingHoursReduced: reducedOpenings});

						// extend with distance from home pin
						var rad = function(x) {
							return x * Math.PI / 180;
						};
						if (nearestSearch) {
							$.extend(enhancedCollectPoints[i], {distance: enhancedCollectPoints[i]});
						} else {
							var homePos = {lng : self.homePoint().x, lat: self.homePoint().y};
							var cpPos = {lng: enhancedCollectPoints[i].address.location.x, lat: enhancedCollectPoints[i].address.location.y}
							var R = 6378137; // Earth’s mean radius in meter
							var dLat = rad(cpPos.lat - homePos.lat);
							var dLong = rad(cpPos.lng - homePos.lng);
							var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
									Math.cos(rad(cpPos.lat)) * Math.cos(rad(homePos.lat)) *
									Math.sin(dLong / 2) * Math.sin(dLong / 2);
							var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
							var d = R * c;
							$.extend(enhancedCollectPoints[i], {distance: d / 1000 });
						}
						$.extend(enhancedCollectPoints[i], {distanceRounded: self.roundDistance(enhancedCollectPoints[i].distance)});
					}

					if(shouldResort) enhancedCollectPoints.sort(sortCollectPointsByDistance);

					// Add index and closest where relevant
					for (var i = 0; i < enhancedCollectPoints.length; i++) {
						$.extend(enhancedCollectPoints[i], {index: i + 1});
						if (i === 0 && nearestSearch) {
							$.extend(enhancedCollectPoints[i], {closest: 1})
						}
					}

					return enhancedCollectPoints;
				},


				getOpenObject : function(startName, endName, open, close) {
					var self = this;
					return {
						'startName': startName,
						'endName': endName,
						'open': open.trim(),
						'close': close.trim(),
						'friendly': function () {
							if (this.startName === this.endName) {
								return self.daysOfTheWeek[this.startName];
							} else {
								return self.daysOfTheWeek[this.startName]  + ' - ' + self.daysOfTheWeek[this.endName];
							}
						},
						// handle closed - closed
						'range': function () {
							if (this.open.trim() === this.close.trim() || this.close.trim().length === 0) {
								return this.open;
							} else {
								return this.open  + ' - ' + this.close;
							}
						}
					}
				},

				/**
				 * @param openingHours
				 * @returns {opens: {}} enhanced and sorted
				 */
				reduceOpenTimesSeparate : function(openingHours) {
					var self = this;
					var opens = [];
					for (var day in self.daysOfTheWeek) {
						if (openingHours[day].open && openingHours[day].close) {
							var open = openingHours[day].open;
							var close = openingHours[day].close;
							if (day === 'monday') {
								opens.push(self.getOpenObject(day, day, open, close));
								continue;
							}
							var lastOpen = opens[opens.length-1];
							// append to last range
							if (opens.length > 0 && lastOpen.open === open && lastOpen.close === close) {
								opens[opens.length-1].endName = day;
								// add new open
							} else {
								opens.push(self.getOpenObject(day, day, open, close));
							}
						}
					}
					return opens;
				},

				roundDistance : function distanceDisplay(distance) {
					if(distance > 5) {
						return Math.round(distance) + " KM";
					}
					if(distance > 0.1) {
						return Math.round(distance*10) /10 + " KM";
					}
					return Math.round(distance* 1000) + " Metres";
				},

				getPostcode: function(postcode, country) {}
			}

		}
);
