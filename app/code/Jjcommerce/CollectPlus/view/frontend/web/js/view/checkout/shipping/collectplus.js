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
    'Magento_Shipping/js/model/config'

], function ($, Component, ko, config) {
    'use strict';

    var collectavailable = Number(window.collectavailable);
    var collectlogo = window.collect_logo;
    //alert(collectavailable);
    if(!collectavailable) {
        return Component.extend({
            config: config(),
            collectlogo:collectlogo,
            collectavailable:collectavailable
        });
    }
    return Component.extend({
        defaults: {
            template: 'Jjcommerce_CollectPlus/checkout/shipping/collectplus-select'
        },
        config: config(),
        collectlogo:collectlogo,
        collectavailable:collectavailable
    });

});
