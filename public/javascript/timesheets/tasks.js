Chronflux.Timesheets.Tasks = function($wrapper, options)
{
    // public vars
    this.$    = $wrapper;
    this.btns = false;
    this.list = {};

    // private vars
    var self             = this;
    var _options         = options || {};
    var _deleteTask      = false;
    var _selectedTaskId  = false;
    var _onDidSelectTask = false;

    this.init = function()
    {
        // init button group
        this.btns  = new Chronflux.ButtonSet(this.$.find('.task-btn'));

        // init buttons
        initBtns();

        // init shortcuts
        initShortcuts();

        // default selected task
        //initDefaultSelectedTask();

        // hide and disable delete button
        this.disableDeleteBtn();

        return this;
    }

    this.show = function()
    {
        // TODO: implement tooltip
        self.$.show();
    }

    this.hide = function()
    {
        // TODO: implement tooltip
        self.$.hide();

        // disable delete button
        self.disableDeleteBtn();
    }

    this.getTaskById = function(id)
    {
        if (self.list[id]) {
            return self.list[id];
        } else {
            return false;
        }
    }

    this.getSelectedTask = function()
    {
        return self.getTaskById(_selectedTaskId);
    }

    this.selectFirstTask = function()
    {
        if (self.btns.list[1]) {
            self.btns.list[1].select();
            return true;
        } else {
            return false;
        }
    }

    this.deselect = function()
    {
        self.btns.deselect();
    }

    this.enableDeleteBtn = function()
    {
        _deleteTask.btn.enable();
        _deleteTask.btn.$.show();
    }

    this.disableDeleteBtn = function()
    {
        _deleteTask.btn.$.hide();
        _deleteTask.btn.disable();
    }

    this.onDidSelectTask = function(func)
    {
        _onDidSelectTask = func;
    }

    this.onDidDeselect = function(func)
    {
        this.btns.onDidDeselect(func);
    }

    function initBtns()
    {
        var btnList = self.btns.list;

        for (var i = 0; i < btnList.length; i++) {
            var btn    = btnList[i];
            var taskId = btn.$.data('id') || 0;

            btn.onDidSelect(onTaskBtnDidSelect);

            // add new task to list
            self.list[taskId] = {
                id   : taskId,
                btn  : btn,
                color: btn.$.data('color')
            };
        }

        // store delete task
        _deleteTask = self.list[0];
    }

    function initShortcuts()
    {
        // key shortcuts
        $(window).keyup(onKeyUpHandler);

        // legend
        $('#tasks-keys-toggle-link').click(onLegendClick);
    }

    function initDefaultSelectedTask()
    {
        // TODO: store last used task
        self.selectFirstTask();
    }

    function onTaskBtnDidSelect(event, btn)
    {
        // store task ID
        _selectedTaskId = btn.$.data('id');

        // custom callback
        if ($.isFunction(_onDidSelectTask)) {
            _onDidSelectTask(self.getSelectedTask());
        }
    }

    function onKeyUpHandler(event)
    {
        var keyCode = event.keyCode;

        // only look for number keys
        if (48 <= keyCode && keyCode <= 57) {
            var shortcut = keyCode - 48;

            // delete button
            if (0 == shortcut) {
                _deleteTask.btn.select();
            } else {
                // make sure task exists
                if (self.btns.list[shortcut]) {
                    self.btns.list[shortcut].select();
                }
            }
        }
    }

    function onLegendClick()
    {
        self.$.toggleClass('show-keys');
    }

    return this.init();
}