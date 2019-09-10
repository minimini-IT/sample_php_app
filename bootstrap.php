<?php
require "/var/www/html/sample_php_app/mvc/Loader.php";
$loader = new Loader();
//ディレクトリ登録
$loader->regDirectory("/var/www/html/sample_php_app/mvc");
$loader->regDirectory("/var/www/html/sample_php_app/models");
//オートロードに登録
$loader->register();
