<?php

function debug ()
{
    // liefert Funktionsargumente als Array
    $argumentList = func_get_args();

    foreach ( $argumentList as $argument )
    {
        // speichert Fehlermeldungen in Datei
        error_log( var_export( $argument, true ), 3, '../log/error.log' );
    }
}
