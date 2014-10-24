<?

class News {

	function show($id) {
		fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $id);
	}
}