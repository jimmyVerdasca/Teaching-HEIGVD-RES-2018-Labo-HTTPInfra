<?php
	$staticApp = getenv('STATIC_APP');
	$dynamicApp = getenv('DYNAMIC_APP');
?>
<VirtualHost *:80>
	ServerName demo.res.ch

	ProxyPass '/api/animals/' 'http://<?php print "$dynamicApp"?>:3000/'
	ProxyPassReverse '/api/animals/' 'http://<?php print "$dynamicApp"?>:3000/'
	  
	ProxyPass '/' 'http://<?php print "$staticApp"?>:80/'
	ProxyPassReverse '/' 'http://<?php print "$staticApp"?>:80/'
</VirtualHost>