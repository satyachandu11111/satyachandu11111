
define(
	[
		'jquery',
		'ko',
		'uiComponent',
		'underscore',
		'googlemaps!',

		'HubBox_HubBox/js/model/hubbox',
		'HubBox_HubBox/js/model/collectpoints',
		'HubBox_HubBox/js/action/view-explore',
        'HubBox_HubBox/js/action/set-loading'
	],
	function (
		$,
		ko,
		Component,
		_,
		googleMaps,

		hubBox,
		CollectPoints,
		action_viewExplore,
		action_setLoading
	) {
		'use strict';

        return Component.extend({

			defaults: {
				template: 'HubBox_HubBox/widget/info',
			},

			searchQuery: hubBox.postCode,

			show:  ko.computed(function(){
				return hubBox.view() === 'info';
			}),

			searchClick: function() {

				if ($('#hubbox-search-input-info').val().length >= 3) {
                    CollectPoints.getNearest($('#hubbox-search-input-info').val());
                    action_viewExplore();
                    action_setLoading();
				}
			}

		});
	}
);
