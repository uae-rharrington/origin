define([
    'jquery',
    'underscore',
    'matchMedia',
    'Swissup_Navigationpro/js/aim',
    'Swissup_Navigationpro/js/touch',
    'jquery/ui',
    'mage/menu'
], function ($, _, matchMedia, aim, touch) {
    'use strict';

    $.widget('swissup.navpro', $.mage.menu, {
        options: {
            icons: {
                submenu: 'navpro-icon-caret'
            },
            menus: '.navpro-dropdown',
            responsive: true,
            expanded: true,
            delay: 300,
            position: {
                my: 'left top',
                at: 'right top',
                collision: 'fit'
            },
            level0: {
                position: {
                    my: 'center top',
                    at: 'center bottom',
                    collision: 'fit none'
                }
            }
        },

        /**
         * Init navigation
         * @private
         */
        _create: function () {
            this._super();

            var debouncedUpdateDimensions;

            // fit dropdown dimensions into screen size
            this._updateDimensions();
            debouncedUpdateDimensions = _.debounce(this._updateDimensions.bind(this), 100);
            $(window).on('resize', debouncedUpdateDimensions);

            if (!this._isAccordion() && !$(this.element).hasClass('click')) {
                matchMedia({
                    media: '(max-width: ' + this._getMobileMaxWidth() + 'px)',
                    entry: this._toggleClickMode.bind(this),
                    exit: this._toggleHoverMode.bind(this)
                });
            } else {
                this._toggleClickMode();
            }
        },

        /**
         * @private
         */
        _listen: function () {
            // add toggle navigation listeners for mobile view
            // @todo: call parent method once only
            if (this.element.closest('.nav-sections').length) {
                this._super();
            }
        },

        /**
         * Used for:
         *  Height calculation for vertical columns mode
         *  Dropdown positioning
         * @return {Boolean}
         */
        _shouldUseMobileFriendlyFallbacks: function () {
            return window.innerWidth <= this._getMobileMaxWidth() ||
                this._isAccordion();
        },

        /**
         * Get max width for mobile view
         * @return {Number}
         */
        _getMobileMaxWidth: function () {
            return $(this.element).next('.navpro-mobile').width();
        },

        /**
         * Checks if menu orientation is vertical
         * @returns {bool}
         * @private
         */
        _isOrientationVertical: function () {
            return $(this.element).parent('.navpro').hasClass('orientation-vertical');
        },

        /**
         * Checks if menu is accordion
         * @returns {bool}
         * @private
         */
        _isAccordion: function () {
            return $(this.element).parent('.navpro').hasClass('orientation-accordion');
        },

        /**
         * Add/remove 'shown' class on a dropdown Element when it does not have
         * display:none style.
         *
         * If display:none is found on a dropdown element, this method will
         * not stop an event. (xs-hide-dropdown compatibility)
         *
         * @param  {jQuery} dropdown
         * @param  {Event} event
         */
        _toggleDropdownVisibility: function (dropdown, event) {
            if (dropdown.css('display') === 'none') {
                return;
            }

            var target = $(event.target).closest('.ui-menu-item'),
                currentTarget;

            // copy of mouseenter handler
            currentTarget = $(event.currentTarget);
            currentTarget.siblings().children('.ui-state-active').removeClass('ui-state-active');

            if (!currentTarget.children('.shown').length) {
                this.focus(event, currentTarget);
            }
            // end of copy

            if (!dropdown.hasClass('shown')) {
                event.preventDefault();

                // fix click on a active item without link to close menu
                setTimeout(function () {
                    if (!dropdown.hasClass('shown')) {
                        this._open(dropdown);
                    }
                }.bind(this), 200);
            } else if (!target.find('> a').length ||
                target.find('> a').attr('href') === '#') {

                this._close();
            }
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleClickMode: function () {
            $(this.element).off('mouseleave mouseenter click focus blur');
            this._on({
                /**
                 * @param {Event} event
                 */
                'click .ui-state-disabled > a': function (event) {
                    event.preventDefault();
                },
                // Prevent all events when clicking custom content inside dropdown
                /**
                 * @param {Event} event
                 */
                'click .navpro-dropdown': function (event) {
                    event.stopPropagation();
                },
                /**
                 * @param {Event} event
                 */
                'click .ui-menu-item:has(.navpro-dropdown)': function (event) {
                    var target = $(event.target).closest('.ui-menu-item'),
                        dropdown = target.children('.ui-menu');

                    if (dropdown.length) {
                        this._toggleDropdownVisibility(dropdown, event);
                    }
                }
            });
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleHoverMode: function () {
            $(this.element).off('mouseleave mouseenter click focus blur');
            this._on({
                /**
                 * @param {Event} event
                 */
                'click .ui-state-disabled > a': function (event) {
                    event.preventDefault();
                },
                // Prevent all events when clicking custom content inside dropdown
                /**
                 * @param {Event} event
                 */
                'click .navpro-dropdown': function (event) {
                    event.stopPropagation();
                },
                /**
                 * @param {Event} event
                 */
                'click .ui-menu-item:has(.navpro-dropdown)': function (event) {
                    var target = $(event.target).closest('.ui-menu-item'),
                        dropdown = target.children('.ui-menu');

                    if (touch.touching() && dropdown.length) {
                        this._toggleDropdownVisibility(dropdown, event);
                    }
                },
                /**
                 * @param {Event} event
                 */
                'mouseenter .ui-menu-item': function (event) {
                    var target = $(event.currentTarget);
                    // Remove ui-state-active class from siblings of the newly focused menu item
                    // to avoid a jump caused by adjacent elements both having a class with a border
                    target.siblings().children('.ui-state-active').removeClass('ui-state-active');

                    // `if` is added to fix non-clickable items in first dropdown on ipad
                    if (!touch.touching() || $(event.target).closest('.ui-menu-item').children('.ui-menu').length) {
                        this.focus(event, target);
                    }
                },
                /**
                 * @param {Event} event
                 */
                'mouseleave': function (event) {
                    this.collapseAll(event, true);
                },
                /**
                 * @param {Event} event
                 * @returns {*}
                 */
                'mouseleave .ui-menu': function (event) {
                    if ($(event.relatedTarget).parent('.ui-menu-item').length) {
                        // default behaviour
                        return this.collapseAll(event);
                    }

                    // close dropdown when mouseout to custom content
                    clearTimeout(this.timer);

                    var currentMenu = $(event.currentTarget).parent().closest('.ui-menu');

                    this.timer = this._delay(function () {
                        this._close(currentMenu);
                    }, this.delay);
                }
            });
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleMobileMode: function () {
            // disable magento listeners
        },

        /**
         * Overriden to prevent multiple mouseenter and mouseleave events
         */
        _toggleDesktopMode: function () {
            // disable magento listeners
        },

        /**
         * Fit all submenus into viewport width
         */
        _updateDimensions: function () {
            var width = 1280,
                dropdowns,
                self = this;

            // Sync dropdown width's with theme container width and viewport size
            $('#maincontent, .header.content').each(function () {
                var currentWidth = $(this).outerWidth();

                if (currentWidth && currentWidth > 300 && currentWidth < width) {
                    width = currentWidth;
                }
            });
            dropdowns = [
                '.navpro-dropdown.size-fullwidth > .navpro-dropdown-inner',
                '.navpro-dropdown.size-boxed > .navpro-dropdown-inner',
                '.navpro-dropdown.size-xlarge',
                '.navpro-dropdown.size-large',
                '.navpro-dropdown.size-medium'
            ];
            $(dropdowns.join(','), this.element).each(function (i, el) {
                // 1. restore max dropdown size
                $(el).css({
                    'max-width': width
                });
                // 2. shrink it, to fit the viewport
                this._fitSubmenuWidth($(el));
            }.bind(this));

            // split vertical list into columns
            $('.vertical', this.element).each(function () {
                var top, isOnTop, i,
                    minHeight = 0,
                    columns = $(this).data('columns');

                if (self._shouldUseMobileFriendlyFallbacks()) {
                    // use single column mode
                    $(this).height('auto');
                    return;
                }

                $(this).children().each(function () {
                    var height = $(this).height();

                    if (height > minHeight) {
                        minHeight = height;
                    }
                });

                $(this).height(Math.max(
                    $(this).height() / columns,
                    minHeight
                ));

                // loop over children of vertical type to fix possible invalid column count
                // If everything is fine, children count with offsetTop=top will
                // be equal to column count
                top = $(this).children().get(0).offsetTop;

                /**
                 * @param {Number} i
                 * @param {Element} el
                 * @returns {bool}
                 */
                isOnTop = function (i, el) {
                    return el.offsetTop === top;
                };

                i = 0;
                while ($(this).children().filter(isOnTop).length > columns && i++ < 200) {
                    $(this).height($(this).height() + 50);
                }
            });

            // amazon menu
            if ($(this.element).hasClass('navpro-amazon')) {
                $('.navpro-dropdown-level2', this.element)
                    .not('.navpro-dropdown-expanded')
                    .each(function () {
                        var parent = $(this).parent().closest('.navpro-dropdown');
                        $(this).css({
                            'min-height': parent.outerHeight()
                        });
                    });
            }

            // stacked menu
            if ($(this.element).hasClass('navpro-stacked')) {
                $('.navpro-dropdown', this.element)
                    .not('.navpro-dropdown-expanded')
                    .each(function () {
                        var parent = $(this).parent().closest('.navpro-dropdown');
                        $(this).css({
                            'min-height': parent.outerHeight()
                        });
                    });
            }
        },

        /**
         * Fit submenu width to stay inside viewport
         * @param  {jQuery} submenu
         */
        _fitSubmenuWidth: function (submenu) {
            var viewportWidth = $(window).width(),
                cssLeft = isNaN(parseInt(submenu.css('left'))) ? 0 : parseInt(submenu.css('left')),
                left  = Math.max(submenu.offset().left, cssLeft),
                right = left + submenu.width(),
                overlap = right - viewportWidth;

            if (overlap > 0) {
                submenu.css({
                    'max-width': submenu.width() - overlap - 1
                });
            }
        },

        /**
         * Overridden to add dynamic delay value calculation
         * @param  {jQuery} submenu
         */
        _startOpening: function (submenu) {
            clearTimeout(this.timer);

            // Don't open if already open fixes a Firefox bug that caused a .5 pixel
            // shift in the submenu position when mousing over the carat icon
            if (submenu.attr('aria-hidden') !== 'true') {
                return;
            }

            var delay = aim.getActivationDelay(
                submenu,
                submenu.parents(this.options.menus + ',ul.ui-menu').first(),
                this._getSubmenuPosition(submenu)
            );

            if (delay === false) {
                delay = this.options.delay;
            }

            this.timer = this._delay(function () {
                this._close();
                this._open(submenu);
            }, delay);
        },

        /**
         * Get submenu positioning properties
         * @param  {jQuery} submenu
         * @return {Object}
         */
        _getSubmenuPosition: function (submenu) {
            var within, width,
                position = $.extend({
                    of: this.active
                }, this.options.position);

            if (this.options['level' + submenu.data('level')] &&
                this.options['level' + submenu.data('level')].position) {

                within = this.element;
                width = $(this.element).outerWidth();

                // Constrain dropdown inside parent edges
                $(this.element)
                    .closest('.header.content, .column.main, .page-main, .footer.content')
                    .each(function () {
                        var currentWidth = $(this).outerWidth();

                        if (currentWidth && currentWidth > width) {
                            within = this;
                        }
                    });

                position = $.extend(
                    {
                        within: within
                    },
                    position,
                    this.options['level' + submenu.data('level')].position
                );
            }

            // add padding and border width's to the position.at values
            // if `at` is a staight left, right, top or bottom
            // var parentUl   = submenu.closest('ul'),
            //     parentMenu = submenu.parents(this.options.menus + ',ul.ui-menu').first(),
            //     parentUlOffset = parentUl.offset(),
            //     parentMenuOffset = parentMenu.offset(),
            //     parentUlCoords = {
            //         left  : parentUlOffset.left,
            //         top   : parentUlOffset.top,
            //         right : parentUlOffset.left + parentUl.outerWidth(),
            //         bottom: parentUlOffset.top + parentUl.outerHeight()
            //     },
            //     parentMenuCoords = {
            //         left  : parentMenuOffset.left,
            //         top   : parentMenuOffset.top,
            //         right : parentMenuOffset.left + parentMenu.outerWidth(),
            //         bottom: parentMenuOffset.top + parentMenu.outerHeight()
            //     };

            // if (position.at.indexOf('left ') === 0) {
            //     var offset = parentUlCoords.left - parentMenuCoords.left;
            //     position.at = position.at.replace('left', 'left-' + offset);
            // }
            // if (position.at.indexOf(' top') > 0) {
            //     var offset = parentUlCoords.top - parentMenuCoords.top;
            //     position.at = position.at.replace('top', 'top-' + offset);
            // }
            // if (position.at.indexOf('right ') === 0) {
            //     var offset = parentMenuCoords.right - parentUlCoords.right;
            //     position.at = position.at.replace('right', 'right+' + (offset - 1));
            // }
            // if (position.at.indexOf(' bottom') > 0) {
            //     var offset = parentMenuCoords.bottom - parentUlCoords.bottom;
            //     position.at = position.at.replace('bottom', 'bottom+' + offset);
            // }

            return position;
        },

        /**
         * Overridden for:
         *  1. Add ability to use different position per level
         *  2. Remove `show` and `hide` methods in favor of classNames
         *
         * @param {jQuery} submenu - jQuery object with element
         */
        _open: function (submenu) {
            clearTimeout(this.timer);

            if (!this._shouldUseMobileFriendlyFallbacks()) {
                this.element.find('.ui-menu').not(submenu.parents('.ui-menu'))
                    .removeClass('shown')
                    .attr('aria-hidden', 'true');
            }

            submenu
                .addClass('shown')
                .removeAttr('aria-hidden')
                .attr('aria-expanded', 'true');

            if (!this._shouldUseMobileFriendlyFallbacks()) {
                submenu.position(this._getSubmenuPosition(submenu));

                // fix subpixel shift on mac when fit was used
                var coordinates = submenu.offset();
                submenu.offset({
                    top: Math.round(coordinates.top),
                    left: Math.round(coordinates.left)
                });
            }
        },

        /**
         * Overridden to remove `show` and `hide` methods in favor of classNames
         * @param  {jQuery} startMenu
         */
        _close: function (startMenu) {
            if (!startMenu) {
                startMenu = this.active ? this.active.parent() : this.element;
            }

            startMenu
                .find('.ui-menu')
                    .removeClass('shown')
                    .attr('aria-hidden', 'true')
                    .attr('aria-expanded', 'false')
                .end()
                .find('a.ui-state-active')
                    .removeClass('ui-state-active');
        },

        /**
         * Overridden to fix the following issues:
         *
         *   1. Do not add `ui-menu-item` class to the `navpro-dropdown-inner`
         *      Fixed with more precise selector: **li**:not(.ui-menu-item):has(a)
         *
         *   2. Add `ui-menu-item` class to the each li of `ul.children` element
         *      Fixed with `.add(menus.find('ul.children')...` logic
         */
        refresh: function () {
            var menus,
                icon = this.options.icons.submenu,
                submenus = this.element.find(this.options.menus);

            this.element.toggleClass('ui-menu-icons', !!this.element.find('.ui-icon').length);

            // Initialize nested menus
            submenus.filter(':not(.ui-menu)')
                .addClass('ui-menu ui-widget ui-widget-content ui-corner-all')
                .removeClass('shown')
                .attr({
                    role: this.options.role,
                    'aria-hidden': 'true',
                    'aria-expanded': 'false'
                })
                .each(function () {
                    var menu = $(this),
                        item = menu.prev('a'),
                        submenuCarat = $('<span>')
                            .addClass('ui-menu-icon ui-icon ' + icon)
                            .data('ui-menu-submenu-carat', true);

                    item
                        .attr('aria-haspopup', 'true')
                        .prepend(submenuCarat);
                    menu.attr('aria-labelledby', item.attr('id'));
                });

            menus = submenus.add(this.element);

            // Don't refresh list items that are already adapted
            menus.children('li:not(.ui-menu-item):has(a)')
                .add(menus.find('ul.children').children('li:not(.ui-menu-item):has(a)'))
                .addClass('ui-menu-item')
                .attr('role', 'presentation')
                .children('a')
                    .uniqueId()
                    .addClass('ui-corner-all')
                    .attr({
                        tabIndex: -1,
                        role: this._itemRole()
                    });

            // Initialize unlinked menu-items containing spaces and/or dashes only as dividers
            menus.children('li:not(.ui-menu-item)')
                .add(menus.find('ul.children').children('li:not(.ui-menu-item)'))
                .each(function () {
                    var item = $(this);
                    // hyphen, em dash, en dash
                    if (!/[^\-\u2014\u2013\s]/.test(item.text())) {
                        item.addClass('ui-widget-content ui-menu-divider');
                    }
                });

            // Add aria-disabled attribute to any disabled menu item
            menus.children('.ui-state-disabled')
                .add(menus.find('ul.children').children('.ui-state-disabled'))
                .attr('aria-disabled', 'true');

            // If the active item has been removed, blur the menu
            if (this.active && !$.contains(this.element[0], this.active[0])) {
                this.blur();
            }
        }
    });

    return $.swissup.navpro;
});
