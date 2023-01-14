<?php
global $_ADDONLANG;
$_ADDONLANG['menu_label'] = "Firewall Unblocker";
$_ADDONLANG['header'] = "Firewall Unblocker";
$_ADDONLANG['header_description'] = "If you or one of your clients are having issues accessing your website/server, use the form below to check if you or your clients IP is blocked in the firewall. Your current IP Address is automatically filled in below, you may overwrite your IP to check another.";
$_ADDONLANG["address_to_check"] = "IP Address to check";
$_ADDONLANG['check_for_ip'] = "Check and Unblock";
$_ADDONLANG['cannot_unblock'] = "Cannot unblock the IP Address, ";
$_ADDONLANG['from'] = ", from ";
$_ADDONLANG['invalid_ip'] = "Invalid IP Address";
$_ADDONLANG['cannot_connect'] = "Could not connect to";
$_ADDONLANG['contact_support'] = ". Please contact Tech Support.<br>";
$_ADDONLANG['ip_was_removed'] = "IP Block was removed from server: ";
$_ADDONLANG['ip_was_found'] = "IP Block was found on server: ";
$_ADDONLANG['reason'] = "Reason: ";
$_ADDONLANG['no_block_found'] ="No block found on server: ";
$_ADDONLANG['max_unblocks'] = "You have reached the maximum amount of unblock requests. Please try your request again in ";
$_ADDONLANG['max_unblocks_2'] =" minutes or contact Tech Support.";


$_ADDONLANG['detailed_block_information'] = "Detailed Block Information:";
$_ADDONLANG['ip_block_auto_removed'] = "Your IP Address was blocked on the server - if you were having issues accessing your server, please try again now. The block has been removed.";
$_ADDONLANG['ip_auto_block_failed'] = "Your IP Address is blocked on the server and we are unable to remove it automatically.";
$_ADDONLANG['request_removal_of_block'] = "Request Removal of Block";
$_ADDONLANG['ip_address_is_blocked'] = "Your IP Address is blocked on your server and we are unable to remove it automatically.";
$_ADDONLANG['please_open_a_support_ticket'] = "Please open a Support Ticket.";


/* Simplified Reasons */
$_ADDONLANG['sshd'] = 'Your IP was blocked due to multiple SSH login failures';
$_ADDONLANG['smtpauth'] = 'Your IP was blocked due to multiple SMTP/Email login failures';
$_ADDONLANG['pop3d'] = 'Your IP was blocked due to multiple POP/Email login failures';
$_ADDONLANG['imapd'] = 'Your IP was blocked due to multiple IMAP/Email login failures';
$_ADDONLANG['ftpd'] = 'Your IP was blocked due to multiple FTP login failures';
$_ADDONLANG['cpanel'] = 'Your IP was blocked due to multiple cPanel login failures';
$_ADDONLANG['mod_security'] = 'Your IP was blocked because a mod security rule was triggered';
$_ADDONLANG['portscans'] = 'Your IP accessed one or more ports that are closed in the servers firewall';
$_ADDONLANG['PERMBLOCK'] = 'Your IP has had multiple temporary IP blocks.';
?>