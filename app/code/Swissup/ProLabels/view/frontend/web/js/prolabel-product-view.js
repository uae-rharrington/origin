define([
    "jquery"
], function ($) {
    'use strict';

    var options, ProLabelsProductInit = {
        ProLabelsProductInit: function (settings) {
            options = settings;
            ProLabelsProductInit.moveLabelsToContent(options.contentContainer);
        },
        moveLabelsToContent: function(selector) {
            $(selector).append(
                $('.prolabels-content-labels').html()
            );
        }
    };

    $(document).on('gallery:loaded', function(e) {
        setTimeout(function() {
            $(options.baseImageWrapper).wrap(function() {
              return "<div class='prolabels-wrapper'></div>";
            });
            $(".prolabels-wrapper").append(
                $('.prolabels-product-image-labels').html()
            );
        }, 100);
    });

    return ProLabelsProductInit;
});