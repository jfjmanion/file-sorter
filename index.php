<?php
require_once(dirname(__FILE__) . '/vendor/JamesHeinrich/getID3/getid3/getid3.php');

require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/videoFile.php');
require_once(dirname(__FILE__) . '/audioFile.php');
require_once(dirname(__FILE__) . '/main.php');

$config = new Config();
$run = new MainProgram();
$run->run($config);
