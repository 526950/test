<?

/**
 * 1.
 * вызов в в конструкторе класса наследника parent::__construct ();
 *
 * $field_rules json строка :
 * - city : имеет смысл только параметр false, по дефолту true
 * - photo_one : одно или несколько полей для загрузки по одному фото
 * - photo_multi : одно или несколько полей для загрузки неограниченного числа фото
 * - bridge : необходимо передать имя поля в мосте касающегося второй таблицы
 * - checkbox : имя поля при отсутсвии которого в POST в БД будет обнуляться значение
 *
 * $field_verify json строка :
 * - empty : спсиок полей которые проверяются на заполнение
 */
class _Module {
	use Base, Page, Ajax;
	public $section = false, $verify = true, $where = '', $order = 't.name', $admin_brief = false, $islogin = true, $message = false, $get_list_mode = 'xml', $item_on_page_client = ITEM_ON_PAGE, $item_on_page_admin = ITEM_ON_PAGE;
	private $rules, $field_verify;

	function __construct($table = false, $field_rules = null, $field_verify = null) {
		$this->db = new Db();
		$this->rules = json_decode($field_rules);
		$this->field_verify = json_decode(mb_convert_encoding($field_verify, 'UTF-8'));
		$this->table = (!$table) ? strtolower(get_class($this)) : $table;
		if (isset($_GET['ACTIVE']))
			$this->active(intval($_GET['ACTIVE']));
	}	

	function isOwner($id) {
		if ($this->db->getOne("SHOW FIELDS FROM {$this->table} WHERE Field='id_user'")) {
			$id_user = $this->db->getOne("SELECT id_user FROM ?n WHERE id=?i", $this->table, $id);
			return ($id_user != $_SESSION['user']['id'] && !Users::isSuper()) ? false : true;
		} else
			return true;
	}

	function brief() {
		XML::from_db('/', "SELECT * FROM {$this->table} WHERE active=1", null, 'brief');
	}

	function show() {
		if (isset($_GET['DEL']))
			$this->delete($_GET['DEL']);
		
		$is_save = false;
		if (isset($_POST)) {
			foreach ( $_POST as $k => $v ) {
				if ($k == 'unsavePublic' || preg_match("'^save[a-z_]*'i", $k)) {
					$is_save = true;
					break;
				}
			}
		}
		if ($is_save && $this->verify()) {
			if (isset($_POST['savePublic']) || isset($_GET['ajax']))
				$_POST['active'] = 1;
			if (isset($_POST['unsavePublic']))
				$_POST['active'] = 0;
			$id = (isset($_REQUEST['EDIT'])) ? intval($_REQUEST['EDIT']) : null;
			$this->save($id);
			if (!isset($_GET['ajax'])) {
				$location = 'Location: ' . rtrim(DOMAIN, '/');
				$location .= rtrim((isset($_GET['ADD'])) ? Utils::urlRemove('ADD') : Utils::urlRemove('EDIT'), '?');
				if (isset($_SESSION['REDIRECT_URL']) && $_SESSION['REDIRECT_URL'] != '') {
					$location = 'Location: ' . $_SESSION['REDIRECT_URL'];
					unset($_SESSION['REDIRECT_URL']);
				}
				header($location);
				die();
			}
		}
		
		if (isset($_GET['ITEM'])) {
			$this->item($_GET['ITEM']);
		} elseif (isset($_GET['ADD'])) {
			unset($_SESSION['edit_owner']);
			$this->add();
		} elseif (isset($_GET['EDIT'])) {
			$this->add($_GET['EDIT']);
		} else {
			$this->getList();
		}
	}

	function add($id = false, $fields = "*") {
		if ($this->islogin)
			Users::isLogin($this->message);
		XML::add_node('/', 'edit');
		if (!empty($_POST)) {
			XML::from_array('//edit', array ($_POST));
		} elseif ($id) {
			if ($this->islogin) {
				return XML::from_db('//edit', "SELECT {$fields} FROM {$this->table} WHERE id=?i AND (id_user=?i OR ?i&1=1)", array ($id,$_SESSION['user']['id'],$_SESSION['user']['role']));
			} else {
				return XML::from_db('//edit', "SELECT {$fields} FROM {$this->table} WHERE id=?i", $id);
			}
		}
		return null;
	}

