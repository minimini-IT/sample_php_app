<?php
class ConnectModel{
  //PDOクラスのインスタンスを配列で保持
  protected $_dbConnections = array();

  //接続名保持
  protected $_connectName;

  //データモデルのサブクラスのインスタンスを保持
  protected $_modelList = array();

  const MODEL = "Model";

  //----------connectメソッド----------
  //DB接続用のPDOオブジェクト生成
  //name->接続名
  //      dbConnectionsのキー
  public function connect($name, $connection_strings){
    try{
      //PDOオブジェクト生成
      $cnt = new PDO(
        //DB接続情報
        $connection_strings["string"],
        $connection_strings["user"],
        $connection_strings["password"]
      );
    }catch(PDOException $e){
      exit("データベースの接続に失敗しました。：{$e->getMessage()}");
    }

    //PDOオブジェクトにエラー発生時に例外を投げる属性を設定
    $cnt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //接続名をキーにしてPDOオブジェクトを格納
    $this->_dbConnections[$name] = $cnt;

    $this->_connectName = $name;
  }

  //-----------getConnectionメソッド--------------
  //生成したPDOオブジェクトを返す
  public function getConnection($name = null){
    if(is_null($name)){
      //接続名デフォルトではnull。nullの場合は配列の先頭を返す
      return current($this->_dbConnections);
    }
    //接続名に応じたPDOオブジェクトを返す
    return $this->_dbConnections[$name];
  }

  //-----------getModelConnectionメソッド--------------
  //データモデルに対応するデータベース接続を取得
  public function getModelConnection(){
    //connectionNameに接続名があればその名前でPDOオブジェクトを取得
    if(isset($this->_connectName)){
      $name = $this->_connectName;
      $cnt = $this->getConnection($name);
    //なければ接続名なしで
    }else{
      $cnt = $this->getConnection();
    }
    //取得したPDOオブジェクトを返す
    return $cnt;
  }

  //---------getメソッド-----------
  //データモデルオブジェクトを取得
  //引数はデータモデル名
  public function get($model_name){

    //modelListにデータモデル名がなければ
    if(!isset($this->_modelList[$model_name])){

      //サブクラス名を生成
      $mdl_class = $model_name . self::MODEL;

      //PDOオブジェクト取得
      $cnt = $this->getModelConnection();

      //サブクラスのインスタンスを生成
      $obj = new $mdl_class($cnt);

      //データモデル名をキーとしてインスタンスを格納
      $this->_modelList[$model_name] = $obj;
    }
    //データモデルクラスのインスタンスを返す
    $modelObj = $this->_modelList[$model_name];
    return $modelObj;
    }

    //-----------デストラクター-----------
    //PDOオブジェクトとデータモデルオブジェクトを破棄
  public function __destruct(){
    foreach($this->_modelList as $model){
      unset($model);
    }
    foreach($this->_dbConnections as $cnt){
      unset($cnt);
    }
  }
}
?>
