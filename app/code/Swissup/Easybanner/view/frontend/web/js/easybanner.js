define([
    'jquery',
    'mage/cookies'
], function ($) {
    var Easybanner = {};

    Easybanner.Cookie = (function () {
        var _cookie = {};

        /**
         * Write cookies
         */
        function write() {
            $.mage.cookies.set(
                'easybanner',
                JSON.stringify(_cookie),
                {
                    expires: new Date('Tue, 19 Jan 2038 03:14:07 GMT')
                }
            );
        }

        /**
         * Read cookies
         */
        function read() {
            var jsonString = $.mage.cookies.get('easybanner');

            if (jsonString && jsonString.length) {
                try {
                    _cookie = JSON.parse(jsonString);
                } catch (e) {}
            }
        }

        read();

        return {
            /**
             * Get banner parameters from cookie
             *
             * @param {Number} bannerId
             * @param {String} key
             * @param {*} defaultValue
             * @returns {*}
             */
            get: function (bannerId, key, defaultValue) {
                defaultValue = defaultValue || 0;

                if (typeof _cookie[bannerId] === 'undefined') {
                    _cookie[bannerId] = {};
                }

                if (key) {
                    if (_cookie[bannerId][key] !== undefined) {
                        return _cookie[bannerId][key];
                    } else {
                        return defaultValue;
                    }
                } else {
                    return _cookie[bannerId];
                }
            },

            /**
             * Set parameter in cookie
             *
             * @param {Number} bannerId
             * @param {String} key
             * @param {*} value
             */
            set: function (bannerId, key, value) {
                _cookie[bannerId][key] = value;
                write();
            }
        };
    })();

    //Timer Class
    Easybanner.Timer = (function () {
        var _frequency = 1000,
            _timers    = {
                inactivity: 0,
                activity: 0,
                browsing: localStorage.getItem('easybanner_timer_browsing') || 0
            },
            events = ['mousemove', 'click', 'scroll', 'keyup'],
            index;

        /**
         * Timer's tick
         */
        function tick() {
            for (var i in _timers) {
                _timers[i]++;
            }

            if (_timers.inactivity >= 10) {
                reset('activity');
            }
        }

        /**
         * Reset timer
         * @param {String} timer
         */
        function reset(timer) {
            _timers[timer] = 0;
        }

        setInterval(tick.bind(this), _frequency);

        for (index in events) {
            $(document).on(events[index], $.proxy(reset, this, 'inactivity'));
        }

        $(document).ready(function () {
            // reset browsing time, if last visit was more that two hours ago
            var lastVisit = localStorage.getItem('easybanner_last_visit'),
                now = new Date();

            localStorage.setItem('easybanner_last_visit', now.toISOString());

            if (!lastVisit) {
                return;
            }

            lastVisit = new Date(lastVisit);

            if (isNaN(lastVisit.getTime())) {
                return;
            }

            if (((Math.abs(now - lastVisit) / 1000) / 60) > 120) {
                reset('browsing');
            }
        });
        $(window).on('beforeunload', function () {
            localStorage.setItem('easybanner_timer_browsing', _timers.browsing);
        });

        return {
            /**
             * @returns {Number}
             */
            getInactivityTime: function () {
                return _timers.inactivity;
            },
            /**
             * @returns {Number}
             */
            getActivityTime: function () {
                return _timers.activity;
            },
            /**
             * @returns {Number}
             */
            getBrowsingTime: function () {
                return _timers.browsing;
            }
        };
    })();

    //Rule Class
    Easybanner.Rule = (function () {
        var _conditions = {},
            _timer      = Easybanner.Timer,
            _cookie     = Easybanner.Cookie,
            _currentId;

        /**
         * Compare conditions
         * @param {*} v1
         * @param {*} v2
         * @param {String} op
         * @returns {Boolean}
         * @private
         */
        function _compareCondition(v1, v2, op) {
            var result = false;

            switch (op) {
                case '>':
                    result = (parseInt(v2) > parseInt(v1));
                    break;

                case '<':
                    result = (parseInt(v2) < parseInt(v1));
                    break;
            }

            return result;
        }

        /**
         * Validate banner conditions
         * @param {Object} filter
         * @param {Object} aggregator
         * @param {*} value
         * @returns {Boolean}
         * @private
         */
        function _validateConditions(filter, aggregator, value) {
            var result = true,
                comparator,
                condition,
                i;

            if (filter.aggregator && filter.conditions) {
                for (i = 0; i < filter.conditions.length; i++) {
                    condition = filter.conditions[i];
                    result = _validateConditions(
                        condition, filter.aggregator, filter.value
                    );

                    if ((filter.aggregator === 'all' && filter.value == '1' && !result) ||
                        (filter.aggregator === 'any' && filter.value == '1' && result)) {

                        break;
                    } else if ((filter.aggregator === 'all' && filter.value == '0' && result) ||
                        (filter.aggregator === 'any' && filter.value == '0' && !result)) {

                        result = !result;
                        break;
                    }
                }
            } else if (filter.attribute) {
                switch (filter.attribute) {
                    case 'browsing_time':
                        comparator = _timer.getBrowsingTime();
                        break;

                    case 'inactivity_time':
                        comparator = _timer.getInactivityTime();
                        break;

                    case 'activity_time':
                        comparator = _timer.getActivityTime();
                        break;

                    case 'display_count_per_customer':
                        comparator = _cookie.get(_currentId, 'display_count');
                        break;

                    case 'display_count_per_customer_per_day':
                    case 'display_count_per_customer_per_week':
                    case 'display_count_per_customer_per_month':
                        var counter = filter.attribute.replace('_per_customer', ''),
                            timeCounterCookie = counter + '_time',
                            compareDate = new Date(_cookie.get(_currentId, timeCounterCookie)),
                            currentDate = new Date();

                        switch (counter) {
                            case 'display_count_per_day':
                                // compareDate.setSeconds(compareDate.getSeconds() + 5);
                                compareDate.setDate(compareDate.getDate() + 1);
                                break;
                            case 'display_count_per_week':
                                compareDate.setDate(compareDate.getDate() + 7);
                                break;
                            case 'display_count_per_month':
                                compareDate.setMonth(compareDate.getMonth() + 1);
                                break;
                        }

                        comparator = _cookie.get(_currentId, counter);
                        if (compareDate <= currentDate) {
                            _cookie.set(_currentId, counter, 0);
                            comparator = 0;
                        }

                        break;

                    case 'scroll_offset':
                        comparator  = $(window).scrollTop();
                        break;
                    default:
                        return true;
                }
                result = _compareCondition(filter.value, comparator, filter.operator);
            }

            return result;
        }

        return {
            /**
             * Validate condition
             * @param {*} id
             * @returns {Boolean}
             */
            validate: function (id) {
                _currentId = id;

                return _validateConditions(_conditions[id]);
            },
            /**
             * Adds conditions
             * @param {Object} conditions
             */
            addConditions: function (conditions) {
                for (var i in conditions) {
                    _conditions[i] = conditions[i];
                }
            }
        };
    })();

    //Popup Class
    Easybanner.Popup = (function () {
        var _cookie = Easybanner.Cookie,
            _rule   = Easybanner.Rule,
            _bannerIds = [],
            _lightbox,
            _awesomebar;

        _lightbox = {
            overlayId: 'easybanner-overlay-el',
            id: 'easybanner-lightbox-el',
            markup: [
                '<div id="easybanner-overlay-el" class="easybanner-overlay-el" style="display:none;"></div>',
                '<div id="easybanner-lightbox-el" class="easybanner-lightbox-el" style="display:none;">',
                    '<span class="easybanner-close easybanner-close-icon"></span>',
                    '<div class="easybanner-lightbox-content"></div>',
                '</div>'
            ].join(''),
            /**
             * Add markup to the body
             */
            create: function () {
                $('body').append(this.markup);
                this.overlay = $('#' + this.overlayId);
                this.el      = $('#' + this.id);
            },
            /**
             * Prepare popup observers
             */
            addObservers: function () {
                if (!this._onKeyPressBind) {
                    this._onKeyPressBind = this._onKeyPress.bind(this);
                    this._hideBind = this.hide.bind(this);
                    this._dontShowBind = this.dontShow.bind(this);
                }

                $(this.el).find('.easybanner-close').on('click', this._hideBind);
                $(this.el).find('.easybanner-close-permanent').on('click', this._dontShowBind);

                $(this.el).find('img').each(function () {
                    $(this).onload = this.center.bind(this);
                }.bind(this));

                $(document).off('keyup', this._onKeyPressBind);
                $(document).on('keyup', this._onKeyPressBind);

                if ('addEventListener' in window) {
                    window.addEventListener('resize', this.center.bind(this));
                } else {
                    window.attachEvent('onresize', this.center.bind(this));
                }
            },
            /**
             * Get popup content
             * @returns {*|jQuery}
             */
            getContentEl: function () {
                return $(this.el).children('.easybanner-lightbox-content');
            },
            /**
             * Show html in popup
             * @param {String} html
             */
            show: function (html) {
                if (!html) {
                    return;
                }

                if (!this.el) {
                    this.create();
                }
                this.getContentEl().append(html);

                // update class names to include all names of current banner
                $(this.el)
                    .removeClass()
                    .addClass('easybanner-lightbox-el')
                    .addClass(this.getContentEl().children().first().data('class'));

                this.addObservers();
                $(this.overlay).fadeIn();
                $(this.el).fadeIn();
                this.center();
            },
            /**
             * Hide popup
             */
            dontShow: function (e) {
                e.preventDefault();

                var id = this.getContentEl().children().first().attr('id');

                if (id) {
                    _cookie.set(id, 'dont_show', 1);
                }

                this.hide();
            },
            /**
             * Hide popup
             */
            hide: function () {
                if (this._onKeyPressBind) {
                    $(document).off('keyup', this._onKeyPressBind);
                }
                $('.easybanner-popup-banner').first().append(
                    this.getContentEl().children().first()
                );
                $(this.overlay).hide();

                $(this.el)
                    .hide()
                    .removeClass()
                    .addClass('easybanner-lightbox-el');
            },
            /**
             * Reset popup layout
             */
            resetLayout: function () {
                this.getContentEl().css({
                    height: 'auto'
                });
                $(this.el).css({
                    width: 0,
                    height: 0
                });
                $(this.el).css({
                    width: 'auto',
                    height: 'auto',
                    margin: 0,
                    left: 0,
                    top: 0
                });
            },
            /**
             * Align popup window to the center of viewport
             */
            center: function () {
                this.resetLayout();

                var viewportSize = {
                        'width': $(window).width(),
                        'height': $(window).height()
                    },
                    width = $(this.el).outerWidth(),
                    height,
                    newHeight,
                    gap = {
                        horizontal: 50,
                        vertical: 50
                    };

                if (viewportSize.width < (width + gap.horizontal)) {
                    width = viewportSize.width - gap.horizontal;
                }

                $(this.el).css({
                    width: width + 'px',
                    left: '50%',
                    marginLeft: -width / 2 + 'px'
                });

                height = $(this.el).outerHeight();

                if (viewportSize.height < (height + gap.vertical)) {
                    height = viewportSize.height - gap.vertical;
                }
                this.getContentEl().css({
                    height: (height -
                        parseInt($(this.el).css('paddingTop')) -
                        parseInt($(this.el).css('paddingBottom'))) + 'px'
                });

                $(this.el).css({
                    top: '50%',
                    marginTop: -height / 2 + 'px'
                });
            },
            /**
             * Key press observer
             * @param {Event} e
             * @private
             */
            _onKeyPress: function (e) {
                if (e.keyCode === 27) {
                    this.hide();
                }
            }
        };

        _awesomebar = {
            id: 'easybanner-awesomebar-el',
            markup: [
                '<div id="easybanner-awesomebar-el" class="easybanner-awesomebar-el" style="display:none;">',
                    '<span class="easybanner-close easybanner-close-icon"></span>',
                    '<div class="easybanner-awesomebar-content"></div>',
                '</div>'
            ].join(''),
            /**
             * Prepare html markup
             */
            create: function () {
                $('body').append(this.markup);
                this.el = $('#' + this.id);
            },
            /**
             * Add event observers
             */
            addObservers: function () {
                if (!this._hideBind) {
                    this._hideBind = this.hide.bind(this);
                    this._dontShowBind = this.dontShow.bind(this);
                }

                $(this.el).find('.easybanner-close').on('click', this._hideBind);
                $(this.el).find('.easybanner-close-permanent').on('click', this._dontShowBind);
            },
            /**
             * @returns {*|jQuery}
             */
            getContentEl: function () {
                return $(this.el).children('.easybanner-awesomebar-content');
            },
            /**
             * @returns {Number}
             */
            getTransitionDuration: function () {
                var duration = $(this.el).css('transition-duration');

                if (duration) {
                    duration = parseFloat(duration) * 1000;
                } else {
                    return 0;
                }

                return duration;
            },
            /**
             * Show content in awesomebar panel
             * @param {String} html
             */
            show: function (html) {
                if (!html) {
                    return;
                }

                if (!this.el) {
                    this.create();
                }

                this.getContentEl().append(html);

                // update class names to include all names of current banner
                $(this.el)
                    .removeClass()
                    .addClass('easybanner-awesomebar-el')
                    .addClass(this.getContentEl().children().first().data('class'));

                this.addObservers();

                $(this.el).show();
                setTimeout(function () {
                    $(this.el).css({
                        top: 0
                    });
                }.bind(this), 10);
            },
            /**
             * Hide popup
             */
            dontShow: function (e) {
                e.preventDefault();

                var id = this.getContentEl().children().first().attr('id');

                if (id) {
                    _cookie.set(id, 'dont_show', 1);
                }

                this.hide();
            },
            /**
             * Hide awesomebar
             */
            hide: function () {
                $(this.el).css({
                    top: - $(this.el).outerHeight() - 30 + 'px'
                });

                // time to hide the bar before move it
                setTimeout(function () {
                    $('.easybanner-popup-banner').append({
                        bottom: this.getContentEl().children().first()
                    });

                    $(this.el)
                        .hide()
                        .removeClass()
                        .addClass('easybanner-awesomebar-el');
                }.bind(this), this.getTransitionDuration());
            }
        };

        return {
            /**
             * Collect all rendered banners and add them into array
             */
            init: function () {
                $('.easybanner-popup-banner .easybanner-banner').each(function () {
                    _bannerIds.push($(this).attr('id'));
                });
                this.initBanners();
            },
            /**
             * 1. Show banner if needed.
             * 2. Call every second for conditional banners
             */
            initBanners: function () {
                var shownIds = [],
                    limit = _bannerIds.length,
                    i;

                for (i = 0; i < limit; ++i) {
                    if (_rule.validate(_bannerIds[i])) {
                        this.show(_bannerIds[i]);
                        shownIds.push(_bannerIds[i]);
                    }
                }

                for (i = 0; i < shownIds.length; ++i) {
                    _bannerIds.splice(_bannerIds.indexOf(shownIds[i]), 1);
                }

                if (_bannerIds.length) {
                    setTimeout(this.initBanners.bind(this), 1000);
                }
            },
            /**
             * Show banner by its ID
             * @param {String} id
             */
            show: function (id) {
                var el = $('#' + id),
                    count,
                    counters = [
                        'display_count',
                        'display_count_per_day',
                        'display_count_per_week',
                        'display_count_per_month',
                    ];

                if (!el) {
                    return;
                }

                if ($(el).hasClass('placeholder-lightbox')) {
                    popupObject = _lightbox;
                } else if ($(el).hasClass('placeholder-awesomebar')) {
                    popupObject = _awesomebar;
                } else {
                    return;
                }

                // show only one banner at once
                if (popupObject.el && popupObject.el.is(':visible')) {
                    return;
                }
                popupObject.show(el);

                $.each(counters, function (i, counter) {
                    count = _cookie.get(id, counter);
                    if (!count) {
                        count = 0;
                    }

                    if (counter !== 'display_count') {
                        var timeCounterCookie = counter + '_time',
                            currentDate = new Date();

                        _cookie.set(id, timeCounterCookie, currentDate.getTime());
                    }

                    _cookie.set(id, counter, ++count);
                });
            },
            /**
             * Hide banner by its ID
             * @param {String} id
             */
            hide: function (id) {
                var el = $('#' + id);

                if (el.up('.easybanner-lightbox-el')) {
                    popupObject = _lightbox;
                } else if (el.up('.easybanner-awesomebar-el')) {
                    popupObject = _awesomebar;
                } else {
                    return;
                }

                popupObject.hide();
            }
        };
    })();

    return function (conditions) {
        Easybanner.Rule.addConditions(conditions);
        Easybanner.Popup.init();
    }
});
