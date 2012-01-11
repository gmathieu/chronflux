Chronflux.Timesheets.Jobs = function($wrapper, projects)
{
    // public variables
    this.$                           = $wrapper;
    this.selectedBubbleSet           = new Chronflux.BubbleSet;
    this.conflictingBubbleSet        = new Chronflux.BubbleSet;
    this.projectConflictingBubbleSet = new Chronflux.BubbleSet;
    this.allJobs                     = {};
    this.timeColumns                 = {};

    // private variables
    var self             = this;
    var _selecting       = false;
    var _startTime       = false;
    var _stopTime        = false;
    var _selectedProject = false;

    // custom events
    var _onDidSelect;

    this.init = function()
    {
        // init projects jobs
        projects.each(function(i, project) {
            initProjectJobs(project);
        });

        return this;
    }

    this.getColumnsByTime = function(time)
    {
        if (this.timeColumns[time]) {
            return this.timeColumns[time];
        }
    }

    this.getStartTime = function()
    {
        // start time depends on when which way user selected bubbles
        return (_startTime < _stopTime) ? _startTime : _stopTime;
    }

    this.getStopTime = function()
    {
        // stop time depends on when which way user selected bubbles
        var time = (_stopTime > _startTime) ? _stopTime : _startTime;

        // each bubble represents 15 minutes of time so add 0.25
        return time + 0.25;
    }

    this.getSelectedProject = function()
    {
        return _selectedProject;
    }

    this.getLastSelectedBubble = function()
    {
        // the time determines the order in which bubbles were selected
        if (_stopTime > _startTime) {
            var lastItemIndex = this.selectedBubbleSet.length() - 1;
            return this.selectedBubbleSet.list[lastItemIndex];
        } else {
            return this.selectedBubbleSet.list[0];
        }
    }

    this.setSelectedColor = function(color)
    {
        // re-select items with new color
        this.selectedBubbleSet.setColor(color);
    }

    this.save = function()
    {
        // delete conflicting jobs
        this.conflictingBubbleSet.each(deleteJob);

        // save all selected jobs
        this.selectedBubbleSet.each(function(i, job) {
            // update all jobs with new job
            self.allJobs[job.time] = job;

            // associate new job with project
            projects.get(job.projectId).jobs[job.time] = job;
        });

        // reset everything
        this.reset();
    }

    this.delete = function()
    {
        // delete all selected jobs
        this.selectedBubbleSet.each(deleteJob);

        // reset everything
        this.reset();
    }

    this.reset = function()
    {
        resetAllBubbleSets();

        // reset variables
        _startTime       = false;
        _stopTime        = false;
        _selectedProject = false;
    }

    this.onDidSelect = function(func)
    {
        _onDidSelect = func;
    }

    function initProjectJobs(project)
    {
        // add lookup tables to project
        project.bubbles = {};
        project.jobs    = {};

        var $projectJobs = $('#project_jobs_' + project.id);

        // find bubbles
        $projectJobs.find('.bubble').each(function() {
            var $bubble        = $(this);
            var $bubbleWrapper = $bubble.parent();
            var time           = $bubble.data('time');
            var bubble         = new Chronflux.Bubble($bubble);

            // store projectId on bubble
            bubble.projectId = project.id;

            // add bubble to "jobs" if task is filled
            if (bubble.isFilled()) {
                project.jobs[time] = bubble;
                self.allJobs[time] = bubble;
            }

            // add bubble to project
            project.bubbles[time] = bubble;

            // save time column
            if (self.timeColumns[time]) {
                self.timeColumns[time] = self.timeColumns[time].add($bubbleWrapper);
            } else {
                self.timeColumns[time] = $bubbleWrapper;
            }

            // init events
            var eventData = {'project': project, 'bubble': bubble};
            $bubbleWrapper.mousedown(eventData, onColumnMouseDown);
            $bubbleWrapper.mouseup(eventData, onColumnMouseUp);
            $bubbleWrapper.mouseenter(eventData, onColumnMouseEnter);
        });
    }

    function selectProjectJobs(time)
    {
        // invert startTime and stopTime when current time is less than _startTime
        if (time <= _startTime) {
            var startTime = time;
            var stopTime  = _startTime;
        } else {
            var startTime = _startTime;
            var stopTime  = time;
        }

        // reset all bubble sets
        resetAllBubbleSets();

        // select all bubbles between startTime and stopTime
        while (startTime <= stopTime) {
            // select bubble
            var bubble = _selectedProject.bubbles[startTime].select();

            // save selected bubble
            self.selectedBubbleSet.list.push(bubble);

            // check for conflicting jobs
            if (conflictingBubble = self.allJobs[startTime]) {
                // dim conflicting bubble
                conflictingBubble.dim();

                // current project job conflicts
                if (conflictingBubble.projectId == _selectedProject.id) {
                    self.projectConflictingBubbleSet.list.push(conflictingBubble);
                } else {
                    // other projects job conflicts
                    self.conflictingBubbleSet.list.push(conflictingBubble);
                }
            }

            // increment time by 15 minutes
            startTime += 0.25;
        }
    }

    function resetAllBubbleSets()
    {
        // deselect current bubble set and clear
        self.selectedBubbleSet.deselect().clear();

        // clear conflicting bubble sets
        self.conflictingBubbleSet.undim().clear();
        self.projectConflictingBubbleSet.undim().clear();
    }

    function deleteJob(i, job) {
        // reset color
        job.setColor('');

        // delete job from project
        delete(projects.get(job.projectId).jobs[job.time]);

        // delete job from all jobs lookup only they have the same project ID
        if (self.allJobs[job.time] && 
            self.allJobs[job.time].projectId == job.projectId
        ) {
            delete(self.allJobs[job.time]);
        }
    }

    function onColumnMouseDown(e)
    {
        // mouse can get stuck in selecting mode
        if (_selecting) {
            return onColumnMouseUp(e);
        }

        _selectedProject = e.data.project;
        _startTime       = e.data.bubble.time;
        _stopTime        = false;

        // change state
        _selecting = true;

        // select bubbles
        selectProjectJobs(e.data.bubble.time);

        // prevent default selection
        e.preventDefault();
    }

    function onColumnMouseEnter(e)
    {
        // make sure bubbles are being selected
        if (_selecting) {
            selectProjectJobs(e.data.bubble.time);
        }
    }

    function onColumnMouseUp(e)
    {
        // save stop time
        _stopTime = e.data.bubble.time;

        // change state
        _selecting = false;

        // custom callbak
        if ($.isFunction(_onDidSelect)) {
            _onDidSelect();
        }
    }

    return this.init();
}