<?php

$message = 'Votre inscription a bien été pris en compte. Afin de valider votre inscription, un mail vous a été envoyé avec un liens d\'activation, merci de consulter votre messagerie et de suivre les instructions.';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/accountRegistrationCompleted', '', array('MESSAGE' => $message));
G::RenderPage('publish', 'blank');
die();