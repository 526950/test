<?php

trait _Section {
	public $ar, $ar_plain;

	function getModulesForAjax($path) {
		return $this->db->getRow("SELECT s.*, sl.params, '' as current FROM _section as s JOIN _section_link as sl ON s.id=sl.id1 AND (s.id=sl.id2 OR sl.id2 IS NULL) WHERE s.active=1 AND s.path=?s", rtrim($path, '/'));
	}

	function getModules() {
		global $config;
		$modules = array ();
		$path =$_GET['path']= (isset($_GET['path'])) ? rtrim($_GET['path'], '/') : '';
		
		$row = $this->db->getRow("SELECT s.*, sl.params, '' as current FROM _section as s JOIN _section_link as sl ON s.id=sl.id1 AND (s.id=sl.id2 OR sl.id2 IS NULL) WHERE s.active=1 AND s.path=?s", $path);
		
		if ($row) {
			$modules[] = $row;
			$res = $this->db->query('SELECT s.*, sl.params FROM _section AS s  JOIN _section_link AS sl ON sl.id2=s.id WHERE s.active=1 AND sl.id1=?i AND sl.id1!=sl.id2 AND sl.id2 IS NOT NULL
UNION
SELECT s.*, sl.params FROM _section AS s  JOIN _section_link AS sl ON sl.id1=s.id AND sl.id2 IS NULL WHERE s.active=1 AND sl.id1!=?i', $row['id'], $row['id']);			
			if ($res)
				while ( $row = $this->db->fetch($res) )
					$modules[] = $row;
		} else
			$this->setHTTPCode(404);
		
		_Array::multikey_sort($modules, 'sort');
		return $modules;
	}
}