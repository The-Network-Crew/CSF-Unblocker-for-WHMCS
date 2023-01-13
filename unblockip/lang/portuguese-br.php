<?php
global $_ADDONLANG;	
$_ADDONLANG['menu_label'] = "Liberar IP";
$_ADDONLANG['header'] = "Liberar IP";
$_ADDONLANG['header_description'] = "Se você ou um de seus clientes estão tendo problemas ao acessar seu site / servidor, use o formulário abaixo para verificar se o IP está bloqueado no nosso Firewall. O seu IP atual foi preenchido automaticamente abaixo, você pode substituir por outro IP para verificar e liberar um IP diferente.";
$_ADDONLANG["address_to_check"] = "IP a ser verificado";
$_ADDONLANG['check_for_ip'] = "Verificar e LIBERAR este IP";
$_ADDONLANG['cannot_unblock'] = "Não foi possível liberar este IP, ";
$_ADDONLANG['from'] = ", de ";
$_ADDONLANG['invalid_ip'] = "IP inválido";
$_ADDONLANG['cannot_connect'] = "Impossível conectar no";
$_ADDONLANG['contact_support'] = ". Por favor, contate o Suporte.<br>";
$_ADDONLANG['ip_was_removed'] = "Este IP foi liberado no servidor: ";
$_ADDONLANG['ip_was_found'] = "IP foin encontrado no servidor: ";
$_ADDONLANG['reason'] = "Razão do bloqueio: ";
$_ADDONLANG['no_block_found'] ="Este IP não foi encontrado no servidor: ";
$_ADDONLANG['max_unblocks'] = "Você atingiu o limete máximo de solicitações de desbloqueios de IPs. Por favor, tente novamente após ";
$_ADDONLANG['max_unblocks_2'] =" minutos para contatar o suporte.";

$_ADDONLANG['ip_address_is_blocked'] = "Your IP Address is blocked on your server and we are unable to remove the block automatically.";
$_ADDONLANG['please_open_a_support_ticket'] = "Please open a support ticket.";
$_ADDONLANG['detailed_block_information'] = "Informações sobre o Bloqueio:";
$_ADDONLANG['ip_block_auto_removed'] = "Seu IP estava bloqueado em nosso servidor. Se você estava tendo problemas de acesso, tente realizar novamente agora, pois seu IP foi desbloqueado.";
$_ADDONLANG['ip_auto_block_failed'] = "Seu IP se encontra bloqueado em nosso servidor, porém não podemos realizar o desbloqueio automaticamente.";
$_ADDONLANG['request_removal_of_block'] = "Solicitar Desbloqueio";
$_ADDONLANG['ip_address_is_blocked'] = "Seu IP se encontra bloqueado em nosso servidor, porém não podemos realizar o desbloqueio automaticamente.";
$_ADDONLANG['please_open_a_support_ticket'] = "Por favor, abra um ticket de suporte.";


/* Simplified Reasons */
$_ADDONLANG['sshd'] = 'Este IP estava bloqueado por multiplas tentativas de acesso SSH.';
$_ADDONLANG['smtpauth'] = 'Este IP estava bloqueado por multiplas tentativas de acesso SMTP/Email.';
$_ADDONLANG['pop3d'] = 'Este IP estava bloqueado por multiplas tentativas de acesso POP/Email.';
$_ADDONLANG['imapd'] = 'Este IP estava bloqueado por multiplas tentativas de acesso IMAP/Email.';
$_ADDONLANG['ftpd'] = 'Este IP estava bloqueado por multiplas tentativas de acesso FTP.';
$_ADDONLANG['cpanel'] = 'Este IP estava bloqueado por multiplas tentativas de acesso painel cPanel.';
$_ADDONLANG['mod_security'] = 'Este IP estava bloqueado por multiplas tentativas de acesso violando diretivas no Mod_Security.';
$_ADDONLANG['portscans'] = 'Este IP estava bloqueado por multiplas tentativas de acesso a múltiplas portas.';
$_ADDONLANG['PERMBLOCK'] = 'Este IP estava bloqueado por multiplas bloqueios temporários.';

?>