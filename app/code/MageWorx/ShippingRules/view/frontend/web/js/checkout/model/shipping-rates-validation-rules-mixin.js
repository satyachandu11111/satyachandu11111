/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper'
],function ($, Wrapper) {
    "use strict";

    var additionalFields = [
        'region',
        'region_id',
        'street',
        'city',
        'country_id',
        'postcode'
    ];

    var additionalRules = {
        'postcode': {
            'required': true
        },
        'country_id': {
            'required': true
        },
        'region_id': {
            'required': true
        },
        'region_id_input': {
            'required': true
        },
        'city': {
            'required': true
        }
    };

    return function (origRules) {
        origRules.getObservableFields = Wrapper.wrap(
            origRules.getObservableFields,
            function (originalAction) {
                var fields = originalAction();

                additionalFields.forEach(function (field) {
                    if (fields.indexOf(field) === -1) {
                        fields.push(field);
                    }
                });

                return fields;
            }
        );

        origRules.getRules = Wrapper.wrap(
            origRules.getRules,
            function (originalAction) {
                var rules = originalAction();
                rules['mageworx'] = additionalRules;

                return rules;
            }
        );

        return origRules;
    };
});
