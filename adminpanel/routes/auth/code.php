<?php

/**
* @return string
**/
function getCodeForm ()
{
	return file_get_contents(__DIR__ . '/../../inc/code-form.html');
}

/**
 * @return boolean
 */
function saveCode ( mysqli $db, $code, $id )
{
	// Code und ID von User der den Code angefordert hat in DB speichern
	$insert = 'INSERT INTO
					auth_codes(code, id_user)
				VALUES 
				(
					"' . $code . '",
					"' . $id . '"
				)';

	return $db->query( $insert );
}

/**
* @return array
**/
function getCode ( $db, $code )
{
	// Code und ID von User der den Code angefordert hat aus DB auslesen
	$sql = 'SELECT
                code, id_user
            FROM
                auth_codes
            WHERE
            	code = "'. $code .'"';

    $dbRead = $db->query( $sql );

    $row = $dbRead->fetch_assoc();

    return $row;
}

function deleteCode ( $db, $code )
{
	$delete = 'DELETE FROM auth_codes WHERE code = "'. $code .'"';

    return $db->query( $delete );
}