<?php

$insert = 'INSERT INTO `user`
(
	`username`,
	`useremail`,
	`password`,
	`role`
)
VALUES
(
    "'. $db->real_escape_string( "sudo" ) .'",
    "'. $db->real_escape_string( "sudo@master.com" ). '",
	"'. $db->real_escape_string( password_hash( 'sudo', PASSWORD_BCRYPT ) ) .'",
	"suadm"
)';