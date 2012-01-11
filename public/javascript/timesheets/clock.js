Chronflux.Timesheets.Clock = function()
{
    var self             = this;
    var _started         = false;
    var _intervalId      = false;
    var _previousSeconds = false;
    var _previousMinutes = false;

    // callback
    var _onEverySecond = function() {};
    var _onEveryMinute = function() {};

    this.init = function()
    {
        // assign window events
        $(window).blur(onWindowBlur);
        $(window).focus(onWindowFocus);

        return this;
    }

    this.start = function()
    {
        // check that clock hasn't already been started
        if (!_intervalId) {
            _intervalId = setInterval(intervalHandler, 500);
            _started = true;
        }
    }

    this.pause = function()
    {
        // check that interval has been started
        if (_intervalId) {
            clearInterval(_intervalId);
            _intervalId = false;
        }
    }

    this.stop = function()
    {
        this.pause();

        _started = false;
    }

    this.setOnEverySecond = function(func)
    {
        _onEverySecond = func;
    }

    this.setOnEveryMinute = function(func)
    {
        _onEveryMinute = func;
    }

    // private functions

    function setSecond(date)
    {
        var seconds = date.getSeconds();
        if (seconds != _previousSeconds) {
            _previousSeconds = seconds;
            _onEverySecond(date);
        }
    }

    function setMinute(date)
    {
        var minutes = date.getMinutes();
        if (minutes != _previousMinutes) {
            _previousMinutes = minutes;
            _onEveryMinute(date);
        }
    }

    function intervalHandler()
    {
        var date = new Date;
        setSecond(date);
        setMinute(date);
    }

    // event handlers

    function onWindowFocus()
    {
        if (_started) {
            self.start();
        }
    }

    function onWindowBlur()
    {
        if (_started) {
            self.pause();
        }
    }

    return this.init();
}