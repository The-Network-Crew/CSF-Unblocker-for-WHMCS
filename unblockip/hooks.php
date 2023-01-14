<?php
// WHMCS Firewall Unblocker - hooks.php
// https://github.com/LEOPARD-host/WHMCS-Firewall-Unblocker-CSF

use WHMCS\User\Client;
use Illuminate\Database\Capsule\Manager as Capsule;


// Registered against the ClientAreaHomepage hook point near EOF
function unblockip_show($vars) {

	search_for_ip_block($vars);
	$returned_values = array();
	$whmcs_client_id = (int)$_SESSION['uid'];

	$num_services = Capsule::table('tblservers')->select(["tblservers.ipaddress","tblservers.username","tblservers.password","tblservers.accesshash","tblservers.name","tblservers.secure","tblservers.type"])->join("tblhosting","tblservers.id","=","tblhosting.server")->where('tblhosting.userid', $whmcs_client_id)->whereRaw("(tblservers.type = 'cpanel' or tblservers.type='directadmin' or tblservers.type='cpanelextended') and tblservers.disabled = 0 and tblhosting.domainstatus = 'Active'")->count();

	if ($num_services == 0) {
		$isactive = 0;		
	}
	else {
		$isactive = 1;
		if (isset($_SESSION['unblockip_results'])) {
			$unblock_results = $_SESSION['unblockip_results'];
			$smartyvalues["unblockip"] = $unblock_results['unblocked_ip'];
			$smartyvalues["unblockip_alerts"] = $unblock_results['alerts'];
			$smartyvalues["unblockip_errors"] = $unblock_results['errors'];
			$smartyvalues["donotautounblock"] = Capsule::table('tbladdonmodules')->where('module','unblockip')->where('setting','option5')->first()->value;
			$smartyvalues["unblockip_isactive"] = $isactive;
			unset($_SESSION['unblockip_results']);

			$current_user = Client::find((int)$_SESSION['uid']);

			$lang = $current_user->language;
			if ($lang == "") {
				global $CONFIG;
				$lang = $CONFIG["Language"];
			}

			$_ADDONLANG = array();

			if ( file_exists(dirname(__FILE__) . "/lang/". strtolower($lang.".php"))  ) {
				require(dirname(__FILE__) . "/lang/". strtolower($lang.".php"));
			}
			else {
				require(dirname(__FILE__) . "/lang/english.php");	      
			}


			$smartyvalues["unblock_lang"] = $_ADDONLANG;

			$smarty = new Smarty();
			$smarty->assign($smartyvalues);
			$smarty->caching = false;
			$smarty->compile_dir = $GLOBALS['templates_compiledir'];
			$output = $smarty->fetch(dirname(__FILE__) . '/templates/clientareaoutput.tpl');  
		}
	}
	
	return $output;
}


// Registered against the ClientLogin hook point near EOF
function trigger_unblock_search($vars) {
	require_once("functions.php");
	$_SESSION['run_unblock_search'] = true;
}


