<?php
use Illuminate\Database\Capsule\Manager as Capsule;	
require_once  dirname(__FILE__) . "/functions.php";

function unblockip_config() {
    $configarray = array(
    "name" => "CSF Unblock IP",
    "description" => "Allows clients to unblock an IP that has been blocked on a cPanel or DirectAdmin server by CSF.",
    "version" => "2.17",
    "author" => "The Network Crew Pty Ltd",
    "language" => "english",
    "fields" => array(
        "option1" => array ("FriendlyName" => "Max recent unblocks", "Type" => "text", "Size" => "5", "Description" => "Max unblocks a user can requests in the time period specified in the Minute interval option.", "Default" => "5", ),
        "option2" => array ("FriendlyName" => "Minute interval", "Type" => "text", "Size" => "5", "Description" => "How may minutes does a client need to wait before the max unblocks is reset.", "Default" => "5"),
        "option3" => array ("FriendlyName" => "Automatically check for an IP Block upon client login and unblock the ip address.", "Type" => "yesno", "Size" => "5", "Description" => "", "Default" => "",),
        "option5" => array ("FriendlyName" => "Automatically check for an IP Block upon client login but do not unblock the ip", "Type" => "yesno", "Size" => "5", "Description" => "", "Default" => "",),
        "option4" => array ("FriendlyName" => "Do not allow clients to remove ip addresses with do not delete in the deny comment", "Type" => "yesno", "Size" => "5", "Description" => "", "Default" => "", ),
    ));
    return $configarray;
}

function unblockip_clientarea($vars) {
  $modulelink = $vars['modulelink'];
  $max_unblocks = (int)$vars['option1'];
  $unblock_interval = (int)$vars['option2'];
  $lang_file = $vars['_lang'];

  // if max unblock option is not set or invalid, set to 5.  
  if ($max_unblocks == 0) {
    $max_unblocks = 5;
  }

  if ($unblock_interval == 0) { 
    $unblock_interval = 5;
  }
 
  $whmcs_client_id = (int)$_SESSION['uid'];
  $templatefile = "unblockip";
  
  if ($whmcs_client_id) { 
    if ($_POST['action'] == "remove_ip_block") {
	  check_token();
      $smartyvalues = process_request($_POST['ip_address'], $whmcs_client_id, $max_unblocks, $unblock_interval, false, $vars);
    }
  
  }
  else {
	  return false;
  }
  $smartyvalues["modulelink"] =  $modulelink;
  
  $smartyvalues["unblock_lang"] = $vars['_lang'];
  
   return array(
        'pagetitle' => $lang_file["header"],
        'breadcrumb' => array($modulelink=>$lang_file["header"]),
        'templatefile' => $templatefile,
        'requirelogin' => true, # or false
        'vars' => $smartyvalues,
    );
    
}


function unblockip_output($vars) {
  $modulelink = $vars['modulelink'];
  
  if ($_POST['action'] == "unblock") {
   
    if (!filter_var($_POST['ip_address'], FILTER_VALIDATE_IP)) {
      echo '<div class="errorbox">';
	  echo 'Invalid IP address <br>';
	  echo '</div>';
    }
    else {
      $results = process_admin_request($_POST['ip_address'],$_POST['server_id'], $vars, $_POST['debug']);
      if ($results['errors'] != "") {
        echo '<div class="errorbox">';
	   	echo $results['errors'] . "<br>";
	   	echo '</div>';
   	  }
   	  if ($results['alerts'] != "") {
	    echo '<div class="successbox">';
	   	echo $results['alerts'] . "<br>";
        echo '</div>';
   	  }
      if (!$results['block_found']) {
	    echo '<div class="successbox">';
	    if ($_POST['server_id'] == "all") {
	      echo "The IP " . $_POST['ip_address'] . " is not blocked on any active server.";  
	    }
	    else {
		  echo "The IP " . $_POST['ip_address'] . " is not blocked on ". Capsule::table('tblservers')->find($_POST['server_id'])->name;  
	    }
        echo '</div>';
      }
    }

   	echo "<hr>";
  }

   echo '
<h4>Query your Servers for a CSF Blockage of an IP Address.</h4>
<form action="'.$modulelink.'" method="POST">
<table class="form" width="80%" border="0" cellspacing="2" cellpadding="3">
<tr><td class="fieldlabel">Debug Mode</td><td class="fieldarea">
<input type="checkbox" name="debug" value="1"></td>
</tr>
<tr><td class="fieldlabel">Server</td><td class="fieldarea">

<select name="server_id">
<option value="all">Search all active Compatible Servers</option>

';
  
  $servers = Capsule::table('tblservers')->whereRaw("(type = 'cpanel' OR type='directadmin' or type = 'cpanelextended' ) and disabled = 0")->get();
  
  
  foreach($servers as $server) {
    echo "<option value='".$server->id ."'>" . $server->name . "</option>";	
  	
  }
echo
'</select>
</td></tr>
<tr><td class="fieldlabel">IP Address</td><td class="fieldarea">
<input type="text" name="ip_address" size="60">
</td></tr>
</table>
<br>
<input type="hidden" name="action" value="unblock">
<input type="submit" name="submit" value="Search and Unblock">
</form>';

 
}


