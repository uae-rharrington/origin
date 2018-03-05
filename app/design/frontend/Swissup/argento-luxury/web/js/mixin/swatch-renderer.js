define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (swatchRenderer) { // swatchRenderer == Result that Magento_Swatches/js/swatch-renderer returns.
        swatchRenderer.prototype._RenderControls
            =  wrapper.wrap(swatchRenderer.prototype._RenderControls, function (originalFunction) {

                originalFunction();

                // add wrapper to swatch label
                var container = this.element,
                    classes = this.options.classes;

                container.children('.'+classes.attributeClass).each(function(){
                    var wrapper = $('<div class="' + classes.attributeLabelClass + '-wrapper"></div>')
                    wrapper.append($(this).find('.'+classes.attributeLabelClass));
                    wrapper.append($(this).find('.'+classes.attributeSelectedOptionLabelClass));
                    $(this).prepend(wrapper);
                });
            });

        return swatchRenderer;
    };
});
