define([
    'jquery',
    'argentoSticky'
], function($) {
    $('.header.wrapper').argentoSticky({
        media: '(min-width: 768px) and (min-height: 600px)',
        parent: $('.page-wrapper'),
        inner_scrolling: false
    });
});
