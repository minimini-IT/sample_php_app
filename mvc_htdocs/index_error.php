<?php
$f = "index_error";
require("../bootstrap.php");
require("../BlogApp.php");
//true->エラー表示モード
echo $f;
echo "<p>BlogAppインスタンス生成前</p></br>";
$app = new BlogApp(true);
echo $f;
echo "<p>BlogAppインスタンス生成後</p>";
$app->run();
