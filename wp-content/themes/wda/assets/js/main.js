'use strict';

(function ($) {

    function debounce(func, wait, immediate) {
        var timeout;
        return function () {
            var context = this, args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    function clickOutsideDivHandler(selector, callback) {
        var args = Array.prototype.slice.call(arguments);
        $(document).on("mouseup.clickOFF touchend.clickOFF", function (e) {
            var container = $(selector);

            if (!container.is(e.target)
                && container.has(e.target).length === 0)
            {
                $(document).off("mouseup.clickOFF touchend.clickOFF");
                if (callback) callback.apply(this, args);
            }
        });
    }

    var s, d;
    var Wda = {
        settings: {
            document: $(document),
            body: $(document.body),
            window: $(window),
            browserWidth: 0,
            browserHeight: 0,
            scrolltop: jQuery("#scrolltop"),
        },
        init: function () {
            d = this;
            s = this.settings;

            document.addEventListener('DOMContentLoaded', this.ready);

            window.addEventListener('resize', debounce(function () {
                d.updateBrowserDimension();
            }, 250));

            this.updateBrowserDimension();

            s.scrolltop.click( function() {
                body.animate({ scrollTop: 0 }, 1000);
            } );
            this.bindScrollToTop();
        },
        ready: function () {
            
        },
        updateBrowserDimension : function () {
            s.browserWidth = s.window.width();
            s.browserHeight = s.window.height();
        },
        bindScrollToTop : function() {
            s.window.bind('scroll', function () {
                if (s.window.scrollTop() > 100) {
                    s.scrolltop.addClass('fixed');
                }
                else
                {
                    s.scrolltop.removeClass('fixed');
                }
            });
        }
    };

    Wda.init();

}(jQuery));
