<?php
use Illuminate\Database\Capsule\Manager as Capsule;
function unblock_whm_session($ipaddress,$username,$token, $authmethod) {
    global  $attachments_dir;
    require_once  dirname(__FILE__) . "/xmlapi.php";
	
	try {
      $xmlapi = new xmlapi($ipaddress);
      
      if ($authmethod == "basic") {
        $xmlapi->password_auth($username,$token);
      }
      else {
        $xmlapi->hash_auth($username,$token);
      }
      $xmlapi->set_output("json");
    
      $session = $xmlapi->xmlapi_query("create_user_session", array("user" => $username, "service" => "whostmgrd",'api.version'=>1));
	  $session = json_decode($session);
	  
	
	  $curl = curl_init();
	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	  curl_setopt($curl, CURLOPT_COOKIESESSION, true );
	  $cookie_name = uniqid();
	  curl_setopt($curl, CURLOPT_COOKIEJAR, $attachments_dir . '/' . $cookie_name);
	  curl_setopt($curl, CURLOPT_URL, $session->data->url);
	  $r = curl_exec($curl);
	  curl_close($curl);
      return array("token" => $session->data->cp_security_token, "hostname" => parse_url($session->data->url,PHP_URL_HOST), "cookie" => $cookie_name);
	}
	catch(Exception $e) {
	  return false;	
	}
}

function remove_whm_session_cookie() {
	global $attachments_dir;
	$result = unlink($attachments_dir  . "/" . $_SESSION['whm_session_token']["cookie"]);
}  

function unblock_cphulk($server_ip, $whmuser, $whmauth, $authmethod,$ip_address, $srv_secure) {
  
  if ($srv_secure == "on") {
    $port = 2087;
  }
  else {
	$port = 2086;
  }
  
  $results = excecute_whm_csf_command("https://" . $_SESSION['whm_session_token']["hostname"] . ":" . $port . "/" . $_SESSION['whm_session_token']["token"] ."/json-api/flush_cphulk_login_history_for_ips?api.version=1&ip=" . $ip_address,$whmuser, $whmauth, $authmethod, $server_ip);

  $results = json_decode($results);

  if ($results->data->records_removed > 0) {
    return true;
  }
  else {
	return false;
  }  
}

function excecute_whm_csf_command($url,$whmuser, $whmauth, $authmethod, $ipaddress) {
  try {	
	if ($authmethod == "basic") {
	  $curl = curl_init();
	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	  $authstr = 'Authorization: Basic ' . base64_encode($whmuser .':'. $whmauth) . "\r\n";
	  $header[0] = $authstr;
	  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	  curl_setopt($curl, CURLOPT_URL, $url);
	  $r = curl_exec($curl);
	  if ($r == false) {
		error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $url");
	  }
	  curl_close($curl);
	  

	  return $r;	    
    }
    else {
	  global $attachments_dir;
	  $curl = curl_init();
  	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($curl, CURLOPT_URL, $url);
	  curl_setopt ($curl, CURLOPT_COOKIEFILE,   $attachments_dir  . "/" . $_SESSION['whm_session_token']["cookie"]);
	  $r = curl_exec($curl);
	  if ($r == false) {
		error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $url");
	  }
	  curl_close($curl);
	  return $r;

    }
  }
  catch (Exception $e) {
	  
  }
}

