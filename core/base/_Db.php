<?php
//@formatter:off
/** 
 * Supported placeholders at the moment are:
 * 
 * ?s ("string")  - strings (also DATE, FLOAT and DECIMAL)
 * ?i ("integer") - the name says it all 
 * ?n ("name")    - identifiers (table and field names) 
 * ?a ("array")   - complex placeholder for IN() operator  (substituted with string of 'a','b','c' format, without parentesis)
 * ?u ("update")  - complex placeholder for SET operator (substituted with string of `field`='value',`field`='value' format)
 * and
 * ?p ("parsed") - special type placeholder, for inserting already parsed statements without any processing, to avoid double parsing.
 * 
 * Some examples:
 *
 * $db = new SafeMySQL(); // with default settings
 * 
 * $opts = array(
 *		'user'    => 'user',
 *		'pass'    => 'pass',
 *		'db'      => 'db',
 *		'charset' => 'latin1'
 * );
 * $db = new SafeMySQL($opts); // with some of the default settings overwritten
 * 
 * 
 * $name = $db->getOne('SELECT name FROM table WHERE id = ?i',$_GET['id']);
 * $data = $db->getInd('id','SELECT * FROM ?n WHERE id IN ?a','table', array(1,2));
 * $data = $db->getAll("SELECT * FROM ?n WHERE mod=?s LIMIT ?i",$table,$mod,$limit);
 *
 * $ids  = $db->getCol("SELECT id FROM tags WHERE tagname = ?s",$tag);
 * $data = $db->getAll("SELECT * FROM table WHERE category IN (?a)",$ids);
 * 
 * $data = array('offers_in' => $in, 'offers_out' => $out);
 * $sql  = "INSERT INTO stats SET pid=?i,dt=CURDATE(),?u ON DUPLICATE KEY UPDATE ?u";
 * $db->query($sql,$pid,$data,$data);
 * 
 * if ($var === NULL) {
 *     $sqlpart = "field is NULL";
 * } else {
 *     $sqlpart = $db->parse("field = ?s", $var);
 * }
 * $data = $db->getAll("SELECT * FROM table WHERE ?p", $bar, $sqlpart);
 */
// @formatter:on
class _Db {
	use Error;
	private static $link;
	public static $debug;
	private $stats;
	private $defaults = array ('host' => DBHOST,'user' => DBLOGIN,'pass' => DBPASSWORD,'db' => DBNAME,'port' => NULL,'socket' => NULL,'charset' => 'utf8');

	function __construct($opt = array()) {
		$opt = array_merge($this->defaults, $opt);
		self::$link = mysqli_connect($opt['host'], $opt['user'], $opt['pass'], $opt['db'], $opt['port'], $opt['socket']);
		mysqli_set_charset(self::$link, $opt['charset']);
		
		if (isset($_GET['d']))
			$_SESSION['noXml']['d'] = true;
		if (isset($_GET['d-']))
			unset($_SESSION['noXml']['d']);
		
		if (isset($_SESSION['noXml']['d']) && (DEBUG || $_SESSION['user']['role'] & 1 == 1))
			self::$debug = true;
	}

	/**
	 * Conventional function to run a query with placeholders. A mysqli_query wrapper with placeholders support Examples: $db->query("DELETE FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return resource|FALSE whatever mysqli_query returns
	 */
	public function query() {
		return $this->rawQuery($this->prepareQuery(func_get_args()));
	}

	/**
	 * Conventional function to fetch single row.
	 *
	 *
	 * @param resource $result - myqli result
	 * @param int $mode - optional fetch mode, MYSQLI_ASSOC|MYSQLI_NUM, default MYSQLI_ASSOC
	 * @return array|FALSE whatever mysqli_fetch_array returns
	 */
	public function fetch($result, $mode = MYSQLI_ASSOC) {
		return mysqli_fetch_array($result, $mode);
	}

	/**
	 * Conventional function to get number of affected rows.
	 *
	 *
	 * @return int whatever mysqli_affected_rows returns
	 */
	public function affectedRows() {
		return mysqli_affected_rows(self::$link);
	}

	/**
	 * Conventional function to get last insert id.
	 *
	 *
	 * @return int whatever mysqli_insert_id returns
	 */
	public function insertId() {
		return mysqli_insert_id(self::$link);
	}

	/**
	 * Conventional function to get number of rows in the resultset.
	 *
	 *
	 * @param resource $result - myqli result
	 * @return int whatever mysqli_num_rows returns
	 */
	public function numRows($result) {
		return mysqli_num_rows($result);
	}

	/**
	 * Conventional function to free the resultset.
	 */
	public function free($result) {
		mysqli_free_result($result);
	}

