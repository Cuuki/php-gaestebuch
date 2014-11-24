<?php

error_reporting(-1);
ini_set('log_errors', 1);

$dboptions = array(
	"Hostname" => "localhost",
	"Username" => "root",
	"Password" => "XDrAgonStOrM129",
	"Databasename" => "gaestebuch"
);

$db = dbConnect( $dboptions );

$db->query("SET NAMES utf8");