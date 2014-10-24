<?

class _config {
	public $user, $table, $fileConfig = 'configUser.php';

	function __construct() {
		global $USER_CONSTANTS;
		$this->user = $USER_CONSTANTS;
	}

	function show() {
		Users::isLogin();
		if (Users::isSuper() && isset($_GET['ADMIN'])) {
			if (isset($_POST['save'])) {
				$this->save();
			}
			$cfgs = file_get_contents(ROOT . $this->fileConfig);
			preg_match_all("'^define.*[\'\"]([a-z0-9_]+)[\'\"].+;\s*(?://(.*))?$'imU", $cfgs, $ar);
			if (count($ar[1]) > 0) {
				foreach ( $ar[1] as $k => $v )
					$this->user[$v] = array (stripslashes($this->user[$v]),$ar[2][$k]);
				XML::from_array('/', $this->user, 'defines');
			}
		}
	}

	function save() {
		$str = '';
		foreach ( $_POST['DEF'] as $k => $v ) {
			$str .= "define('$k', '" . addslashes($v) . "');";
			if (isset($_POST['DEF_HID'][$k]))
				$str .= " //" . $_POST['DEF_HID'][$k] . "\n";
			else
				$str .= "\n";
		}
		if (trim($str) != '') {
			$stat = file_put_contents(ROOT . $this->fileConfig, "<?\n" . $str);
			if ($stat) {
				Message::success('Конфигурация сохранена.');
				header('Location: /' . $_GET['path'] . '?ADMIN');
				exit();
			} else
				Message::error('Конфигурация не сохранена! <br/> Возможно нету прав доступа к файлу!');
		}
	}
}