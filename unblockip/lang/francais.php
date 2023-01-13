<?php
global $_ADDONLANG;
$_ADDONLANG['menu_label'] = "Unblock IP Address";
$_ADDONLANG['header'] = "V&eacute;rification d'IP bloqu&eacute;s";
$_ADDONLANG['header_description'] = "Si vous ou un de vos clients rencontrez des probl&egrave;mes pour acc&eacute;der &agrave; votre site web, vos courriels ou au serveur, veuillez utiliser le champ ci-dessous afin de v&eacute;rifier si cette adresse IP est bloqu&eacute;e dans notre pare-feu. Votre adresse IP actuelle est automatiquement inscrite ci-dessous. Au besoin, remplacez-la pour en v&eacute;rifier une autre.";
$_ADDONLANG["address_to_check"] = "Adresse IP &agrave; v&eacute;rifier";
$_ADDONLANG['check_for_ip'] = "V&eacute;rifier et retirer un IP bloqu&eacute;";
$_ADDONLANG['cannot_unblock'] = "Impossible de d&eacute;bloquer cette adresse IP, ";
$_ADDONLANG['from'] = ", de ";
$_ADDONLANG['invalid_ip'] = "Adresse IP invalide";
$_ADDONLANG['cannot_connect'] = "Impossible de se connecter &agrave;";
$_ADDONLANG['contact_support'] = ". Veuillez nous contacter via un billet de support technique.<br>";
$_ADDONLANG['ip_was_removed'] = "L'adresse IP a &eacute;t&eacute; retir&eacute; du serveur. ";
$_ADDONLANG['ip_was_found'] = "L'adresse IP bloqu&eacute;e a &eacute;t&eacute; trouv&eacute;e sur le serveur: ";
$_ADDONLANG['reason'] = "Raison: ";
$_ADDONLANG['no_block_found'] ="Aucune adresse IP bloqu&eacute;e n'a &eacute;t&eacute; trouv&eacute;e sur ce serveur: ";
$_ADDONLANG['max_unblocks'] = "Vous avez atteint le nombre maximum de demandes de d&eacute;blocage permis. Veuillez r&eacute;essayer dans";
$_ADDONLANG['max_unblocks_2'] =" minutes ou veuillez nous contacter via un billet de support technique";


/* Needs Translating */
$_ADDONLANG['detailed_block_information'] = "Detailed Block Information:";
$_ADDONLANG['ip_block_auto_removed'] = "Your IP Address was blocked on your server, if you were having issues accessing your server, please try again now. The block has been removed.";
$_ADDONLANG['ip_auto_block_failed'] = "Your IP Address is blocked on your server and we are unable to remove the block automatically.";
$_ADDONLANG['request_removal_of_block'] = "Request Removal of Block";
$_ADDONLANG['ip_address_is_blocked'] = "Your IP Address is blocked on your server and we are unable to remove the block automatically.";
$_ADDONLANG['please_open_a_support_ticket'] = "Please open a support ticket.";

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
