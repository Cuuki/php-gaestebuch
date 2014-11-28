<?php

/**
 * TODO: Doctrine
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
 * TODO: Doctrine
 * @return array
 */
function getLogindata ( mysqli $db, $username )
{
    $sql = 'SELECT
                id, username, useremail, password, role
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
 * TODO: Doctrine
 * @return array
 */
function getUser ( mysqli $db, $id )
{
    $sql = 'SELECT
                id, username, useremail, password
            FROM
                user
            WHERE
                id = "'. $id .'"';

    $dbRead = $db->query( $sql );
    $userdata = array();

    $row = $dbRead->fetch_assoc();

    array_push($userdata, $row);

    return $userdata;
}

/**
 * TODO: Doctrine
 * @return array
 */
function getAllUsers ( mysqli $db )
{
    $sql = 'SELECT
                id, username, useremail, password
            FROM
                user';

    $dbRead = $db->query( $sql );
    $userdata = array();

    while( $row = $dbRead->fetch_assoc() )
    {
        array_push($userdata, $row);
    }

    return $userdata;
}

