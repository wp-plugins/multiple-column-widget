<?php
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
		case 'settings':
			require_once( MULTIC_PLUGIN_DIR.'includes/settings.php' );
			break;
		case 'dashboard':
		case '':
			require_once( MULTIC_PLUGIN_DIR.'includes/dashboard.php' );
			break;
	};
	?>

</div>
