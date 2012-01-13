Chronflux.Settings = function()
{
    var self = this;

    var _selectedBubble = false;

    this.init = function()
    {
        // init reorderable lists
        $('.side-column-reorderable').each(this.initSortableList);

        // init inline-editing
        $('.inline-row-editing-edit-link, .inline-row-editing-form-row-cancel').click(onInlineEditingLinkCLick);

        // confirm delete button
        $('.delete').click(onDeleteClick);

        // replace radio buttons with filled bubble
        initBubbles();

        return this;
    }

    this.initSortableList = function()
    {
        $(this).sortable({
            axis  : 'y',
            handle: '.sortable-handler',
            update: onSortableUpdate
        });
    }

    function initBubbles()
    {
        $('input[name="color"]').each(function() {
            var $input  = $(this).hide();
            var $parent = $input.parent();
            var color   = $parent.text();

            // clear parent content
            $parent.empty();

            // create bubble
            var $bubble = $('<span />', {
                'class': 'bubble filled',
                'css'  : { 'color': '#' + color }
            });

            // selected state
            if ($input.prop('checked')) {
                _selectedBubble = $bubble.addClass('selected');
            }

            // create inner bubble
            var $innerBubble = $('<span />', {
                'class': 'inner-bubble',
                'css'  : { 'backgroundColor': '#' + color }
            });

            // add input back to parent
            $parent.append($input).addClass('bubble-click-area');

            // add bubble to parent
            $bubble.append($innerBubble).appendTo($parent);

            // assign click events
            $parent.click(function() {
                var $trigger = $(this);

                // deselect current
                if (_selectedBubble) {
                    _selectedBubble.removeClass('selected');
                }

                // select new bubble
                _selectedBubble = $trigger.find('.bubble').addClass('selected');
            })
        });
    }

    function onSortableUpdate(e, ui)
    {
        var $sortable      = $(e.target);
        var serializedData = $sortable.sortable('serialize', {
            'key'      : 'ids[]',
            'attribute': 'data-id'
        });

        $.ajax({
            type    : 'POST',
            url     : $sortable.data('reorderUrl') + '?' + serializedData,
            dataType: 'json'
        });
    }

    function onInlineEditingLinkCLick()
    {
        $(this).closest('.inline-row-editing').toggleClass('active');
    }

    function onDeleteClick()
    {
        return confirm('Are you sure you want to delete this?');
    }

    return this.init();
}