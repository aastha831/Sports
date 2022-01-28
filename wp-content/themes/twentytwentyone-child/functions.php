<?php
//require get_stylesheet_directory().'/includes/email_template.php';

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
   wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
function aw_custom_meta_boxes( $post_type, $post ) {
    add_meta_box(
        'aw-meta-box',
        __( 'Custom Image' ),
        'render_aw_meta_box',
        array('post', 'page'), //post types here
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'aw_custom_meta_boxes', 10, 2 );
 
function render_aw_meta_box($post) {
    $image = get_post_meta($post->ID, 'aw_custom_image', true);
    ?>
    <table>
        <tr>
            <td><a href="#" class="aw_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></td>
            <td><input type="text" name="aw_custom_image" id="aw_custom_image" value="<?php echo $image; ?>" style="width:500px;" /></td>
        </tr>
    </table>
    <?php
}
function aw_include_script() {
 
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
  
    wp_enqueue_script( 'awscript', get_stylesheet_directory_uri() . '/js/awscript.js', array('jquery'), null, false );
}
add_action( 'admin_enqueue_scripts', 'aw_include_script' );
function aw_save_postdata($post_id)
{
    if (array_key_exists('aw_custom_image', $_POST)) {
        update_post_meta(
            $post_id,
            'aw_custom_image',
            $_POST['aw_custom_image']
        );
    }
}
add_action('save_post', 'aw_save_postdata');
function my_admin_menu () {
   $page_title = 'Theme Settings Page';
   $menu_title = 'Theme Settings';
   $capability = 'edit_posts';
   $menu_slug = 'theme_options_page';
   $function = 'my_theme_settings_page';
   $icon_url = '';
   $position = 50;

   add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}

add_action('admin_menu', 'my_admin_menu');

function my_theme_settings_page(){

/*** New section ***/

?>
   <h1>Theme Settings Page</h1>
   <?php settings_errors(); ?> 

   <form method="post" action="options.php" enctype="multipart/form-data">
       <?php settings_fields("ff_theme_options");?>
       <?php do_settings_sections('theme_options')?>
       <?php submit_button();?>
   </form>

<?php
}

/*** Options fields ***/

add_action('admin_init','ff_custom_setting');
function ff_custom_setting(){
    register_setting('ff_theme_options', 'phone_field');
    register_setting("ff_theme_options", "logo", "handle_logo_upload"); 
    add_settings_section('ff_theme_options','Theme Options', null, 'theme_options');
 
    add_settings_field('logo','Website Logo','logo_display', 'theme_options','ff_theme_options');
}

function ff_theme_options(){
    echo 'Add your theme options';
}

function logo_display()
{
?>
    <input type="file" name="logo" /> 
    <?php echo get_option('logo'); ?>
<?php
}


function handle_logo_upload($option)
{
  if(!empty($_FILES["logo"]["tmp_name"]))
  {
    $urls = wp_handle_upload($_FILES["logo"], array('test_form' => FALSE));
    $temp = $urls["url"];
    return $temp;  
  }
 
  return $option;
}

/*function change_author_permalinks() {
  global $wp_rewrite;
   // Change the value of the author permalink base to whatever you want here
   $wp_rewrite->author_base = 'user';
  $wp_rewrite->flush_rules();
}

add_action('init','change_author_permalinks');
add_filter('query_vars', 'users_query_vars');
function users_query_vars($vars) {
    // add lid to the valid list of variables
    $new_vars = array('user');
    $vars = $new_vars + $vars;
    return $vars;
}
function user_rewrite_rules( $wp_rewrite ) {
  $newrules = array();
  $new_rules['user/(\d*)$'] = 'index.php?author=$matches[1]';
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules','user_rewrite_rules');*/
add_action('init', 'sent_mail',99,1);
function sent_mail($args){
ob_start();
get_template_part('/includes/email_template');
$var = ob_get_contents();
ob_end_clean();
global $wpdb;
$data = $wpdb->get_results("SELECT user_email FROM cususers");
foreach ( $data as $user ) {
   $abc= $user->user_email;
$to = $abc;
$subject = 'The subject';
$body =$var;
$headers = array('Content-Type: text/html; charset=UTF-8');
wp_mail($to, $subject, $body, $headers);
}
}