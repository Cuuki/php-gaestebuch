<?php

namespace Guestbook;

interface UserInterface
{
    /**
     * @return array
     */
    public function getUsers ( $db );

    /**
     * @return stmt
     */
    public function updateUser ( $db );

    /**
     * @return stmt
     */
    public function saveUser ( $db );

    /**
     * @return stmt
     */
    public function deleteUser ( $db );
}