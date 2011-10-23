Chronflux.ButtonSet = function($elements, options)
{
    this.list = [];

    var self         = this;
    var _options     = options || {};
    var _selectedBtn = false;

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

    function initBtn()
    {
        var btn  = new Chronflux.Button($(this));

        // init button selection
        btn.onSelect(onBtnSelect);

        // add custom handlers
        btn.onSelect(_options.onSelect);
        btn.onDeselect(_options.onDeselect);

        self.list.push(btn)
    }

    function onBtnSelect(event, btn)
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
        this.$.trigger('btnSelected', [self]);
    }

    this.deselect = function()
    {
        if (false == this.enabled) {
            return false;
        }

        this.$.removeClass('selected');
        this.$.trigger('btnDeselected', [self]);
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

    this.onSelect = function(func)
    {
        this.$.bind('btnSelected', func);

        return this;
    }

    this.onDeselect = function(func)
    {
        this.$.bind('btnDeselect', func);

        return this;
    }

    function onDidClick()
    {
        self.select();
    }

    return this.init();
}