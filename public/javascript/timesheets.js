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

    this.init = function()
    {
        this.user     = opts.user;
        this.tasks    = new Chronflux.Timesheets.Tasks($('#tasks-tooltip'));
        this.projects = new Chronflux.Timesheets.Projects($('#projects'));
        this.jobs     = new Chronflux.Timesheets.Jobs($('#jobs'), this.projects);

        // init task events
        this.tasks.onDidSelectTask(onDidSelectTask);
        this.tasks.onTooltipDidHide(onTasksTooltipDidHide);

        // init job events
        this.jobs.onDidSelect(onDidSelectJobs);

        // store current date
        this.date = $('#calendar-date time').attr('datetime');

        return this;
    }

    this.saveJobs = function()
    {
        saveJobsHelper('add');
    }

    this.deleteJobs = function()
    {
        saveJobsHelper('remove');
    }

    /* PRIVATE FUNCTIONS */

    function getJobsUrl(action)
    {
        var selectedTask = self.tasks.getSelectedTask();

        return Chronflux.BASE_URL
            + '/user/' + self.user.username
            + '/jobs/' + action
            + '/date/' + self.date
            + '/project_id/' + self.jobs.getSelectedProject().id
            + '/start_time/' + self.jobs.getStartTime()
            + '/stop_time/' + self.jobs.getStopTime()
            + ((selectedTask) ? '/task_id/' + selectedTask.id : '');
    }

    function saveJobsHelper(action)
    {
        $.ajax({
            type: 'POST',
            url: getJobsUrl(action),
            dataType: 'json'
        });
    }

    /* TASKS EVENT HANDLERS */

    function onDidSelectTask(task)
    {
        // update selected jobs' color
        self.jobs.setSelectedColor(task.color);

        if (task.color) {
            // save jobs on server
            self.saveJobs();

            // save jobs visual changes
            self.jobs.save();
        } else {
            // remove jobs from server
            self.deleteJobs();

            // remove jobs visually
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

    function onDidSelectJobs()
    {
        // there's something to delete, show delete button
        if (self.jobs.projectConflictingBubbleSet.length() > 0) {
            self.tasks.enableDeleteBtn();
        }

        // update add new task link
        var nextUrl = getJobsUrl('add');

        self.tasks.setNextUrl(nextUrl);

        // get last selected item
        var bubble = self.jobs.getLastSelectedBubble();

        // show tasks
        self.tasks.showRelativeTo(bubble.$);
    }

    return this.init();
};