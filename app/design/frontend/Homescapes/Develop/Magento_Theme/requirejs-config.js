var config = {
    paths: {
            'bootstrap':'Magento_Theme/js/bootstrap.bundle',
            'owl_carousel':'Magento_Theme/js/owl.carousel',
            'owl_config':'Magento_Theme/js/owl.config',
            'time_counter':'Magento_Theme/js/jquery.time-to',
	        'slickd': 'Magento_Theme/js/slick.min',
            'category_custom' : 'Magento_Theme/js/category_custom'
    } ,
	shim: {
        'bootstrap': {
            'deps': ['jquery']
        },
		'owl_carousel': {
            'deps': ['jquery']
        },
		'owl_config': {
            'deps': ['jquery','owl_carousel']
        },
        'time_counter': {
            'deps': ['jquery']
        },
    	'slickd': {
                'deps': ['jquery']
            },
        'category_custom': {
                'deps': ['jquery']
            }    

    }
};


