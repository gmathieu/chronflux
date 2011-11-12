Chronflux.BubbleSet = function($elements)
{
    // public variables
    this.list = [];

    // private variables
    var self = this;

    this.init = function()
    {
        if ($elements) {
            $elements.each(initBubble);
        }

        return this;
    }

    this.select = function()
    {
        return callFunctionOnList('select');
    }

    this.deselect = function()
    {
        return callFunctionOnList('deselect');
    }

    this.dim = function()
    {
        return callFunctionOnList('dim');
    }

    this.undim = function()
    {
        return callFunctionOnList('undim');
    }

    this.setColor = function(color)
    {
        return callFunctionOnList('setColor', color);
    }

    this.clear = function()
    {
        delete(this.list);
        this.list = [];

        return this;
    }

    this.each = function(callback)
    {
        for (var i = 0; i < self.length(); i++) {
            callback(i, self.list[i]);
        }

        return this;
    }

    this.length = function()
    {
        return this.list.length;
    }

    function initBubble()
    {
        var bubble = new Chronflux.Timesheets.Bubble($(this));
        this.list.push(bubble);
    }

    function callFunctionOnList(callback, argument)
    {
        self.each(function(i, bubble) {
            bubble[callback](argument);
        });

        return self;
    }

    return this.init();
}

Chronflux.Bubble = function($wrapper)
{
    // public variables
    this.$ = $wrapper;
    this.$inner;

    // private variables
    var self = this;

    this.init = function()
    {
        // assign data attributes to public variables
        var data = this.$.data()
        for (var key in data) {
            self[key] = data[key];
        }

        // find inner bubble
        this.$inner = this.$.children();

        return this;
    }

    this.isFilled = function()
    {
        return this.color.length > 0;
    }

    this.select = function()
    {
        this.$.addClass('selected');

        return this;
    }

    this.deselect = function()
    {
        this.$.removeClass('selected');

        return this;
    }

    this.dim = function()
    {
        this.$.addClass('dimmed');

        return this;
    }

    this.undim = function()
    {
        this.$.removeClass('dimmed');

        return this;
    }

    this.clear = function()
    {
        this.setColor();
    }

    this.setColor = function(color)
    {
        this.color = color || '';

        if (this.color === '') {
            this.$.css('color', '').removeClass('filled');
            this.$inner.css('backgroundColor', '');
        } else {
            this.$.css('color', color).addClass('filled');
            this.$inner.css('backgroundColor', color);
        }

        return this;
    }

    return this.init();   
}