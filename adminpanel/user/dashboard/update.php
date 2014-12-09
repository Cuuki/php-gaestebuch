<?php

/**
 * TODO: Doctrine
 * @return boolean
 */
function updateUser ( $db, array $params, $id )
{
	$update =	'UPDATE user
				SET
                    username = "'. $db->real_escape_string( $params["username"] ) .'",
                    useremail = "'. $db->real_escape_string( $params["useremail"] ). '",
                    password = "'. $db->real_escape_string( password_hash( $params["password"], PASSWORD_BCRYPT ) ) .'"
				WHERE
					id = "'. $id .'"';

	$dbRead = $db->query( $update );

    return $dbRead;
}

/**
 * TODO: Doctrine
 * @return boolean
 */
function updateUsername ( $db, $username, $id )
{
    $update =   'UPDATE user
                SET
                    username = "'. $db->real_escape_string( $username ) .'"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

/**
 * TODO: Doctrine
 * @return boolean
 */
function updateEmail ( $db, $email, $id )
{
    $update =   'UPDATE user
                SET
                    useremail = "'. $db->real_escape_string( $email ). '"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

/**
 * TODO: Doctrine
 * @return boolean
 */
function updatePassword ( $db, $password, $id )
{
    $update =   'UPDATE user
                SET
                    password = "'. $db->real_escape_string( password_hash( $password, PASSWORD_BCRYPT ) ) .'"
                WHERE
                    id = "'. $id .'"';

    $dbRead = $db->query( $update );

    return $dbRead;
}

