<?php
require_once CORE . 'lib/UploadHandler.php';

class _Upload extends UploadHandler {

	function __construct() {
		parent::__construct(array ('upload_dir' => ROOT . 'uploads/' . $_GET['module'] . '/','upload_url' => DOMAIN . 'uploads/' . $_GET['module'] . '/','image_versions' => array ('' => array ('auto_orient' => true))));
	}

	protected function get_unique_filename($file_path, $name, $size, $type, $error, $index, $content_range) {
		$name = Utils::translitUrl($name,false);
		while ( is_dir($this->get_upload_path($name)) ) {
			$name = $this->upcount_name($name);
		}
		// Keep an existing filename if this is part of a chunked upload:
		$uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
		while ( is_file($this->get_upload_path($name)) ) {
			if ($uploaded_bytes === $this->get_file_size($this->get_upload_path($name))) {
				break;
			}
			$name = $this->upcount_name($name);
		}
		return $name;
	}

	protected function upcount_name_callback($matches) {
		$index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
		$ext = isset($matches[2]) ? $matches[2] : '';
		return '(' . $index . ')' . $ext;
	}

	protected function upcount_name($name) {
		return preg_replace_callback('/(?:(?:\(([\d]+)\))?(\.[^.]+))?$/', array ($this,'upcount_name_callback'), $name, 1);
	}
}