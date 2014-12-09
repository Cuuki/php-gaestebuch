<?php

/**
 * @return boolean
 */
function deleteUser ( $db, $id )
{
    return $db->delete( 'user', array( 'id' => $id ) );
}
