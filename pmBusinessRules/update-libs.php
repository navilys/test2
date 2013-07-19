#!/usr/bin/env php
<?php

define('HOME_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

chdir(HOME_DIR . '../');
echo "Updating components..." . PHP_EOL;
echo `git submodule foreach git pull origin master`;


// move to libs dir
chdir(HOME_DIR . 'lib');

// update BusinessRuleControl JS
chdir('BusinessRuleControl');

$brcFiles = array(
    'build/css/easyselector-0.1.1.css' => 'public_html/easyselector-0.1.1.css',
    'build/css/jcore.brcontrol.css' => 'public_html/jcore.brcontrol.css',
    'build/img/adam_sprite_with_zoom_new.png' => 'public_html/images/adam_sprite_with_zoom_new.png',
    'build/img/easyselector-sprite.png' => 'public_html/images/easyselector-sprite.png',
    'build/img/gear.png' => 'public_html/images/gear.png',
    'build/js/jcore.brcontrol-0.1.0.min.js' => 'public_html/js/jcore.brcontrol-0.1.0.min.js',
    'build/lib/jcore.pmdraw-0.2.0.js' => 'public_html/js/jcore.pmdraw-0.2.0.js',
    'build/lib/underscore-min.js' => 'public_html/js/underscore-min.js',
);

echo " * Updating BusinessRuleControl lib." . PHP_EOL;
echo "   Executing rake tasks..." . PHP_EOL;
echo `rake`;

echo " Copying files..." . PHP_EOL;
foreach ($brcFiles as $source => $target) {
    $targetFile = HOME_DIR . $target;
    echo  " Copy: $source -> $target" . PHP_EOL;
    echo `cp $source $targetFile`;
    echo `chmod 0777 $targetFile`;
}
echo " DONE!" . PHP_EOL;

echo "Updating sources...";
$files = array('public_html/easyselector-0.1.1.css', 'public_html/jcore.brcontrol.css');

foreach ($files as $target) {
    $content = str_replace('../img/', 'images/', file_get_contents(HOME_DIR . $target));
    file_put_contents(HOME_DIR . $target, $content);
}

echo " DONE!" . PHP_EOL;

echo " Finished" . PHP_EOL . PHP_EOL;



