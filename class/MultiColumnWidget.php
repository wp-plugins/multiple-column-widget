<?php
class MultiColumnWidget extends WP_Widget {

	const POST_TYPE = 'mcw_widget';
	const POST_NAME = 'Multiple Column Widget Post';

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'mtc_widget', 

			// Widget name will appear in UI
			__('Multiple Column Widget', 'mtc_widget_domain'), 

			// Widget description
			array( 'description' => __( 'A widget that can be used to put multiple items in a row evenly', 'mtc_widget_domain' ) ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		
		if( $instance['use-post-title'] ){
			$title = null;
		}

		if ( !empty( $title ) ){
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// This is where you run the code and display the output


		ob_start();

		if( !empty( $instance['selected-widget'] ) ){
			$posts = MultiColumnWidgetDb::getWidgetPost( $instance['selected-widget'] );
			
			if( !empty( $posts ) ){
				$separator = 12 / (count( $posts ));
		?> <div class="mcw-row"> <?php
				foreach ($posts as $post) {
					//var_dump($post->title);
		?>
			<div class="mcw-md-<?php echo $separator; ?> mcw-sm-<?php echo $separator; ?> mcw-xs-12">
				<div class="mcw-p5">

					<?php if( $instance['show-post-title'] ){ ?>
					<h3 class="widget-title mcw-text-center"><?php echo $post->post_title; ?></h3>
					<?php
					}

					if( $instance['show-featured-image'] ){
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

			//var_dump($posts);
		}

		$content = ob_get_clean();
		echo __( $content, 'mtc_widget_domain' );
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'New title', 'mtc_widget_domain' );
		}

		$widgets = MultiColumnWidgetDb::getWidget();

		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('selected-widget'); ?>">Select Widget</label>
			<select id="<?php echo $this->get_field_id('selected-widget'); ?>" name="<?php echo $this->get_field_name('selected-widget'); ?>">
			<?php
			if( count( $widgets ) > 0 ){
				foreach ($widgets as $widgetId => $widget) {
			?> <option value="<?php echo $widgetId; ?>"<?php echo ( $instance['selected-widget'] == $widgetId ) ? ' selected="selected"' : ''; ?>><?php echo $widget['name']; ?></option> <?php
				}
			}
			?>	
			</select>
		</p>

		<?php /* ?>
		<p>
			<label for="<?php echo $this->get_field_id('use-post-title'); ?>">Use Post Title</label>
			<input type="checkbox" id="<?php echo $this->get_field_id('use-post-title'); ?>" name="<?php echo $this->get_field_name('use-post-title'); ?>" value="1"<?php echo $instance['use-post-title'] ? ' checked="checked"' : ''; ?> />
			<br />
			<small>Use post title to replace default widget title.</small>
		</p><?php */ ?>

		<p>
			<label for="<?php echo $this->get_field_id('show-post-title'); ?>">Show Post Title</label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show-post-title'); ?>" name="<?php echo $this->get_field_name('show-post-title'); ?>" value="1"<?php echo $instance['show-post-title'] ? ' checked="checked"' : ''; ?> />
			<br />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('show-featured-image'); ?>">Show Featured Image</label>
			<input type="checkbox" id="<?php echo $this->get_field_id('show-featured-image'); ?>" name="<?php echo $this->get_field_name('show-featured-image'); ?>" value="1"<?php echo $instance['show-featured-image'] ? ' checked="checked"' : ''; ?> />
			<br />
		</p>

		<?php
	}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['selected-widget'] = $new_instance['selected-widget'];
		//$instance['use-post-title'] = $new_instance['use-post-title'];
		$instance['show-post-title'] = $new_instance['show-post-title'];
		$instance['show-featured-image'] = $new_instance['show-featured-image'];

		return $instance;
	}
} // Class multi_column_widget ends here


// Register and load the widget
function multi_column_widget_widget() {
	register_widget( 'MultiColumnWidget' );
}
add_action( 'widgets_init', 'multi_column_widget_widget' );
?>