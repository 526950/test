<?

class _Core {
	use Section, Error;
	private $db;
	public $xsl;

	function __construct() {
		$this->db = new Db();
		
		if (isset($_GET['ajaxModule']))
			$this->ajaxRun(trim($_GET['ajaxModule']));
	}

	function run() {
		$this->xsl = file_get_contents(CORE . 'xsl/index.sample.xsl');
		$modules = $this->getModules();
		foreach ( $modules as $row ) {
			$this->importTemplate(classname_exists($row['module'], $row['submodule']));
			$nameClass = ($row['submodule']) ? $row['submodule'] : $row['module'];

			$module = new $nameClass();
			
			if (property_exists($module, 'section'))
				$module->section = $row['id'];
			
			$root_tag = 'mod_' . $nameClass;
			XML::add_node('/', $root_tag);
			
			if (isset($row['current'])) {
				$this->xsl = str_replace(array ('CURRENT','CLASS'), array ($root_tag,$nameClass), $this->xsl);
				$module->show($row['id']);
				
				if (isset($_GET['ADMIN']))
					$theme_path = ($row['theme_admin']) ? $row['theme_admin'] : 'admin';
				else
					$theme_path = ($row['theme_client']) ? $row['theme_client'] : 'client';
			} elseif (method_exists($module, 'brief') && (!isset($_GET['ADMIN']) || (isset($module->admin_brief) && $module->admin_brief)))
				$module->brief();
			
			$xml_content[$root_tag] = XML::get_dom();
		}
		
		$this->importTemplate(ROOT . "themes/{$theme_path}/xsl/head.xsl");
		$this->importTemplate(ROOT . "themes/{$theme_path}/xsl/content.xsl");
		$this->importTemplate(ROOT . "themes/{$theme_path}/xsl/templates.xsl");
		
		$this->toXML(array_values($xml_content), true);
	}

	function importTemplate($path) {
		if ($path) {
			$path = str_replace('.xsl', '', $path) . '.xsl';
			if (file_exists($path)) {
				$this->xsl = preg_replace("'<!--\s*import modules\s*-->'i", "<xsl:import href='{$path}'/>\n<!--import modules-->", $this->xsl);
			}
		}
	}

	function ajaxRun($path) {
		$row = $this->getModulesForAjax($path);
		if ($row) {
			$path = classname_exists($row['module'], $row['submodule']);
			if ($path) {
				$nameClass = ($row['submodule']) ? $row['submodule'] : $row['module'];
				$root_tag = 'mod_' . $nameClass;
				XML::add_node('/', $root_tag);
				
				$module = new $nameClass();
				$str = $module->ajaxShow($row['id']);
				
				if (isset($str) && !is_null($str) && is_string($str)) {
					echo $str;
				} else {
					$this->xsl = file_get_contents(CORE . 'xsl/index.sample.ajax.xsl');
					$this->importTemplate(classname_exists($row['module'], $row['submodule']));
					$this->xsl = str_replace(array ('CURRENT','CLASS'), array ($root_tag,$nameClass), $this->xsl);
					XML::add_node('/','mod_message');
					$this->toXML(array (XML::get_dom()));
					echo XML::transform(false, $this->xsl, XML::get_dom());
				}
			}
		}
		exit();
	}

	function toXML($xml_content, $config = false) {
		global $USER_CONSTANTS;
		
		XML::add_node('/', 'root');
		XML::from_array('/', $xml_content, 'content');
		XML::from_array('//mod_message', Message::get(), 'list');
		
		XML::add_node('/', 'DEBUG', DEBUG);
		XML::add_node('/', 'domain', DOMAIN);
		XML::add_node('/', 'domain_clear', DOMAIN_CLEAR);
		// XML::add_node('/', 'url', GET('DEL'));
		// if(isset($_SERVER['HTTP_REFERER'])) XML::add_node('/', 'REFERRER', $_SERVER['HTTP_REFERER']);
		
		$ses = $_SESSION;
		unset($ses['messages'], $ses['noXML']);
		
		XML::from_array('/', array ('post' => $_POST,'get' => $_GET,'session' => $ses,'server' => filterArray($_SERVER, array ('HTTP_REFERER','HTTP_COOKIE','REQUEST_URI'))), 'requests');
		
		XML::from_array('/', array ('date' => date("d.m.Y"),'time' => date("H:i:s"),'unix' => time(),'day' => date("d"),'month' => date("m"),'year' => date("Y"),'expire_cache' => gmdate("D, d M Y h:i:s", strtotime("+1 month", time()))), 'date', null, true);
		
		if ($config)
			XML::from_array('/', $USER_CONSTANTS, 'config');
	}
}