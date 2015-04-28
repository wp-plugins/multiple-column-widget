<?php
/*
	Plugin Name: Multiple Column Widget
	Plugin URI: http://www.sanusiyaakub.org
	Description: You will be able to put a list of posts in a single row
	Author: Sanusi Yaakub (sanusi87@gmail.com)
	Version: 0.1
	Author URI: http://visitmeifyoulike.blogspot.com/
*/

#user scripts and styles
function multiUserCss(){
	wp_enqueue_style( 'multi_user_style', plugins_url( MULTIC.'/style.css' ) );
}
add_action( 'get_header', 'multiUserCss' );

/*
function multiUserJs(){
	wp_enqueue_script( 'multi_user_script', plugins_url( MULTIC.'/js/script.js' , __FILE__ ), 'jquery' );
}
add_action( 'get_footer', 'multiUserJs' );
*/

function multi_column_widget(){
	global $multiCurrentPage;

	# this will add a menu in Settings
	//$multiPage = add_options_page( 'Multiple Column Widget Options', MULTITITLE, 'manage_options', MULTIPAGE, 'multi_column_widget_settings' );

	//add_submenu_page( 'my-top-level-handle', 'Page title', 'Sub-menu title', 'manage_options', 'my-submenu-handle', 'my_magic_function');
	//$multiPage = add_submenu_page( 'edit.php?post_type='.MultiColumnWidget::POST_TYPE, 'Multiple Column Widget Options', 'Create Widget', 'manage_options', MultiColumnWidget::POST_TYPE );

	$multiPage = add_submenu_page( 'edit.php?post_type='.MultiColumnWidget::POST_TYPE, 'Multiple Column Widget Options', 'Create Widget', 'manage_options', MULTIC_PLUGIN_DIR.'admin.php' );
	
	#use this to load CSS file
	//add_action( 'admin_head-' . $multiPage, 'multiAdminCss' );
	wp_enqueue_style( 'multi_user_style', plugins_url( MULTIC.'/style.css' ) );
	wp_enqueue_style( 'multi_admin_style', plugins_url( MULTIC.'/css/admin.style.css' ) );
	wp_enqueue_style( 'multi_admin_typicons', plugins_url( MULTIC.'/css/font/typicons.min.css'  ) );

	#use this to load JS file
	//add_action( 'admin_print_scripts-' . $multiPage, 'multiAdminJs' ); 
	wp_enqueue_script( 'multi_admin_script', plugins_url( MULTIC.'/js/admin.script.js' ), 'jquery' );
}
add_action( 'admin_menu', 'multi_column_widget' );



/* shortcode to be placed on page */
function multiple_column_widget_shortcode( $attrs ){
	
	$shortcodeParam = array();
	$shortcodeParam['selectedwidget'] = 0;
	$shortcodeParam['showposttitle'] = 1;
	$shortcodeParam['usefeaturedimage'] = 0;

	$instance = shortcode_atts( $shortcodeParam, $attrs, 'multic' );

	/******* the map ********/
	ob_start();
	if( !empty( $instance['selectedwidget'] ) ){
		$posts = MultiColumnWidgetDb::getWidgetPost( $instance['selectedwidget'] );
		
		if( !empty( $posts ) ){
			$separator = 12 / (count( $posts ));
	?> <div class="mcw-row"> <?php
			foreach ($posts as $post) {
				//var_dump($post->title);
	?>
		<div class="mcw-md-<?php echo $separator; ?> mcw-sm-<?php echo $separator; ?> mcw-xs-12">
			<div class="mcw-p5">
				<?php if( $instance['showposttitle'] ): ?>
				<h3 class="mcw-text-center"><?php echo $post->post_title; ?></h3>
				<?php endif; ?>

				<?php
				if( $instance['usefeaturedimage'] ){
					echo get_the_post_thumbnail( $post->ID, get_option( 'mcw_thumbnail_size' ) );
				}
				?>

				<div><?php echo $post->post_content; ?></div>
			</div>
		</div>
	<?php
			}
	?> </div> <?php
		}
	}
	$content = ob_get_clean();
	/*********************/
	
	return $content;
}
add_shortcode( 'multic', 'multiple_column_widget_shortcode' );


/****************************media explorer***************/


/****************************media explorer***************/


#process form submit
$currentPage = $_GET['p'];
$result = array();
if( isset( $_POST ) && !empty( $_POST ) ){
	// CREATE PAGE
	if( $currentPage == 'create' ){
		$widgetName = $_POST['widget-name'];
		$columnCount = (int)$_POST['column-count'];
		$selectedPost = $_POST['selected-post'];
		$postID = (int)$_GET['id'];

		$mcwDb = new MultiColumnWidgetDb();
		$dataArr = array();
		$dataArr['name'] = $widgetName;
		$insertID = $mcwDb->saveWidget($dataArr, $postID);
		
		if( !empty( $postID ) ){
			$insertID = $postID;
		}

		if( $columnCount > 0 && $insertID !== false ){
			$success = true;

			if( !empty( $postID ) ){
				MultiColumnWidgetDb::deleteWidgetItem( $postID );
			}

			for( $i=0; $i< $columnCount; $i++ ){
				$dataItemArr = array();
				$dataItemArr['widget_id'] = $insertID;
				$dataItemArr['post_id'] = $selectedPost[$i];
				$nInsertId = $mcwDb->saveWidgetItem( $dataItemArr );
				
				if( $nInsertId ){
					$success = $success && true;
				}else{
					$success = $success && false;
				}
			}

			if( $success ){
				$result['code'] = 1;
				$result['text'] = 'Widget saved!';
			}else{
				$result['code'] = 0;
				$result['text'] = 'Failed to save widget!';
			}
		}else{
			$result['code'] = 0;
			$result['text'] = 'Failed to save widget!';
		}

		if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ){
			header('Content-Type: application/json');
			echo json_encode( $result );
			exit;
		}
	}elseif( $currentPage == 'getpost' ){
		$result = MultiColumnWidgetDb::listOfPost( true, $_POST['type'] );
		
		if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ){
			header('Content-Type: application/json');
			echo json_encode( $result );
			exit;
		}
	}elseif( $currentPage == 'delete' ){
		// DELETE PAGE
		$widgetId = $_POST['id'];
		$affectedRows = MultiColumnWidgetDb::deleteWidget( $widgetId );
		if( $affectedRows ){
			$affectedRows2 = MultiColumnWidgetDb::deleteWidgetItem( $widgetId );
			if( $affectedRows2 ){
				$result['code'] = 1;
				$result['text'] = 'Widget deleted!';
			}else{
				$result['code'] = 0;
				$result['text'] = 'Failed to delete widget item!';
			}
		}else{
			$result['code'] = 0;
			$result['text'] = 'Failed to delete widget!';
		}

		if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ){
			header('Content-Type: application/json');
			echo json_encode( $result );
			exit;
		}
	}elseif( $currentPage == 'settings' ){
		$thumbnailSize = $_POST['thumbnail-size'];

		if( !array_key_exists( $thumbnailSize, MultiColumnWidgetDb::$allThumbnailSize ) ){
			$thumbnailSize = 'medium';
		}
		
		$saved = update_option( 'mcw_thumbnail_size', $thumbnailSize );

		if( $saved ){
			$result['code'] = 1;
			$result['text'] = 'Settings has been updated!';
		}else{
			$result['code'] = 0;
			$result['text'] = 'Failed to update settings!';
		}

		if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ){
			header('Content-Type: application/json');
			echo json_encode( $result );
			exit;
		}
	}
}
?>
