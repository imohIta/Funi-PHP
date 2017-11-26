<?php
use core\libs;
use core\libs\Database;
use core\libs\logger;

defined("ACCESS") || die('Permission Denied');

global $registry;


require_once 'base/funiObject.class.php';
require_once realpath(__DIR__ . '/../') . '/configs/config.class.php';
require_once 'autoloader.class.php';
require_once 'session.class.php';
require_once 'router.class.php';
require_once 'registry.class.php';
require_once 'uri.class.php';
require_once 'utility/minifier.class.php';
require_once 'database.class.php';
require_once 'includer.class.php';
require_once 'logger.class.php';
require_once 'utility/sanitizer.class.php';
require_once 'authenticator.class.php';




/**
* Create all Core Objects that the Framework require to Boot
* Should Always be require in the index page before requiring appFactory and before Booting App
*/

# set Environment
$env = stripos($_SERVER['HTTP_HOST'], 'localhost') === false
		? 'production' : 'development';

# check if framework has been installed from installer
if(!file_exists(PATH . '/install/index.php')){

	$configFile = $env == 'development' ? 'dbConfig.php' ? 'dbConfig-prod.php';

	# check if config file exist
	if (is_file(PATH . '/core/configs/' . $configFile)) {


		require_once PATH . '/core/configs/' . $configFile;

		# create database object
		$database = new Database();

		# set database object with pdo object created from installation ( $pdo and $dbName are from dbCOnfig.php )
		# This will be Database configuration for development Env
		$database->setDatabase($pdo, $dbName);

		# fetch settings
		$query = 'select ' . $env . ' as ' . $env . ' from funiSettings where id = :id';

		$settings = $database->bindFetch($query, array('id' => 1), array($env));
		$settings = json_decode($settings[$env]);


		$config = new Config(array(
						'basePath' => PATH,
						'baseUri' => $settings->baseUrl,
						'appTitle' => $settings->appName,
						'ds' => '/',
						'dbHost' => $settings->dbHost,
						'dbName' => $settings->dbName,
						'dbPwd' => Authenticator::simpleCrypt($settings->dbPwd, 'd'),
						'dbUser' => $settings->dbUser
					));


		# create registry object
		$registry = Registry::getInstance();

		# create Autoloader Object
		$autoloader = new Autoloader(array('application','core'), $config);


		# create minifier object
		$minifier = new Minifier;


		# create includer
		$includer = new Includer($config, array(
										'tmplPath' => 'application/components/views/parts',
										'header' => 'header',
										'sidebar' => 'sidebar',
										'footer' => 'footer'
									  ));

		# create Sanitizer object
		$sanitizer = new Sanitizer();

		# create Logger
		$logger = new logger();

		//authenticator object
		$authenticator = new authenticator();

		# create Router Object ... when using windows...u can omit the second param cos it is defaulted
		$viewPath = ($env == 'development') ? '/' . $config->get('appTitle') . '/public/' : '/public/';
		$router = new Router($_SERVER['REQUEST_URI'], $viewPath);

		# create uri object
		$uri = new Uri($config);


		/**
		* Register all FrameWork Boot Classes
		* So they become accessible throughtout the App
		*/
		$registry->set('config', $config)
				->set('autoLoader', $autoloader)
				->set('router', $router)
				->set('uri', $uri)
				->set('environment', $env)
				->set('minifier', $minifier)
				->set('db', $database)
				->set('includer', $includer)
				->set('form', $sanitizer)
				->set('authenticator', $authenticator)
				->set('logger', $logger);




	}else{
		# if dbConfig files does not exist
		# rename setUpDir to install and force user to install by ridrecting to install Dir

		# rename setUpDir Directory to install
        rename('../../setUpDir', '../../install');

		# redirect to install page
		$uri = new Uri();
		$uri->redirect("http://" . $_SERVER['HTTP_HOST'] . "/" . PARENT_DIR . "/install");

	}


}else{

	# if framework has not been installed Yet
	# create config object with default params...to be used by bootstrap

	$config = new Config(array(
					'basePath' => PATH,
					'baseUri' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . PARENT_DIR . '/',
					'appTitle' => PARENT_DIR,
					'ds' => '/',
					'dbHost' => 'localhost',
					'dbName' => '',
					'dbPwd' => '',
					'dbUser' => ''
				));


	# create registry object
	$registry = Registry::getInstance();


	# create Autoloader Object
	$autoloader = new Autoloader(array('application','core'), $config);

	# create Router Object ... when using windows...u can omit the second param cos it is defaulted
	$viewPath = ($env == 'development') ? '/' . $config->get('appTitle') . '/public/' : '/public/';
	$router = new Router($_SERVER['REQUEST_URI'], $viewPath);

	# create uri object
	$uri = new Uri($config);


	# registry classes
	$registry->set('config', $config)
			 ->set('autoLoader', $autoloader)
			 ->set('router', $router)
			 ->set('uri', $uri);

}
