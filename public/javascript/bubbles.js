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

    this.select = function(color)
    {
        return callFunctionOnList('select', color);
    }

    this.deselect = function()
    {
        return callFunctionOnList('deselect');
    }

    this.setColor = function(color)
    {
        return callFunctionOnList('setColor', color);
    }

    this.clear = function()
    {
        return callFunctionOnList('clear');
    }

    this.clear = function()
    {
        delete(this.list);
        this.list = [];

        return this;
    }

    this.each = function(callback)
    {
        for (var i = 0; i < self.list.length; i++) {
            callback(i, self.list[i]);
        }

        return this;
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

    // private variables
    var self = this;

    this.init = function()
    {
        // assign data attributes to public variables
        var data = this.$.data()
        for (var key in data) {
            self[key] = data[key];
        }

        return this;
    }

    this.isFilled = function()
    {
        return this.color.length > 0;
    }

    this.select = function(color)
    {
        var color = color || '#ffffff';
        this.$.css('backgroundColor', color);
        this.$.addClass('selected');

        return this;
    }

    this.deselect = function()
    {
        // restore color
        self.setColor(self.color);
        this.$.removeClass('selected');

        return this;
    }

    this.clear = function()
    {
        this.setColor();
    }

    this.setColor = function(color)
    {
        this.color = color || '';

        if (color === '') {
            this.$.removeClass('filled');
            this.$.css({
                'backgroundColor': '',
                'color'          : ''
            });
        } else {
            this.$.addClass('filled');
            this.$.css({
                'backgroundColor': color,
                'color'          : color
            });
        }

        return this;
    }

    return this.init();   
}