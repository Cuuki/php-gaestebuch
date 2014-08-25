<?php

use Symfony\Component\HttpFoundation\Response;

function form ()
{
	$form = file_get_contents( __DIR__ . '/../../inc/add.html' );

	return new Response( $form );
};

/**
 * @return array
 */
function sanitizeLogindata ( array $params )
{
	$data = array(
		"username" => filter_var( trim($params["username"]), FILTER_SANITIZE_STRING ),
		"useremail" => filter_var( trim($params["useremail"]), FILTER_VALIDATE_EMAIL ),
		"password" => filter_var( trim($params["password"]), FILTER_SANITIZE_STRING )
	);

	return $data;
}

/**
 * @return int
 */
function saveLogindata ( array $params, mysqli $db )
{
    $insert = 'INSERT INTO
                    user(username, useremail, password)
                VALUES
                (
                    "'. $db->real_escape_string( $params["username"] ) .'",
                    "'. $db->real_escape_string( $params["useremail"] ). '",
                    "'. $db->real_escape_string( $params["password"] ) .'"
                )';
    
    $db->query( $insert );

    return $db->insert_id;
}