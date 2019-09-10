<?php
echo "<p>require BlogApp</p></br>";
var_dump(file_exists("/home/acsw/sample_app/mvc/Loader.php"));
require("../BlogApp.php");
echo "<p>require bootstrap</p></br>";
require("../bootstrap.php");
//true->エラー表示モード
echo "<p>BlogAppインスタンス生成前</p></br>";
$app = new BlogApp(true);
echo "<p>BlogAppインスタンス生成後</p>";
$app->run();