	function verify() {
		if (isset($this->field_verify->empty)) {
			foreach ( (array) $this->field_verify->empty as $field => $value ) {
				if (!isset($_POST[$field]) || @$_POST[$field] == '' || (is_string($_POST[$field]) && strtoupper(@$_POST[$field]) == 'NULL')) {
					$this->verify = false;
					Message::error('Не заполнено поле "' . $value . '"');
				}
			}
		}
		return $this->verify;
	}

	function delete($id) {
		if (!$this->isOwner($id))
			return false;
		
		if (isset($this->rules->photo_one)) {
			$row = $this->db->getRow("SELECT * FROM {$this->table} WHERE id=?i", $id);
			foreach ( (array) $this->rules->photo_one as $field ) {
				if (isset($row[$field]) && trim($row[$field]) != '')
					@unlink(ROOT . 'uploads/' . $this->table . '/' . $row[$field]);
			}
		}
		if (isset($this->rules->photo_multi)) {
			foreach ( (array) $this->rules->photo_multi as $field ) {
				$res = $this->db->query("SELECT name FROM _file WHERE id_parent=?i AND id_section=?i AND table_name=?s AND field=?s", $id, $this->section, $this->table, $field);
				while ( $row = $this->fetch($res) ) {
					@unlink(ROOT . "uploads/{$this->table}/{$row['name']}");
				}
			}
		}
		$this->db->query("DELETE FROM {$this->table} WHERE id=?i", $id);
		
		$location = 'Location: ' . rtrim(DOMAIN, '/');
		$location .= rtrim(Utils::urlRemove('DEL'), '?');
		header($location);
		die();
	}

