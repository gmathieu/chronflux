Chronflux.ButtonSet = function($elements, options)
{
    // public variables
    this.list = [];

    // private varaiables
    var self           = this;
    var _options       = options || {};
    var _selectedBtn   = false;
    var _onDidDeselect;

    this.init = function()
    {
        $elements.each(initBtn);

        return this;
    }

    this.getSelected = function()
    {
        return _selectedBtn;
    }

    this.find = function(selector)
    {
        var output = [];

        for (var i = 0; i < self.list.length; i++) {
            var btn = self.list[i];

            // add to output if selector matches
            if (btn.$.is(selector)) {
                output.push(btn);
            }
        }

        return output;
    }

    this.deselect = function()
    {
        if (_selectedBtn) {
            _selectedBtn.deselect();

            if ($.isFunction(_onDidDeselect)) {
                _onDidDeselect(_selectedBtn);
            }
        }
    }

    this.onDidDeselect = function(func)
    {
        _onDidDeselect = func;
    }

    function initBtn()
    {
        var btn  = new Chronflux.Button($(this));

        // init button selection
        btn.onDidSelect(onDidSelectBtn);

        // add custom handlers
        btn.onDidSelect(_options.onDidSelect);
        btn.onDidDeselect(_options.onDidDeselect);

        self.list.push(btn)
    }

    function onDidSelectBtn(event, btn)
    {
        // check that selected button exists and is different
        if (_selectedBtn && _selectedBtn != btn) {
            // deselect selected button
            _selectedBtn.deselect();
        }

        // store current selected button
        _selectedBtn = btn;
    }

    return this.init();
}

Chronflux.Button = function($elt)
{
    this.$       = $elt;
    this.enabled = true;

    var self = this;

    this.init = function()
    {
        this.$.click(onDidClick);

        return this;
    }

    this.select = function()
    {
        if (false == this.enabled) {
            return false;
        }

        this.$.addClass('selected');
        this.$.trigger('btnDidSelect', [self]);
    }

    this.deselect = function()
    {
        this.$.removeClass('selected');
        this.$.trigger('btnDidDeselect', [self]);
    }

    this.enable = function()
    {
        this.enabled = true;
        this.$.removeClass('disabled');
    }

    this.disable = function()
    {
        this.enabled = false;
        this.$.addClass('disabled');
    }

    this.onDidSelect = function(func)
    {
        this.$.bind('btnDidSelect', func);

        return this;
    }

    this.onDidDeselect = function(func)
    {
        this.$.bind('btnDidDeselect', func);

        return this;
    }

    function onDidClick()
    {
        self.select();
    }

    return this.init();
}