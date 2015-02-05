<?php

/**
 * @return array
 * */
function getCode ( $db, $code )
{
    // Code und ID von User der den Code angefordert hat aus DB auslesen
    $select = 'SELECT * FROM auth_codes WHERE code = ?';

    return $db->fetchAssoc( $select, array( $code ) );
}

/**
 * @return array
 * */
function getMail ( $db, $postdata )
{
    // E-Mail von Eingabe aus DB auslesen
    $select = 'SELECT
                id, useremail
            FROM
                user
            WHERE
            	useremail = ?';

    return $db->fetchAssoc( $select, array( $postdata ) );
}

/**
 * @return boolean
 */
function saveCode ( $db, $code, $id )
{
    // Code und ID von User der den Code angefordert hat in DB speichern
    $insert = 'INSERT INTO
                    auth_codes(code, id_user)
               VALUES 
               (
                    :code,
                    :id_user
               )';

    return $db->executeQuery( $insert, array(
                'code' => $code,
                'id_user' => $id
            ) );
}

/**
 * @return boolean
 */
function deleteCode ( $db, $code )
{
    return $db->delete( 'auth_codes', array( 'code' => $code ) );
}