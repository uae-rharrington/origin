var config = {
    shim: {
        "js/lib/jquery.visible": ["jquery"],
        "js/lib/sticky-kit": ["jquery"]
    },
    map: {
        "*": {
            "argentoTabs": "js/argento-tabs",
            "argentoSticky": "js/argento-sticky",
            "jquery/visible": "js/lib/jquery.visible"
        }
    },
    deps: [
        "js/argento-base",
        "js/argento-theme",
        "js/argento-custom"
    ]
};
