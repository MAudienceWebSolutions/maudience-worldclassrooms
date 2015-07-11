<?php 

	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 3600'); // 1 hour = 3600 seconds
	mail("pete@maudience.com", "Database Error", "Database Error: ".$_SERVER['SERVER_NAME'].";", "From: Maudience");

?>
<!DOCTYPE HTML>
<html dir="ltr" lang="en-US">
	<head>
		<title><?php echo $_SERVER['SERVER_NAME']; ?> | 503 Service Temporarily Unavailable</title>
		<style type="text/css">
			h1, h2, p {
				font-family: Helvetica, sans-serif;
				font-size: 24px;
				color: #333;
				}
			p {
				font-size: 14px;
				}
		</style>
	</head>
	<body>
		<center><img src="http://www.maudience.com/wp-content/uploads/2014/08/iowa-city-web-design-maudience-logo.png" alt="Iowa City Web Design and Development - Maudience Marketing" id="logo"></center>
		<center>
		<h1>Server update in progress.</h1>
		<p><a href='http://maudience.com'>Maudience</a> is currently optimizing and/or performing routine server maintenance!</p>
		<br />
		<h2>Please be patient </h2>
		<p><strong><?php echo $_SERVER['SERVER_NAME']; ?></strong></p>
		<p> will be back up shortly</p>
		</center>
	</body>
</html>