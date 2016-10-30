$(document).ready(function() {
    $('.toolbar').each(function() {
        var $target = $($(this).data('target'));
        var $that = $(this);
        var mapping = {
            'select': function() {
                $target.select();
            }
        }

        $.each(mapping, function(name, cb) {
            $that.find('[data-action-'+name+']').on('click', cb);
        });
    });
});