function process_admin_request($ip, $server_id, $vars, $debug) {
   $errors = "";
   $alerts = "";
   $block_found = false;
   $results = array();
   
   if ($server_id == "all") {
	   $server_id = "";
   }
   else {
	   $server_id = " AND id = ". (int)$server_id;
   }

   $servers = Capsule::table('tblservers')->whereRaw("(type = 'cpanel' or type='directadmin' or type='cpanelextended') and disabled = 0 " . $server_id)->get();
   foreach($servers as $server) {
   
     $srv_ip = $server->ipaddress;
	 $srv_user = $server->username;
	 $srv_pass = $server->password;
	 $srv_hash = $server->accesshash;
	 $srv_secure = $server->secure;
	 $srv_type = $server->type;
			
	 $auth_valid = false;
	 if ($srv_hash) {
	   $authhash = preg_replace("'(\r|\n)'","",$srv_hash);
	   $authmethod = "accesshash";
	   $auth_valid = true;
	  } 
	  elseif ($srv_pass) {
	   $authmethod = "basic";
	   $authhash = decrypt($srv_pass);
	   $auth_valid = true;
	  } else {
	    $errors = $errors . "Cannot connect to ". $server->name;
	  }
			
	  if ($srv_secure) {
	    if ($srv_type == "directadmin") {
	      $url = "https://$srv_ip:2222/CMD_PLUGINS_ADMIN/csf/index.html";
	    }
	    else {	    
          $_SESSION['whm_session_token']  = unblock_whm_session($srv_ip,$srv_user,$authhash,$authmethod);
	      $url = "https://" . $_SESSION['whm_session_token']['hostname'] . ":2087/" . $_SESSION['whm_session_token']["token"] . "/cgi/configserver/csf.cgi";	 
	    }
	  } 
	  else {
	    if ($srv_type == "directadmin") {
	      $url = "http://$srv_ip:2222/CMD_PLUGINS_ADMIN/csf/index.html";
	    }
	    else {	    
           $_SESSION['whm_session_token']  = unblock_whm_session($srv_ip,$srv_user,$authhash, $authmethod);
	       $url = "http://" . $_SESSION['whm_session_token']['hostname'] . ":2086/" .$_SESSION['whm_session_token']["token"] . "/cgi/configserver/csf.cgi";
	    }
	  }
	 
	  if ( $_SESSION['whm_session_token'] === false || !$auth_valid) {
		$errors = $errors . "Cannot connect to ". $server->name; 
		$auth_valid = false;
	  }
				
	  if ($auth_valid) {
	    if ($srv_type != "directadmin") {
		 if (unblock_cphulk($srv_ip, $srv_user, $authhash, $authmethod,$ip,$srv_secure)) {
		  $alerts = $alerts . $lang_file['ip_was_removed'] . $server->name . "<br>" .  $lang_file['reason']. $lang_file["cpanel"] . "<br>";
		  logActivity("Unblocked the IP Address " . $ip . " from " . $server->name . " - Reason: cPhulk - User ID: ".$whmcs_client_id);
		  $unblocked_ip = true;
		 }
		}
	    $reason = search_for_ip($ip, $url, $srv_user, $authhash, $authmethod, $vars['_lang'], $srv_ip, $debug);

		if ($reason != "") {
			  if ($srv_user != "root" && ($srv_type == 'cpanel' || $srv_type == 'cpanelextended')) {
				$query_url = $url.'?action=qkill&ip='.$ip;  
			  }
			  else {
	   		    $query_url = $url.'?action=kill&ip='.$ip;  
			  }
			  $data = excecute_whm_csf_command($query_url, $srv_user, $authhash, $authmethod, $srv_ip);
   	          if (!preg_match('/Unblock '.$ip.'/s', $data)) {
	            $query_url = $url.'?action=kill&ip='.$ip;
	  	        $data = excecute_whm_csf_command($query_url, $srv_user, $authhash, $authmethod, $srv_ip);
              }

			  $alerts = $alerts . "IP Block was removed from server: " . $server->name . "<br>" . "Reason: ". $reason . "<br>";
			  logActivity("Unblocked the IP Address " . $ip . " from " . $server->name . " - Reason: ". $reason);
	   	      $block_found = true;
			}
   	    } 		
   	   remove_whm_session_cookie();
   }   

   $results['errors'] = $errors;
   $results['alerts'] = $alerts;
   $results['block_found'] = $block_found;
   return $results;
}

?>
