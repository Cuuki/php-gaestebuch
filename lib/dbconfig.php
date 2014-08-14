<?php

error_reporting(-1);
ini_set('log_errors', 1);

$dboptions = array(
	"Hostname" => "localhost",
	"Username" => "root",
	"Password" => "",
	"Databasename" => "gaestebuch"
);

$db = dbConnect( $dboptions );

$db->query("SET NAMES utf8");