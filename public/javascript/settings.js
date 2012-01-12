Chronflux.Settings = function()
{
    var self = this;

    this.init = function()
    {
        // init reorderable lists
        $('.side-column-reorderable').each(this.initSortableList);

        // init inline-editing
        $('.inline-row-editing-edit-link, .inline-row-editing-form-row-cancel').click(onInlineEditingLinkCLick);

        // confirm delete button
        $('.delete').click(onDeleteClick);

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