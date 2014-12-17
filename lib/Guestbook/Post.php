<?php

namespace Guestbook;

class Post extends AbstractDatabaseAction
{
    /**
     * Automatically generated and increased
     * @var integer
     */
    protected $id_entry;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $content;

    /**
     * Automatically generated
     * @var timestamp
     */
    protected $created;

    /**
     * @return array
     */
    public function getPostsOrderBy ( $db, $rowsperpage, $currentpage )
    {
        $offset = ($currentpage - 1) * $rowsperpage;

        $select = 'SELECT * FROM guestbook ORDER BY created DESC LIMIT ' . (int) $offset . ', ' . (int) $rowsperpage . '';

        return $db->fetchAll( $select );
    }

}