function search_for_ip($ip, $url, $whmuser, $whmauth, $authmethod, $_lang, $srv_ip, $debug = false) {
    $reason = "";
	$query_url = $url.'?action=grep&ip='.$ip;
	$data = excecute_whm_csf_command($query_url, $whmuser, $whmauth, $authmethod, $srv_ip);
	if ($debug == 1) {
	  echo "Debug: <br><pre><textarea>" . $data . "</textarea></pre><hr>";
	}
	
	$matches = array();
	$pattern = '/.*<td>(.*)<\/td>.*/s';
	preg_match($pattern, $data, $matches);

	$pattern = '/Temporary Blocks/';
	if (preg_match($pattern, $matches[1]) > 0) {
	  $pattern = '/\((.*)\)/s';
  	  preg_match($pattern, $matches[1], $matches);
  	  $reason = trim($matches[1]);
	}
	else {
	  // First check for a reason, if no reason is found check to see if the unblock link was displayed which indicates a block was found.
	  $pattern = '/\#(.*)/s';
  	  preg_match($pattern, $matches[1], $matches);
  	  if (!isset($matches[1])){
	     $pattern = '/action=kill&ip='.$ip.'(.*)/s';  
	     preg_match($pattern, $data, $matches);
	     if (isset($matches[1])) {
	       $reason = "Unknown";
	     }
  	  }
  	  else {
	    $reason = trim($matches[1]);
  	  }
  	  
	}
	
	if (strpos($reason,"sshd") !== false ) {
      $reason = $_lang["sshd"];
    }	
    elseif (strpos($reason,"smtpauth") !== false ) {
      $reason = $_lang["smtpauth"];
    }	
    elseif (strpos($reason,"pop3d") !== false ) {
      $reason = $_lang["pop3d"];
    }	
    elseif (strpos($reason,"imapd") !== false ) {
      $reason = $_lang["imapd"];
    }	
    elseif (strpos($reason,"ftpd") !== false ) {
      $reason = $_lang["ftpd"];
    }
    elseif (strpos($reason,"cpanel") !== false ) {
      $reason = $_lang["cpanel"];
    }
    elseif (strpos($reason,"mod_security") !== false ) {
      $reason = $_lang["mod_security"];
    }
    elseif (strpos($reason,"Port Scan") !== false ) {
      $reason = $_lang["portscans"];
    }
    elseif (strpos($reason,"PERMBLOCK") !== false ) {
      $reason = $_lang["PERMBLOCK"];
    }


	// filter out allowed ips
	$matches = array();
	$pattern = '/csf.allow/s';
	preg_match($pattern, $data, $matches);
	
	if (sizeof($matches) > 0) {
      return "";
	}
	else {
	  return $reason;		
	}
}

function number_of_recent_unblocks($whmcs_client_id, $unblock_interval) {
  return Capsule::table('tblactivitylog')->where('userid', $whmcs_client_id)->whereRaw('(date between DATE_SUB(now(), interval '. $unblock_interval .' minute) and now())')->whereRaw('description LIKE \'%Unblocked the IP Address%\'')->count();
}

