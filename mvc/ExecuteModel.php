<?php
abstract class ExecuteModel{
  //PDOオブジェクト保持
  protected $_pdo;

  //----------コンストラクター-------------
  //PDOオブジェクトをsetPdo()に渡す
  public function __construct($pdo){
    $this->setPdo($pdo);
  }

  //----------setPdoメソッド-------------
  //$_pdoにPDOオブジェクトを格納
  public function setPdo($pdo){
    $this->_pdo = $pdo;
  }

  //----------executeメソッド-------------
  //SQLクエリを発行
  //sql -> sql文
  //parameter -> プレースホルダーと値のペア
  public function execute($sql, $parameter = array()){
    //プリペアドステートメント生成
    $stmt = $this->_pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
    //プリペアドステートメント実行
    $stmt->execute($parameter);
    //実行結果を返す
    return $stmt;
  }

  //----------getAllRecordメソッド-------------
  //クエリの結果をすべて取得する
  public function getAllRecord($sql, $parameter = array()){
    $all_rec = $this->execute($sql, $parameter)->fetchAll(PDO::FETCH_ASSOC);
    return $all_rec;
  }

  //----------getRecordメソッド-------------
  //クエリ結果を１行のみ取得
  public function getRecord($sql, $parameter = array()){
    $rec = $this->execute($sql, $parameter)->fetch(PDO::FETCH_ASSOC);
    return $rec;
  }

}
?>
