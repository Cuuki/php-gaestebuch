<?php

namespace Guestbook;

abstract class AbstractUser implements UserInterface
{
    protected $id;
    protected $username;
    protected $useremail;
    protected $password;
    protected $role;

    /**
     * @return array
     */
    abstract public function getUserByName ( $db );

    /**
     * @return array
     */
    abstract public function getUserById ( $db );

    /**
     * @return stmt
     */
    abstract public function updateUsername ( $db );

    /**
     * @return stmt
     */
    abstract public function updateEmail ( $db );

    /**
     * @return stmt
     */
    abstract public function updatePassword ( $db );
}
