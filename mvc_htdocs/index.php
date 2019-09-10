<?php
require "../bootstrap.php";
require "../BlogApp.php";
//false->エラー表示モード
$app = new BlogApp(false);
$app->run();
