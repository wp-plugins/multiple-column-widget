<?php
class MultiColumnWidgetDb{

	public static $allThumbnailSize = array(
		'thumbnail' => 'Thumbnail',
		'medium' => 'Medium',
		'large' => 'Large',
		'full' => 'Full'
	);

	public static function listOfPostTypes(){
		global $wpdb;
		$queryStr = "SELECT DISTINCT $wpdb->posts.post_type FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'";
		$rows = $wpdb->get_results( $wpdb->prepare( $queryStr, null ) );
		return $rows;
	}

	/**
	return a list of posts of mcw_widget type
	*/
	public static function listOfPost( $filterByPostType = false, $filterPostType = null ){
		global $wpdb;

		$queryStr = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish'";

		if( $filterByPostType ){
			$queryStr .= " AND $wpdb->posts.post_type = %s";
			if( empty( $filterPostType ) ){
				$filterPostType = MultiColumnWidget::POST_TYPE;
			}
		}

		$rows = $wpdb->get_results( $wpdb->prepare( $queryStr, $filterPostType ) );

		return $rows;
	}

	/**
	save the widget itself
	@dataArr = array( name => value )
	*/
	public function saveWidget( $dataArr, $id=null ){
		global $wpdb;

		if( empty( $id ) ){
			$wpdb->insert( $wpdb->prefix.'mcw_widget', $dataArr );
		}else{
			$where = array();
			$where['id'] = $id;
			$wpdb->update( $wpdb->prefix.'mcw_widget', $dataArr, $where );
		}
		
		return $wpdb->insert_id;
	}

	/**
	save widget items
	@dataArr  = array( name => value )
	*/
	public function saveWidgetItem( $dataArr ){
		global $wpdb;

		$row = $wpdb->insert( $wpdb->prefix.'mcw_widget_item', $dataArr );
				
		//return $wpdb->insert_id; // cannot be used as this table has no auto-increment column
		return $row;
	}

	/**
	get a list of widgets with widget items also
	$id int
	*/
	public static function getWidget( $id=null ){
		global $wpdb;

		$widgetTable = $wpdb->prefix.'mcw_widget';
		$widgetItemTable = $wpdb->prefix.'mcw_widget_item';

		/*
		$queryStr = "SELECT $widgetTable.*, GROUP_CONCAT($widgetItemTable.post_id) as post_id FROM $widgetTable LEFT JOIN $widgetItemTable ON $widgetItemTable.widget_id = $widgetTable.id";
		if( !empty( $id ) ){
			$queryStr .= " WHERE $widgetTable.id=%d";
		}
		$queryStr .= " GROUP BY $widgetTable.id";
		*/

		$queryStr = "SELECT $widgetTable.*, $widgetItemTable.widget_id, $widgetItemTable.post_id, $wpdb->posts.post_title FROM $widgetTable LEFT JOIN $widgetItemTable ON $widgetItemTable.widget_id = $widgetTable.id LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $widgetItemTable.post_id";
		if( !empty( $id ) ){
			$queryStr .= " WHERE $widgetTable.id=%d";
		}
		$rows = $wpdb->get_results( $wpdb->prepare( $queryStr, $id ) );
		$items = array();

		if( count( $rows ) > 0 ){

			foreach ($rows as $row) {
				if( !array_key_exists($row->widget_id, $items) ){
					$items[$row->widget_id] = array();
					$items[$row->widget_id]['name'] = $row->name;
				}
				$items[$row->widget_id]['posts'][] = array(
					'post_id' => $row->post_id,
					'post_title' => $row->post_title
				);
			}
		}
		return $items;
	}

	/*
	get the post assigned to this widget ID
	*/
	public static function getWidgetPost( $id ){
		global $wpdb;
		$widgetItemTable = $wpdb->prefix.'mcw_widget_item';

		//$queryStr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.ID IN( SELECT post_id FROM $widgetItemTable WHERE widget_id=%d )";

		$queryStr = "SELECT $widgetItemTable.post_id, $wpdb->posts.* FROM $widgetItemTable LEFT JOIN $wpdb->posts ON $wpdb->posts.ID = $widgetItemTable.post_id WHERE $widgetItemTable.widget_id=%d";

		$rows = $wpdb->get_results( $wpdb->prepare( $queryStr, $id ) );
		return $rows;
	}

	// you know what it means
	public static function deleteWidget( $id ){
		global $wpdb;

		$where = array();
		$where['id'] = $id;
		return $wpdb->delete( $wpdb->prefix.'mcw_widget', $where );
	}

	// you know what it means
	public static function deleteWidgetItem( $widgetId ){
		global $wpdb;
		
		$where = array();
		$where['widget_id'] = $widgetId;
		return $wpdb->delete( $wpdb->prefix.'mcw_widget_item', $where );
	}

}
?>