// Registered against the ClientAreaPage hook point near EOF
function search_for_ip_block($vars) {
	if ($_SESSION['run_unblock_search']) {
		$_SESSION['run_unblock_search'] = false;
		require_once(dirname(__FILE__) . "/functions.php");

		$current_user = Client::find((int)$_SESSION['uid']);

		$lang = $current_user->language;
		if ($lang == "") {
			global $CONFIG;
			$lang = $CONFIG["Language"];
		}

		$_ADDONLANG = array();

		if ( file_exists(dirname(__FILE__) . "/lang/". strtolower($lang.".php"))  ) {
			require(dirname(__FILE__) . "/lang/". strtolower($lang.".php"));
		}
		else {
			require(dirname(__FILE__) . "/lang/english.php");	      
		}

		$vars["_lang"] = $_ADDONLANG;
		$whmcs_client_id = (int)$_SESSION['uid'];

		$max_unblocks = (int)Capsule::table('tbladdonmodules')->where('module','unblockip')->where('setting','option1')->first()->value;
		$unblock_interval = (int)Capsule::table('tbladdonmodules')->where('module','unblockip')->where('setting','option2')->first()->value;
		$run_automatic_ip_check = Capsule::table('tbladdonmodules')->where('module','unblockip')->where('setting','option3')->first()->value;
		$run_automatic_ip_check_dont_unblock = Capsule::table('tbladdonmodules')->where('module','unblockip')->where('setting','option5')->first()->value;

		if ($run_automatic_ip_check == "on") {
	      // if max unblock option is not set or invalid, set to 5.  
			if ($max_unblocks == 0) {
				$max_unblocks = 5;
			}

			if ($unblock_interval == 0) { 
				$unblock_interval = 5;
			}
			$unblock_results = process_request($_SERVER['REMOTE_ADDR'], $whmcs_client_id, $max_unblocks, $unblock_interval, true, $vars);

			if ($unblock_results['unblocked_ip'] == true) {
				$_SESSION['unblockip_results'] = $unblock_results;
			}  
		}

		if ($run_automatic_ip_check_dont_unblock == "on") {
	      // if max unblock option is not set or invalid, set to 5.  
			if ($max_unblocks == 0) {
				$max_unblocks = 5;
			}

			if ($unblock_interval == 0) { 
				$unblock_interval = 5;
			}

			$unblock_results = process_request($_SERVER['REMOTE_ADDR'], $whmcs_client_id, $max_unblocks, $unblock_interval, true, $vars, false);
			if ($unblock_results['alerts'] != "") {
				$_SESSION['unblockip_results'] = $unblock_results;
			}  
		}
	}
}


// Register the hooks against the above functions
add_hook("ClientAreaPage",100,"search_for_ip_block");
add_hook("ClientAreaHomepage", 2,"unblockip_show");
add_hook("ClientLogin",1,"trigger_unblock_search");

add_hook('ClientAreaNavbars', 1, function ()
{
	$user_id =$_SESSION['uid'];
	$current_user = Client::find($user_id);

	if (isset($_SESSION['uid']) && !isset($_SESSION['unblockip_isactive'])) {
		$num_services = Capsule::table('tblservers')->select(["tblservers.ipaddress","tblservers.username","tblservers.password","tblservers.accesshash","tblservers.name","tblservers.secure","tblservers.type"])->join("tblhosting","tblservers.id","=","tblhosting.server")->where('tblhosting.userid',  $user_id)->whereRaw("(tblservers.type = 'cpanel' or tblservers.type='directadmin' or tblservers.type='cpanelextended') and tblservers.disabled = 0 and tblhosting.domainstatus = 'Active'")->count();


		if ($num_services > 0) {
			$_SESSION["unblockip_isactive"] = true;
		}
		else {
			$_SESSION["unblockip_isactive"] = false;
		}
	}

	if (isset($_SESSION['uid']) && $_SESSION["unblockip_isactive"]) {
		$current_user = Client::find($user_id);

		$lang = $current_user->language;
		if ($lang == "") {
			global $CONFIG;
			$lang = $CONFIG["Language"];
		}
		$_ADDONLANG = array();

		if ( file_exists(dirname(__FILE__) . "/lang/". strtolower($lang.".php"))  ) {
			require(dirname(__FILE__) . "/lang/". strtolower($lang.".php"));
		}
		else {
			require(dirname(__FILE__) . "/lang/english.php");	      
		}

		$primaryNavbar = Menu::primaryNavbar();
		$secondaryNavbar = Menu::secondaryNavbar();
		$support_link = $primaryNavbar->getChild('Support');

		$support_link->addChild('unblock-ip', array(
			'label' => $_ADDONLANG["menu_label"],
			'uri' => 'index.php?m=unblockip',
			'order' => 1,
		)); 	
	}
});

?>