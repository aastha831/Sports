<?php
//require get_stylesheet_directory().'/includes/email_template.php';

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
function create_posttype() {
register_post_type( 'news',
// CPT Options
array(
  'labels' => array(
   'name' => __( 'news' ),
   'singular_name' => __( 'News' )
  ),
  'public' => true,
  'has_archive' => false,
  'rewrite' => array('slug' => 'news'),
 )
);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );