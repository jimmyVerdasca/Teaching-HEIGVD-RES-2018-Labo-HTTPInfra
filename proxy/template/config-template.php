<?php
	$staticApp1 = getenv('STATIC_APP1');
	$dynamicApp1 = getenv('DYNAMIC_APP1');
	$staticApp2 = getenv('STATIC_APP2');
	$dynamicApp2 = getenv('DYNAMIC_APP2');
?>
<VirtualHost *:80>
	ServerName demo.res.ch

	<Proxy balancer://staticapp>
	    BalancerMember "http://<?php print "$staticApp1"?>"
	    BalancerMember "http://<?php print "$staticApp2"?>"
	</Proxy>

	<Proxy balancer://dynamicapp>
	    BalancerMember "http://<?php print "$dynamicApp1"?>"
	    BalancerMember "http://<?php print "$dynamicApp2"?>"
	</Proxy>

	ProxyPass '/api/animals/' 'balancer://dynamicapp'
	ProxyPassReverse '/api/animals/' 'balancer://dynamicapp'
	  
	ProxyPass '/' 'balancer://staticapp'
	ProxyPassReverse '/' 'balancer://staticapp'
</VirtualHost>