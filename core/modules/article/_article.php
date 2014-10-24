<?

class _Article extends Module {

	function __construct() {
		$field_verify = '{ "empty" : { "title" : "Название",  "text" : "Полный текст"}}';
		$field_rules = '{"photo_one" : "photo_anons",	"photo_multi" : "photo"}';
		parent::__construct('article', $field_rules, $field_verify);
	}

	function ajaxShow() {
		if (isset($_GET['ITEM'])) {
			$this->item(intval($_GET['ITEM']));
		} else
			parent::ajaxShow();
	}

	function add($id = false, $fields = "*") {
		Users::isLogin();
		if (!empty($_POST))
			XML::from_array('/', array ($_POST), 'edit');
		if ($id) {
			if (empty($_POST)) {
				$ar = XML::from_db('/', "SELECT * FROM {$this->table} WHERE id=?i AND (id_user=?i OR ?i&1=1)", array ($_GET['EDIT'],$_SESSION['user']['id'],$_SESSION['user']['role']), 'edit');
				if (isset($ar[0]))
					XML::from_db('//edit/item', "SELECT name, note FROM _file WHERE id_parent=?i AND field=?s AND table_name=?s AND id_section=?i", array ($id,'photo', $this->table, $this->section), 'photo');
				else
					Message::error('Доступ запрещен или не существует такой записи!');
			}
		} else {
			if (!Message::errorState())
				XML::add_node('/', 'add');
		}
	}

	function getList($query = '', $param = null, $item_on_page = false, $visible_pages = VISIBLE_PAGES) {
		if (isset($_GET['ADMIN'])) {
			Users::isLogin();
			$ars = parent::getList("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} WHERE (id_user={$_SESSION['user']['id']} OR {$_SESSION['user']['role']}&1=1) AND id_section={$this->section} ORDER BY sort", null, 50);
			//XML::debug();
			fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $ars); 
		} elseif (isset($_GET['all'])) {
			$list = $this->db->get_all("SELECT * FROM {$this->table} WHERE active=1 AND id_section=?i  ORDER BY sort", $this->section);
			if ($list)
				foreach ( $list as &$row ) {
					$row['photo'] = $this->db->getAll("SELECT name, note FROM _file WHERE id_parent=?i AND field='photo' AND id_section=?i AND table_name=?s",$row['id'], $this->section, $this->table);
				}
			Xml::from_array('/', $list);
		} else {
			$ars = $this->db->getAll("SELECT * FROM `{$this->table}` WHERE `active`=1 AND `id_section`=?i", $this->section);
			if (count($ars) > 1) {
				$ars = parent::getList("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->table} WHERE active=1 AND id_section=?i ORDER BY sort", $this->section);
				//Utils::setMeta( '', 'description' );
			} else
				$this->item($ars[0]['id']);
		}
		if (isset($ars))
			foreach ( $ars as $ar )
				$title[] = $ar['title'];
		//if ( isset( $title ) ) Utils::setMeta( implode( ', ', $title ) );
	}

	function item($id) {
		$ar = XML::from_db('/', "SELECT * FROM {$this->table} WHERE id=?i AND id_section=?i", array ($id,$this->section), null);
		if (is_null($ar))
			error::status(404);
		if ($ar) {
			//if (isset($ar[0]['title'])) Utils::setMeta( $ar[0]['title'] );
			//if (isset($ar[0]['anons'])) Utils::setMeta( $ar[0]['anons'], 'description' );
			XML::from_db('//item', "SELECT `name`, `note` FROM _file WHERE id_parent={$id} AND field='photo' AND table_name='{$this->table}' AND id_section={$this->section}", null, 'photo');
		}
	}

	function save($id = null, $message = false) {
		if (is_null($id)) {
			$_POST['date'] = date("Y-m-d H:i:s");
			$_POST['id_user'] = $_SESSION['user']['id'];
			$_POST['id_section'] = $this->section;
		}
		$_POST['title_show'] = (isset($_POST['title_show'])) ? $_POST['title_show'] : 0;
		$_POST['date_edit'] = date("Y-m-d H:i:s");
		parent::save($id);
	}
}