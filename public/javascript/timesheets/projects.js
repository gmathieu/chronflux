Chronflux.Timesheets.Projects = function($wrapper)
{
    // public variables
    this.$       = $wrapper;
    this.list    = [];

    // private variables
    var self = this;

    this.init = function()
    {
        // init project list
        this.$.find('.project').each(initProject);

        return this;
    }

    this.each = function(callback)
    {
        for (var i = 0; i < self.list.length; i++) {
            callback(i, self.list[i]);
        }

        return this;
    }

    function initProject()
    {
        var project = new Chronflux.Timesheets.Project($(this));

        self.list.push(project);
    }

    return this.init();
}

Chronflux.Timesheets.Project = function($wrapper)
{
    // public variables
    this.$;
    this.id;

    // private variables
    var self = this;

    this.init = function()
    {
        this.$  = $wrapper;
        this.id = this.$.data('id');

        return this;
    }

    return this.init();
}