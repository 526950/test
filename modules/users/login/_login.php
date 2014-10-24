<?

class _Login extends Users {

	function brief() {
		if (isset($_REQUEST['logout'])) {
			if (isset($_SESSION['admin']['id'])) {
				$_SESSION['user']['id'] = $_SESSION['admin']['id'];
				Users::refreshUserData();
				unset($_SESSION['admin']);
				header('Location: /users/?ADMIN');
				exit();
			} else {
				$this->db->query("UPDATE {$this->table} SET date_last=NOW() WHERE id=?i", $_SESSION['user']['id']);
				unset($_SESSION['user']);
				unset($_SESSION['reg_email']);
				setcookie('login', "", time() - COOKIE_LIFE_TIME);
				setcookie('password', "", time() - COOKIE_LIFE_TIME);
				$_SESSION['user']['role'] = 8;
				header('Location: /');
				exit();
			}
		}
		if (isset($_SESSION['user']['id'])) {
			XML::add_node('/', 'user', $_SESSION['user']);
		} elseif (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
			$_POST['login'] = $_COOKIE['login'];
			$_POST['password'] = $_COOKIE['password'];
			$_POST['remember'] = 1;
			$this->verify(true);
		}
	}

	function show() {
		XML::add_node('/', 'form_login');
		if (isset($_POST['save']) && $this->verify()) {
			if (isset($_SESSION['REDIRECT_URL']) && $_SESSION['REDIRECT_URL'] != '') {
				header('Location: ' . $_SESSION['REDIRECT_URL']);
				unset($_SESSION['REDIRECT_URL']);
			} else
				header('Location: ' . DOMAIN);
			die();
		}
	}

	function verify($cookie = false) {
		if (trim($_POST['login']) == '' || trim($_POST['password']) == '') {
			Message::error('Для входа необходимо заполнить оба поля');
		} else {
			if (!$cookie)
				$_POST['password'] = md5($_POST['password']);
			$row = XML::from_db('/', "SELECT " . USER_FIELDS . " FROM {$this->table} WHERE login=?s AND password=?s AND active=1", array ($_POST['login'],$_POST['password']), null, 'user');
			if ($row) {
				$_SESSION['user'] = $row[0];
				if (isset($_POST['remember'])) {
					setcookie('login', $row[0]['login'], time() + COOKIE_LIFE_TIME, '/');
					setcookie('password', $row[0]['password'], time() + COOKIE_LIFE_TIME, '/');
				}
				if ($_SESSION['user']['role'] & 1 == 1)
					setcookie('admin', $_SESSION['user']['login'], time() + COOKIE_LIFE_TIME * 28, '/');
				$this->updateLastAccess();
			} else {
				$this->setHTTPCode(401);
				//@todo сделать блокировку IP, убрать задержку что бы уйти от http флуда 
				//sleep(10);
				return Message::error("<b>Такие данные для входа не зарегистрированы<br/> Неверный логин или пароль</b><br/>
Проверьте правильность написания.<br/>
Убедитесь, что пароль вводится на том же языке, что и при регистрации.<br/>
Посмотрите, не нажат ли [Caps Lock].");
			}
		}
		return !Message::errorState();
	}
}