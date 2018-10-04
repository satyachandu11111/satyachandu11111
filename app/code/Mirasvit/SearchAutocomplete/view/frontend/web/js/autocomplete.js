define([
    'jquery',
    'ko',
    'underscore',
    'mage/translate',
    'Magento_Catalog/js/price-utils',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, ko, _, $t, priceUtils) {
    "use strict";
    
    var $input;
    var isVisible = false;
    var isShowAll = true;
    var loading = false;
    
    ko.bindingHandlers.highlight = {
        init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
            var needle      = bindingContext.$parents[2].result.query,
                haystack    = $(element).html(),
                regEx       = new RegExp(needle, "ig"),
                replaceMask = '<span class="searchautocomplete__highlight">' + needle.charAt(0) + needle.slice(1) + '</span>';
            
            $(element).html(haystack.replace(regEx, replaceMask));
        }
    };
    
    ko.bindingHandlers.price = {
        init: function (element) {
            $(element).html(priceUtils.formatPrice($(element).html(), window.priceFormat));
        }
    };
    
    return {
        placeholderSelector: '.searchautocomplete__autocomplete',
        wrapperSelector:     '.wrapper',
        
        xhr: null,
        
        config: {
            query:           '',
            priceFormat:     {},
            minSearchLength: 3,
            url:             '',
            delay:           300,
            popularSearches: []
        },
        
        init: function (selector, config) {
            $input = $(selector);
            
            this.config = _.defaults(config, this.config);
            
            window.priceFormat = this.config.priceFormat;
            
            this.doSearch = _.debounce(this._doSearch, this.config.delay);
            
            $($('#searchAutocompletePlaceholder').html()).appendTo($input.parent());
        },
        
        $spinner: function () {
            return $(".searchautocomplete__spinner");
        },
        
        search: function () {
            this.ensurePosition();
            
            $input.off("keydown");
            $input.off("blur");
            
            if (this.xhr != null) {
                this.xhr.abort();
                this.xhr = null;
            }
            
            if ($input.val().length >= this.config.minSearchLength) {
                this.doSearch();
            } else {
                return this.doPopular();
            }
            
            return true;
        },
        
        _doSearch: function () {
            isVisible = true;
            
            this.$spinner().show();
            
            this.xhr = $.ajax({
                url:      this.config.url,
                dataType: 'json',
                type:     'GET',
                data:     {
                    q:   $input.val(),
                    cat: false
                },
                success:  function (data) {
                    this.processApplyBinding(data);
                    
                    this.$spinner().hide();
                }.bind(this)
            });
        },
        
        viewModel: function (data) {
            var model = {
                onMouseOver: function (item, event) {
                    $(event.currentTarget).addClass('_active');
                }.bind(this),
                
                onMouseOut: function (item, event) {
                    $(event.currentTarget).removeClass('_active');
                }.bind(this),
                
                afterRender: function (el) {
                    $(el).catalogAddToCart({});
                }.bind(this),
                
                onClick: function (item, event) {
                    if (event.button === 0) { // left click
                        event.preventDefault();
                        
                        if ($(event.target).closest('.tocart').length) {
                            return;
                        }
                        
                        if (event.target.nodeName === 'A'
                            || event.target.nodeName === 'IMG'
                            || event.target.nodeName === 'LI'
                            || event.target.nodeName === 'SPAN'
                            || event.target.nodeName === 'DIV') {
                            this.enter(item);
                        }
                    }
                }.bind(this),
                
                onSubmit: function (item, event) {
                }.bind(this),
                
                bindPrice: function (item, event) {
                    return true;
                }.bind(this)
            };
            
            model.isVisible = isVisible;
            model.loading = loading;
            model.result = data;
            model.result.isShowAll = isShowAll;
            model.form_key = $.cookie('form_key');
            
            return model;
        },
        
        enter: function (item) {
            if (item.url) {
                window.location.href = item.url;
            } else {
                this.pasteToSearchString(item.query);
            }
        },
        
        pasteToSearchString: function (searchTerm) {
            $input.val(searchTerm);
            this.search();
        },
        
        doPopular: function () {
            if (this.config.popularSearches.length) {
                this.processApplyBinding(this._showQueries(this.config.popularSearches));
                
                return true;
            }
            
            return false;
        },
        
        processApplyBinding: function (data) {
            if ($(this.wrapperSelector, this.placeholderSelector).length > 0) {
                if (!!ko.dataFor($(this.wrapperSelector, this.placeholderSelector)[0])) {
                    ko.cleanNode($(this.wrapperSelector, this.placeholderSelector)[0]);
                }
            }
            
            $(this.wrapperSelector, this.placeholderSelector).remove();
            
            var wrapper = $('#searchAutocompleteWrapper').html();
            
            $(this.placeholderSelector).append(wrapper);
            
            ko.applyBindings(this.viewModel(data), $(this.wrapperSelector, this.placeholderSelector)[0]);
            
            this.ensurePosition();
        },
        
        _showQueries: function (data) {
            var self = this;
            var queries = data;
            var items = [];
            var item;
            var result, index;
            
            _.each(queries, function (query, idx) {
                if (idx < 5) {
                    item = {};
                    item.query = query;
                    item.enter = function () {
                        self.query = query;
                    };
                    
                    items.push(item);
                }
            }, this);
            
            result = {
                totalItems: items.length,
                query:      $input.val(),
                indices:    [],
                isShowAll:  false
            };
            
            index = {
                totalItems:   items.length,
                isShowTotals: false,
                items:        items,
                identifier:   'popular',
                title:        $t('Hot Searches')
            };
            
            result.indices.push(index);
            
            return result;
        },
        
        ensurePosition: function () {
            var position = $input.position();
            var left = position.left + parseInt($input.css('marginLeft'), 10);
            var top = position.top + parseInt($input.css('marginTop'), 10);
            
            $(this.placeholderSelector)
                .css('top', $input.outerHeight() - 1 + top)
                .css('left', left)
                .css('width', $input.outerWidth());
        }
    }
});

