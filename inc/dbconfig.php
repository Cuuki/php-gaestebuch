<?php

$dboptions = array(
	"Hostname" => "localhost",
	"Username" => "root",
	"Password" => "",
	"Databasename" => "gaestebuch"
);

$db = dbConnect( $dboptions );

$db->query("SET NAMES utf8");