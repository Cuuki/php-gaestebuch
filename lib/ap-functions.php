<?php

/**
 * TODO: Doctrine
 * @return stmt
 */
function saveLogindata ( array $params, $db )
{
    $insert = 'INSERT INTO
                    user(username, useremail, password)
                VALUES
                (
                    :username,
                    :useremail,
                    :password
                )';
    
    return $db->executeQuery( $insert, array(
                'username' => $params["username"],
                'useremail' => $params["useremail"],
                'password' => password_hash( $params["password"], PASSWORD_BCRYPT )
            ) );
}

/**
 * TODO: Doctrine
 * @return array
 */
function getLogindata ( $db, $username )
{
    $sql = 'SELECT
                id, username, useremail, password, role
            FROM
                user
            WHERE
                username = "' . $username . '"';

    $dbRead = $db->query( $sql );
    $logindata = array();
    $row = $dbRead->fetch_assoc();
    array_push( $logindata, $row );

    return $logindata;
}

/**
 * TODO: Doctrine
 * @return array
 */
function getUser ( $db, $id )
{
    $sql = 'SELECT
                id, username, useremail, password
            FROM
                user
            WHERE
                id = "' . $id . '"';

    $dbRead = $db->query( $sql );
    $userdata = array();

    $row = $dbRead->fetch_assoc();

    array_push( $userdata, $row );

    return $userdata;
}

/**
 * TODO: Doctrine
 * @return array
 */
function getAllUsers ( $db )
{
    $sql = 'SELECT
                id, username, useremail, password
            FROM
                user';

    $dbRead = $db->query( $sql );
    $userdata = array();

    while ( $row = $dbRead->fetch_assoc() )
    {
        array_push( $userdata, $row );
    }

    return $userdata;
}
