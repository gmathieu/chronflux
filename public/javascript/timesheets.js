Chronflux.Timesheets = function(opts)
{
    // public variables
    this.user;
    this.tasks;
    this.projects;
    this.jobs;
    this.date;

    // private variables
    var self = this;

    this.init = function() {
        this.user     = opts.user;
        this.tasks    = new Chronflux.Timesheets.Tasks($('#tasks-tooltip'));
        this.projects = new Chronflux.Timesheets.Projects($('#projects'));
        this.jobs     = new Chronflux.Timesheets.Jobs($('#jobs'), this.projects);

        // init task events
        this.tasks.onDidSelectTask(onDidSelectTask);
        this.tasks.onTooltipDidHide(onTasksTooltipDidHide);

        // init job events
        this.jobs.onWillSelect(onWillSelectJobs);
        this.jobs.onDidSelect(onDidSelectJobs);

        // store current date
        this.date = $('#calendar-date time').attr('datetime');

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
        // update selected jobs' color
        self.jobs.setSelectedColor(task.color);

        if (task.color) {
            // save jobs visual changes
            self.jobs.save();
        } else {
            self.jobs.delete();
        }

        // deselect current buttons
        self.tasks.deselect();

        // close tasks
        self.tasks.hide();
    }

    function onTasksTooltipDidHide()
    {
        // reset selected jobs
        self.jobs.reset();
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
        if (self.jobs.projectConflictingBubbleSet.length() > 0) {
            self.tasks.enableDeleteBtn();
        }

        // update add new task link
        var nextUrl = Chronflux.BASE_URL
            + '/user/' + self.user.username
            + '/jobs/add/date/' + self.date
            + '/project_id/' + self.jobs.getSelectedProject().id
            + '/start_time/' + self.jobs.getStartTime()
            + '/stop_time/' + self.jobs.getStopTime();

        self.tasks.setNextUrl(nextUrl);

        // get last selected item
        var bubble = self.jobs.getLastSelectedBubble();

        // show tasks
        self.tasks.showRelativeTo(bubble.$);
    }

    return this.init();
};