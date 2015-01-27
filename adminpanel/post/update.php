<?php

/**
 * @return boolean
 */
function updatePost ( $db, array $params, $id )
{
    $update = 'UPDATE
                    guestbook
               SET
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    content = :content
               WHERE
                    id_entry = :id_entry';

    return $db->executeQuery( $update, array(
                'firstname' => $params["firstname"],
                'lastname' => $params["lastname"],
                'email' => $params["email"],
                'content' => $params["textinput"],
                'id_entry' => $id
            ) );
}

/**
 * @return boolean
 */
function updateFirstname ( $db, $firstname, $id )
{
    $update = 'UPDATE
                    guestbook
               SET
                    firstname = :firstname
               WHERE
                    id_entry = :id_entry';

    return $db->executeQuery( $update, array(
                'firstname' => $firstname,
                'id_entry' => $id
            ) );
}

/**
 * @return boolean
 */
function updateLastname ( $db, $lastname, $id )
{
    $update = 'UPDATE
                    guestbook
               SET
                    lastname = :lastname
               WHERE
                    id_entry = :id_entry';

    return $db->executeQuery( $update, array(
                'lastname' => $lastname,
                'id_entry' => $id
            ) );
}

/**
 * @return boolean
 */
function updateEmail ( $db, $email, $id )
{
    $update = 'UPDATE
                    guestbook
               SET
                    email = :email
               WHERE
                    id_entry = :id_entry';

    return $db->executeQuery( $update, array(
                'email' => $email,
                'id_entry' => $id
            ) );
}

/**
 * @return boolean
 */
function updateContent ( $db, $content, $id )
{
    $update = 'UPDATE
                    guestbook
               SET
                    content = :content
               WHERE
                    id_entry = :id_entry';

    return $db->executeQuery( $update, array(
                'content' => $content,
                'id_entry' => $id
            ) );
}