	function save($id = null, $message = false) {
		if (@$_SESSION['user']['role'] != 1 && isset($this->field_verify->none_save)) {
			$mode = (is_null($id)) ? 1 : 2;
			foreach ( (array) $this->field_verify->none_save as $field => $value )
				if (isset($_POST[$field]) && ($value == $mode || $value == 3))
					unset($_POST[$field]);
		}
		
		if (!is_null($id) && !$this->isOwner($id)) {
			ERROR::status(403);
			return false;
		}
		
		if (!isset($_POST['alias'])) {
			if (isset($_POST['title']))
				$_POST['alias'] = (defined('URL_TRANSLIT') && URL_TRANSLIT == 'RU') ? Utils::translitUrlRu($_POST['title']) : Utils::translitUrl($_POST['title']);
			if (isset($_POST['name']))
				$_POST['alias'] = (defined('URL_TRANSLIT') && URL_TRANSLIT == 'RU') ? Utils::translitUrlRu($_POST['name']) : Utils::translitUrl($_POST['name']);
		}
		$fields = $params = array ();
		$field_active = false;
		$res = $this->db->query("SHOW COLUMNS FROM ?n", $this->table);
		while ( $row = $this->db->fetch($res) ) {
			if ($row['Field'] == 'active')
				$field_active = true;
			if (isset($_POST[$row['Field']])) {
				$fields[$row['Field']] = '`' . $row['Field'] . '`';
				$fields[$row['Field']] .= (is_null($_POST[$row['Field']]) || strtoupper($_POST[$row['Field']]) == 'NULL' || is_numeric($_POST[$row['Field']])) ? '=?i' : '=?s';
				if (isset($this->rules->photo_one) && in_array($row['Field'], (array) $this->rules->photo_one))
					$params[] = pathinfo($_POST[$row['Field']], PATHINFO_BASENAME);
				else
					$params[] = rtrim(trim($_POST[$row['Field']]), ',');
			} elseif ($row['Field'] == 'id_user') {
				// TODO теоритически дырка, если постом передать id_user
				if (is_null($id)) {
					$fields[$row['Field']] = '`' . $row['Field'] . '`=?i';
					$params[] = (isset($_SESSION['edit_owner'])) ? $_SESSION['edit_owner'] : @$_SESSION['user']['id'];
				}
			} elseif (isset($this->rules->checkbox)) {
				foreach ( (array) $this->rules->checkbox as $field ) {
					if ($row['Field'] == $field) {
						$fields[$row['Field']] = '`' . $row['Field'] . '`=?i';
						$params[] = 0;
					}
				}
			}
		}
		if (count($fields) > 0) {
			$add_query = '`' . $this->table . '` SET ' . implode(', ', $fields);
			if (is_null($id)) {
				if (isset($this->rules->create_date)) {
					foreach ( (array) $this->rules->create_date as $field ) {
						$add_query .= ', ' . $field . '=NOW()';
					}
				}
				array_unshift($params, 'INSERT ' . $add_query);
				call_user_func_array(array ($this->db,'query'), $params);
				$id = $this->db->insertId();
			} else {
				$params[] = $id;
				array_unshift($params, "UPDATE {$add_query} WHERE id=?i");
				call_user_func_array(array ($this->db,'query'), $params);
			}
		}
		
		if (isset($this->rules->bridge)) {
			foreach ( (array) $this->rules->bridge as $field ) {
				$ar_field = explode('_', $field);
				if (is_array($ar_field)) {
					$other_table = $ar_field[1];
					$this->db->query('DELETE FROM ' . $this->table . '_' . $other_table . ' WHERE id_' . $this->table . '=?', $id);
					if (isset($_POST[$field]) && $_POST[$field] != 'NULL') {
						$query = "INSERT {$this->table}_{$other_table} SET {$field}=?i, id_{$this->table}=?i";
						$_POST[$field] = (array) $_POST[$field];
						foreach ( $_POST[$field] as $other_id => $value )
							$this->db->query($query, $other_id, $id);
					}
				}
			}
		}
		
		if (isset($this->rules->photo_one)) {
			foreach ( (array) $this->rules->photo_one as $field ) {
				$uploads_path = (isset($_POST['uploads_path'][$field])) ? $uploads_path = $_POST['uploads_path'][$field] : $this->table;
				if (isset($_POST[$field]) && trim($_POST[$field]) != '') {
					if (Utils::moveFile(ROOT . $_POST[$field], ROOT . 'uploads/' . $uploads_path . '/' . pathinfo($_POST[$field], PATHINFO_BASENAME))) {
						$_POST[$field] = pathinfo($_POST[$field], PATHINFO_BASENAME);
					}
				}
			}
		}
		
		if (isset($this->rules->photo_multi)) {
			foreach ( (array) $this->rules->photo_multi as $field ) {
				$this->db->query("DELETE FROM _file WHERE id_parent=?i AND id_section=?i AND table_name=?s AND field=?s", $id, $this->section, $this->table, $field);
				if (isset($_POST[$field]) && is_array($_POST[$field])) {
					
					$uploads_path = (isset($_POST['uploads_path'][$field])) ? $uploads_path = $_POST['uploads_path'][$field] : $this->table;
					
					foreach ( (array) $_POST[$field] as $key => $path ) {
						$this->db->query("INSERT _file SET id_parent=?i, id_section=?i, table_name=?s, field=?s, name=?s, note=?s", $id, $this->section, $this->table, $field, pathinfo($path, PATHINFO_BASENAME), @$_POST['note'][$key]);
						if (Utils::moveFile(ROOT . $path, ROOT . 'uploads/' . $uploads_path . '/' . pathinfo($path, PATHINFO_BASENAME))) {
							$_POST[$field][$key] = pathinfo($path, PATHINFO_BASENAME);
						}
					}
				}
			}
		}
		if ($message === false) {
			if ($field_active)
				$active = $this->db->getOne("SELECT active FROM {$this->table} WHERE id=?i", $id);
			
			$message = (!isset($active) || $active == 1) ? 'Данные сохранены!' : 'Данные сохранены, но не будут отображаться до тех пор, пока Вы не нажмете на линк [&nbsp;отобразить&nbsp;] напротив соответствующей записи!';
		}
		Message::success($message);
		
		return $id;
	}

