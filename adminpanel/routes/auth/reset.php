<?php

/**
* @return string
**/
function getResetForm ()
{
	return file_get_contents( __DIR__ . '/../../inc/reset-form.html' );
};

/**
* @return array
**/
function getMail ( $db, $postdata )
{
	// E-Mail von Eingabe aus DB auslesen
	$sql = 'SELECT
                id, useremail
            FROM
                user
            WHERE
            	useremail = "'. $postdata .'"';

    $dbRead = $db->query( $sql );

    $row = $dbRead->fetch_assoc();

    return $row;
}