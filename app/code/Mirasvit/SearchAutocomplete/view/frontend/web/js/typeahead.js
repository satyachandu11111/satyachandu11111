define([
    'jquery',
    'underscore'
], function ($, _) {
    
    "use strict";
    
    var storage = false;
    var suggestKey;
    var placeholderHtml = '<input value="" class="input-text mst-search-autocomplete__typeahead-overlay" type="text" />';
    var $placeholder = $(placeholderHtml);
    var $input = false;
    
    return {
        config: {
            minSearchLength:  3,
            typeaheadUrl:     '',
            minSuggestLength: 2
        },
        
        init: function (selector, config) {
            $input = $(selector);
            this.config = _.defaults(config, this.config);
            if ($input.val().length >= this.config.minSuggestLength) {
                this.retrieveTypeaheadStorage();
            }
        },
        
        suggest: function () {
            if (!$input) {
                return false;
            }
            
            $placeholder.val('');
            $placeholder.remove();
            
            var inputLength = $input.val().length;
            var emptyStorage = storage.length === 0;
            var suggestKeyMatches = $input.val().indexOf(suggestKey) === 0;
            var moreOrEqualsMinSuggestLength = inputLength >= this.config.minSuggestLength;
            var moreOrEqualsMinSearchLength = inputLength >= this.config.minSearchLength;
            
            if (emptyStorage) {
                if (moreOrEqualsMinSuggestLength) {
                    this.retrieveTypeaheadStorage();
                }
            } else {
                if (suggestKeyMatches) {
                    if (moreOrEqualsMinSearchLength) {
                        this._doSuggest();
                        this.ensurePosition();
                    }
                } else {
                    this.retrieveTypeaheadStorage();
                }
            }
        },
        
        _doSuggest: function () {
            $.each(JSON.parse(storage.replace("/", "")), function (i, item) {
                $placeholder.remove();
                if (item.indexOf($input.val().toLowerCase()) === 0) {
                    $input.parent().after($placeholder);
                    $placeholder.val(item.replace($input.val().toLowerCase(), $input.val()));
                    return false;
                }
            });
        },
        
        completeQuery: function () {
            if ($placeholder.val().length >= this.config.minSearchLength) {
                $input.val($placeholder.val());
                $input.trigger("input");
            }
        },
        
        retrieveTypeaheadStorage: function () {
            $.ajax({
                url:      this.config.typeaheadUrl,
                dataType: 'json',
                type:     'GET',
                data:     {
                    q: $input.val().toLowerCase()
                },
                success:  function (data) {
                    storage = JSON.stringify(data);
                    suggestKey = $input.val().substring(0, 2).toLowerCase();
                }
            });
        },
        
        ensurePosition: function () {
            var position = $input.position();
            var left = position.left + 1 + parseInt($input.css('marginLeft'), 10);
            var top = position.top + parseInt($input.css('marginTop'), 10);
            
            $placeholder
                .css('top', top)
                .css('left', left)
                .css('width', $input.outerWidth());
        }
    }
});