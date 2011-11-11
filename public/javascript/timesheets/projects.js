Chronflux.Timesheets.Projects = function($wrapper)
{
    // public variables
    this.$           = $wrapper;
    this.list        = [];
    this.indexLookup = {};

    // private variables
    var self = this;

    this.init = function()
    {
        // init project list
        this.$.find('.project').each(initProject);

        return this;
    }

    this.get = function(id)
    {
        return this.list[this.indexLookup[id]];
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

        // update lookup
        self.indexLookup[project.id] = self.list.length;

        // add project to list
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