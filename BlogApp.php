<?php
class BlogApp extends AppBase{
  //データベース接続
  protected function doDbConnection(){
    $this->_connect_model->connect("master", array(
      "string" => "mysql:dbname=sample_app;host=localhost; charset=utf8",
      "user" => "acsw",
      "password" => "acsw"
    ));
  }
  //ルートディレクトリパス取得
  public function getRootDirectory(){
    return dirname(__FILE__);
  }
  //ルーティングの定義登録
  #protected function getRouteDefinition(){
  #protected function getRootDefinition(){
  #public function getRootDefinition(){
  public function getRouteDefinition(){
    return array(
      "/account" => array("controller" => "account", "action" => "index"),
      "/account/:action" => array("controller" => "account"),
      "/follow" => array("controller" => "account", "action" => "follow"),
      "/" => array("controller" => "blog", "action" => "index"),
      "/status/post" => array("controllr" => "blog", "action" => "post"),
      "/user/:user_name" => array("controller" => "blog", "action" => "user"),
      "/user/:user_name/status/:id" => array("controller" => "blog", "action" => "specific")
    );
  }
  public function getRootDefinition(){}
}
?>
