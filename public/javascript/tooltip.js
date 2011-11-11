Chronflux.Tooltip = function(options)
{
    // public variables
    this.$;
    this.$document;
    this.$arrow;
    this.$overlay;

    // private variables
    var self            = this;
    var _opts           = options || {};
    var _arrowDirection = options.arrowDirection || 'up';

    this.init = function()
    {
        self.$document = $(document);

        // init wrapper
        initWrapper();

        // init arrow
        initArrow();

        // init overlay
        initOverlay();

        return this;
    }

    this.setPositionRelativeTo = function($target)
    {
        var offset = $target.offset();
        var width  = $target.outerWidth();
        var height = $target.outerHeight();

        // left: target offset left + target's center point
        var targetLeft = offset.left + (width / 2);

        // top: target offset top + target's height
        var targetTop = offset.top + height;

        // adjust position based on width
        this.$.css({'left': targetLeft, 'top': targetTop});
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

    this.position = function(x, y)
    {
        this.$.css({
            'left': x,
            'top' : y
        });
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