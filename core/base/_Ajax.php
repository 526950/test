<?

trait _Ajax {

	function cmdTest() {
		fb::dump('post', $_POST);
		fb::dump('get', $_POST);
		exit;
	}

	function cmdActive() {
		if (isset($_REQUEST['id'])) {
			$id = intval($_REQUEST['id']);
			if (method_exists($this, 'isOwner') && $this->isOwner($id) && $id)
				exit($this->db->query("UPDATE {$this->table} SET active=NOT(active) WHERE id={$id}"));
			else
				exit(Error::status(403));
		}
	}

// 	function cmd_uploadify() {
// 		$dir = (isset($_REQUEST['folder']) && trim($_REQUEST['folder']) != '') ? $_REQUEST['folder'] : 'temp';
// 		$file = $_FILES['Filedata'];
// 		$filename = UTILS::uploadFile($dir, $_REQUEST['destination'], $file, $_REQUEST['prefix']);
// 		$path = str_replace('//', '/', ROOT . $dir . '/' . $filename);
// 		if (isset($_REQUEST['width']) && isset($_REQUEST['height']) && $_REQUEST['type'] == 'image')
// 			Utils::writeFoto($path, $path, $_REQUEST['width'], $_REQUEST['height'], 85);
// 		$filename = pathinfo($filename);
// 		echo $filename['basename'];
// 	}

// 	function cmd_rotateImg() {
// 		//TODO проверка на owners
// 		$name = ROOT . $_REQUEST['file'];
// 		$degrees = $_REQUEST['rot'];
// 		echo Utils::rotateFoto($name, $degrees);
// 	}

// 	function cmd_deleteFile() {
// 		//TODO проверка на owners
// 		if (isset($_POST['file'])) {
// 			$file = strstr($_POST['file'], 'temp');
// 			if ($file)
// 				$_POST['file'] = $file;
// 			$file = pathinfo($_POST['file']);
			
// 			if ($_POST['multi'] == 'true') {
// 				if (intval($_POST['id_parent']) != 0) {
// 					$this->db->query("DELETE FROM _file WHERE id_parent=?i AND id_section=?i AND table_name=?s AND name=?s", $_POST['id_parent'],$this->section,$this->table,$file['basename']);
// 				}
// 			} else {
// 				$this->db->query('UPDATE `!` SET `!`="" WHERE `!`=?', array ($_POST['table'],$_POST['field'],$_POST['field'],$file['basename']));
// 			}
// 			if (($_POST['table'] == 'users')) {
// 				Users::refreshUserData();
// 			}
// 			@unlink(ROOT . $_POST['file']);
// 		}
// 	}
}