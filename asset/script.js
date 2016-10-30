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
    $('.db-item input[type=checkbox]').on('change', function() {
        var $parent = $(this).parents('.db-item');
        var $target = $($parent.data('target'));
        var total = $parent.find('input[type=checkbox]').length;
        var checked = $parent.find('input[type=checkbox]:checked').length;
        $target.find('.checked-info').remove();
        $target.append('<span class="checked-info"> ('+checked+'/'+total+')</span>');
    });
});
