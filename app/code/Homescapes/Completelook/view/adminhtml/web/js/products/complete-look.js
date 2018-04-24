define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            products = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;
        $('complete_look').value = Object.toJSON(products);       

        /**
         * Register Note Customer
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCustomer(grid, element, checked) {
            
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    //console.log('element val :'+element.value);
                    //console.log('element posi :'+element.positionElement.value);
                    products.set(element.value,element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                console.log('unchecked....');
                products.unset(element.value);
            }
            console.log(Object.toJSON(products));
            $('complete_look').value = Object.toJSON(products);
            grid.reloadParams = {
                'selected_product[]': products.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function productRowClick(grid, event) {
            console.log('clicked on row ckicked...');
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    console.log('clicked on product row...');
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    //console(checked);
                    checked=checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
                //$('complete_look').value = Object.toJSON(products);
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
             console.log('call positionChange');
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                products.set(element.checkboxElement.value, element.value);
                $('complete_look').value = Object.toJSON(products);
            }
        }

        /**
         * Initialize product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function productRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];
                
            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
                Event.observe(position, 'click', positionChange);
            }
        }

        gridJsObject.rowClickCallback = productRowClick;
        gridJsObject.initRowCallback = productRowInit;
        gridJsObject.checkboxCheckCallback = registerCustomer;

        if (gridJsObject.rows) {            
            gridJsObject.rows.each(function (row) {                
                productRowInit(gridJsObject, row);
            });
        }
    };
});
