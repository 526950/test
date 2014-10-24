<?

class Config extends _Module {

	function show() {
		if (!empty($_FILES)) {
			$_GET['module'] = 'config';
			$upload = new Upload();
			header('Location: /config/?ADMIN');
		}
		parent::show();	
	}
}