<?php

/**
 * @return boolean
 */
function updateUser ( $db, array $params, $id )
{
    $update = 'UPDATE
                    user
               SET
                    username = :username,
                    useremail = :useremail,
                    password = :password
               WHERE
                    id = :id';

    return $db->executeQuery( $update, array(
                'username' => $params['username'],
                'useremail' => $params['useremail'],
                'password' => password_hash( $params['password'], PASSWORD_BCRYPT ),
                'id' => $id
            ) );
}

/**
 * @return boolean
 */
function updateUsername ( $db, $username, $id )
{
    $update = 'UPDATE
                    user
               SET
                    username = :username
               WHERE
                    id = :id';

    return $db->executeQuery( $update, array(
                'username' => $username,
                'id' => $id
            ) );
}

/**
 * @return boolean
 */
function updateEmail ( $db, $email, $id )
{
    $update = 'UPDATE
                    user
               SET
                    useremail = :useremail
               WHERE
                    id = :id';

    return $db->executeQuery( $update, array(
                'useremail' => $email,
                'id' => $id
            ) );
}

/**
 * @return boolean
 */
function updatePassword ( $db, $password, $id )
{
    $update = 'UPDATE
                    user
               SET
                    password = :password
               WHERE
                    id = :id';

    return $db->executeQuery( $update, array(
                'password' => password_hash( $password, PASSWORD_BCRYPT ),
                'id' => $id
            ) );
}

/**
 * @return boolean
 */
function updateRole ( $db, $role, $id )
{
    $update = 'UPDATE
                    user
               SET
                    role = :role
               WHERE
                    id = :id';

    return $db->executeQuery( $update, array(
                'role' => $role,
                'id' => $id
            ) );
}