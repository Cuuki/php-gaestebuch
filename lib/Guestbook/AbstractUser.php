<?php

namespace Guestbook;

abstract class AbstractUser implements UserInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $useremail;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
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
