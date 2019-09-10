<?php
class Response{
  //コンテンツを保持
  protected $_content;
  //ステータスメッセージを保持
  protected $_statusCode = 200;
  //ステータスコードのテキスト保持
  protected $_statusMsg = "OK";
  //レスポンスヘッダーのフィールド保持
  protected $_headers = array();
  const HTTP = "HTTP/1.1 ";

  //----------setStatusCodeメソッド--------------
  //ステータスコードとメッセージを格納
  public function setStatusCode($code, $msg = ""){
    $this->_statusCode = $code;
    $this->_statusMsg = $msg;
  }

  //----------setHeaderメソッド--------------
  //ヘッダーフィールドをプロパティに格納
  public function setHeader($name, $value){
    $this->_headers[$name] = $value;
  }

  //----------setContentメソッド--------------
  //コンテンツをプロパティに格納
  public function setContent($content){
    $this->_content = $content;
  }

  //----------sendメソッド--------------
  //レスポンスヘッダーとレスポンスボディを生成
  public function send(){
    header(self::HTTP . $this->_statusCode . $this->_statusMsg);
    foreach($this->_headers as $name => $value){
      header($name . ": " . $value);
    }
    print $this->_content;
  }
}
?>
