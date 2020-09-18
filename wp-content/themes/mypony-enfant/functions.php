<?php

add_action('wp_enqueue_scripts', function ()
{
    wp_enqueue_style('mypony-child', get_stylesheet_uri());
    wp_deregister_style('bootstrap');
}, 11);

// https://developer.wordpress.org/apis/handbook/internationalization/
add_action('after_setup_theme', function(){
    load_child_theme_textdomain('mypony-enfant', get_stylesheet_directory() . '/languages');
});

add_filter('mypony_search_title', function(){
    return 'Recherche : %s';
});