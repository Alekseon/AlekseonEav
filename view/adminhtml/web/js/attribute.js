/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
define([
    'jquery',
], function ($) {
    'use strict';

    return function (config) {

        var attribute = {
            options_element: $('#options_fieldset'),
            visible_in_grid_element: $('#visible_in_grid'),
            is_wysiwyg_enabled_element: $('#is_wysiwyg_enabled'),
            has_option_codes_element: $('#has_option_codes'),
            input_validator_element: $('#input_validator'),
            default_input_type_config: config.input_types_config['default'],

            frontendInputChanged: function (newValue) {

                var input_type_config = [];

                $.extend(
                    input_type_config,
                    this.default_input_type_config,
                    config.input_types_config[newValue]
                );

                if (this.options_element) {
                    if (input_type_config.show_options) {
                        var elems = document.getElementsByName('default[]');
                        for (var i = 0; i < elems.length; i++) {
                            elems[i].type = input_type_config.optionInputType;
                        }
                        this.options_element.show();
                        this.has_option_codes_element.parent().parent().show();
                    } else {
                        this.options_element.hide();
                        this.has_option_codes_element.parent().parent().hide();
                    }
                }

                if (input_type_config.can_be_visibile_in_grid) {
                    this.visible_in_grid_element.attr('disabled',false);
                } else {
                    this.visible_in_grid_element.attr('disabled',true);
                    this.visible_in_grid_element.val(0);
                }

                if (input_type_config.can_use_wysiwyg) {
                    this.is_wysiwyg_enabled_element.parent().parent().show();
                } else {
                    this.is_wysiwyg_enabled_element.parent().parent().hide();
                }

                if (input_type_config.can_use_input_validator) {
                    this.input_validator_element.parent().parent().show();
                } else {
                    this.input_validator_element.parent().parent().hide();
                }
            }
        };

        var frontendInput = $('#frontend_input');

        if (frontendInput) {
            frontendInput.change(function (event) {
                attribute.frontendInputChanged(this.value);
            });
            frontendInput.change();
        };
    };
});
