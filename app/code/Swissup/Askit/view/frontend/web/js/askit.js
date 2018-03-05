define([
    "jquery"
], function ($) {
    var _config = {};
    return {
        version: function () {
            return '1.2.10';
        },
        config: function () {
            return _config;
        },
        setConfig: function (config) {
            jQuery.extend(_config, config);
            return this;
        },
        init: function () {
            $(".askit-item-trigger").click(function () {
                $(this).parent().parent().toggleClass("askit-item--commenting");
            });
        }
    }
});