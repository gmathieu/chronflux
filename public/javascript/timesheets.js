Chronflux.Timesheets = function()
{
    // public variables
    this.tasks;
    this.projects;
    this.jobs;

    // private variables
    var self = this;

    this.init = function() {
        this.tasks    = new Chronflux.Timesheets.Tasks($('#tasks-wrapper'));
        this.projects = new Chronflux.Timesheets.Projects($('#projects'));
        this.jobs     = new Chronflux.Timesheets.Jobs($('#jobs'), this.projects);

        // init task events
        this.tasks.onDidSelectTask(onDidSelectTask);

        // init job events
        this.jobs.onWillSelect(onWillSelectJobs);
        this.jobs.onDidSelect(onDidSelectJobs);

        return this;
    }

    this.save = function() {
        var task      = self.tasks.getSelectedTask();
        var startTime = self.jobs.getStartTime();
        var stopTime  = self.jobs.getStopTime();
    }

    /* TASKS EVENT HANDLERS */

    function onDidSelectTask(task)
    {
        // update selected jobs' colors
        self.jobs.setSelectedColor(task.color);

        // close tasks
        self.tasks.hide();

        // deselect current buttons
        self.tasks.deselect();

        // save jobs visual changes
        self.jobs.save();
    }

    /* JOBS EVENT HANDLERS */

    function onWillSelectJobs()
    {
        // hide tasks
        self.tasks.hide();
    }

    function onDidSelectJobs()
    {
        // there's something to delete, show delete button
        if (self.jobs.projectConflictingBubbleSet.list.length > 0) {
            self.tasks.enableDeleteBtn();
        }

        // show tasks
        self.tasks.show();
    }

    return this.init();
};