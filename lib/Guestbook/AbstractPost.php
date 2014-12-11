<?php

namespace Guestbook;

abstract class AbstractPost implements PostInterface
{
    /**
     * @var integer
     */
    private $id_entry;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $latname;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $created;

    /**
     * @return array
     */
    abstract public function getPostById ( $db );
}
