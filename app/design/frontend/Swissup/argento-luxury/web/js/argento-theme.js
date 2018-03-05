define([
    "jquery",
    "jquery/ui",
    "mage/collapsible",
    "domReady!",
    "argentoSticky"
], function($) {
    var media = '(min-width: 768px) and (min-height: 600px)';

    $('.nav-sections').argentoSticky({
        media: media,
        parent: $('.page-wrapper'),
        inner_scrolling: false
    });

    var footerLinks = $(".footer.links > li");
    var toggleFooterBlocks = function (mql) {
        if (mql.matches) {
            if (footerLinks.data('collapsible')) {
                footerLinks
                    .collapsible("activate")
                    .collapsible("destroy");
            }
        } else {
            footerLinks
                .collapsible({ icons: {"header": "plus", "activeHeader": "minus"}})
                .collapsible("deactivate");
        }
    };
    if (footerLinks.length) {
        var mqlFooter = matchMedia('(min-width: 768px)');
        toggleFooterBlocks(mqlFooter);
        mqlFooter.addListener(toggleFooterBlocks);
    }
});
