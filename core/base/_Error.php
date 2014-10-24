<?

trait _Error {

	static function getErrors($err = '') {
		$str = "<style>.errorReport{background: #EEE; margin: 10px 10px; border: 3px double #CCC; padding:10px;font-size:13px;} .errorReport .red{color:red; font-size:14px;} .errorReport hr{border:0px; border-top: 1px solid #ccc}</style><div class='errorReport'><code class='red'>{$err}</code>";
				
		$code = 0;
		if (preg_match("'Unknown database'is", $err))
			$code = 1;
		
		switch ($code) {
			case -1 :
				break;
			case 1 :
			default :
// 				var_dump(debug_backtrace());exit;
				foreach ( debug_backtrace() as $t_ ) {
					if (isset($t_['args']) && isset($t_['function'])  && $t_['function'] == 'error_handler' ) {
						$str .= "<br/><code>в файле {$t_['args'][2]} строка {$t_['args'][3]}</code><br/>";
					}
				}
				break;
		}
		
		if (DEBUG) {
			echo $str . '</div>';
			// die;
		} else {
			if (isset($_SERVER['HTTP_USER_AGENT']))
				$str .= "<br/><b>HTTP_USER_AGENT:</b> " . $_SERVER['HTTP_USER_AGENT'];
			$str .= "<br/><b>IP User:</b> " . Utils::getUserIP();
			if (isset($_GET) && count($_GET) > 0)
				$default['$_GET'] = $_GET;
			if (isset($_POST) && count($_POST) > 0)
				$default['$_POST'] = $_POST;
			if (isset($_SESSION) && count($_SESSION) > 0)
				$default['$_SESSION'] = $_SESSION;
			$subject = 'ERROR REPORT ' . DOMAIN_CLEAR . ((isset($_SERVER)) ? $_SERVER['REQUEST_URI'] : ' CRON');
			
			if (isset($default))
				foreach ( $default as $key => $value )
					$str .= "<hr /><b>{$key}</b><br />" . highlight_string("<?\n" . stripslashes(str_replace('\\\\', '/', var_export($value, true))) . "\n?>", true);
			
			echo $str . '</div>';
			
			if (!preg_match("'MySQL server has gone away'", $str)) {
				$ml = new sendmail();
				$ml->addHtml($str);
				$ml->send(EMAIL_REPORT, $subject);
			}
		}
	}

	static function getErrors_($err = '') {
		$str = '<style>.errorReport{background: #EEE; margin: 10px 10px; border: 3px double #CCC; padding:10px;font-size:13px;} .errorReport .red{color:red; font-size:14px;} .errorReport hr{border:0px; border-top: 1px solid #ccc}</style><div class="errorReport">';
		$code = 0;
		if (preg_match("'Unknown database'is", $err))
			$code = 1;
		
		$trace = debug_backtrace();
		switch ($code) {
			case 1 :
				$str .= "<code class='red'>{$err}</code>";
				break;
			
			default :
				foreach ( $trace as $t ) {
					if (!filterVar($t['function'], array ('report','error'))) {
						if (isset($t['file']))
							$str .= "<code class='red'>{$err}</code><br/><code>в файле " . str_replace(ROOT, '../', $t['file']) . " строка {$t['line']}<br/>функция {$t['function']}</code><br/>";
						else
							$str .= "<code class='red'>{$err}</code><br/>";
					}
				}
				break;
		}
		
		if (DEBUG) {
			echo $str . '</div>';
			// die;
		} else {
			if (isset($_SERVER['HTTP_USER_AGENT']))
				$str .= "<br/><b>HTTP_USER_AGENT:</b> " . $_SERVER['HTTP_USER_AGENT'];
			$str .= "<br/><b>IP User:</b> " . Utils::getUserIP();
			if (isset($_GET) && count($_GET) > 0)
				$default['$_GET'] = $_GET;
			if (isset($_POST) && count($_POST) > 0)
				$default['$_POST'] = $_POST;
			if (isset($_SESSION) && count($_SESSION) > 0)
				$default['$_SESSION'] = $_SESSION;
			$subject = 'ERROR REPORT ' . DOMAIN_CLEAR . ((isset($_SERVER)) ? $_SERVER['REQUEST_URI'] : ' CRON');
			
			if (isset($default))
				foreach ( $default as $key => $value )
					$str .= "<hr /><b>{$key}</b><br />" . highlight_string("<?\n" . stripslashes(str_replace('\\\\', '/', var_export($value, true))) . "\n?>", true);
			
			echo $str . '</div>';
			
			if (!preg_match("'MySQL server has gone away'", $str)) {
				$ml = new sendmail();
				$ml->addHtml($str);
				$ml->send(EMAIL_REPORT, $subject);
			}
		}
	}

	function setHTTPCode($code) {
		$code = intval($code);
		switch ($code) {
			case 401 :
				header("HTTP/1.1 401 	Authorization Required");
				Message::error('401 	Authorization Required<br/>Требуется авторизация');
				break;
			case 403 :
				header("HTTP/1.1 403 Forbidden");
				Message::error('403 Forbidden<br/>Доступ запрещен');
				break;
			case 404 :
				header("HTTP/1.1 404 Not Found", true, 404);
				Message::error('404 Not Found<br/>Страница не найдена');
				die();
			case 500 :
				header("HTTP/1.1 500 Internal Server Error");
				Message::error('500 Internal Server Error<br/>Внутренняя ошибка сервера');
				break;
			default :
				break;
		}
	}
}