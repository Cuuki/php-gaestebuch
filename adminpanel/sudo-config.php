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
    "' . $db->real_escape_string( "sudo" ) . '",
    "' . $db->real_escape_string( "sudo@master.com" ) . '",
	"' . $db->real_escape_string( password_hash( "sudo", PASSWORD_BCRYPT ) ) . '",
	"suadm"
)';

// lege superuser an, wenn nicht schon vorhanden
if ( getLogindata( $db, 'sudo' )[0] == NULL )
{
    $db->query( $insert );
}