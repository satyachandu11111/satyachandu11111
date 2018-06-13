define([
    'jquery',
    'jquery/ui',
    'mage/url',
   'mage/storage',
], function($, urlBuilder,storage) {
    "use strict";

    $.widget('mage.orderswatches', {
      // Logicial code

      // Optional 
      options: {
         urlgetproducts: {},
      },

      _create: function() {
          console.log('create function');
          this._initializeOptions();
          this._initializeheader();
          //this._headerShowHide();
          this._addOrderSwatch();          
         // Init code
         //this.closeModal();
      },

      _initializeOptions: function () {
           var self = this;
           var serviceUrl = this.options.urlgetproducts;
           
            $.ajax( {url: serviceUrl ,
                                data: { },
                                type: 'POST',                                
                                dataType:'json',
                                
                            } ).done(
               function (response) {
                   //console.log(response.selectedProducts);
                   //console.log(response.selectedProducts.length);
                   if(response.selectedProducts != null){
                    var selectedProducts = response.selectedProducts.split(',');

                    jQuery.each(selectedProducts, function(index, item) {
                         $('#swatch-checkbox'+item).prop('checked',true); 
                         $('.swatch-label'+item).html('remove swatch');
                         //console.log(item);
                     }.bind(this));
                   }     
               }
           ).fail(
               function (response) {
                   console.log(response);
               }
           );
          
          
          
          
      },
      _initializeheader : function(){
          var self = this;
          //console.log('urlHeaderSwatch');
          var urlHeaderSwatch = this.options.urlHeaderSwatch;
          //console.log(urlHeaderSwatch);
          $.ajax( {url: urlHeaderSwatch ,
                                data: { },
                                type: 'POST',                                
                                dataType:'json',
                                
                            } ).done(
               function (response) {
                   //console.log(response.html);
                   $('#header-swatch').html(response.html);
                   self._headerShowHide();
                   //this._headerShowHide();
               }
           ).fail(
               function (response) {
                   console.log(response);
               }
           );
          
      },
      _headerShowHide : function(){
          //console.log('_headerShowHide');
          var self = this;
          // header swatch enable if swatch exist 
          var samplecountdown=parseInt($('#sample_count').val());   
          //console.log(samplecountdown);
            if($('#swatchcloseEvent').val() =='close123')
            {
                if(samplecountdown>0)
                {   
                    $(".order-swatch-verticalcount").html(" ("+samplecountdown+")"); 
                    $(".vertical-swatch").fadeIn(); 
                    $(".swatches-dialog").hide();
                }
                else
                {           
                    $(".vertical-swatch").hide(); 
                    $(".swatches-dialog").hide();
                }
            }
            else
            {
                if(samplecountdown>0)
                {      
                    $(".order-swatch-verticalcount").html(" ("+samplecountdown+")");       
                    $(".vertical-swatch").show(); 
                    $(".swatches-dialog").show();
                }
                else
                {
                    $(".vertical-swatch").hide(); 
                    $(".swatches-dialog").hide();           
                }
            }
            self._removeOrderSwatchHeader();
            self._closeOrderSwatch();
            self._openSwatch();
      },
      
      _addOrderSwatch : function() {
          var self = this;
          
          $('.swatch-checkbox').on('change', function(element){
                    
                    var samplecountdown=parseInt($('#sample_count').val());                       
                    //console.log(this.id);
                    
                    
                if($(this).prop( "checked") == true)
                {
                    var productId = $(this).attr('data-id');
                    if(samplecountdown<=4)
                    {
                        
                        var productImage = $(this).attr('data-img');
                        var productName = $(this).attr('data-name');
                        var url= $(this).attr('data-url');
                        //console.log(url);                   
                        
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
                            //console.log(result);
                            //console.log(result.html);
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
                            self._removeOrderSwatchHeader();
                        } );
                        
                    }else{
                        //console.log('greated than 5');
                        $('.swatch-quantity-error').hide();
                        //console.log('#swatch-checkbox'+productId);
                        $('#swatch-checkbox'+productId).attr('checked', false);
                        $('#swatch-checkbox'+productId).nextAll('p').show();
                    }
                    
                }
                else
                {
                    // remove product from disselect checkbox
                    console.log('remove product from disselect checkbox');
                    $('.swatch-quantity-error').hide();
                    var samplecountdown=parseInt($('#sample_count').val());   
                    var url= self.options.removeProductSample;
                    var productId = $(this).attr('data-id');
                    var request = jQuery.ajax( {
                            url: url ,
                            data: { productId: productId},
                            type: 'POST',
                            showLoader: true,
                            beforeSend: function(){
                                jQuery('.please-wait'+productId).show();
                                },
                        } );
                        request.done( function (result)
                        {   
                            console.log('----remove product------');                            
                            var json = result;
                            $("#updatedswatches li:nth-child("+json.productnumber+")").remove();

                            $('ul#updatedswatches').append(json.html);
                            $('.please-wait'+productId).hide();
                            if(samplecountdown==1)
                            {
                              $( ".swatches-dialog" ).hide();
                              $( ".vertical-swatch" ).hide();
                            }
                            $('#sample_count').val(samplecountdown-1);
                            $(".order-swatch-verticalcount").html(" ("+$('#sample_count').val()+")"); 
                            $('.swatch-label'+productId).html("Order free swatch");       
                        } );
                        request.fail( function ( error )
                        {
                            console.dir(error);             
                        } );
                }
                    
            });
          
      },
      _removeOrderSwatchHeader : function(){
          console.log('remove click');
          var self = this;
          $('.remove-swatch').bind('click', function(element){
              
              // remove product from onclick 
                    console.log('remove product onclick event');
                    $('.swatch-quantity-error').hide();
                    var samplecountdown=parseInt($('#sample_count').val());   
                    var url= self.options.removeProductSample;
                    var productId = $(this).attr('data-id');
                    var request = $.ajax( {
                            url: url ,
                            data: { productId: productId},
                            type: 'POST',
                            showLoader: true,
                            beforeSend: function(){
                                //jQuery('.please-wait'+productId).show();
                                },
                        } );
                        request.done( function (result)
                        {   
                            console.log('----remove product------');                            
                            var json = result;
                            $("#updatedswatches li:nth-child("+json.productnumber+")").remove();

                            $('#swatch-checkbox'+productId).attr('checked',false);
                            $('.swatch-label'+productId).html("Order free swatch");
                            $('ul#updatedswatches').append(json.html);
                            //$('.please-wait'+productId).hide();
                            if(samplecountdown==1)
                            {
                              $( ".swatches-dialog" ).hide();
                              $( ".vertical-swatch" ).hide();
                            }
                            $('#sample_count').val(samplecountdown-1);
                            $(".order-swatch-verticalcount").html(" ("+$('#sample_count').val()+")"); 
                            $('.swatch-label'+productId).html("Order free swatch");       
                        } );
                        request.fail( function ( error )
                        {
                            console.dir(error);             
                        } );
              
          });
          
      },
      
    _closeOrderSwatch : function(){
        var self = this;
        $('.swatches-dialog .btn-close').on('click', function(element){
               var samplecountdown=parseInt(jQuery('#sample_count').val());
               var url= self.options.closeswatch;              
               
                var request = $.ajax( {
                            url: url ,
                            data: { flag: 'close'},
                            type: 'POST'                                                        
                        } );
                request.done( function (result){$('#swatchcloseEvent').val("close");});   
                request.fail( function ( error )
                    {
                        console.dir(error);             
                    } ); 
            $('.swatches-dialog').fadeOut(400);          
            $(".vertical-swatch").show(); 
            if(samplecountdown==0)
            {
              $( ".vertical-swatch" ).hide();            
            }  
               
               
        });
    },
    
    _openSwatch : function(){
        var self = this;
        $('.order-swatch-vertical').on('click', function(element){
               var samplecountdown=parseInt(jQuery('#sample_count').val());
               var url= self.options.closeswatch;              
               
                var request = $.ajax( {
                            url: url ,
                            data: { flag: 'open'},
                            type: 'POST'                                                        
                        } );
                request.done( function (result){$('#swatchcloseEvent').val("open");});   
                request.fail( function ( error )
                    {
                        console.dir(error);             
                    } ); 
            $('.swatches-dialog').fadeIn(400);        
            if(samplecountdown>0)
            {
              $( ".vertical-swatch" ).show();  
            }  
            else
            {
                $( ".vertical-swatch" ).hide();
            }
               
               
        });
    }

    });

    return $.mage.orderswatches;
});