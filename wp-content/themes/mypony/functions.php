<?php

require_once('walker/CommentWalker.php');
require_once('options/apparence.php');
require_once('options/cron.php');

function mypony_supports (){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('html5');
    register_nav_menu('header', 'En-tête du menu');
    register_nav_menu('footer', 'Pied de page');

    add_image_size('post-thumbnail', 350, 215, true);
}

function mypony_register_assets (){
    wp_register_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_register_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', ['popper', 'jquery'], false, true);
    wp_register_script('popper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', [], false, true);
    if (!is_customize_preview()){
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'https://code.jquery.com/jquery-3.5.1.slim.min.js', [], false, true);
    }
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

function mypony_pagination (){
    $pages = paginate_links(['type' => 'array']);
    if ($pages === null){
        return;
    }
    echo '<nav aria-label="Pagination" class="my-4">';
    echo '<ul class="pagination">';
    foreach($pages as $page){
        $active = strpos($page, 'current') !== false;
        $class = 'page-item';
        if ($active){
            $class .= ' active';
        }
        echo '<li class="' . $class . '">';
        echo str_replace('page-numbers', 'page-link', $page);
        echo '</li>'; 
    }
    echo '</ul>';  
    echo '</nav>';
}

function mypony_init(){
    register_taxonomy('sport', 'post', [
        'labels' => [
            'name'              => 'Sport',
            'singular_name'     => 'Sport',
            'plural_name'       => 'Sports',
            'search_items'      => 'Rechercher des sports',
            'all_items'         => 'Tous les sports',
            'edit_item'         => 'Editer le sport',
            'update_item'       => 'Mettre à jour le sport',
            'add_new_item'      => 'Ajouter un nouveau sport',
            'new_item_name'     => 'Renommer le sport',
            'menu_name'         => 'Sport',
        ],
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'show_admin_column' => true,
    ]);
}

add_action('init', 'mypony_init');
add_action('after_setup_theme', 'mypony_supports');
add_action('wp_enqueue_scripts', 'mypony_register_assets');
add_filter('document_title_separator', 'mypony_title_separator');
add_filter('nav_menu_css_class', 'mypony_menu_class');
add_filter('nav_menu_link_attributes', 'mypony_menu_link_class');

require_once('metaboxes/sponso.php');
require_once('options/agence.php');

SponsoMetaBox::register();
AgenceMenuPage::register();

add_filter('manage_bien_posts_columns', function ($columns){
    return [
        'cb' => $columns['cb'],
        'thumbnail' => 'Miniature',
        'title' => $columns['title'],
        'date' => $columns['date']
    ];
});

add_filter('manage_bien_posts_custom_column', function ($column, $postId){
    if ($column === 'thumbnail'){
        the_post_thumbnail('thumbnail', $postId);
    }
}, 10, 2);

add_action('admin_enqueue_scripts', function (){
    wp_enqueue_style('admin_mypony', get_template_directory_uri() . '/assets/admin.css');
});

add_filter('manage_post_posts_columns', function ($columns){
    $newColumns = [];
    foreach($columns as $k => $v){
        if ($k === 'date'){
            $newColumns['sponso'] = 'Article sponsorisé ?';
        }
        $newColumns[$k] = $v;
    }
    return $newColumns;
});

add_filter('manage_post_posts_custom_column', function ($column, $postId){
    if ($column === 'sponso'){
        if (!empty(get_post_meta($postId, SponsoMetaBox::META_KEY, true))){
            $class = 'yes';
        } else {
            $class = 'no';
        }
        echo '<div class="bullet bullet-' . $class . '"></div>';
    }
}, 10, 2);

function mypony_pre_get_posts (WP_Query $query){
    if (is_admin() || !is_home() || !$query->is_main_query()){
        return;
    }
    if (get_query_var('sponso') === '1'){
        $meta_query = $query->get('meta_query', []);
        $meta_query[] = [
            'key' => SponsoMetaBox::META_KEY,
            'compare' => 'EXISTS',
        ];
        $query->set('meta_query', $meta_query);
    }
}

function mypony_query_vars($params){
    $params[] = 'sponso';
    return $params;
}

add_action('pre_get_posts', 'mypony_pre_get_posts');
add_filter('query_vars', 'mypony_query_vars');

require_once 'widgets/YoutubeWidget.php';

function mypony_register_widget(){
    register_widget(YoutubeWidget::class);
    register_sidebar([
        'id' => 'homepage',
        'name' => __('Sidebar Accueil', 'mypony'),
        'before_widget' => '<div class="p-4 %2$s" id="%1$s">',
        'after-widget' => '</div>',
        'before_title' => '<h4 class="font-italic">',
        'after_title' => '</h4>'
    ]);
}

add_action('widgets_init', 'mypony_register_widget');

add_filter('comment_form_default_fields', function ($fields){
    $fields['email'] = <<<HTML
    <div class="form-group"><label for="email">Email</label><input class="form-control" type="text" name="email" id="email" required></div>
HTML;
    return $fields;
});

add_action('after_switch_theme', 'flush_rewrite_rules');

add_action('switch_theme', 'flush_rewrite_rules');

// https://developer.wordpress.org/apis/handbook/internationalization/
add_action('after_setup_theme', function(){
    load_theme_textdomain('mypony', get_template_directory() . '/languages');
});

/*
/** @var wpdb
global $wpdb;

$tag = "tag1";
$query = $wpdb->prepare("SELECT name FROM {$wpdb->terms} WHERE slug=%s", [$tag]);
$results = $wpdb->get_results($query);
echo '<pre>';
var_dump($results);
echo '</pre>';
die();
*/

// Api : https://developer.wordpress.org/rest-api
add_action('rest_api_init', function(){
    register_rest_route('mypony/v1', '/demo/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request){
            $postID = (int)$request->get_param('id');
            $post = get_post($postID);
            if ($post === null){
                return new WP_Error('rien', 'On a rien à dire', ['status' => 404]);
            }
            return $post->post_title;
        },
        'permission_callback' => function(){
            return current_user_can('edit_posts');
        }
    ]);
});

add_filter( 'rest_authentication_errors', function( $result ) {
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }

    /** @var WP $wp */
    global $wp;
    if (strpos($wp->query_vars['rest_route'], 'mypony/v1') !== false){
        return true;
    }
    return $result;
}, 9);

/*
function myponyReadData(){
    $data = wp_cache_get('data', 'mypony');
    if ($data === false){
        var_dump('je lis');
        $data = file_get_contents(__DIR__ .DIRECTORY_SEPARATOR . 'data');
        wp_cache_set('data', $data, 'mypony', 60);
    }
    return $data;
}

if (isset($_GET['cachetest'])){
    var_dump(myponyReadData());
    var_dump(myponyReadData());
    var_dump(myponyReadData());
    die();
}
*/