define(
    [
        'ko',
        'uiComponent',
        'HubBox_HubBox/js/model/hubbox',
        'Magento_Ui/js/form/form'
    ],
    function (
        ko,
        Component,
        hubBox
    ) {
        'use strict';

        return Component.extend({

            defaults: {
                template: 'HubBox_HubBox/widget/firstlastnameform',
            },

            show:  ko.computed(function(){
                return window.checkoutConfig.hubBox.showFirstLastname;
            }),

            firstname: ko.observable(""),

            lastname: ko.observable(""),


            initialize: function () {
                this._super();
                var self = this;

                self.firstname.subscribe(function(firstname) {
                    hubBox.firstName(firstname);
                });

                self.lastname.subscribe(function(lastname) {
                    hubBox.lastName(lastname);
                });

                return this;
            },

            onSubmit: function() {
                // trigger form validation
                this.source.set('params.invalid', false);
                this.source.trigger('hbCheckoutForm.data.validate');

                // verify that form data is valid
                if (!this.source.get('params.invalid')) {
                    // data is retrieved from data provider by value of the customScope property
                    var formData = this.source.get('customCheckoutForm');
                    // do something with form data
                    console.dir(formData);
                }
            }

        });
    }
);
