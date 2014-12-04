<?php

/**
 * TODO: Doctrine
 * @return boolean
 */
function deleteUser ( mysqli $db, $id )
{
    $delete = 'DELETE FROM user WHERE id = "' . $id . '"';

    return $db->query( $delete );
}
