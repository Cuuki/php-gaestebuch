<?php

/**
 * TODO: Doctrine
 * @return boolean
 */
function updatePost ( mysqli $db, array $params, $id )
{
	$update =	'UPDATE guestbook
				SET
                    firstname = "'. $db->real_escape_string( $params["firstname"] ) .'",
                    lastname = "'. $db->real_escape_string( $params["lastname"] ). '",
                    email = "'. $db->real_escape_string( $params["email"] ). '",                    
                    content = "'. $db->real_escape_string( $params["textinput"] ) .'"
				WHERE
					id_entry = "'. $id .'"';

	$dbRead = $db->query( $update );

    return $dbRead;
}

