<?
setlocale(LC_COLLATE, 'ru_RU.UTF-8');
setlocale(LC_CTYPE, 'ru_RU.UTF-8');
setlocale(LC_TIME, 'ru_RU.UTF-8');
setlocale(LC_MESSAGES, 'ru_RU.UTF-8');
setlocale(LC_MONETARY, 'ru_RU.UTF-8');
setlocale(LC_NUMERIC, 'en_US');

date_default_timezone_set('Europe/Kiev');

mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

define('DOMAIN_CLEAR', 'skrepka.biz');

// DataBase >>>
define('DBHOST', 'localhost');
define('DBNAME', 'core');
define('DBLOGIN', 'root');
define('DBPASSWORD', '');
// <<< DataBase

if (isset($_SERVER['SERVER_NAME']))
	define('DOMAIN', 'http://' . $_SERVER['SERVER_NAME'] . '/');
else
	define('DOMAIN', 'http://' . DOMAIN_CLEAR . '/');

define('CACHE', false);

define('USER_PREFIX', '');
define('USER_UID_NUMBER', 5);
define('USER_NUMBER_ON_FOLDER', 1000);

// LUCENE >>>
define('LUCENE', 1);
define('LUCENE_ENCODING', 'UTF-8');
define('SEARCH_PATH_INDEX', TEMP . 'search_index/' . DOMAIN_CLEAR);
define('SEARCH_MIN_SCORE', 0);
// <<< LUCENE

//define('URL_TRANSLIT', 'RU');

set_include_path(CORE . 'lib/');

// ARRAYS >>>
$menu = array('menu_main'=>'Главное','menu_bottom'=>'Нижнее', 'menu_left' => 'Левое', 'menu_right'=>'Правое');
$admin_menu_array = array(
		//array('name'=>'Галерея','url'=>'/gallery/?ADMIN'),
);
//<<< ARRAYS