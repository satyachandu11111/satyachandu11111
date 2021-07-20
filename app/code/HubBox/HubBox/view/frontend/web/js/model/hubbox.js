define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/checkout-data',
        'mage/storage',

        'HubBox_HubBox/js/model/checkout-data',

        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address'
    ],
    function (
        $,
        ko,
        quote,
        addressConverter,
        checkoutData,
        storage,
        hbCheckoutData,

        customer,
        addressList,
        createShippingAddress,
        selectShippingAddress
    ) {
        'use strict';

        var open = ko.observable(false);

        // confirmed collect point, the big one
        var collectPoint = ko.observable(null);
        var collectPointConfirmed = ko.computed(function(){
            return collectPoint() !== null;
        });

        // selected collect point, we are just looking for now
        var selectedCollectPoint = ko.observable(null);
        var collectPointSelected = ko.computed(function(){
            return selectedCollectPoint() !== null;
        });

        var currentView = ko.observable('info'); // default to info page
        var loading     = ko.observable(false);
        var firstName   = ko.observable('Collect');
        var lastName    = ko.observable('Point:');
        var postCode    = ko.observable(null);

        var newShippingAddress;
        var shippingAddress = {};

        firstName.subscribe(function(firstName) {
           // Update shipping if set
        });

        function getCompanyAppend(cp) {
            var str = '';
            if(cp.type === 'hubbox') {
                return ' HubBox';
            }
            if(cp.type === 'ups') {
                return ' UPS D2R';
            }
            if(((cp|| {}).meta||{}).connect) {
                return ' Connect';
            }
            return str;
        }

        function getCollectPointType(cp) {
            var type = cp.type;
            if(((cp || {}).meta || {}).connect) {
                return 'connect';
            }
            return type;
        }

        /**
         * Add address to order from cp data
         * @param cp
         */
        function setHubBoxAddressToOrder(cp)
        {
            hbCheckoutData.setCollectPoint(cp);

            if (customer.isLoggedIn()) { shippingAddress.customer_id = customer.customerData.id; }

            shippingAddress.firstname   = firstName();
            shippingAddress.lastname    = lastName();
            shippingAddress.company     = cp.shortName + getCompanyAppend(cp);
            shippingAddress.city        = (cp.address.city.trim().length > 0) ? cp.address.city : 'Unknown';
            shippingAddress.postcode    = cp.address.postcode;
            shippingAddress.telephone   = '02078594577';
            shippingAddress.country_id  = 'GB';
            shippingAddress.street      =  [cp.address.street1, cp.address.street2];
            shippingAddress.region      = cp.address.region;
            shippingAddress.save_in_address_book    = 0;
            shippingAddress.custom_attributes       = { isHubBoxAddress: true };

            newShippingAddress = createShippingAddress(shippingAddress);
            checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
            selectShippingAddress(newShippingAddress);
        }

        /**
         * Clear HB shipping data from order
         */
        function clearShippingFromOrder()
        {
            hbCheckoutData.setCollectPoint(null);
            checkoutData.setSelectedShippingAddress(null);
            var address = addressConverter.formAddressDataToQuoteAddress({});
            quote.shippingAddress(address);
        }

        /**
         * Set HubBox in the backend
         */
        function setHubBox()
        {
            var deferred = $.Deferred();
            var cp = collectPoint();
            var params = {
                isHubBox: 1,
                collectPointId: cp.id,
                collectPointType: getCollectPointType(cp)
            };
            var paramstr = jQuery.param( params );

            storage.get(
                'hubbox/recalculate/index?' + paramstr,
                true
            ).done(
                function (response) {
                    console.log('HubBox set success');
                }
            ).fail(
                function (response) {
                    console.log('HubBox set fail');
                }
            ).always(
                function(response) {
                    deferred.resolve();
                }
            );

            return deferred.promise();
        }

        /**
         * Unset HubBox from the backend
         */
        function unsetHubBox()
        {
            var deferred = $.Deferred();
            var params = {
                isHubBox: 0
            };
            var paramstr = jQuery.param( params );
            var addresses = addressList();
            var filteredAddressList = [];
            for (let i = 0; i < addresses.length; i++) {
                var address = addresses[i];
                if(!address.isHubBoxAddress || !address.isHubBoxAddress()) {
                    filteredAddressList.push(address);
                }
            }
            addressList(filteredAddressList);
            storage.get(
                'hubbox/recalculate/index?' + paramstr
            ).done(
                function (response) {
                    console.log('HubBox unset success');
                }
            ).fail(
                function (response) {
                    console.log('HubBox unset fail');
                }
            ).always(
                function(response) {
                    deferred.resolve();
                }
            );

            return deferred.promise();
        }

        return {
            open: open,
            firstName: firstName,
            lastName: lastName,
            postCode: postCode,
            loading: loading,
            view: currentView,

            collectPoint: collectPoint,
            collectPointConfirmed: collectPointConfirmed,

            selectedCollectPoint: selectedCollectPoint,
            collectPointSelected: collectPointSelected,

            setHubBoxAddressToOrder: setHubBoxAddressToOrder,
            clearShippingFromOrder: clearShippingFromOrder,

            setHubBox: setHubBox,
            unsetHubBox: unsetHubBox,

            /**
             * Set the shipping first name / last name again
             * @param firstName
             * @param lastName
             */
            updateFirstLastname: function(firstName, lastName){

                // Set the Firstname and the Lastname
                shippingAddress.firstname   = firstName;
                shippingAddress.lastname    = lastName;

                newShippingAddress = createShippingAddress(shippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                selectShippingAddress(newShippingAddress);

            }

        }
    }
);
