<?php

/**
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
 * @return array
 */
function getLogindata ( $db, $username )
{
    $select = 'SELECT * FROM user WHERE username = ?';

    return $db->fetchAssoc( $select, array( $username ) );
}

/**
 * @return array
 */
function getUser ( $db, $id )
{
    $select = 'SELECT * FROM user WHERE id = ?';

    return $db->fetchAssoc( $select, array( $id ) );
}

/**
 * @return array
 */
function getUsersLimit ( $db, $rowsperpage, $currentpage )
{
    $offset = ($currentpage - 1) * $rowsperpage;   
    
    $select = 'SELECT * FROM user ORDER BY id ASC LIMIT ' . (int) $offset . ', ' . (int) $rowsperpage . '';

    return $db->fetchAll( $select );
}

/**
 * @return array
 */
function getAllUsers ( $db )
{
    $select = 'SELECT * FROM user';

    return $db->fetchAll( $select );
}