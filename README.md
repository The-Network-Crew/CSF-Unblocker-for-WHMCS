# (WHMCS Addon Module) Firewall Unblocker for CSF

This extension provides the ability to self-service Unblock IPs from within WHMCS.

### Pre-requisites for Usage:

- `software-ConfigServer-csf` (Reseller ACL in WHM needs this enabled)
- `whmuser:0:USE,ALLOW,DENY,GREP,UNBLOCK` (/etc/csf/csf.resellers)

### Installing the WHMCS Module:

- Download the latest version of the module from the repository
- Upload the unblockip/ folder into WHMCS_WEBROOT/modules/addons/
- Visit WHMCS Admin > Settings > Addon Modules > enable the Module
- From there, it will be visible in the Admin and Client interfaces

### What the Module does for you:

- Allow your customers to easily unblock their IP from your cPanel and DirectAdmin servers' CSF firewall.
- Checks for an IP Block across every cPanel and DirectAdmin server that is associated with an active service of the WHMCS Client.
- Uses a Smarty template file to easily change look/feel of the addon.
- Only shows the link to the module in your menu if the client has an active cPanel service.
- Displays the CSF log entry so the client can see the reason why the IP was blocked.
- Logs successful Unblocks in the WHMCS client log.
- For added security, you can set maximum number of Unblocks a client can issue in a configurable minute time period.
- Quickly search for and Unblock an IP Address from all active cPanel servers from the WHMCS admin area.
- Automatically check for and remove an IP Block when upon client login to WHMCS (Optional, disabled by default).
- Easy installation!

### System Requirements:

- Server Integrated OK
- WHMCS v8.x latest
- CSF 14.x latest
- PHP# latest