	/**
	 * Helper function to get scalar value right out of query and optional arguments Examples: $name = $db->getOne("SELECT name FROM table WHERE id=1"); $name = $db->getOne("SELECT name FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return string|FALSE either first column of the first row of resultset or FALSE if none found
	 */
	public function getOne() {
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			$row = $this->fetch($res);
			if (is_array($row)) {
				return reset($row);
			}
			$this->free($res);
		}
		return FALSE;
	}

	/**
	 * Helper function to get single row right out of query and optional arguments Examples: $data = $db->getRow("SELECT * FROM table WHERE id=1"); $data = $db->getOne("SELECT * FROM table WHERE id=?i", $id);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE either associative array contains first row of resultset or FALSE if none found
	 */
	public function getRow() {
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			$ret = $this->fetch($res);
			$this->free($res);
			return $ret;
		}
		return FALSE;
	}

	/**
	 * Helper function to get single column right out of query and optional arguments Examples: $ids = $db->getCol("SELECT id FROM table WHERE cat=1"); $ids = $db->getCol("SELECT id FROM tags WHERE tagname = ?s", $tag);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE either enumerated array of first fields of all rows of resultset or FALSE if none found
	 */
	public function getCol() {
		$ret = array ();
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			while ( $row = $this->fetch($res) ) {
				$ret[] = reset($row);
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Helper function to get all the rows of resultset right out of query and optional arguments Examples: $data = $db->getAll("SELECT * FROM table"); $data = $db->getAll("SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE enumerated 2d array contains the resultset. Empty if no rows found.
	 */
	public function getAll() {
		$ret = FALSE;
		$query = $this->prepareQuery(func_get_args());
		if ($res = $this->rawQuery($query)) {
			while ( $row = $this->fetch($res) ) {
				$ret[] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	function getAllMulti($query, $fetchmode = MYSQLI_ASSOC) {
		if (self::$debug)
			fb::dump(basename(__FILE__) . ' ( ' . __LINE__ . ' )', $query);
		$results = array ();
		if (!mysqli_multi_query(self::$link, $query))
			$this->error(mysqli_error(self::$link));
		do {
			if ($res = mysqli_store_result(self::$link)) {
				$results_ = array ();
				while ( $row = $res->fetch_assoc() ) {
					$results_[] = $row;
				}
				$results = array_merge($results, $results_);
				$res->free();
			}
		} while ( mysqli_more_results(self::$link) && (mysqli_next_result(self::$link) or $this->error(mysqli_error(self::$link))) );
		return (count($results) == 0) ? false : $results;
	}

	/**
	 * Helper function to get all the rows of resultset into indexed array right out of query and optional arguments Examples: $data = $db->getInd("id", "SELECT * FROM table"); $data = $db->getInd("id", "SELECT * FROM table LIMIT ?i,?i", $start, $rows);
	 *
	 * @param string $index - name of the field which value is used to index resulting array
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE - associative 2d array contains the resultset. Empty if no rows found.
	 */
	public function getInd() {
		$args = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		
		$ret = FALSE;
		if ($res = $this->rawQuery($query)) {
			while ( $row = $this->fetch($res) ) {
				$ret[$row[$index]] = $row;
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Helper function to get a dictionary-style array right out of query and optional arguments Examples: $data = $db->getIndCol("name", "SELECT name, id FROM cities");
	 *
	 * @param string $index - name of the field which value is used to index resulting array
	 * @param string $query - an SQL query with placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the query
	 * @return array|FALSE - associative array contains key=value pairs out of resultset. Empty if no rows found.
	 */
	public function getIndCol() {
		$args = func_get_args();
		$index = array_shift($args);
		$query = $this->prepareQuery($args);
		
		$ret = FALSE;
		if ($res = $this->rawQuery($query)) {
			while ( $row = $this->fetch($res) ) {
				$key = $row[$index];
				unset($row[$index]);
				$ret[$key] = reset($row);
			}
			$this->free($res);
		}
		return $ret;
	}

	/**
	 * Function to parse placeholders either in the full query or a query part unlike native prepared statements, allows ANY query part to be parsed useful for debug and EXTREMELY useful for conditional query building like adding various query parts using loops, conditions, etc. already parsed parts have to be added via ?p placeholder Examples: $query = $db->parse("SELECT * FROM table WHERE foo=?s AND bar=?s", $foo, $bar); echo $query; if ($foo) { $qpart = $db->parse(" AND foo=?s", $foo); } $data = $db->getAll("SELECT * FROM table WHERE bar=?s ?p", $bar, $qpart);
	 *
	 * @param string $query - whatever expression contains placeholders
	 * @param mixed $arg,... unlimited number of arguments to match placeholders in the expression
	 * @return string - initial expression with placeholders substituted with data.
	 */
	public function parse() {
		return $this->prepareQuery(func_get_args());
	}

	/**
	 * Function to get last executed query.
	 *
	 *
	 * @return string|NULL either last executed query or NULL if were none
	 */
	public function lastQuery() {
		$last = end($this->stats);
		return $last['query'];
	}

	/**
	 * Function to get all query statistics.
	 *
	 *
	 * @return array contains all executed queries with timings and errors
	 */
	public function getStats() {
		return $this->stats;
	}

	/**
	 * private function which actually runs a query against Mysql server. also logs some stats like profiling info and error message
	 *
	 * @param string $query - a regular SQL query
	 * @return mysqli result resource or FALSE on error
	 */
	private function rawQuery($query) {
		$start = microtime(TRUE);
		$res = mysqli_query(self::$link, $query) or $this->error(mysqli_error(self::$link));
		$timer = sprintf("%0.5F", microtime(true) - $start); // microtime(TRUE) - $start;
		

		$this->stats[] = array ('query' => $query,/*'start' => $start,*/'timer' => $timer);
		if (!$res) {
			$error = mysqli_error(self::$link);
			
			end($this->stats);
			$key = key($this->stats);
			$this->stats[$key]['error'] = $error;
			$this->cutStats();
			
			$this->error("$error. Full query: [$query]");
		}
		$this->cutStats();
		return $res;
	}

	private function prepareQuery($args) {
		$query = '';
		$raw = array_shift($args);
		$array = preg_split('~(\?[nsiuap])~u', $raw, null, PREG_SPLIT_DELIM_CAPTURE);
		$anum = count($args);
		$pnum = floor(count($array) / 2);
		if ($pnum != $anum) {
			$this->error("Number of args ($anum) doesn't match number of placeholders ($pnum) in [$raw]");
		}
		
		foreach ( $array as $i => $part ) {
			if (($i % 2) == 0) {
				$query .= $part;
				continue;
			}
			
			$value = array_shift($args);
			switch ($part) {
				case '?n' :
					$part = $this->escapeIdent($value);
					break;
				case '?s' :
					$part = $this->escapeString($value);
					break;
				case '?i' :
					$part = $this->escapeInt($value);
					break;
				case '?a' :
					$part = $this->createIN($value);
					break;
				case '?u' :
					$part = $this->createSET($value);
					break;
				case '?p' :
					$part = $value;
					break;
			}
			$query .= $part;
		}
		return $query;
	}

	function __destruct() {
		if (self::$debug)
			if ($query = $this->getStats())
				foreach ( $query as $q )
					fb::dump($q['timer'], $q['query']);
	}

	private function escapeInt($value) {
		if ($value === NULL || $value=='NULL') {
			return 'NULL';
		}
		if (!is_numeric($value)) {
			$this->error("Integer (?i) placeholder expects numeric value, " . gettype($value) . " given");
			return FALSE;
		}
		if (is_float($value)) {
			$value = number_format($value, 0, '.', ''); // may lose precision on big numbers
		}
		return $value;
	}

	public function escapeString($value) {
		if ($value === NULL) {
			return 'NULL';
		}
		return "'" . mysqli_real_escape_string(self::$link, $value) . "'";
	}

	private function escapeIdent($value) {
		if ($value) {
			return "`" . str_replace("`", "``", $value) . "`";
		} else {
			$this->error("Empty value for identifier (?n) placeholder");
		}
	}

	private function createIN($data) {
		if (!is_array($data)) {
			$this->error("Value for IN (?a) placeholder should be array");
			return;
		}
		if (!$data) {
			return 'NULL';
		}
		$query = $comma = '';
		foreach ( $data as $value ) {
			$query .= $comma . $this->escapeString($value);
			$comma = ",";
		}
		return $query;
	}

	private function createSET($data) {
		if (!is_array($data)) {
			$this->error("SET (?u) placeholder expects array, " . gettype($data) . " given");
			return;
		}
		if (!$data) {
			$this->error("Empty array for SET (?u) placeholder");
			return;
		}
		$query = $comma = '';
		foreach ( $data as $key => $value ) {
			$query .= $comma . $this->escapeIdent($key) . '=' . $this->escapeString($value);
			$comma = ",";
		}
		return $query;
	}

	private function error($err = '') {
		$this->getErrors($err);
	}

	/**
	 * On a long run we can eat up too much memory with mere statsistics Let's keep it at reasonable size, leaving only last 100 entries.
	 */
	private function cutStats() {
		if (count($this->stats) > 100) {
			reset($this->stats);
			$first = key($this->stats);
			unset($this->stats[$first]);
		}
	}

	public function autocommit($flag = TRUE) {
		mysqli_autocommit(self::$link, $flag);
	}

	public function commit() {
		mysqli_commit(self::$link);
	}

	public function rollback() {
		mysqli_rollback(self::$link);
	}
}
