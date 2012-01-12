Chronflux.Settings = function()
{
    var self = this;

    this.init = function()
    {
        // init reorderable lists
        $('.side-column-reorderable').each(this.initSortableList);

        return this;
    }

    this.initSortableList = function()
    {
        var $list = $(this);

        $list.sortable({
            axis  : 'y',
            handle: '.sortable-handler',
            update: onSortableUpdate
        });
    }

    function onSortableUpdate(e, ui)
    {
        $sortable = $(e.target);

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

    return this.init();
}