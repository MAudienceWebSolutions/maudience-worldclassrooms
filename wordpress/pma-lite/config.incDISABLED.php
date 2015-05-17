<?php

/* Servers configuration */
$i = 0;

/* Server: localhost [1] */
$i++;
$cfg['Servers'][$i]['verbose'] = 'localhost';
$cfg['Servers'][$i]['port'] = '';
$cfg['Servers'][$i]['socket'] = '';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['extension'] = 'mysql';
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['AllowNoPassword'] = false;

$cfg['Servers'][$i]['verbose'] = 'server1';

$cfg['Servers'][$i]['host'] = 'localhost'; 
if (0) {
	/*
	define('DB_NAME', 'myworlf5_wrd1');
	define('DB_USER', 'myworlf5_wrd1');
	define('DB_PASSWORD', 'DgxInLDKCD');
*/
} else {
	$cfg['Servers'][$i]['user'] = 'myworlf5_wrd1';
	$cfg['Servers'][$i]['password'] = 'DgxInLDKCD';
}

/* End of servers configuration */

$cfg['DefaultLang'] = 'en-utf-8';
$cfg['ServerDefault'] = 1;
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';


/* rajk - for blobstreaming */
$cfg['Servers'][$i]['bs_garbage_threshold'] = 50;
$cfg['Servers'][$i]['bs_repository_threshold'] = '32M';
$cfg['Servers'][$i]['bs_temp_blob_timeout'] = 600;
$cfg['Servers'][$i]['bs_temp_log_threshold'] = '32M';


?>
