<?php
echo "<p>require Loader.php</p></br>";
$dir = dirname(__FILE__);
require $dir . "/../mvc/Loader.php";
$loader = new Loader();
$loader->regDirectory("/var/www/html/sample_php_app/mvc");
$loader->regDirectory("/var/www/html/sample_php_app/models");
$loader->register();

class BlogApp extends AppBase{}
echo "<p>終了</p></br>";
