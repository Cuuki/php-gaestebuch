<?php

namespace Guestbook;

interface DatabaseQueriesInterface
{
//    TODO Interface als Schablone für alle Datenbankabfragen
    
    /**
     * @return array
     */
    public function select ( $db );

    /**
     * @return stmt
     */
    public function update ( $db );

    /**
     * @return stmt
     */
    public function insert ( $db );

    /**
     * @return stmt
     */
    public function delete ( $db );
}