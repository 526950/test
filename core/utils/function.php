<?

/**
 * function to implement whitelisting feature sometimes we can't allow a non-validated user-supplied data to the query even through placeholder especially if it comes down to SQL OPERATORS Example: $order = filterVar($_GET['order'], array('name','price')); $dir = filterVar($_GET['dir'], array('ASC','DESC')); if (!$order || !dir) { throw new http404(); //non-expected values should cause 404 or similar response } $sql = "SELECT * FROM table ORDER BY ?p ?p LIMIT ?i,?i" $data = $db->getArr($sql, $order, $dir, $start, $per_page);
 *
 * @param string $iinput - field name to test
 * @param array $allowed - an array with allowed variants
 * @param string $default - optional variable to set if no match found. Default to false.
 * @return string|FALSE - either sanitized value or FALSE
 */
function filterVar($input, $allowed, $default = FALSE) {
	$found = array_search($input, $allowed);
	return ($found === FALSE) ? $default : $allowed[$found];
}

/**
 * function to filter out arrays, for the whitelisting purposes useful to pass entire superglobal to the INSERT or UPDATE query OUGHT to be used for this purpose, as there could be fields to which user should have no access to. Example: $allowed = array('title','url','body','rating','term','type'); $data = filterArray($_POST,$allowed); $sql = "INSERT INTO ?n SET ?u"; $db->query($sql,$table,$data);
 *
 * @param array $input - source array
 * @param array $allowed - an array with allowed field names
 * @return array filtered out source array
 */
function filterArray($input, $allowed) {
	foreach ( array_keys($input) as $key ) {
		if (!in_array($key, $allowed)) {
			unset($input[$key]);
		}
	}
	return $input;
}



function str(&$str, $link = false) {
	$s = new _String();
	($link) ? $s->setLink($str) : $s->set($str);
	return $s;
}

function arr($arr) {
	$s = new _Array();
	$s->set($arr);
	return $s;
}