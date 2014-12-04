<?php

/**
 * TODO: Doctrine
 * @return boolean
 */
function deletePost ( mysqli $db, $id )
{
    $delete = 'DELETE FROM guestbook WHERE id_entry = "'. $id .'"';

    $dbRead = $db->query( $delete );

    return $dbRead;
}