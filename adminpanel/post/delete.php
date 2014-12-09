<?php

/**
 * @return boolean
 */
function deletePost ( $db, $id )
{
    return $db->delete( 'guestbook', array( 'id_entry' => $id ) );
}
