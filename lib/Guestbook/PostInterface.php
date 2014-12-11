<?php

namespace Guestbook;

interface PostInterface
{

    /**
     * @return array
     */
    public function getPosts ( $db, $rowsperpage, $currentpage );

    /**
     * @return stmt
     */
    public function updatePost ( $db );

    /**
     * @return stmt
     */
    public function savePost ( $db );

    /**
     * @return stmt
     */
    public function deletePost ( $db );
}
