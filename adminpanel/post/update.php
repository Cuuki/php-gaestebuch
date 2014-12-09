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
