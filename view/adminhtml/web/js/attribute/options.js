/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'jquery',
    'mage/template',
], function ($, mageTemplate) {
    'use strict';

    return function (config) {
        var attributeOption = {
            itemCount: 0,
            optionsContainer: $('[data-role=options-container]'),
            template: mageTemplate('#row-template'),

            add: function (data) {
                var element;
                if (typeof data.id == 'undefined') {
                    data = {
                        'id': 'option_' + this.itemCount,
                        'sort_order': this.itemCount + 1
                    };
                }

                if (!data.intype) {
                    data.intype = this.getOptionInputType();
                }

                this.itemCount++;

                element = this.template({
                    data: data
                });
                this.optionsContainer.append(element);
            },
            remove: function (event) {
                var element = $(event.target).closest("tr");
                if (element) {
                    var deleteFlag = element.find('.delete-flag');
                    if (deleteFlag[0]) {
                        deleteFlag[0].value = 1;
                    }
                    element.hide();
                }
            },
            render: function (data) {
                for (var i = 0; i < data.length; i++) {
                    this.add(data[i]);
                }
            },
            getOptionInputType: function () {
                var optionDefaultInputType = 'radio';

                if ($('#frontend_input') && $('#frontend_input').val() === 'multiselect') {
                    optionDefaultInputType = 'checkbox';
                }

                return optionDefaultInputType;
            }
        };

        if ($('#add_new_option_button')) {
            $('#add_new_option_button').click(
                function () {
                    attributeOption.add({});
                }
            );
        }

        $('#manage-options-panel').on('click', '.delete-option', function (event) {
            attributeOption.remove(event);
        });

        if (!config.readOnly) {
            $(function ($) {
                $('[data-role=options-container]').sortable({
                    distance: 8,
                    tolerance: 'pointer',
                    cancel: 'input, button',
                    axis: 'y',
                    update: function () {
                        $('[data-role=options-container] [data-role=order]').each(function (index, element) {
                            $(element).val(index + 1);
                        });
                    }
                });
            });
        }
        attributeOption.render(config.attributesData);
    };
});
