<?

class _Admin extends Module {
	private $modules = array (), $ignore_modules = array ();

	function __construct() {
		Users::isLogin();
		parent::__construct('_section');
	}

	function getModules($path) {
		if ($handle = opendir($path)) {
			while ( false !== ($dir = readdir($handle)) ) {
				if ($dir != '.' && $dir != '..' && !preg_match("'\..+'i", $dir)) {
					$config = $path . $dir . '/config.xml';
					if (file_exists($config)) {
						$config = simplexml_load_file($config);
						$this->modules[$dir] = array ('title' => (string) $config['title'],'deny-for-select' => (string) $config['deny-for-select']);
					} else
						$this->modules[$dir] = array ();
					
					if ($subHandle = opendir($path . $dir)) {
						while ( false !== ($subDir = readdir($subHandle)) ) {
							if ($subDir != '.' && $subDir != '..' && !preg_match("'\..+'i", $subDir)) {
								$config = $path . $dir . '/' . $subDir . '/config.xml';
								if (file_exists($config)) {
									$config = simplexml_load_file($config);
									$this->modules[$dir]['submodules'][$subDir] = array ('title' => (string) $config['title'],'deny-for-select' => (string) $config['deny-for-select']);
								} else
									$this->modules[$dir]['submodules'][$subDir] = array ();
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}

	function cmdAdd() {
		global $menu;
		fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $_POST);
		// Если не передано в POST "E" значит это добавление, иначе редактирование
		if (!in_array('E', $_POST)) {
			$_POST['id_parent'] = $_POST['pk'];
			$_POST['pk'] = null;
		}
		$_POST['name'] = $_POST['value']['name'];
		if ($_POST['value']['alias'] != '')
			$_POST['alias'] = $_POST['value']['alias'];
		
		$ar_module = explode('_', $_POST['value']['module']);
		$_POST['module'] = $ar_module[0];
		$_POST['submodule'] = (isset($ar_module[1])) ? $ar_module[1] : '';
		$_POST['active'] = ($_POST['value']['active'] == 'true') ? 1 : 0;
		$id1 = $id2 = $this->save($_POST['pk'], '');
		if (isset($_POST['value']['link'])) {
			if (!is_null($_POST['pk']))
				$this->db->query('DELETE FROM _section_link WHERE id1=?i AND id2!=?i AND id2 IS NOT NULL', $id1, $id1);
			if ($_POST['value']['link'] == 'all')
				$id2 = null;
		}
		$params = trim($_POST['value']['params']);
		// если редактирование
		if (!is_null($_POST['pk'])) {
			$params_old = $this->db->getOne('SELECT params FROM _section_link WHERE id1=?i AND (id2=?i OR id2 IS NULL)', $id1, $id1);
			if ($params_old) {
				parse_str($params, $ar_params);
				parse_str($params_old, $ar_params_old);
				// Если чекбокс не отмечен, удаляет этот ключ из старых параметров
				foreach ( $menu as $key => $value ) {
					if (!array_key_exists($key, $ar_params))
						unset($ar_params_old[$key]);
				}
				// если в старых параметрах нет, то добавляет, если есть, то заменяет на новые
				$params = str_replace('=&', '&', rtrim(http_build_query(array_replace($ar_params_old, $ar_params)), '='));
				// fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $params);
			}
			$this->db->query('UPDATE _section_link SET id2=?i, params=?s WHERE id1=?i AND (id2=?i OR id2 IS NULL)', $id2, $params, $id1, $id1);
		} else {
			$this->db->query('INSERT _section_link SET ?u', array ('params' => $params,'id1' => $id1,'id2' => $id2));
		}
	}

	function cmdSave() {
		global $menu;
		fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $_POST);
		$this->db->query('DELETE FROM _section_link WHERE id1=?i', $_POST['pk']);
		if (isset($_POST['value']['id2'])) {
			foreach ( $_POST['value']['id2'] as $id2 ) {
				$params[] = intval($_POST['pk']) . ',' . intval($id2) . ',' . $this->db->escapeString($_POST['value']['params'][$id2]);
			}
			$this->db->query('INSERT INTO _section_link (id1,id2,params) VALUES (?p)', implode('),(', $params));
		}
	}


	function cmdShow() {
		if (isset($_POST['id_section'])) {
			$ar = $this->db->getAll('SELECT IFNULL(id2,id1) AS id2, params FROM _section_link WHERE id1=?i', $_POST['id_section']);
			return json_encode($ar);
		} else
			parent::show();
	}

	function getList($query = '', $param = NULL, $item_on_page = false, $visible_pages = '10') {
		global $menu;
		if (isset($_GET['ADMIN'])) {
			$this->getModules(MODULES);
			$this->getModules(MODULES_LOCAL);
			XML::from_array('/', $this->modules, 'select-modules', 'item', true);
			XML::from_array('/', $menu, 'select-menu');
			parent::getList("SELECT s.*, sl.params, IF((SELECT COUNT(*) FROM _section_link WHERE id1=s.id)>1,'custom', IF (sl.id2 IS NULL, 'all', 'self')) AS link FROM _section AS s LEFT JOIN _section_link AS sl ON s.id=sl.id1 AND (s.id=sl.id2 OR sl.id2 IS NULL) WHERE id>99 ORDER BY s.sort", null, 0);
		}
	}
}