/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

 var config = {
 	map: {
 		'*': {
             custompriceBox:  'Homescapes_Completelook/js/custompricebox',             
             mdConfigurable: 'Homescapes_Completelook/js/configurable'
        }
    },
    shim: {        
        'Magedelight_Looknbuy/js/configurable':
        {
            deps: ['jquery']
        },
    }
};