function process_request($ip,$whmcs_client_id, $max_recent_blocks, $unblock_interval, $from_hook, $vars, $unblock = true) {

	$errors = "";
	$alerts = "";
	$unblocked_ip = false;
	$lang_file = $vars['_lang'];
	
	if (!filter_var($ip, FILTER_VALIDATE_IP)) {
	  $smartyvalues['errors'] = $lang_file["invalid_ip"];
	  return $smartyvalues;
	}
	
	
	if (number_of_recent_unblocks($whmcs_client_id, $unblock_interval) < $max_recent_blocks) {
	    $servers = Capsule::table('tblservers')->select(["tblservers.ipaddress","tblservers.username","tblservers.password","tblservers.accesshash","tblservers.name","tblservers.secure","tblservers.type"])->join("tblhosting","tblservers.id","=","tblhosting.server")->where('tblhosting.userid', $whmcs_client_id)->whereRaw("(tblservers.type = 'cpanel' or tblservers.type='directadmin' or tblservers.type='cpanelextended') and tblservers.disabled = 0 and tblhosting.domainstatus = 'Active'")->distinct()->get();
 
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
			} elseif ($srv_pass) {
				$authmethod = "basic";
				$authhash = decrypt($srv_pass);
				$auth_valid = true;
			} else {
				$errors = $errors . $lang_file['cannot_connect']. " ". $server->name;
			}
			
			if ($srv_secure == "on") {
	          if ($srv_type == "directadmin") {
	            $url = "https://$srv_ip:2222/CMD_PLUGINS_ADMIN/csf/index.html";
	          }
	          else {	    
	            $_SESSION['whm_session_token']  = unblock_whm_session($srv_ip,$srv_user,$authhash, $authmethod);
	            $url = "https://" . $_SESSION['whm_session_token']["hostname"] . ":2087/" . $_SESSION['whm_session_token']["token"] . "/cgi/configserver/csf.cgi";
	            
	          }
			} else {
	          if ($srv_type == "directadmin") {
	            $url = "http://$srv_ip:2222/CMD_PLUGINS_ADMIN/csf/index.html";
	          }
	          else {	    
	            $_SESSION['whm_session_token']  = unblock_whm_session($srv_ip,$srv_user,$authhash, $authmethod);
	            $url = "http://" . $_SESSION['whm_session_token']["hostname"] . ":2086/" .$_SESSION['whm_session_token']["token"] . "/cgi/configserver/csf.cgi";
	            
	          }
	        }
	        
	        if ( $_SESSION['whm_session_token'] === false || !$auth_valid) {
		       $errors = $errors . $lang_file['cannot_connect']. " ". $server->name; 
		       $auth_valid = false;
	        }
		
			if ($auth_valid) {
				
				if ($srv_type != "directadmin") {
				  if (unblock_cphulk($srv_ip, $srv_user, $authhash, $authmethod,$ip, $srv_secure)) {
					 $alerts = $alerts . $lang_file['ip_was_removed'] . $server->name . "<br>" .  $lang_file['reason']. $lang_file["cpanel"] . "<br>";
			         logActivity("Unblocked the IP Address " . $ip . " from " . $server->name . " - Reason: cPhulk - User ID: ".$whmcs_client_id);
			         $unblocked_ip = true;
				  }
				}
				$reason = search_for_ip($ip, $url, $srv_user, $authhash, $authmethod, $vars['_lang'], $srv_ip);
				if($vars['option4'] == "on" && strpos($reason,"do not delete") !== false) {
			      $errors = $errors . $lang_file['cannot_unblock'] .$ip . $lang_file['from'] . $server->name . $lang_file['contact_support'];
	            }
                else {
					if ($reason != "" && $unblock) {
	                  if ($srv_user != "root" && ($srv_type == 'cpanel' || $srv_type = 'cpanelextended')) {
				        $query_url = $url.'?action=qkill&ip='.$ip;  
				        $data = excecute_whm_csf_command($query_url, $srv_user, $authhash, $authmethod, $srv_ip);
				        if (!preg_match('/Unblock '.$ip.'/s', $data)) {
				          $query_url = $url.'?action=kill&ip='.$ip;
					      $data = excecute_whm_csf_command($query_url, $srv_user, $authhash, $authmethod, $srv_ip);
               	        }
			          }
			          else {
					    $query_url = $url.'?action=kill&ip='.$ip;
					    $data = excecute_whm_csf_command($query_url, $srv_user, $authhash, $authmethod, $srv_ip);
					  }
					  

					  $alerts = $alerts . $lang_file['ip_was_removed'] .  $server->name . "<br>" . $lang_file['reason']. $reason . "<br>";
			          logActivity("Unblocked the IP Address " . $ip . " from " . $server->name . " - Reason: " . $reason . " - User ID: ".$whmcs_client_id);
			          $unblocked_ip = true;
			        }
			        else {
			          if (!$from_hook && $reason == "" && !$unblocked_ip) {
			            $alerts = $alerts . $lang_file['no_block_found']. $server->name . "<br>";
			          }
			          elseif($reason != "") {
				         $alerts = $alerts . $lang_file['ip_was_found'] .  $server->name . "<br>" . $lang_file['reason']. $reason . "<br>";
			          }
			        }
		          }
	        }
		    remove_whm_session_cookie();		
	    }		
    }
    else {
      $unblocked_ip = true;
	  $errors = $lang_file['max_unblocks'] . $unblock_interval . $lang_file['max_unblocks2'];
    }
	
	$smartyvalues["errors"] = $errors;
	$smartyvalues["alerts"] = $alerts;
	$smartyvalues["unblocked_ip"] = $unblocked_ip;
	  
	return $smartyvalues;
}
?>
