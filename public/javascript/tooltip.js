Chronflux.Tooltip = function(options)
{
    // public variables
    this.$;
    this.$document;
    this.$window;
    this.$arrow;
    this.$overlay;
    this.$relativePositionElt;

    // private variables
    var self              = this;
    var _opts             = options || {};
    var _arrowDirection   = options.arrowDirection || 'up';
    var _offset           = options.offset || 15;
    var _arrowCenterPoint = {};

    this.init = function()
    {
        self.$document = $(document);
        self.$window   = $(window);

        // init wrapper
        initWrapper();

        // init arrow
        initArrow();

        // init overlay
        initOverlay();

        return this;
    }

    this.rePosition = function()
    {
        if (this.$relativePositionElt) {
            this.setPositionRelativeTo(this.$relativePositionElt);
        }
    }

    this.setPositionRelativeTo = function($elt)
    {
        var offset = $elt.offset();
        var width  = $elt.outerWidth();
        var height = $elt.outerHeight();

        // left: target offset left + target's center point
        var left = offset.left + (width / 2);

        // top: target offset top + target's height
        var top = offset.top + height;

        // store current target
        this.$relativePositionElt = $elt;

        this.setPosition(left, top);
    }

    this.setPosition = function(left, top)
    {
        // try to position tooltip at these coordinates
        var targetLeft = left;
        var targetTop  = top;

        // adjust for offset and arrow center point
        targetLeft += -_offset - _arrowCenterPoint.x;

        // reposition tooltip to get fully expanded width
        this.$.css({'left': -2000, 'top': -2000});

        // check if popup is cut on the right
        var offScreenDelta = this.$window.width() - (targetLeft + this.$.outerWidth());
        if (offScreenDelta < 0) {
            // shit left by 1px for rounding errors
            targetLeft += offScreenDelta - 1;
        }

        // adjust position based on width
        this.$.css({'left': targetLeft, 'top': targetTop});

        // position arrow
        this.$arrow.css({
            'left': left - targetLeft - _arrowCenterPoint.x
        });
    }

    this.show = function()
    {
        // show overlay
        self.$overlay.show();

        // show wrapper
        this.$.show();

        // bind keyboard shortcuts
        self.$document.keyup(onDocumentKeyup);
    }

    this.hide = function()
    {
        // hide wrapper
        this.$.hide();

        // hide overlay
        self.$overlay.hide();

        // trigger callback
        this.$.trigger('tooltipDidHide', [self]);
    }

    this.cancel = function()
    {
        // hide tooltip
        this.hide();

        // trigger callback
        this.$.trigger('tooltipDidCancel', [self]);
    }

    this.onDidHide = function(func)
    {
        this.$.bind('tooltipDidHide', func);
    }

    this.onDidCancel = function(func)
    {
        this.$.bind('tooltipDidCancel', func);
    }

    this.setArrowDirection = function(direction)
    {
        _arrowDirection = direction;

        // remove all classes
        self.$arrow.removeClass();

        // only set required classes
        self.$arrow.addClass('arrow').addClass('arrow-' + direction);
    }

    this.onDidHide = function(func)
    {
        self.$.bind('tooltipDidHide', func);
    }

    function initWrapper()
    {
        if (options.wrapper) {
            self.$ = options.wrapper;
        }

        // init wrapper events
        self.onDidHide(unbindEvents);
    }

    function initArrow()
    {
        // find arrow
        self.$arrow = self.$.find('.arrow');

        // set arrow direction
        self.setArrowDirection(_arrowDirection);

        // store arrow width
        _arrowCenterPoint = {
            x: self.$arrow.width() / 2,
            y: self.$arrow.height() / 2
        };
    }

    function initOverlay()
    {
        // overlay is a singletone find or create
        self.$overlay = $('#tooltip-overlay');

        if (self.$overlay.length == 0) {
            self.$overlay = $(document.createElement('div')).attr('id', 'tooltip-overlay');
            self.$overlay.appendTo($('body'));
        }

        // init over events
        self.$overlay.click(onOverlayClick);
    }

    function unbindEvents()
    {
        self.$document.unbind('keyup', onDocumentKeyup);
    }

    function onOverlayClick()
    {
        self.hide();
    }

    function onDocumentKeyup(e)
    {
        // escape key
        if (e.which == 27) {
            self.cancel();
        }
    }

    return this.init();
}