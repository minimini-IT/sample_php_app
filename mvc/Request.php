<?php
class Request{
  //------------getHostNameメソッド------------
  //ホスト名取得
  public function getHostName(){
    if(!empty($_SERVER["HTTP_HOST"])){
      return $_SERVER["HTTP_HOST"];
    }
    return $_SERVER["SERVER_NAME"];
  }

  //------------getRequestUriメソッド------------
  //ホスト部以降を取得
  public function getRequestUri(){
    return $_SERVER["REQUEST_URI"];
  }

  //------------getBaseUriメソッド------------
  //フロントコントローラまでのパス取得
  public function getBaseUrl(){
    //現在のスクリプトとホスト名以下
    $scriptName = $_SERVER["SCRIPT_NAME"];
    $requestUri = $this->getRequestUri();

    //フロントコントローラがURLに含まれる場合
    //requestUrlの先頭にscriptNameがあるなら
    if(0 === strpos($requestUri, $scriptName)){
      return $scriptName;
    //フロントコントローラが省略されている場合
    //requestUrlの先頭にscriptNameの親ディレクトリががあるなら
    }else if(0 === strpos($requestUri, dirname($scriptName))){
      return rtrim(dirname($scriptName), "/");
    }
    return "";
  }

  //------------getPathメソッド------------
  //フロントコントローラ以降のパス取得
  public function getPath(){
    $base_url = $this->getBaseUrl();
    $requestUri = $this->getRequestUri();

    //リクエストURLに?が含まれているか
    //$sp = strpos($requestUri, "?")->?の位置(int)を代入
    if(false !== ($sp = strpos($requestUri, "?"))){
      //substr()で?以前のパス取得
      $requestUri = substr($requestUri, 0, $sp);
    }
    //ホスト部以降のパスからフロントコントローラまでのパスを除く
    $path = (string)substr($requestUri, strlen($base_url));
    return $path;
  }

  //------------isPostメソッド------------
  //リクエストがPOSTかどうか
  public function isPost(){
    if($_SERVER["REQUEST_METHOD"] === "POST"){
      return true;
    }
    return false;
  }

  //get/postで送信された情報を取得
  //------------getPostメソッド------------
  public function getPost($name, $param = null){
    if(isset($_POST[$name])){
      return $_POST[$name];
    }
    //なければデフォルト(null)を返す
    return $param;
  }

  //------------getGetメソッド------------
  public function getGet($name, $param = null){
    if(isset($_GET[$name])){
      return $_GET[$name];
    }
    return $param;
  }
}
?>
