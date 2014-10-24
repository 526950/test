<?

class _Profile extends Users {

	function __construct() {
		$this->islogin = false;
		$field_rules = '{"photo_one" : "foto"}';
		parent::__construct('users', $field_rules);
	}

	function show() {
		Users::isLogin();
		if (isset($_GET['change_password']) && ENABLE_RECOVERY_PASSWORD) {
			$this->change_password();
		} elseif (isset($_GET['change_hash']) && ENABLE_RECOVERY_PASSWORD) {
			$this->new_password();
		} else {
			if (isset($_GET['ADMIN']))
				$_GET['EDIT'] = $_SESSION['user']['id'];
			parent::show();
		}
	}

	function save($id = null, $message = false) {
		if ($this->verify()) {
			if (@trim($_POST['password']) != '')
				$_POST['password'] = md5($_POST['password']);
			else
				unset($_POST['password']);
			parent::save($_SESSION['user']['id'], 'Ваши данные сохранены!');
			parent::refreshUserData();
		}
	}

	function verify() {
		if (trim($_POST['password']) != '') {
			if (trim($_POST['repassword'] == '')) {
				Message::error('Для смены пароля необходимо заполнить оба поля "Пароль" и "Пароль (повтор)"');
			} else {
				if ($_POST['password'] != $_POST['repassword']) {
					Message::error('Введенные пароли не совпадают!');
				}
			}
		}
		return !Message::errorState();
	}

	function change_password() {
		if (isset($_POST['save'])) {
			$row = $this->db->get_row('SELECT id, login, password FROM `' . $this->table . '` WHERE `active`="1" AND login=? ', $_POST['login']);
			if ($row) {
				$hash = md5($row['login'] . $row['password'] . time());
				$this->db->query('UPDATE `' . $this->table . '` SET `hash`="' . $hash . '" WHERE `id`="' . $row['id'] . '"');
				$send = new sendmail();
				$msg = 'Здравствуйте2!<br /><br />
Вы получили это письмо, так как Ваш e-mail был указан для восстановлении пароля на сайте <a href="http://' . DOMAIN_CLEAR . '">' . DOMAIN_CLEAR . '</a>.<br />
Если Вы не делали этого просто проигнорируйте и удалите это письмо.<br /><br />
	
Для продолжения восстановления проследуйте по следующей ссылке или скопируйте в адресную строку браузера <a href="http://' . DOMAIN_CLEAR . '/login/?change_hash=' . $hash . '">http://' . DOMAIN_CLEAR . '/login/?change_hash=' . $hash . '</a>' . SIGNATURE;
				$send->addHtml($msg);
				$send->send($row['email'], 'Восстановление пароля на ' . DOMAIN_CLEAR);
				
				Message::success('Вам на почту отправлена инструкция по восстановлению пароля');
			} else {
				unset($_POST);
				Message::error('Такого аккаунта не существует!', 'login');
				XML::add_node('/', 'form_change_password');
			}
		} else {
			XML::add_node('/', 'form_change_password');
		}
	}

	function new_password() {
		if (isset($_POST['save'])) {
			if (trim(@$_POST['password'] == ''))
				Message::error('Не заполнено поле "Пароль"', 'password');
			if (trim(@$_POST['repassword'] == ''))
				Message::error('Не заполнено поле "Повтор пароля"', 'repassword');
			if (trim(@$_POST['password']) != '' && trim(@$_POST['repassword']) != '' && @$_POST['password'] != @$_POST['repassword'])
				Message::error('Введенные пароли не совпадают!', 'repassword');
			
			if (!Message::errorState()) {
				$this->db->query('UPDATE `' . $this->table . '` SET `password`="' . md5($_POST['password']) . '", `hash`="" WHERE `active`="1" AND `hash`="' . $_GET['change_hash'] . '"');
				if ($this->db->error) {
					Message::success('Пароль успешно изменен!<br/>Теперь вы можете авторизоваться');
					header('Location: /login/');
					exit();
				}
			} else
				XML::add_node('/', 'form_new_password');
		} else {
			$id = $this->db->get_one('SELECT `id` FROM `' . $this->table . '` WHERE `active`="1" AND `hash`=?', array ($_GET['change_hash']));
			if ($id)
				XML::add_node('/', 'form_new_password');
			else
				header('Location: /');
		}
	}
}