<?php

$message = 'Thank you for registering. An email has been sent to your inbox with details on how to activate your account.';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'externalRegistration/accountRegistrationCompleted', '', array('MESSAGE' => $message));
G::RenderPage('publish', 'blank');
die();