<?php
/*
Plugin Name: Cleaner Wordpress Editor
Plugin URI: http://www.humbug.in/2011/wordpress-plugin-cleaner-wordpress-editor-trying-to-make-wordpress-editing-a-pleasure/
Description: Cleans up the Post Edit Window so that you can concentrate on writing
Version: 0.4
Author: Pratik Sinha
Author URI: http://www.humbug.in/
License: GPL2
*/


add_action( 'admin_print_styles-post.php', 'clean_post_admin_css' );
add_action( 'admin_print_styles-post-new.php', 'clean_post_admin_css' );

function clean_post_admin_css() {
  ?>
  <style type="text/css">
  
  #wp-content-editor-container textarea#content, #editorcontainer textarea#content, body#tinymce.mceContentBody { font-size:140%!important; font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif; color: #333; background: #fcfcfc; }
  .ed_button { font-size:120%!important; font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif; }
  .post-php h2, .post-new-php h2 { display: none; }
  .post-php div#wphead, .post-new-php div#wphead { display: none; }
  .post-php div#icon-edit, .post-new-php div#icon-edit { display:none; }
  .post-php div#post-body-content, .post-new-php div#post-body-content { margin-right: 0; }
  .post-php div.columns-prefs, .post-new-php div.column-prefs { display: none; }
  .post-php div#post-body, .post-new-php div#post-body {margin-top: 30px;}
	.post-php #title, .post-new-php #title { background: #fcfcfc !important; }
  </style>
  <?php
}

function enforce_single_column($cols, $id, $scr) {
  $desired_screen = convert_to_screen('post.php');
  if($id == $desired_screen)
		$col['post'] = 1;
		$col['page'] = 1;
	 return $cols;
}

add_filter('screen_layout_columns', 'enforce_single_column', 10, 3);

function set_user_option_screen_layout() {
	global $current_user;
	get_currentuserinfo();
	$userid =  $current_user->ID;
	update_user_option( $userid,"screen_layout_post",'1');
	update_user_option( $userid,"screen_layout_page",'1');
	$meta_key['order'] = 'meta-box-order_post';
	$meta_value = array(
		'side' => '',
		'normal' => 'submitdiv,categorydiv,tagsdiv-post_tag,postimagediv,formatdiv,pageparentdiv,post-stylesheets,postexcerpt,postcustom,commentstatusdiv,commentsdiv,trackbacksdiv,slugdiv,authordiv,revisionsdiv',
		'advanced' => '',
	);
	update_user_meta( $userid, $meta_key['order'], $meta_value );
	$meta_key['order'] = 'meta-box-order_page';
	update_user_meta( $userid, $meta_key['order'], $meta_value );
}

add_action('admin_init', 'set_user_option_screen_layout');


function set_size() {
	echo "
	<script type='text/javascript'>
		if ( jQuery('body').hasClass('post-php') || jQuery('body').hasClass('post-new-php') )
			adminMenu.fold();
		function visual_editor_font_size_tinymce_setup(ed) {
			ed.onPostRender.add(function(ed, cm) {
					jQuery('#content_ifr').contents().find('#tinymce').css('font-size', '110%');
					jQuery('#content_ifr').contents().find('#tinymce').css('line-height', '120%');
					jQuery('#content_ifr').contents().find('#tinymce').css('color', '#333');
					jQuery('#content_ifr').contents().find('#tinymce').css('background', '#fcfcfc');
			});
			return true;
		}
	</script>
	";
}

add_action( 'admin_print_footer_scripts', 'set_size', 25 + 10 );
add_filter( 'tiny_mce_before_init', create_function('$a', '$a["setup"] = "visual_editor_font_size_tinymce_setup"; return $a;'));

register_deactivation_hook(__FILE__, 'plugin_deinstall');

/**
* Delete options in database
*/
function plugin_deinstall() {
	global $current_user;
	get_currentuserinfo();
	$userid =  $current_user->ID;
	update_user_option( $userid,"screen_layout_post",'2');
	update_user_option( $userid,"screen_layout_page",'2');
	$meta_key['order'] = 'meta-box-order_post';
	$meta_value = array(
		'side' => 'submitdiv,formatdiv,categorydiv,tagsdiv-post_tag',
		'normal' => 'postimagediv,postexcerpt,postcustom,commentstatusdiv,commentsdiv,trackbacksdiv,slugdiv,authordiv,revisionsdiv',
		'advanced' => '',
	);
	update_user_meta( $userid, $meta_key['order'], $meta_value );
	$meta_value = array(
		'side' => 'submitdiv,formatdiv,categorydiv,tagsdiv-post_tag,pageparentdiv,post-stylesheets',
		'normal' => 'postimagediv,postexcerpt,postcustom,commentstatusdiv,commentsdiv,trackbacksdiv,slugdiv,authordiv,revisionsdiv',
		'advanced' => '',
	);

	$meta_key['order'] = 'meta-box-order_page';
	update_user_meta( $userid, $meta_key['order'], $meta_value );
}

?>
