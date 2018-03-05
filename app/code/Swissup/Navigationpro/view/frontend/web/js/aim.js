define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var mouseLocks = [],
        mouseLocksTracked = 3,
        tolerance = 75,

        /**
         * @param {Event} e
         */
        trackMouseMove = _.throttle(function (e) {
            mouseLocks.push({
                x: e.pageX,
                y: e.pageY
            });

            if (mouseLocks.length > mouseLocksTracked) {
                mouseLocks.shift();
            }
        }, 50);

    $(document).on('mousemove', trackMouseMove);

    return {

        /**
         * The method is taken from:
         *  https://github.com/kamens/jQuery-menu-aim
         *  https://github.com/banesto/jQuery-menu-aim
         *
         * @param {jQuery} submenu
         * @param {jQuery} parentMenu
         * @param {Object} position
         */
        getActivationDelay: function (submenu, parentMenu, position) {
            var offset,
                upperLeft,
                upperRight,
                lowerLeft,
                lowerRight,
                loc = mouseLocks[mouseLocks.length - 1],
                prevLoc = mouseLocks[0],
                decreasingCorner,
                increasingCorner,
                submenuDirection,
                decreasingSlope,
                increasingSlope,
                prevDecreasingSlope,
                prevIncreasingSlope;

            if (!loc) {
                return 0;
            }

            // calculate menu coords
            offset = parentMenu.offset();
            upperLeft = {
                x: offset.left,
                y: offset.top - tolerance
            };
            upperRight = {
                x: offset.left + parentMenu.outerWidth(),
                y: upperLeft.y
            };
            lowerLeft = {
                x: offset.left,
                y: offset.top + parentMenu.outerHeight() + tolerance
            };
            lowerRight = {
                x: offset.left + parentMenu.outerWidth(),
                y: lowerLeft.y + 500
            };

            if (!prevLoc) {
                prevLoc = loc;
            }

            if (prevLoc.x < offset.left || prevLoc.x > lowerRight.x ||
                prevLoc.y < offset.top || prevLoc.y > lowerRight.y) {
                // If the previous mouse location was outside of the entire
                // menu's bounds, immediately activate.
                return 0;
            }

            if (this.lastDelayLoc && loc.x === this.lastDelayLoc.x && loc.y === this.lastDelayLoc.y) {
                // If the mouse hasn't moved since the last time we checked
                // for activation status, immediately activate.
                return 0;
            }

            // Detect if the user is moving towards the currently activated
            // submenu.
            //
            // If the mouse is heading relatively clearly towards
            // the submenu's content, we should wait and give the user more
            // time before activating a new row. If the mouse is heading
            // elsewhere, we can immediately activate a new row.
            //
            // We detect this by calculating the slope formed between the
            // current mouse location and the upper/lower right points of
            // the menu. We do the same for the previous mouse location.
            // If the current mouse location's slopes are
            // increasing/decreasing appropriately compared to the
            // previous's, we know the user is moving toward the submenu.
            //
            // Note that since the y-axis increases as the cursor moves
            // down the screen, we are looking for the slope between the
            // cursor and the upper right corner to decrease over time, not
            // increase (somewhat counterintuitively).
            /**
             * @param {Object} a
             * @param {Object} b
             * @returns {Number}
             */
            function slope(a, b) {
                return (b.y - a.y) / (b.x - a.x);
            }

            decreasingCorner = upperRight;
            increasingCorner = lowerRight;

            // Our expectations for decreasing or increasing slope values
            // depends on which direction the submenu opens relative to the
            // main menu. By default, if the menu opens on the right, we
            // expect the slope between the cursor and the upper right
            // corner to decrease over time, as explained above. If the
            // submenu opens in a different direction, we change our slope
            // expectations.
            submenuDirection = 'right';

            if (position.my.indexOf('top') > -1 && position.at.indexOf('bottom') > -1) {
                submenuDirection = 'below';
            } else if (position.my.indexOf('right') > -1 && position.at.indexOf('left') > -1) {
                submenuDirection = 'left';
            } else if (position.my.indexOf('bottom') > -1 && position.at.indexOf('top') > -1) {
                submenuDirection = 'above';
            }

            if (submenuDirection === 'left') {
                decreasingCorner = lowerLeft;
                increasingCorner = upperLeft;
            } else if (submenuDirection === 'below') {
                decreasingCorner = lowerRight;
                increasingCorner = lowerLeft;
            } else if (submenuDirection === 'above') {
                decreasingCorner = upperLeft;
                increasingCorner = upperRight;
            }

            decreasingSlope = slope(loc, decreasingCorner);
            increasingSlope = slope(loc, increasingCorner);
            prevDecreasingSlope = slope(prevLoc, decreasingCorner);
            prevIncreasingSlope = slope(prevLoc, increasingCorner);

            if (decreasingSlope < prevDecreasingSlope && increasingSlope > prevIncreasingSlope) {
                // Mouse is moving from previous location towards the
                // currently activated submenu. Delay before activating a
                // new menu row, because user may be moving into submenu.
                this.lastDelayLoc = loc;

                return false;
            }

            this.lastDelayLoc = null;

            return 0;
        }
    }
});
