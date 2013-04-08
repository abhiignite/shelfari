<?php
function getConnection() {
	
	$url=parse_url(getenv("CLEARDB_DATABASE_URL"));
	
	$dbhost=$url["host"];
	$dbuser=$url["user"];
	$dbpass=$url["pass"];
	$dbname=substr($url["path"],1);

	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>