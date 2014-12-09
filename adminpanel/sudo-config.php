<?php

$insert = 'INSERT INTO
                user(username, useremail, password, role)
           VALUES
           (
                :username,
                :useremail,
                :password,
                :role
           )';

// lege superuser an, wenn nicht schon vorhanden
if ( getLogindata( $app['db'], 'sudo' ) == NULL )
{
    $app['db']->executeQuery( $insert, array(
        'username' => 'sudo',
        'useremail' => 'sudo@master.com',
        'password' => password_hash( "sudo", PASSWORD_BCRYPT ),
        'role' => 'suadm'
    ) );
}
