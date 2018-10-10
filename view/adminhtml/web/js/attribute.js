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
                        this.options_element.show();
                    } else {
                        this.options_element.hide();
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
