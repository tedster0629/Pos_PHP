<?php
define('APPNAME', 'Modern-POS');
$timezone = 'America/Guatemala';
// $tmezone should be at line 3 
$timezone = 'UTC';

// date_default_timezone_set($timezone);
if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);


/*
 *---------------------------------------------------------------
 * SYSTEM ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 */
define('ENVIRONMENT', 'development');
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;
}

// Check PHP Version Number
if (version_compare(phpversion(), '5.6.0', '<') == true) {
	exit('PHP5.6+ Required');
}

// Windows IIS Compatibility
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['SCRIPT_FILENAME'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	if (isset($_SERVER['PATH_TRANSLATED'])) {
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if (!isset($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

// Check If SSL or Not
if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Load Config File
require_once __DIR__.DIRECTORY_SEPARATOR.'config.php';
define('PROTOCOL', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? 'https' : 'http');
$subdir = SUBDIRECTORY ? '/' . rtrim(SUBDIRECTORY, '/\\') : '';
define('ROOT_URL', PROTOCOL . '://' . rtrim($_SERVER['HTTP_HOST'], '/\\') . $subdir);

// Auto Load Library
function autoload($class) 
{
	$file = DIR_INCLUDE . 'lib/' . str_replace('\\', '/', strtolower($class)) . '.php';
	if (file_exists($file)) {
		include($file);
		return true;
	} else {
		return false;
	}
}
spl_autoload_register('autoload');
spl_autoload_extensions('.php');

require_once DIR_VENDOR . 'php-hooks/src/voku/helper/Hooks.php';

// Load Registry
$registry = new Registry();

// Session
if (!(PHP_SAPI === 'cli' OR defined('STDIN'))) {
$session = new Session($registry);
$registry->set('session', $session);
}

// log
$log = new Log('log.txt');
$registry->set('log', $log);

// Loader
$loader = new Loader($registry);
$registry->set('loader', $loader);

// Hook
$registry->set('hooks', $Hooks);

// DB CONFIG.
$dbhost = $sql_details['host'];
$dbname = $sql_details['db'];
$dbuser = $sql_details['user'];
$dbpass = $sql_details['pass'];
$dbport = $sql_details['port'];

// Helper Functions
require_once DIR_HELPER . 'utf8.php';
require_once DIR_HELPER . 'language.php';
require_once DIR_HELPER . 'network.php';
require_once DIR_HELPER . 'setting.php';
require_once DIR_HELPER . 'common.php';
require_once DIR_HELPER . 'countries.php';
require_once DIR_HELPER . 'file.php';
require_once DIR_HELPER . 'image.php';
require_once DIR_HELPER . 'pos.php';
require_once DIR_HELPER . 'pos_register.php';
require_once DIR_HELPER . 'box.php';
require_once DIR_HELPER . 'currency.php';
require_once DIR_HELPER . 'expense.php';
require_once DIR_HELPER . 'income.php';
require_once DIR_HELPER . 'customer.php';
require_once DIR_HELPER . 'invoice.php';
require_once DIR_HELPER . 'quotation.php';
require_once DIR_HELPER . 'purchase.php';
require_once DIR_HELPER . 'pmethod.php';
require_once DIR_HELPER . 'product.php';
require_once DIR_HELPER . 'report.php';
require_once DIR_HELPER . 'store.php';
require_once DIR_HELPER . 'supplier.php';
require_once DIR_HELPER . 'brand.php';
require_once DIR_HELPER . 'user.php';
require_once DIR_HELPER . 'usergroup.php';
require_once DIR_HELPER . 'validator.php';
require_once DIR_HELPER . 'category.php';
require_once DIR_HELPER . 'expense_category.php';
require_once DIR_HELPER . 'income_source.php';
require_once DIR_HELPER . 'unit.php';
require_once DIR_HELPER . 'taxrate.php';
require_once DIR_HELPER . 'giftcard.php';
require_once DIR_HELPER . 'banking.php';
require_once DIR_HELPER . 'bankaccount.php';
require_once DIR_HELPER . 'loan.php';
require_once DIR_HELPER . 'installment.php';
require_once DIR_HELPER . 'transfer.php';
require_once DIR_HELPER . 'postemplate.php';
require_once DIR_HELPER . 'sell_return.php';
require_once DIR_HELPER . 'purchase_return.php';

if (!is_cli()) {
	if (in_array(get_real_ip(), denied_ips())) {
		exit('You are not allowed to access!!!');
	}

	if (!empty(allowed_only_ips()) && !in_array(get_real_ip(), allowed_only_ips())) {
		exit('You are not allowed to access!!!');
	}
}

if (file_exists(ROOT.DIRECTORY_SEPARATOR.'.maintenance') && current_nav() != 'maintenance') {
	header('Location: '.root_url().'/maintenance.php', true, 302);
}

// DB Connection
try {
	$db = new Database("mysql:host={$dbhost};port={$dbport};dbname={$dbname};charset=utf8",$dbuser,$dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	function db()
	{
		global $db;
		return $db;
	}
}
catch(PDOException $e) {
	die('Connection error: '.$e->getMessage());
}
$registry->set('db', $db);

if ($dbname) {
    $statement = $db->prepare("SHOW TABLES");
    $statement->execute();
    if ($statement->rowCount() == 0) {
        print '<div style="color:#CC0000,font-weight:500;line-height:180%;width:60%;padding:15px;margin:25px auto;border-radius:5px;border:3px solid #AD0000;background-color:#FFFFD1;">';
        print(nl2br(file_get_contents('How-to-install.txt')));
        print '</div>';
        die();
        exit;
    }
}

// Request
$request = new Request();
$registry->set('request', $request);

// Store
$store = new Store($registry);
$registry->set('store', $store);

define('ESNECIL', 'POWERED-BY-BAB!ATO');

// Timezone
$timezone = get_preference('timezone') ? get_preference('timezone') : $timezone;
// if (!ini_get('date.timezone')) {
	if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
// }

// User
$user = new User($registry);
$registry->set('user', $user);

// Set Language
$user_preference = $user->getAllPreference();
if (isset($request->get['lang']) && $request->get['lang'] && $request->get['lang'] != 'null' && $request->get['lang'] != 'undefined') {
	if (!isset($request->get['ignore_lang_change'])) {
		unset($user_preference['language']);
		$user_preference['language'] = $request->get['lang'];
		$user->updatePreference($user_preference, user_id());
	}
}
if (!isset($user_preference['language']) || !$user_preference['language']) {
	$user_preference['language'] = 'en';
	$user->updatePreference($user_preference, user_id());
}
// if (isset($user_preference['language']) && $user_preference['language'] != 'en') {
// 	$user_preference['language'] = 'en';
// 	$user->updatePreference($user_preference, user_id());
// }

// Language
$active_lang = $user->getPreference('language', 'en');
$language = new Language($active_lang);
$registry->set('language', $language);
$language->load();

// Set Current Store By Query String
if (isset($request->get['active_store_id'])) 
{
	try {
		$store_id = $request->get['active_store_id'];
		$belongsStores = $user->getBelongsStore();
		$store_ids = array();
		foreach ($belongsStores as $the_store) {
			$store_ids[] = $the_store['store_id'];
		}
	    if ($user->getGroupId() != 1 && !in_array($store_id, $store_ids)) {
	      throw new Exception(trans('error_access_permission'));
	      exit();
	    }
		$store->openTheStore($store_id);

		header('Content-Type: application/json');
	    echo json_encode(array('msg' => trans('text_redirecting_to_dashbaord')));
	    exit();

	} catch (Exception $e) { 

		header('HTTP/1.1 422 Unprocessable Entity');
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode(array('errorMsg' => $e->getMessage()));
		exit();
	}
}

// Functions
include ('functions.php');

// Device Detection
$detect = new mobiledetect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

// Document
$document = new Document($registry);
$document->setBodyClass();
$registry->set('document', $document);

// Currency
$currency = new Currency($registry);
$registry->set('currency', $currency);

function registry()
{
	global $registry;
	return $registry;
}

// Datatable
require_once DIR_LIBRARY . 'ssp.class.php';

if (isset($request->get['check_for']) && $request->get['check_for'] == 'update')
{
    dd('Already Updated at: '.date('d-m-Y'));
}