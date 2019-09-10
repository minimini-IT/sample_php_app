<?php
echo "<p>require Loader.php</p></br>";
require "/home/acsw/sample_app/mvc/Loader.php";
$loader = new Loader();
$loader->regDirectory(dirname(__FILE__) . "/mvc");
$loader->regDirectory(dirname(__FILE__) . "/models");
$loader->register();
echo "<p>終了</p></br>";
