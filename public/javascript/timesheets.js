Chronflux.Timesheets = function(opts)
{
    // public variables
    this.user;
    this.tasks;
    this.projects;
    this.jobs;
    this.clock;
    this.formattedDate;
    this.date;

    // private variables
    var self                 = this;
    var _$prevActiveTimeElts = false;

    this.init = function()
    {
        this.user     = opts.user;
        this.tasks    = new Chronflux.Timesheets.Tasks($('#tasks-tooltip'));
        this.projects = new Chronflux.Timesheets.Projects($('#projects'));
        this.jobs     = new Chronflux.Timesheets.Jobs($('#jobs'), this.projects);
        this.clock    = new Chronflux.Timesheets.Clock;

        // init task events
        this.tasks.onDidSelectTask(onDidSelectTask);
        this.tasks.onTooltipDidHide(onTasksTooltipDidHide);

        // init job events
        this.jobs.onDidSelect(onDidSelectJobs);

        // store current date
        this.formattedDate = $('#calendar-date time').attr('datetime');
        var explodedDate   = this.formattedDate.split('-');
        this.date          = new Date(parseInt(explodedDate[0]),
                                      parseInt(explodedDate[1] - 1),
                                      parseInt(explodedDate[2]));

        // init clock events
        this.clock.setOnEveryMinute(onEveryMinute);
        this.clock.start();

        // scroll to user's start time
        if (this.user.clock_in_at) {
            // get hour column
            var $hourCol = $('#hour-column-' + this.user.clock_in_at);

            // re-position scroll
            if ($hourCol.length == 1) {
                $('.layout-fluid-right-column').scrollLeft($hourCol.position().left);
            }
            
        }

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
            + '/date/' + self.formattedDate
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

    /* CLOCK EVENT HANDLERS */

    function onEveryMinute(date)
    {
        // deactivate previous elements
        if (_$prevActiveTimeElts) {
            _$prevActiveTimeElts.removeClass('active');
        }

        // check that date is today
        if (self.date.getDate() != date.getDate()
            || self.date.getMonth() != date.getMonth()
            || self.date.getFullYear() != date.getFullYear()
        ) {
            return false;
        }

        // convert minutes to decimal
        var decimalMinutes = Math.floor(date.getMinutes() / 15) * 25 / 100;
        var decimalTime    = date.getHours() + decimalMinutes;

        // get time columns
        _$prevActiveTimeElts = self.jobs.getColumnsByTime(decimalTime);

        // get time and quater hour
        var $activeElts = $('#hour-column-' + date.getHours()).find('time, .quarter-hour[data-time="' + decimalMinutes + '"]');

        // activate time elements
        _$prevActiveTimeElts = _$prevActiveTimeElts.add($activeElts).addClass('active');
    }

    return this.init();
};