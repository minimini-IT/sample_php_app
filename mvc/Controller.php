<?php
//abstract 抽象クラス
abstract class Controller{

  //アプリケーションクラスのインスタンス保持
  protected $_application;

  //コントローラのクラス名保持
  protected $_controller;

  //アクション名保持
  protected $_action;

  //Requestオブジェクト保持
  protected $_request;

  //Responseオブジェクト保持
  protected $_response;

  //Sessionオブジェクト保持
  protected $_session;

  //ConnectModelオブジェクト保持
  protected $_connect_model;

  //認証が必要なページかどうかを保持
  protected $_authentication=array();

  const PROTOCOL = "http://";
  const ACTION = "Action";

  //$application -> アプリケーション本体のインスタンス
  public function __construct($application){
    $this->_application   = $application;

    //コントローラのクラス名を取得
    $this->_controller    = strtolower(substr(get_class($this), 0, -10));

    //アプリケーションクラスのインスタンスに内包されている各クラスのインスタンスを取得
    $this->_request       = $application->getRequestObject();
    $this->_response      = $application->getResponseObject();
    $this->_session       = $application->getSessionObject();
    $this->_connect_model = $application->getConnectModelObject();
  }

  //---------dispatch()メソッド--------------
  //$params -> ルーティング情報
  public function dispatch($action, $params = array()){
    $this->_action = $action;

    //アクションメソッド名生成
    $action_method = $action . self::ACTION;

    //生成したアクションメソッドが存在しない場合
    if(!method_exists($this, $action_method)){
      //httpNotFound()を呼び出し。エラー画面表示
      $this->httpNotFound();
    }

    //$isAuthentication Controllerクラスの認証確認メソッド
    //isAuthenticated() 認証済みアクションかどうかを調べるSessionクラスのメソッド
    if($this->isAuthentication($action) && !$this->_session->isAuthenticated()){
      throw new AuthorizedException();
    }

    //アクションの実行
    //$thisいるの？
    $content = $this->$action_method($params);
    return $content;
  }

  //---------httpNotFound()メソッド--------------
  protected function httpNotFound(){
    //引数はメッセージ
    //エラーになったコントローラとアクション
    throw new FileNotFoundException("FILE NOT FOUND " . $this->_controller . "/" . $this->_action);
  }

  //---------isAuthentication()メソッド--------------
  protected function isAuthentication($action){
    //$this->_authentication === true -> _authenticationがtrueの場合は、サブクラスで定義したアクションは全て認証が必要
    if($this->_authentication === true || (is_array($this->_authentication) && in_array($action, $this->_authentication))){
      return true;
    }
    return false;
  }

  //---------render()メソッド--------------
  protected function render($param = array(), $viewFile = null, $template = null){
    $info = array(
      "request" => $this->_request,
      "base_url" => $this->_request->getBaseUrl(),
      "session" => $this->_session
    );
  
    //getViewDirectory() -> ビューファイルを格納しているディレクトリパス取得
    $view = new View($this->_application->getViewDirectory(), $info);

    //ビューファイルの指定がない場合はアクション名
    if(is_null($viewFile)){
      $viewFile = $this->_action;
    }

    if(is_null($template)){
      $template = "template";
    }

    //コントローラ名/ビューファイル名のパスを生成
    $path = $this->_controller . "/" . $viewFile;

    //引数：ビューファイルのパス、アクションメソッドから渡された配列、レイアウトファイル名
    $content = $view->render($path, $param, $template);
    return $content;
  }

  //---------redirect()メソッド--------------
  protected function redirect($url){
    //ホスト名取得
    $host = $this->_request->getHostName();

    //ベースURL取得
    $base_url = $this->_request->getBaseUrl();

    //URL作成
    $url = self::PROTOCOL . $host . $base_url . $url;

    //ステータスコード302を設定
    //ステータスメッセージを設定
    $this->_response->setStatusCode(302, "Found");

    //レスポンスヘッダー設定
    //"Location" -> リダイレクトを行うためのヘッダーフィールド
    //生成したURLをリダイレクト先に指定
    $this->_response->setHeader("Location", $url);
  }

  //---------getToken()メソッド--------------
  protected function getToken($form){
    //$_SESSIONのキーを作成
    $key = "token/" . $form;

    //get()で$_SESSIONから値を取得
    //引数はさっき生成したキーと空配列
    $tokens = $this->_session->get($key, array());

    if(count($tokens) >= 10){
      //先頭の要素を削除
      array_shift($tokens);
    }
    $password = session_id() . $form;
    //ハッシュ化するパスワード、　アルゴリズム
    $token = password_hash($password, PASSWORD_DEFAULT);
    $tokens[] = $token;
    //$_SESSIONに$keyをキーにして$tokensを追加する
    $this->_session->set($key, $tokens);
    return $token;
  }

  //---------checkToken()メソッド--------------
  protected function checkToken($form, $token){
    $key = "token/" . $form;
    $tokens = $this->_session->get($key, array());
    //$tokens内に$tokenがあるかどうか検索
    //$presentは配列のキー or false
    if(false !== ($present = array_search($token, $tokens, true))){
      //トークン削除
      unset($tokens[$present]);
      //トークンを再セット
      $this->_session->set($key, $tokens);
      return true;
    }
    return false;
  }

}
