# (WHMCS Addon Module) CSF Unblocker for WHM

This extension provides the ability to self-service Unblock IPs from within WHMCS.

### Pre-requisites for usage:

- software-ConfigServer-csf (Reseller ACL in WHM needs this)
- whmuser:0:USE,ALLOW,DENY,GREP,UNBLOCK (/etc/csf/csf.resellers)

### Problems that need resolving:

- Client Area Output doesn't have necessary tokens; made elsewhere
- cPanel XML-API (deprecated 2017-2018) used, consider JSON-API

### Documentation for old/new APIs:

- https://github.com/CpanelInc/xmlapi-php
- https://api.docs.cpanel.net/whm/introduction

### Client Area - problematic code:

The breadcrumb, template, and other keys that you need to display the client area output are returned by the `unblockip_clientarea()` function in the `unblockip.php` file. These keys are returned as an array which includes 'pagetitle', 'breadcrumb', 'templatefile', 'requirelogin', and 'vars'. These keys are used by the WHMCS system to render the client area output.

In order to fix the issue with the missing keys, you need to make sure that the `unblockip_show()` function in the `hooks.php` file is calling the `unblockip_clientarea()` function in the `unblockip.php` file, and that it is passing the correct variables to it. Once that is done, you can use the returned array of keys to display the client area output.

ie. 

    function unblockip_show($vars) {
        search_for_ip_block($vars);
        $returned_values = unblockip_clientarea($vars); // call the unblockip_clientarea function and store the returned value
        if (!empty($returned_values)) {
            $smarty = new Smarty();
            $smarty->assign($returned_values);
            $smarty->caching = false;
            $smarty->compile_dir = $GLOBALS['templates_compiledir'];
            $output = $smarty->fetch(dirname(__FILE__) . '/templates/clientareaoutput.tpl');  
        } else {
            $output = "";
        }
        return $output;
    }

### Feature sets for the original:

- Allow your customers to easily unblock their ip from your cPanel and DirectAdmin servers CSF firewall.
- Checks for an IP Block across every cPanel and DirectAdmin server that is associated with an active service of the WHMCS Client.
- Uses a smarty template file to easily change look/feel of the addon.
- Only shows the link to the module in your menu if the client has an active cPanel service.
- Displays the csf log entry so the client can see the reason why the ip was blocked.
- Logs successful unblocks in the whmcs client log.
- For added security you can set maximum number of unblocks a client can issue in a configurable minute time period.
- Quickly search for and unblock an IP address from all active cPanel servers from the WHMCS admin area.
- Automatically check for and remove an ip block when upon client login to WHMCS (Optional, disabled by default).
- Free and open source under the BSD Modified License.
- Easy installation

### System Requirements (update!)

- WHMCS 5.0.3 or later
- cPanel properly configured in WHMCS
- CSF 5.40 or later

### CSF Implementation Method

- cpsess##########/cgi/configserver/csf.cgi?action=kill&ip=103.103.103.103
