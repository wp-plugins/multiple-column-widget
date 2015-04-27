<?php
/*
	Plugin Name: Multiple Column Widget
	Plugin URI: http://www.sanusiyaakub.org
	Description: You will be able to put a list of posts in a single row
	Author: Sanusi Yaakub (sanusi87@gmail.com)
	Version: 0.1
	Author URI: http://visitmeifyoulike.blogspot.com/
*/

define( 'MULTIC', 'multi_column_widget' );
define( 'MULTIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MULTIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( MULTIC_PLUGIN_DIR.'install.php' );
require_once( MULTIC_PLUGIN_DIR.'class/MultiColumnWidgetDb.php' );
require_once( MULTIC_PLUGIN_DIR.'class/MultiColumnWidget.php' );

#plugin installation
function multiActivate(){ }
register_activation_hook( __FILE__, 'multiActivate' );

#register custom post type
add_action( 'init', 'create_post_type' );
function create_post_type(){
	register_post_type( MultiColumnWidget::POST_TYPE, array(
		'labels' => array(
			'name' => __( MultiColumnWidget::POST_NAME ),
			'singular_name' => __( MultiColumnWidget::POST_NAME )
		),
		'public' => true,
		'has_archive' => true,
	));
}
/*
// reading custom post type
$args = array( 'post_type' => 'product', 'posts_per_page' => 10 );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post();
	the_title();
	echo '<div class="entry-content">';
	the_content();
	echo '</div>';
endwhile;
*/

require_once( MULTIC_PLUGIN_DIR.'includes/multi_column_admin.php' );
?>