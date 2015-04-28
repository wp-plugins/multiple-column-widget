<?php
function mcw_print_script(){
	$js = <<<PHP
	<script type="text/javascript">
	jQuery(function($){
		$('#mcw-settings-form').submit(function(){
			var t = $(this),
			fr = $('#form-response');

			fr.empty();
			t.find('button[type=submit]').attr('disabled','disabled').children('i').removeClass('typcn-input-checked').addClass('typcn-arrow-sync');
			$.post( t.attr('action'), t.serialize(), function(response){
				t.find('button[type=submit]').removeAttr('disabled').children('i').removeClass('typcn-arrow-sync').addClass('typcn-input-checked');
				if( response.code == 1 ){
					fr.append('<div class="updated"><p><i class="typcn typcn-tick"></i> '+response.text+'</p></div>');
				}else{
					fr.append('<div class="error"><p><i class="typcn typcn-warning-outline"></i> '+response.text+'</p></div>');
				}
			});
			return false;
		});
	});
	</script>
PHP;
	echo $js;
}
add_action( 'admin_print_footer_scripts', 'mcw_print_script' );

$selectedThumbnailSize = 'medium';
$option = get_option( 'mcw_thumbnail_size' );
if ( $option !== false ) {
	$selectedThumbnailSize = $option;
}else{
	$deprecated = null;
	$autoload = 'no';
	add_option( 'mcw_thumbnail_size', $selectedThumbnailSize, $deprecated, $autoload );
}
?>

<div id="form-response"></div>
<form method="post" action="" id="mcw-settings-form">
	<h2>Settings</h2>
	<div class="mcw-row">
		<div class="mcw-md-3 mcw-sm-3 mcw-xs-12">
			<div class="mcw-p5 mcw-text-right">
				<label for="column-count">Thumbnail size</label>
			</div>
		</div>

		<div class="mcw-md-9 mcw-sm-9 mcw-xs-12">
			<div class="mcw-p5">
				<select name="thumbnail-size" id="mcw-thumbnail-size" class="mcw-control">
				<?php foreach ( MultiColumnWidgetDb::$allThumbnailSize as $name => $desc ){ ?>
					<option value="<?php echo $name; ?>"<?php echo ( $selectedThumbnailSize == $name ) ? ' selected="selected"' : ''; ?>><?php echo $desc; ?></option>
				<?php } ?>
				</select>
			</div>
		</div>
	</div>

	<div class="mcw-row">
		<div class="mcw-md-6 mcw-sm-6 mcw-xs-12">
			<div class="mcw-p5">
				<button type="submit">
					<i class="typcn typcn-input-checked"></i> Save
				</button>
			</div>
		</div>
	</div>
</form>