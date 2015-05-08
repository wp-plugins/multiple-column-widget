<?php
/*
this function is used to generate admin content such as menu, notification, ads
*/

class MultiColumnWidgetPage{

	// generate notice
	public static function showNotice( $type, $text ){
		if( $type == 'updated' ){
			$divClass = 'updated';
		}elseif( $type == 'error' ){
			$divClass = 'error';
		}
		
		return "<div class=\"$divClass\"><p>$text</p></div>";
	}

	public static function showMenu( $selected=null ){
		$mainUrl = "edit.php?post_type=".MultiColumnWidget::POST_TYPE."&page=".MULTIC."/admin.php";
		$menuItem = array(
			'dashboard' => array(
				'title' => 'Dashboard',
				'url' => ''
			),
			'create' => array(
				'title' => 'Create Widget',
				'url' => '&p=create'
			),
			'settings' => array(
				'title' => 'Settings',
				'url' => '&p=settings'
			)
		);

		$menuStr = "<div class=\"mcw-text-center\">";
		foreach ($menuItem as $key => $menu){
			$active = "";
			if( !empty( $selected ) ){
				$active = ( $key == $selected ) ? " mcw-admin-menu-active" : "";
			}else{
				if( $key == 'dashboard' ){
					$active = " mcw-admin-menu-active";
				}
			}
			$menuStr .= "<a class=\"mcw-admin-menu{$active}\" href=\"$mainUrl{$menu['url']}\">".$menu['title']."</a>";
		}
		$menuStr .= "<hr /></div>";

		return $menuStr;
	}
}
?>