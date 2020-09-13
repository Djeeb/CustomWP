<?php

function mypony_supports (){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    register_nav_menu('header', 'En-tête du menu');
    register_nav_menu('footer', 'Pied de page');
}

function mypony_register_assets (){
    wp_register_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_register_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', ['popper', 'jquery'], false, true);
    wp_register_script('popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', [], false, true);
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://code.jquery.com/jquery-3.5.1.slim.min.js', [], false, true);
    wp_enqueue_style('bootstrap');
    wp_enqueue_script('bootstrap');
}

function mypony_title_separator ($title){
    return ' | ';
}

function mypony_menu_class($classes){
    $classes[] = 'nav-item';
    return $classes;
}

function mypony_menu_link_class($attrs){
    $attrs['class'] = 'nav-link';
    return $attrs;
}

add_action('after_setup_theme', 'mypony_supports');
add_action('wp_enqueue_scripts', 'mypony_register_assets');
add_filter('document_title_separator', 'mypony_title_separator');
add_filter('nav_menu_css_class', 'mypony_menu_class');
add_filter('nav_menu_link_attributes', 'mypony_menu_link_class');