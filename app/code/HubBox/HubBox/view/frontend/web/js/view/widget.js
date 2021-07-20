define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'HubBox_HubBox/js/model/hubbox'
    ],
    function (
        $,
        ko,
        Component,
        _,
        hubBox
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'HubBox_HubBox/widget',
            },
            loading: hubBox.loading,
	        show: hubBox.open,


            initialize: function () {
                this._super();
                var self = this;

                // on mobile'fix' the body to stop weird cursor issues on mobile
                hubBox.open.subscribe(function(open) {
                    if (window.innerWidth < 650) {
                        if (open === true) {
                            $('body').addClass('hubbox-widget-open');
                        } else {
                            $('body').removeClass('hubbox-widget-open');
                        }
                    }
                });
            }

        });
    }
);
