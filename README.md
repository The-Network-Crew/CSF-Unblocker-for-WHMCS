# Firewall Unblocker (for CSF & WHMCS)

This extension provides the ability to self-service Unblock IPs from within WHMCS.

It supports CSF - ConfigServer Security & Firewall, typically used on cPanel+WHM.

**Locations of the Module Interfaces:**

- Admin GUI: Addons > Firewall Unblocker
- Client GUI: Support > Firewall Unblocker

## 🎯 (Module) System Requirements

**Server Integrated OK, with:**

- WHMCS v8.x latest
- CSF 14.x latest
- PHP latest

## 📋 (Module) Permission Requirements

#### WHM > Reseller ACL

Make sure the Reseller has access to this function:<br>
`software-ConfigServer-csf` (so it appears in WHM)

#### /etc/csf/csf.resellers

Add this line, replacing whmuser with the Reseller cP:<br>
`whmuser:0:USE,ALLOW,DENY,GREP,UNBLOCK` (so it works)

Optionally, you can change 0 to 1, to be alerted on-action.

## ✅ (Install) How to Upload & Config

- Download the latest version of the module
- Upload the module into `WHMCS_WEBROOT/modules/addons/`
- Visit WHMCS Admin > Settings > Addon Modules > enable it
- From there, it will be visible in the Admin & Client GUIs

## ⚙️ (FAQ) What can this module do?

- Allow your customers to unblock their IP from your Server's CSF firewall.
- Checks for IP Block on all cP Servers associated with an active client service.
- Uses a Smarty template file so you can easily change look/feel of the addon module.
- Only shows the link to the module in your menu if the client has an active cPanel.
- Displays the CSF log entry so the client can see the reason for the block.
- Logs successful unblocks in the WHMCS client log for potential review.
- Set max number of unblocks a client can do in a configured time period.
- Admin GUI: Quickly search for and remove blocks across all cPanel Servers.
- Optional: Auto-check for & remove a block when client logs in to WHMCS.

## 🏢 Corporate Sites: TNC & Merlot Digital

**The Network Crew Pty Ltd** :: https://tnc.works

**Merlot Digital** :: https://merlot.digital
