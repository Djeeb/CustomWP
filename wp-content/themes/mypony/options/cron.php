<?php
/*
add_action('mypony_import_content', function () {
    touch(__DIR__ . '/demo-' . time());
});
add_filter('cron_schedules', function ($schedules) {
    $schedules['ten_seconds'] = [
        'interval' => 10,
        'display' => __('Toutes les 10 secondes', 'mypony')
    ];
    return $schedules;
});

/*
if ($timestamp = wp_next_scheduled('mypony_import_content')) {
    wp_unschedule_event($timestamp, 'mypony_import_content');
}

echo '<pre>';
var_dump(_get_cron_array());
echo '</pre>';
die();
*/