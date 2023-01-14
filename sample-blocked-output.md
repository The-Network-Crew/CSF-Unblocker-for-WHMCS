<div class='panel panel-default'>
<h4>ConfigServer Security & Firewall - csf v14.17</h4></div>
<table class='table table-bordered table-striped'>
<tr><td><p>Searching for 103.103.103.103...</p>
<p><pre style='font-family: Courier New, Courier; font-size: 12px'>

Table  Chain            num   pkts bytes target     prot opt in     out     source               destination         

filter DENYIN           201      0     0 DROP       all  --  !lo    *       103.103.103.103      0.0.0.0/0

filter DENYOUT          201      0     0 LOGDROPOUT  all  --  *      !lo     0.0.0.0/0            103.103.103.103


ip6tables:

Table  Chain            num   pkts bytes target     prot opt in     out     source               destination         
No matches found for 103.103.103.103 in ip6tables

csf.deny: 103.103.103.103 # Manually denied: 103.103.103.103 (BD/Bangladesh/-) - Sat Jan 14 11:42:17 2023
</p>
<p>...<b>Done</b>.</p>
</td></tr></table>
<p><form action='csf.cgi' method='post'>
<input type="hidden" name="token" value="4ebc4d82f8e6a709c7161ac794a0307a4260aeeb" /><input type='submit' class='btn btn-default' value='Return'></form></p>
<br>
<pre>csf: v14.17</pre><p>©2006-2021, <a href='http://www.configserver.com' target='_blank'>ConfigServer Services</a> (Way to the Web Limited)</p>
