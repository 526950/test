<?
define('ROOT', str_replace('core/base/', '', dirname($_SERVER['SCRIPT_FILENAME']) . '/'));
if (file_exists(ROOT . 'debug')) {
	define('DEBUG', TRUE);
	error_reporting(-1);
} else {
	define('DEBUG', FALSE);
	set_error_handler ( 'error_handler', E_ALL);
}
include_once str_replace('core/base/', '', dirname($_SERVER['SCRIPT_FILENAME']) . '/configUser.php');
$tmp = get_defined_constants(true);
$USER_CONSTANTS = $tmp['user'];
define('CORE', ROOT . 'core/');
define('MODULES', CORE . 'modules/');
define('MODULES_LOCAL', ROOT . 'modules/');
define('BASE', CORE . 'base/');
define('UTILS', CORE . 'utils/');
define('CL', ROOT . 'class/');

define('TEMP', ROOT . 'temp/');

function error_handler($errno, $errstr, $errfile, $errline) {
	Error::getErrors($errstr);
}

register_shutdown_function(function () {
	$isError = false;
	
	if ($error = error_get_last()) {
		switch ($error['type']) {
			case E_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_USER_ERROR :
				$isError = true;
				break;
		}
	}
	
	if ($isError)
		Error::getErrors($error['message'] . "</code><br/><code>в файле {$error['file']} строка {$error['line']}</code><br/>");
});

function classname_exists($classname, $submodule = false) {
	$return_class = false;
	$classname = strtolower($classname);
	
	// if ((!$submodule && (class_exists($classname, false) || trait_exists($classname, false))) || ($submodule && (class_exists($submodule, false) || trait_exists($submodule, false))))
	// return false;
	
	if ($submodule) {
		$submodule = strtolower($submodule);
		if (file_exists(MODULES_LOCAL . "{$classname}/" . ltrim($submodule, '_') . "/{$submodule}.php"))
			$return_class = MODULES_LOCAL . "{$classname}/" . ltrim($submodule, '_') . "/{$submodule}";
		elseif (file_exists(MODULES . "{$classname}/" . ltrim($submodule, '_') . "/{$submodule}.php"))
			$return_class = MODULES . "{$classname}/" . ltrim($submodule, '_') . "/{$submodule}";
		elseif (file_exists(MODULES . "{$classname}/" . ltrim($submodule, '_') . "/_{$submodule}.php")) {
			$return_class = classname_exists($classname, '_' . $submodule);
			include_once $return_class . '.php';
			if (trait_exists('_' . $submodule, false) && !trait_exists($submodule, false)) {
				eval("class {$submodule} {use _{$submodule};}");
			} elseif (class_exists('_' . $submodule, false) && !class_exists($submodule, false)) {
				eval("class {$submodule} extends _{$submodule} {}");
			}
		}
	} else {
		if (file_exists(CL . $classname . '.php'))
			$return_class = CL . $classname;
		elseif (file_exists(UTILS . $classname . '.php'))
			$return_class = UTILS . $classname;
		elseif (file_exists(BASE . $classname . '.php'))
			$return_class = BASE . $classname;
		elseif (file_exists(MODULES_LOCAL . $classname . '/' . $classname . '.php'))
			$return_class = MODULES_LOCAL . $classname . '/' . $classname;
		elseif (file_exists(MODULES . ltrim($classname, '_') . '/' . $classname . '.php'))
			$return_class = MODULES . ltrim($classname, '_') . '/' . $classname;
		elseif (file_exists(BASE . '_' . $classname . '.php') || file_exists(UTILS . '_' . $classname . '.php') || file_exists(MODULES . ltrim($classname, '_') . '/_' . $classname . '.php')) {
			$return_class = classname_exists('_' . $classname);
			
			include_once $return_class . '.php';
			if (trait_exists('_' . $classname, false) && !trait_exists($classname, false)) {
				eval("trait {$classname} {use _{$classname};}");
			} elseif (class_exists('_' . $classname, false) && !class_exists($classname, false)) {
				eval("class {$classname} extends _{$classname} {}");
			}
		}
	}
	return $return_class;
}

function __autoload($classname) {
	if ($classname = classname_exists($classname)) {
		include_once $classname . '.php';
	}
}
require_once CORE . 'lib/FirePHPCore/fb.php';
Fb::setEnabled(DEBUG);