/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */
define([
    'jquery',
    'uiComponent',
    'ko',

], function ($, Component, ko) {
    'use strict';

    var collectavailable = window.collectavailable;
    return Component.extend({
        defaults: {
            template: 'Jjcommerce_CollectPlus/cart/shipping/collectplus-select'
        },
        collectavailable:collectavailable
    });

});
