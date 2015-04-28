<?php
/*
	Plugin Name: Multiple Column Widget
	Plugin URI: http://www.sanusiyaakub.org
	Description: You will be able to put a list of posts in a single row
	Author: Sanusi Yaakub (sanusi87@gmail.com)
	Version: 0.1
	Author URI: http://visitmeifyoulike.blogspot.com/
*/

function mcw_print_script(){
	$js = <<<PHP
	<script type="text/javascript">
	jQuery(function($){
		$('.delete-widget').click(function(){
			var t = $(this),
			id = /[\d]{1,}$/g.exec( t.attr('href') )[0],
			fr = $('#form-response'),
			ans = confirm('Delete this widget?');

			if( ans ){
				fr.empty();
				$.post( t.attr('href'), {id:id}, function(response){
					if( response.code == 1 ){
						fr.append('<div class="updated"><p><i class="typcn typcn-tick"></i> '+response.text+'</p></div>');
						// and delete the row
						t.parents('.mcw-row:eq(0)').remove();
					}else{
						fr.append('<div class="error"><p><i class="typcn typcn-warning-outline"></i> '+response.text+'</p></div>');
					}
				});
			}
			return false;
		});
	});
	</script>
PHP;
	
	echo $js;
}
add_action( 'admin_print_footer_scripts', 'mcw_print_script' );

$widgets = MultiColumnWidgetDb::getWidget();
//var_dump($widgets);
?>

<h2>Widgets</h2>
<div id="form-response"></div>
<?php
if( count( $widgets ) > 0 ){
	foreach ($widgets as $widgetId => $widget) {
?>
<div class="mcw-row">
	<div class="mcw-md-4 mcw-sm-4 mcw-xs-12">
		<div class="mcw-p5"><?php
		echo "<strong>#{$widgetId}</strong> ";
		echo empty( $widget['name'] ) ? '<i>No name</i>' : $widget['name'];
		?></div>
	</div>

	<div class="mcw-md-4 mcw-sm-4 mcw-xs-12">
		<div class="mcw-p5">[multic selected-widget="<?php echo $widgetId; ?>"]</div>
	</div>
	
	<div class="mcw-md-4 mcw-sm-4 mcw-xs-12">
		<div class="mcw-p5">
			<a href="<?php echo admin_url( "edit.php?post_type=".MultiColumnWidget::POST_TYPE."&page=".MULTIC."/admin.php&p=create&id=".$widgetId ); ?>"><i class="typcn typcn-pencil"></i> Update</a>
			<a href="<?php echo admin_url( "edit.php?post_type=".MultiColumnWidget::POST_TYPE."&page=".MULTIC."/admin.php&p=delete&id=".$widgetId ); ?>" class="delete-widget"><i class="typcn typcn-times"></i> Delete</a>
		</div>
	</div>
</div>
<?php } ?>

<div class="mcw-p10">
	<p>Shortcode available attributes:</p>
	<table>
		<tr>
			<td><strong>selectedwidget</strong></td>
			<td>:</td>
			<td>(int) the ID of the widget to be displayed</td>
		</tr>
		<tr>
			<td><strong>showposttitle</strong></td>
			<td>:</td>
			<td>(0|1) whether to show the post title or not, default 1</td>
		</tr>
		<tr>
			<td><strong>usefeaturedimage</strong></td>
			<td>:</td>
			<td>(0|1) whether to show the post featured images or not, default 0</td>
		</tr>
	</table>
</div>

<?php }else{ ?>
<div class="error"><p><i class="typcn typcn-warning-outline"></i> No widget.</p></div>
<?php } ?>