<?php

error_reporting(-1);
ini_set('log_errors', 1);

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
                    "'. $db->real_escape_string( password_hash( $params["password"], PASSWORD_BCRYPT ) ) .'"
                )';
    
    $db->query( $insert );

    return $db->insert_id;
}

/**
 * @return array
 */
function getLogindata ( mysqli $db, $username )
{
    $sql = 'SELECT
                username, useremail, password
            FROM
                user
            WHERE
                username = "'. $username .'"';

    $dbRead = $db->query( $sql );
    $logindata = array();
    $row = $dbRead->fetch_assoc();
    array_push($logindata, $row);

    return $logindata;
}

/**
 * @return boolean
 */
function login ( mysqli $db, array $params )
{

}

/**
 * @return boolean
 */
function login_check (mysqli $db)
{

}

