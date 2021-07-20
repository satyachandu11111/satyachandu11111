define('jquery', function($) {
        $.fn.CategoryTitlePosition = function() {
        	$(window).resize(function() {

        		var $containerWidth = $(window).width();
        		
        		if ($containerWidth <= 767) {
        			
        			if($('.column.main .category-image').length > 0){
			        	$('.category-image').insertAfter('.column.main');
			        }

			    	if($('.column.main .page-title-wrapper').length > 0){
			    		$('.page-title-wrapper').insertAfter('.column.main');            	
			   		}
        		}else{

        			if($('.columns .category-image').length > 0){
        				$('.column.main').prepend($('.category-image'));
        			}

        			if($('.columns .page-title-wrapper').length > 0){        				
        				$('.column.main').prepend($('.page-title-wrapper'));
        			}

        		}


        	});

        	var $containerWidth = $(window).width();        		
        		if ($containerWidth <= 767) {
        			
        			if($('.column.main .category-image').length > 0){
			        	$('.category-image').insertAfter('.column.main');
			        }

			    	if($('.column.main .page-title-wrapper').length > 0){
			    		$('.page-title-wrapper').insertAfter('.column.main');            	
			   		}
        		}else{

        			if($('.columns .category-image').length > 0){
        				$('.column.main').prepend($('.category-image'));
        			}

        			if($('.columns .page-title-wrapper').length > 0){
        				$('.column.main').prepend($('.page-title-wrapper'));
        			}

        		}
    
        }
}(jQuery));