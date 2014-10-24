<?php

trait _Base {
	protected $db;

	function ajaxShow() {
		if (isset($_REQUEST['cmd']) && method_exists($this, 'cmd' . $_REQUEST['cmd'])) {
			$cmd = 'cmd' . $_REQUEST['cmd'];
			return $this->$cmd();
		}
		if (isset($_POST['sortTree']) && !is_null($this->table)) {
			foreach ( $_POST['sortTree'] as $i => $id )
				$this->db->query("UPDATE `{$this->table}` SET sort=?i WHERE id=?i", $i, $id);
			echo 1;
			exit();
		}
	}
}