	function item($id) {
		$ar = XML::from_db('/', "SELECT * FROM {$this->table} WHERE id=?i", $id, null);
		return $ar;
	}

	function getList($query = '', $param = null, $item_on_page = false, $visible_pages = VISIBLE_PAGES) {
		if ($query == '')
			$query2 = 'SELECT SQL_CALC_FOUND_ROWS t.id, t.name, t.active';
		if (isset($_GET['ADMIN'])) {
			Users::isLogin($this->message);
			if ($query == '')
				$query2 .= " FROM {$this->table} AS t WHERE (t.id_user={$_SESSION['user']['id']} OR ({$_SESSION['user']['role']} & 1 = 1))";
			if ($this->where != '')
				$this->where = ' AND ' . $this->where;
			$tag_name = 'list_admin';
			if ($item_on_page === false) {
				$item_on_page = $this->item_on_page_admin;
			}
		} else {
			if ($query == '')
				$query2 .= " FROM {$this->table} AS t";
			if ($this->where != '')
				$this->where = ' WHERE ' . $this->where;
			$tag_name = 'list';
			if ($item_on_page === false) {
				$item_on_page = $this->item_on_page_client;
			}
		}
		
		if ($query == '') {
			$query = $query2 . $this->where . ' ORDER BY ' . $this->order;
		}
		
		if (!isset($_GET['PAGE']) || intval($_GET['PAGE']) == 0)
			$_GET['PAGE'] = 1;
		if (intval($item_on_page) > 0) {
			$query .= ' LIMIT ' . ($item_on_page * ($_GET['PAGE'] - 1)) . ', ' . $item_on_page;
		}
		if ($this->get_list_mode == 'query') {
			$ar = $this->db->getAll($query, $param);
			XML::add_node('/', $tag_name);
		} else {
			$ar = XML::from_db('/', $query, $param, $tag_name);
			if (!$ar)
				XML::add_node('/', $tag_name);
		}
		
		if (intval($item_on_page) > 0) {
			$count_item = $this->db->getOne('SELECT FOUND_ROWS()');
			if ($count_item) {
				XML::from_array('//' . $tag_name, $this->getPages($count_item, $item_on_page, $visible_pages, $_GET['PAGE']), 'pages');
				XML::add_node('//pages', 'get', Utils::urlRemove('PAGE'));
			}
		}
		return $ar;
	}

	function cmdSort() {
		if (isset($_POST['table'])) {
			function getQuerySort($parentId, $children, $ar = array()) {
				global $i;
				$db = new Db;
				if (!isset($i)) $i=0;
				foreach ( $children as $child ) {
					$ar['path'][$child->id] = (isset($ar['path'][$parentId]) && $ar['path'][$parentId]!='/') ? $ar['path'][$parentId].'/'.$child->alias : $child->alias;
					$path = $db->escapeString($ar['path'][$child->id]);
					$ar['sql'][$child->id] = "({$child->id},{$parentId}," . $i++ . ",{$path})";
					if (count($child->children)>0)
						$ar = getQuerySort($child->id, $child->children, $ar);					
				}
				return $ar;
			}
			$list = json_decode($_POST['list']);
			$ar = getQuerySort('NULL', $list);
			fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', implode(',',$ar['sql']));
			$this->db->query('INSERT INTO ?n (id, id_parent, sort, path) VALUES '.implode(',',$ar['sql']).' ON DUPLICATE KEY UPDATE id_parent = VALUES(id_parent), sort = VALUES(sort), path = VALUES(path)', $_POST['table']);
		}
	}
	
	function cmdUpload() {
		$this->upload = new Upload;
	}
}