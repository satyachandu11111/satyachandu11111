var config = {
    paths: {
            'bootstrap':'Magento_Theme/js/bootstrap.bundle',
            'owl_carousel':'Magento_Theme/js/owl.carousel',
            'owl_config':'Magento_Theme/js/owl.config',
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
        }
    }
};


