define(['jquery'],
 function($) {
    'use strict';
    
          $('.swatch-checkbox').on('change', function(element){
                    console.log(element);
                    var samplecountdown=parseInt($('#sample_count').val());   
                    //alert('You have selected a button');
                    console.log(this.id);
                    
                    
                if($(this).prop( "checked") == true)
                {
                    if(samplecountdown<=4)
                    {
                        var productId = $(this).attr('data-id');
                        var productImage = $(this).attr('data-img');
                        var productName = $(this).attr('data-name');
                        var url= $(this).attr('data-url');
                        console.log(url);                   
                        
                         var request = $.ajax( {
                                url: url ,
                                data: { productId: productId, productImage : productImage, productName: productName },
                                type: 'POST',
                                showLoader: true,
                                dataType:'json',
                                beforeSend: function(){
                                    $('.please-wait'+productId).show();
                                    },
                            } );
                        
                        request.done( function (result)
                        {   
                            console.log(result);
                            console.log(result.html);
                            var json = result;                
                            if(json.productnumber=='0' || json.productnumber==0)
                            {
                                $("#updatedswatches > li:nth-child(1)").before(json.html);

                            }
                            else
                            {
                               $("#updatedswatches > li:nth-child("+json.productnumber+")").after(json.html);
                            }
                            $('#updatedswatches li:last-child').remove();
                            $('.please-wait'+productId).hide(); 
                            $('.swatch-label'+productId).html("remove swatch");
                            $('#sample_count').val(samplecountdown+1);
                            $('.swatches-dialog').show();  
                            $( ".vertical-swatch" ).show();
                            $(".order-swatch-verticalcount").html(" ("+$('#sample_count').val()+")");                
                        } );
                        
                    }
                }
                    
            });
        
    

});