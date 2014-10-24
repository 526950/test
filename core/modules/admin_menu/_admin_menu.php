<?
class _admin_menu {
	public $admin_brief=true;
	function show() {
	$this->brief();
	}
	function brief() {
		global $admin_menu_array;
		if (Users::isSuper()) {
			XML::add_node ( '/', 'admin_menu' );
			//XML::from_db('/', "SELECT name, path FROM _section WHERE module='article' ORDER BY sort", null, 'article_section');
			//XML::from_db('/', "SELECT name, path FROM _section WHERE module='gallery' ORDER BY sort", null, 'gallery_section');
			//XML::from_array('/', $admin_menu_array, 'menu_item');
		}
	}
}