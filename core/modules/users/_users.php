<?
define('USER_FIELDS', 'id, login, password, email, role, position');
define('COOKIE_LIFE_TIME', 3600 * 24 * 14);
define('COOKIE_REFER_TIME', 3600 * 24 * 30);
define('ONLINE_TIME', 60);

class _Users extends Module {
	use Error;

	function __construct() {
		parent::__construct('users');
		if (isset($_GET['LOGIN'])) {
			$this->login(intval($_GET['LOGIN']));
		}
	}

	static function refreshUserData() {
		if (isset($_SESSION['user']['id'])) {
			$id = intval($_SESSION['user']['id']);
			if ($id)
				$_SESSION['user'] = $this->db->getRow("SELECT " . USER_FIELDS . " FROM {$this->table} WHERE id=?i ", $id);
		}
	}

	function updateLastAccess() {
		if (isset($_SESSION['user']['id']))
			$this->db->query("UPDATE {$this->table} SET date_last=NOW() WHERE id=?i", $_SESSION['user']['id']);
	}

	function login($id) {
		if ($_SESSION['user']['role'] & 1 == 1 && $id) {
			$_SESSION['admin']['id'] = $_SESSION['user']['id'];
			$_SESSION['user']['id'] = $id;
			Users::refreshUserData();
			header('Location: /');
			exit();
		}
	}

	function getList($query = '', $param = null, $item_on_page = false, $visible_pages = VISIBLE_PAGES) {
		self::isLogin('Доступ запрещен.');
		if ($_SESSION['user']['role'] & 1 == 1 && $_SESSION['user']['position'] == 'superadmin') {
			
			$_SESSION['sort_trend'] = filterVar(@$_SESSION['sort_trend'], array ('DESC','ASC'), 'DESC');
			$fl = true;
			$addSRCH = 'WHERE id!="' . $_SESSION['user']['id'] . '"';
			if (isset($_GET['SORT']) && $_GET['SORT'] != '') {
				$order = filterVar($_GET['SORT'], array ('id','date_reg','date_last','active'), 'id');
			} else {
				$order = (isset($_SESSION['sort_user'])) ? $_SESSION['sort_user'] : 'id';
				$fl = false;
			}
			if (isset($_SESSION['sort_user']) && $_SESSION['sort_user'] == $order && $fl && !isset($_GET['PAGE'])) {
				$_SESSION['sort_trend'] = ($_SESSION['sort_trend'] == 'ASC') ? 'DESC' : 'ASC';
			}
			$_SESSION['sort_user'] = $order;
			
			if (isset($_GET['SEARCH']) && trim($_GET['SEARCH']) != '') {
				$search = $this->db->escapeString(trim($_GET['SEARCH']));
				$addSRCH .= " AND  (id='$search' OR login LIKE '%$search%' OR email LIKE '%$search%' OR signature LIKE '%$search%' OR phone LIKE
				'%$search%' OR note LIKE '%$search%') ";
			}
			
			$ars = parent::getList("SELECT SQL_CALC_FOUND_ROWS *,
                        IF(date_last>(NOW()-" . (2 * ONLINE_TIME) . "), 1, 0) as online
                        FROM {$this->table} {$addSRCH} ORDER BY `{$_SESSION['sort_user']}` {$_SESSION['sort_trend']}", null, 50, 10);
		} else
			$this->setHTTPCode(403);
	}

	static function isLogin($message = false) {
		if (!isset($_SESSION['user']['id']) && !(isset($_POST['cmd']) && preg_match("/upload.*/", $_POST['cmd']))) {
			if ($message)
				Message::notice($message);
			$_SESSION['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
			header('Location: /login/');
			exit();
		}
	}

	static function isSuper() {
		return (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] & 1 == 1) ? true : false;
	}
}
