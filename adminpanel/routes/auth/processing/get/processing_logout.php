<?php

session_destroy();

// nach ausloggen weiterleiten auf loginseite
return $app->redirect( $app['url_generator']->generate( 'login' ) );
