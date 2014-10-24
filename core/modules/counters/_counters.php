<?

class _Counters extends Module {

	function __construct() {
		$field_verify = '{ "empty" : { "title" : "Название",  "text" : "Код счетчика"}}';
		parent::__construct('counters', null, $field_verify);
	}

	function add($id = false, $fields = "*") {
		Users::isLogin();
		$tagname=($id)?'edit':'add';
		if (!empty($_POST))
			XML::from_array('/', $_POST, $tagname);
			elseif ($id && empty($_POST)) 
				XML::from_array('/', $this->db->getRow("SELECT * FROM {$this->table} WHERE id=?i", $_GET['EDIT']), $tagname);
		elseif (!Message::errorState() && !$id)
			XML::add_node('/', $tagname);
	}

	function getList($query = '', $param = null, $item_on_page = false, $visible_pages = VISIBLE_PAGES) {
		if (isset($_GET['ADMIN'])) {
			Users::isLogin();
			$ars = parent::getList("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} ORDER BY sort");
		}
	}

	function cmdShow() {
		$this->brief();
	}

	function brief() {
		XML::from_db('/', "SELECT id, text FROM {$this->table} WHERE active=1 ORDER BY sort", null, 'show', null, 'id');
	}

	function item($id) {}
}