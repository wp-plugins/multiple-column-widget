<?php
/*
	Plugin Name: Multiple Column Widget
	Plugin URI: http://www.sanusiyaakub.org
	Description: You will be able to put a list of posts in a single row
	Author: Sanusi Yaakub (sanusi87@gmail.com)
	Version: 1.1.6
	Author URI: http://visitmeifyoulike.blogspot.com/
*/

	require_once( MULTIC_PLUGIN_DIR.'class/MultiColumnWidget.php' );
	require_once( MULTIC_PLUGIN_DIR.'class/MultiColumnWidgetPage.php' );
	require_once( MULTIC_PLUGIN_DIR.'class/MultiColumnWidgetDb.php' );

	$currentPage = $_GET['p'];
?>

<div  class="wrap">
	<?php
	echo MultiColumnWidgetPage::showMenu( $currentPage );

	switch( $currentPage ){
		case 'create':
			require_once( MULTIC_PLUGIN_DIR.'includes/create.php' );
			break;
		case 'dashboard':
		case '':
			require_once( MULTIC_PLUGIN_DIR.'includes/dashboard.php' );
			break;
	};
	?>

</div>