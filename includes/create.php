<?php
$id = $_GET['id'];
$column = array();

if( !empty( $id ) ){
	$theWidget = MultiColumnWidgetDb::getWidget( $id );
	//var_dump($theWidget);
	foreach ($theWidget as $id => $widget) {
		$widgetId = $id;
		$widgetName = $widget['name'];
		$column = $widget['posts'];
	}
}

// of this widget post type only
$listOfPosts = MultiColumnWidgetDb::listOfPost( true, MultiColumnWidget::POST_TYPE );
$listOfPostTypes = MultiColumnWidgetDb::listOfPostTypes();

/////////////////////
function mcw_print_script(){
	$loadPostUrl = admin_url( "edit.php?post_type=".MultiColumnWidget::POST_TYPE."&page=".MULTIC."/admin.php&p=getpost" );

	$js = <<<PHP
	<script type="text/javascript">
	jQuery(function($){
		$('#column-count').change(function(){
			var tv = $(this).val()
			$('#sampel-output').empty();

			var separator = 12/tv;
			for( var i=0;i<tv;i++ ){
				var x = '<p><a href="#" class="set-post">Set Post</a><br />Post: <span class="setted-post">None</span></p>',
				classString = 'mcw-lg-'+separator+' mcw-md-'+separator+' mcw-sm-'+separator+' mcw-xs-12';
				$('#sampel-output').append( x ).children(':last').wrap('<div class="'+classString+'" />').wrap('<div class="mcw-p5" />');
			}
		});

		/*
		$('#column-count').change(function(){
			var tv = $(this).val(),
			mpost = $('#main-post-list').clone().show().removeAttr('id');

			$('#sampel-output').empty();

			var separator = 12/tv;
			for( var i=0;i<tv;i++ ){
				var x = mpost.clone(),
				classString = 'mcw-lg-'+separator+' mcw-md-'+separator+' mcw-sm-'+separator+' mcw-xs-12';
				$('#sampel-output').append( x ).children(':last').wrap('<div class="'+classString+'" />').wrap('<div class="mcw-p5" />');
			}
		});
		*/

		$('#widget-create-form').submit(function(){
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
		
		$('#filter-post-by-type').change(function(){
			var t = $(this);
			$('#main-post-list').empty();
			
			$.post( '$loadPostUrl',{type:t.val()}, function(response){
				$.each(response, function(i,e){
					$('#main-post-list').append('<option value="'+e.ID+'">'+e.post_title+'</option>');
				});
			});
		});
		

		$('#sampel-output').find('.mcw-p5').each(function(i,e){
			$(e).bindSelectPost();
		});

		$('#main-post-list').bindSelectedPost();
	});
	
	(function($){
		$.fn.bindSelectPost = function(){
			var t = $(this);

			t.click(function(){
				t.hide();
				t.next().show().append( $('#main-post-list').show() );
				return false;
			});

			/*
			t.hover(function(){
				t.hide();
				t.next().show().append( $('#main-post-list').show() );
			}, function(){
				t.show();
				$('#widget-create-form').after( $('#main-post-list').hide() );
			});
			*/
			return t;
		}

		$.fn.bindSelectedPost = function(){
			var t = $(this);
			t.change(function(){
				var text = t.find('option[value='+t.val()+']').text();
				if( t.val() == '' ){
					text = '';
				}
				t.prev().val( t.val() );
				t.parent().hide().prev().show().find('.setted-post').text( text );
				$('#widget-create-form').after( t.hide() );
			});
		}
	})(jQuery)
	</script>
PHP;
	
	echo $js;
}
add_action( 'admin_print_footer_scripts', 'mcw_print_script' );
/////////////////////

?>
<div id="form-response"></div>
<form method="post" action="" id="widget-create-form">
	<h2>Create/Update widget</h2>
	<div class="mcw-row">
		<!-- set widget name -->
		<div class="mcw-md-6 mcw-sm-6 mcw-xs-12">
			<div class="mcw-p5">
				<label for="column-count">Widget name</label>
				<div class="mcw-row">
					<div class="mcw-md-12 mcw-sm-12 mcw-xs-12">
						<input type="text" name="widget-name" value="<?php echo $widgetName; ?>" class="mcw-control" placeholder="Widget name..." />
					</div>
				</div>
			</div>
		</div>

		<!-- select number of columns -->
		<div class="mcw-md-6 mcw-sm-6 mcw-xs-12">
			<div class="mcw-p5">
				<label for="column-count">Numer of column</label>
				<div class="mcw-row">
					<div class="mcw-md-12 mcw-sm-12 mcw-xs-12">
						<select id="column-count" name="column-count" class="mcw-control">
							<?php
							$item = array(1,2,3,4,6);
							foreach( $item as $i ){ ?>
							<option value="<?php echo $i; ?>"<?php echo count( $column ) == $i ? ' selected="selected"' : ''; ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- sampel output -->
	<br />
	<h2>Select post to be assigned to each section</h2>
	<div>
		<label for="filter-post-by-type">Filter post by type:</label>
		<select id="filter-post-by-type" name="filter-post-by-type">
			<option value="">Select...</option>
			<?php foreach( $listOfPostTypes as $postType ): ?>
			<option value="<?php echo $postType->post_type; ?>"><?php echo $postType->post_type; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	
	<!-- default 1 column -->
	<?php if( count( $column ) == 0 ){ ?>
	<div class="mcw-row" id="sampel-output">
		<div class="mcw-md-12 mcw-sm-12 mcw-xs-12">
			<div class="mcw-p5">
				<select name="selected-post[]" class="select-post mcw-control">
					<option value="">Select post</option>
					<?php foreach( $listOfPosts as $i => $post ){ ?>
					<option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="mcw-p10" style="display:none;"></div>
		</div>
	</div>
	<?php }else{ ?>
	<div class="mcw-row" id="sampel-output">
	<?php
		$separator = 12/(count( $column ));
		foreach ($column as $post) {
	?>
		<div class="mcw-md-<?php echo $separator; ?> mcw-sm-<?php echo $separator; ?> mcw-xs-12">
			<div class="mcw-p5">
				<p>
					<a href="#" class="set-post">Set Post</a>
					<br />Post: <strong class="setted-post"><?php echo $post['post_title']; ?></strong>
				</p>
				<?php /* ?>
				<select name="selected-post[]" class="select-post mcw-control">
					<option value="">Select post</option>
					<?php foreach( $listOfPosts as $i => $post ){ ?>
					<option value="<?php echo $post->ID; ?>"<?php echo $post->ID == $postId ? ' selected="selected"' : ''; ?>><?php echo $post->post_title; ?></option>
					<?php } ?>
				</select>
				<?php */ ?>
			</div>
			<div class="mcw-p10" style="display:none;">
				<input type="hidden" name="selected-post[]" class="select-post" value="<?php echo $post['post_id']; ?>" />
			</div>
		</div>
	<?php } ?>
	</div>
	<?php } ?>
	
	<br />
	<!-- sampel output -->

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

<select name="selected-post[]" class="select-post mcw-control" id="main-post-list" style="display:none;">
	<option value="">Select post</option>
	<?php foreach( $listOfPosts as $i => $post ){ ?>
	<option value="<?php echo $post->ID; ?>"><?php echo $post->post_title; ?></option>
	<?php } ?>
</select>