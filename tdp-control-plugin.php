<?php

/**
 * Plugin Name: tdp-control
 * Description: Runs daily functions from tdp plugins
 * Version: 1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function schedule_tdp_daily_functions()
{
    if (!wp_next_scheduled('run_tdp_daily_functions')) {
        $time = strtotime('01:00:00'); // Set the time you want the function to run
        if ($time < time()) {
            $time += 24 * 60 * 60; // Add 24 hours if the time has already passed today
        }
        wp_schedule_event($time, 'daily', 'run_tdp_daily_functions');
    }
}
add_action('wp', 'schedule_tdp_daily_functions');

function run_tdp_daily_functions()
{
    set_time_limit(3000);
    trigger_error('RUNNING TDP DAILY FUNCTIONS', E_USER_WARNING);
    update_statistics_data_for_all_gd_places();
    sleep(60);
    update_statistics_data_for_all_geolocations();
    sleep(60);
    consolidate_geolocations();
    sleep(60);
    import_boxdepotet_scraper_data();
    sleep(60);
    generate_seo_texts();
    sleep(30);
    generate_top_seo_texts();
    sleep(30);
    generate_missing_static_map_images();
    sleep(30);
    generate_nearby_locations_lists();
    sleep(30);
    generate_meta_titles();
    sleep(30);
    generate_meta_descriptions();
    sleep(30);
    generate_default_unit_list_for_all_gd_places();
    sleep(30);
    generate_missing_chatgpt_geolocation_descriptions(50);
    sleep(30);
    generate_missing_chatgpt_geolocation_short_descriptions(50);
    trigger_error('FINISHED RUNNING TDP DAILY FUNCTIONS', E_USER_WARNING);

    //send an email to the admin
    $subject = "TDP Daily Functions just ran";
    $to = get_option('admin_email'); // Get the admin email

    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $body, $headers);
}
add_action('run_tdp_daily_functions', 'run_tdp_daily_functions');

//add a admin plugin button to run the daily functions
function add_run_tdp_daily_functions_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=run_tdp_daily_functions')) . '">Run TDP Daily Functions</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}
add_filter('plugin_action_links_tdp-control/tdp-control-plugin.php', 'add_run_tdp_daily_functions_button');

function handle_run_tdp_daily_functions()
{
    run_tdp_daily_functions();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_run_tdp_daily_functions', 'handle_run_tdp_daily_functions');
