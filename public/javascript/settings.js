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

        // init color picker and current tasks
        initColorPicker();

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

    function initColorPicker()
    {
        var $input = $('#color');

        if ($input.length == 1) {
            // hide input
            $input.hide();

            // setup bubble
            var color  = $input.val() ? '#' + $input.val() : '';
            var bubble = new Chronflux.Bubble().setColor(color);

            // generate trigger
            var $trigger = $('<a />', {
                'class': 'bubble-wrapper',
                'href' : 'javascript:void(0)',
                'text' : 'Select a color'
            }).prepend(bubble.$).insertAfter($input);

            // init color picker
            $trigger.ColorPicker({
                color: color,
                onShow: function (colpkr) {
                    $(colpkr).show();
                    return false;
                },
                onHide: function (colpkr) {
                    $(colpkr).hide();
                    return false;
                },
                onChange: function (hsb, hex, rgb) {
                    bubble.setColor('#' + hex);
                    // get rid of # and update input
                    $input.val(hex);
                }
            });
        }
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