jQuery(function() {

    $(document).on('click', '.reply-button', function (e) {
        e.preventDefault();
        var row = $(this).closest('.row');
        var form = row.find('.reply-form');
        form.toggleClass('hidden');
    });

    $(document).on('click', '.update-button', function (e) {
        e.preventDefault();
        var row = $(this).closest('.row');
        var form = row.find('.update-form');
        form.toggleClass('hidden');
    });

});
