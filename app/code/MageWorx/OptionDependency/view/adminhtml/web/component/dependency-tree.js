/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/ui-select',
    'uiRegistry',
    'underscore',
    'ko',
    'mage/translate'
], function (Select, registry, _, ko, $t) {
    'use strict';

    return Select.extend({

        defaults: {
            showPath: false,
            isEnabledTitleId: false,
            startPoint: '',
            endPoint: '',
            previousEndPointOption: '',
            previousEndPointOptionValue: '',
            needToUsePrevEndPoint: false,
            massSelectedOptions: []
        },

        /**
         * Clear dependency-tree, add root node with product name
         *
         * @param productName
         */
        resetOptionsTree: function (productName) {
            this.cacheOptions.tree.clear();
            if (productName == '') {
                productName = $t('Unnamed product');
            }
            this.cacheOptions.tree.push(
                {
                    isActive: false,
                    isOption: false,
                    level: '0',
                    value: '-1',
                    label: productName
                }
            );
        },

        /**
         * Add option as child to root node
         *
         * @param data
         * @param isSchedule
         */
        addOption: function (data, isSchedule) {
            var label = this.isEnabledTitleId && data.option_title_id != ''
                ? data.title + ' [' + data.option_title_id + ']'
                : data.title;
            var optionPart = !_.isUndefined(data.mageworx_option_id) && !isSchedule
                ? data.mageworx_option_id
                : data.record_id;
            var option = {
                isActive: true,
                isOption: true,
                level: '0',
                value: optionPart,
                parent: '-1',
                isOptionSelected: ko.observable(false),
                label: label
            };

            this.options([]);
            this.setOption(option);
        },

        /**
         * Add value as child to option node
         *
         * @param data
         * @param optionData
         * @param isSchedule
         */
        addOptionValue: function (data, optionData, isSchedule) {
            var label = this.isEnabledTitleId && data.option_type_title_id != ''
                ? data.title + ' [' + data.option_type_title_id + ']'
                : data.title;
            var optionPart = !_.isUndefined(optionData.mageworx_option_id) && !isSchedule
                ? optionData.mageworx_option_id
                : optionData.record_id;
            var optionValuePart = !_.isUndefined(data.mageworx_option_type_id) && !isSchedule
                ? data.mageworx_option_type_id
                : data.record_id;
            var option = {
                isActive: true,
                isOption: false,
                level: '0',
                value: optionPart + ',' + optionValuePart,
                parent: optionPart,
                label: label
            };

            this.options([]);
            this.setOption(option);
        },

        /**
         * Add "no options" message if there are no available options
         */
        addNoOptionsMessage: function () {
            var label = $t('No Options'),
                option = {
                    isActive: false,
                    isOption: false,
                    level: '0',
                    value: '0',
                    label: label
                };

            this.options([]);
            this.setOption(option);
        },

        /**
         * Change title for parent fieldset
         *
         * @param valueData
         * @param optionData
         */
        setTitle: function (valueData, optionData) {
            if (_.isUndefined(this.containers)) {
                return;
            }

            var optionLabel = this.isEnabledTitleId && optionData.option_title_id != ''
                ? optionData.title + ' [' + optionData.option_title_id + ']'
                : optionData.title;

            if (valueData.length < 1) {
                this.containers.first().label = optionLabel;
                return;
            }

            var valueLabel = this.isEnabledTitleId && valueData.option_type_title_id != ''
                ? valueData.title + ' [' + valueData.option_type_title_id + ']'
                : valueData.title;

            this.containers.first().label = optionLabel + ' - ' + valueLabel;
        },

        /**
         * Toggle all children if option checkbox is pressed
         *
         * @param {Object} data - selected option data
         * @returns {Object} Chainable
         */
        toggleAllChildren: function (data) {
            var self = this;

            var isSelected = false;
            _.each(self.massSelectedOptions, function (value, index) {
                var index = self.massSelectedOptions.indexOf(value);
                if (data.value == value) {
                    isSelected = true;
                    self.massSelectedOptions.splice(index, 1);
                }
            });

            if (isSelected) {
                data.isOptionSelected(false);
            } else {
                data.isOptionSelected(true);
                self.massSelectedOptions.push(data.value);
            }
            _.each(data.optgroup, function (childOption) {
                if (!isSelected) { /*eslint no-lonely-if: 0*/
                    self.value.push(childOption.value);
                } else {
                    self.value(_.without(self.value(), childOption.value));
                }
            });

            return this;
        },

        closeChildLevel: function () {
            if (_.isUndefined(this.cacheOptions.tree[0].optgroup)) {
                return;
            }
            _.each(this.cacheOptions.tree[0].optgroup, function (value) {
                setTimeout(function () {
                    value.visible(false);
                }, 500);
            });
        },

        /**
         * Process option select
         *
         * @param data
         * @param currentValue
         */
        processOptionSelect: function (data, currentValue) {
            if (data.isOption) {
                return data.isOptionSelected;
            } else {
                return this.isSelected(currentValue);
            }
        },

        /**
         * Check if tree node is deactivated
         *
         * @param {Object} data - selected option data
         * @returns {Object} Chainable
         */
        isDeactivated: function (data) {
            return data.isActive ? false : true;
        },


        /**
         * Filtered options list by value from filter options list
         *
         * @param {Array} list - option list
         * @param {String} value
         *
         * @returns {Array} filters result
         */
        _getFilteredArray: function (list, value) {
            var i = 0,
                array = [],
                curOption;

            for (i; i < list.length; i++) {
                curOption = list[i].label.toLowerCase();
                if (curOption.indexOf(value) > -1 && !list[i].isOption) {
                    array.push(list[i]); /*eslint max-depth: [2, 4]*/
                }
            }

            return array;
        },

        /**
         * Clean shift selection variables
         */
        cleanShiftSelection: function () {
            this.startPoint = '';
            this.endPoint = '';
            this.previousEndPointOption = '';
            this.previousEndPointOptionValue = '';
            this.needToUsePrevEndPoint = false;
        },

        /**
         * Process shift selection
         *
         * @param data
         * @param event
         */
        processShiftSelection: function (data, event) {
            if (!event.shiftKey) {
                this.startPoint = data;
                this.endPoint = '';
                this.previousEndPointOption = '';
                this.previousEndPointOptionValue = '';
                this.needToUsePrevEndPoint = false;
            } else if (this.startPoint !== '') {
                this.endPoint = data;
            }

            if (this.startPoint === '' || this.endPoint === '') {
                return false;
            }

            var self = this,
                isStarted = false,
                isFinished = false,
                startPointOption = this.startPoint.value.split(',')[0],
                startPointOptionValue = this.startPoint.value.split(',')[1],
                endPointOption = this.endPoint.value.split(',')[0],
                endPointOptionValue = this.endPoint.value.split(',')[1],
                isPassedStartPointOptionValue = false,
                isPassedEndPointOptionValue = false,
                isPassedPreviousPointOptionValue = false,
                isLastChecked = false,
                isPrevDeletion = false;

            this.options()['0'].optgroup.every(function (option) {
                if (_.isUndefined(option.optgroup)) {
                    return false;
                }
                if (isFinished) {
                    return false;
                }
                if (isStarted ||
                    option.value == startPointOption ||
                    option.value == endPointOption ||
                    option.value == self.previousEndPointOption
                ) {
                    option.optgroup.every(function (optionValue) {
                        if (optionValue.value.split(',')[1] == startPointOptionValue) {
                            isPassedStartPointOptionValue = true;
                        }
                        if (optionValue.value.split(',')[1] == endPointOptionValue) {
                            isPassedEndPointOptionValue = true;
                        }
                        if (self.needToUsePrevEndPoint &&
                            optionValue.value.split(',')[1] == self.previousEndPointOptionValue
                        ) {
                            isPassedPreviousPointOptionValue = true;
                        }

                        if (!isPassedStartPointOptionValue &&
                            !isPassedEndPointOptionValue &&
                            !isPassedPreviousPointOptionValue
                        ) {
                            return true;
                        }
                        isStarted = true;

                        if (!isPassedStartPointOptionValue &&
                            !isPassedEndPointOptionValue &&
                            isPassedPreviousPointOptionValue
                        ) {
                            if (self.isSelected(optionValue.value)) {
                                self.value(_.without(self.value(), optionValue.value));
                                isPrevDeletion = true;
                            }
                        } else if (!isPassedStartPointOptionValue && isPassedEndPointOptionValue) {
                            if (!self.isSelected(optionValue.value)) {
                                self.value.push(optionValue.value);
                                isPrevDeletion = false;
                            }
                        } else if (isPassedStartPointOptionValue && !isPassedEndPointOptionValue) {
                            if (!self.isSelected(optionValue.value)) {
                                self.value.push(optionValue.value);
                                isPrevDeletion = false;
                            }
                        } else if (isPassedStartPointOptionValue &&
                            isPassedEndPointOptionValue &&
                            !isPassedPreviousPointOptionValue
                        ) {
                            if (isLastChecked) {
                                if (self.isSelected(optionValue.value)) {
                                    self.value(_.without(self.value(), optionValue.value));
                                    isPrevDeletion = true;
                                }
                            } else {
                                if (!self.isSelected(optionValue.value)) {
                                    self.value.push(optionValue.value);
                                    isPrevDeletion = false;
                                }
                                isLastChecked = true;
                            }
                        }

                        if (isPassedStartPointOptionValue &&
                            isPassedEndPointOptionValue &&
                            (!self.needToUsePrevEndPoint || isPassedPreviousPointOptionValue)
                        ) {
                            if (isLastChecked) {
                                if (isPrevDeletion) {
                                    if (self.isSelected(optionValue.value)) {
                                        self.value(_.without(self.value(), optionValue.value));
                                    }
                                }
                                isFinished = true;
                                return false;
                            } else {
                                if (!self.isSelected(optionValue.value)) {
                                    self.value.push(optionValue.value);
                                    isPrevDeletion = false;
                                }
                                isLastChecked = true;
                            }
                        }
                        return true;
                    });
                }
                return true;
            });

            this.endPoint = '';
            this.previousEndPointOption = endPointOption;
            this.previousEndPointOptionValue = endPointOptionValue;
            this.needToUsePrevEndPoint = true;
            return true;
        }
    });
});
