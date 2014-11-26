<?php

$dboptions = array(
	"Hostname" => "localhost",
	"Username" => "root",
	"Password" => "XDrAgonStOrM129",
	"Databasename" => "gaestebuch"
);

$db = dbConnect( $dboptions );

$db->query("SET NAMES utf8");