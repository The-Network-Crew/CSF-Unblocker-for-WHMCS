<?php
global $_ADDONLANG;
$_ADDONLANG['menu_label'] = "Desbloquear IP del Servidor";
$_ADDONLANG['header'] = "Desbloquear IP del Servidor";
$_ADDONLANG['header_description'] = "Desde aquí puedes comprobar si tu dirección IP ha sido bloqueada en algún servidor. Revisa bloqueos de web, ftp, email y acceso a cPanel. Tu dirección IP actual se muestra automáticamente. También puedes cambiarla por otra diferente.";
$_ADDONLANG["address_to_check"] = "Dirección IP para Comprobar";
$_ADDONLANG['check_for_ip'] = "Comprobar y quitar el bloqueo";
$_ADDONLANG['cannot_unblock'] = "No se puede desbloquear la dirección IP, ";
$_ADDONLANG['from'] = ", de ";
$_ADDONLANG['invalid_ip'] = "Dirección IP Inválida";
$_ADDONLANG['cannot_connect'] = "No se puede conectar a";
$_ADDONLANG['contact_support'] = ". Porfavor contacte con Soporte.<br>";
$_ADDONLANG['ip_was_removed'] = "La IP a sido eliminada del Servidor: ";
$_ADDONLANG['ip_was_found'] = "Se encontró tu IP bloqueada en el Servidor: ";
$_ADDONLANG['reason'] = "Razón: ";
$_ADDONLANG['no_block_found'] ="No se encontró bloqueo de la IP en el Servidor: ";
$_ADDONLANG['max_unblocks'] = "Has llegado a la cantidad máxima de solicitudes de desbloqueos. Porfavor inténtelo nuevamente en ";
$_ADDONLANG['max_unblocks_2'] =" minutos o contacte con Soporte.";

/* Needs Translating */
$_ADDONLANG['detailed_block_information'] = "Detailed Block Information:";
$_ADDONLANG['ip_block_auto_removed'] = "Your IP Address was blocked on your server, if you were having issues accessing your server, please try again now. The block has been removed.";
$_ADDONLANG['ip_auto_block_failed'] = "Your IP Address is blocked on your server and we are unable to remove the block automatically.";
$_ADDONLANG['request_removal_of_block'] = "Request Removal of Block";
$_ADDONLANG['ip_address_is_blocked'] = "Your IP Address is blocked on your server and we are unable to remove the block automatically.";
$_ADDONLANG['please_open_a_support_ticket'] = "Please open a support ticket.";

/* Simplified Reasons */
$_ADDONLANG['sshd'] = 'Tu IP ha sido bloqueada por accesos fallidos por SSH.';
$_ADDONLANG['smtpauth'] = 'Tu IP ha sido bloqueada por intentos fallidos de autenticación en el SMTP/Email.';
$_ADDONLANG['pop3d'] = 'Tu IP ha sido bloqueada por accesos fallidos utilizando POP/Email.';
$_ADDONLANG['imapd'] = 'Tu IP ha sido bloqueada por varios fallidos utilizando IMAP/Email.';
$_ADDONLANG['ftpd'] = 'Tu IP ha sido bloqueada por varios accesos fallidos utilizando FTP.';
$_ADDONLANG['cpanel'] = 'Tu IP ha sido bloqueada por varios accesos fallidos utilizando cPanel.';
$_ADDONLANG['mod_security'] = 'Tu IP ha sido bloqueada porque ha inclumplido una regla de seguridad mod.';
$_ADDONLANG['portscans'] = 'Tu IP ha intentado conectar con el servidor por puertos no permitidos.';
$_ADDONLANG['PERMBLOCK'] = 'Tu IP ha sido bloqueada por tener múltiples bloqueos temporales.';


?>