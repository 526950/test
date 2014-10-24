<?

class _Message {

	static public function __callStatic($name, $param) {
		array_unshift($param, $name);
		call_user_func_array('self::mess', $param);
	}

	static private function mess($type, $str, $area = 'content', $up = false) {
		if (!isset($_SESSION['messages'][$area][$type]))
			$_SESSION['messages'][$area][$type] = array ();
		if (!in_array($str, $_SESSION['messages'][$area][$type])) {
			if ($up)
				array_unshift($_SESSION['messages'][$area][$type], $str);
			else
				array_push($_SESSION['messages'][$area][$type], $str);
		}
	}

	function brief() {
		if (isset($_SESSION['messages'])) {
			$messages = array ('messages' => $_SESSION['messages']);
			XML::from_array('/', $_SESSION['messages'], 'list');
			unset($_SESSION['messages']);
			if (isset($_SESSION['messages']['log']))
				unset($_SESSION['messages']['log']);
		}
	}

	static function db($db) {
		if (!$db->error)
			Message::success('Информация успешно сохранена в БД');
		else
			Message::error('ОШИБКА при обращении к БД, Информацию НЕ УДАЛОСЬ сохранить.');
	}

	static function errorState($area = null) {
		$result = false;
		if (isset($_SESSION['messages'])) {
			if (!is_null($area)) {
				if (isset($_SESSION['messages'][$area]['error']))
					$result = true;
			} else {
				foreach ( $_SESSION['messages'] as $key => $value ) {
					if (key_exists('error', $value))
						$result = true;
				}
			}
		}
		return $result;
	}

	static function get() {
		if (isset($_SESSION['messages'])) {
			$messages = $_SESSION['messages'];
			unset($_SESSION['messages']);
		} else {
			$messages = array ();
		}
		return $messages;
	}

	static function saveErrorLog($file = false) {
		if ($file && self::$num_err + 1 > MAX_NUM_ERROR) {
			if (isset($_SESSION['messages']['log']['error'])) {
				utils::createPath(ROOT . 'uploads/' . DOMAIN_CLEAR . '/xml_error/');
				$oldDom = XML::get_dom();
				Xml::add_node('/', 'root');
				
				XML::from_array('//root', $_SESSION['messages']['log']['error'], 'error');
				
				$xml = XML::get_dom();
				$error_log = XML::transform(null, str_replace('#ROOT#', ROOT, file_get_contents(ROOT . 'xsl/error_log.xsl')), $xml);
				
				Xml::set_dom($oldDom);
				unset($_SESSION['messages']['log']);
				file_put_contents($file, $error_log);
			}
		}
	}
}