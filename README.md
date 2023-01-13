# (WHMCS Addon Module) CSF Unblocker for WHM

This extension provides the ability to self-service Unblock IPs from within WHMCS.

### Problems that need resolving:

- cPanel XML-API (deprecated 2017-2018) used, move to JSON-API
- Fatal errors during last attempts at using the Addon Module

### Documentation for old/nsw APIs:

- https://github.com/CpanelInc/xmlapi-php
- https://api.docs.cpanel.net/whm/introduction

### Feature sets for the original:

- Allow your customers to easily unblock their ip from your cPanel and DirectAdmin servers CSF firewall.
- Checks for an IP Block across every cPanel and DirectAdmin server that is associated with an active service of the WHMCS Client.
- Uses a smarty template filet to easily change look/feel of the addon.
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
