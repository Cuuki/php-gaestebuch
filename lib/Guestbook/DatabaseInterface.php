<?php

namespace Guestbook;

use Doctrine\DBAL\Driver\PDOStatement;

interface DatabaseActionInterface
{

    /**
     * @return array
     */
    public function select ( $tablename );

    /**
     * @return PDOStatement
     */
    public function insert ( $tablename );

    /**
     * @return PDOStatement
     */
    public function update ( $tablename, $property );

    /**
     * @return PDOStatement
     */
    public function delete ( $tablename, $property );
}
