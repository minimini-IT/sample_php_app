<?php
abstract class AppBase{
  //各クラスのインスタンス保持
  protected $_request;
  protected $_response;
  protected $_session;
  protected $_connectModel;
  protected $_router;

  //サインイン時のコントローラとアクションの組み合わせを保持
  protected $_signinAction = array();

  //エラー表示のオン／オフを保持
  protected $_displayErrors = false;

  //コントローラクラスのベース部分
  const CONTROLLER = "Controller";
  
  //各ディレクトリ情報
  const VIEWDIR = "/views";
  const MODELSDIR = "/models";
  const WEBDIR = "/mvc_htdocs";
  const CONTROLLERSDIR = "/controllers";

  //--------コンストラクター--------------
  //初期化と設定、エラー表示の有無
  public function __construct($dspErr){
    echo "AppBaseコンストラクタ起動";
    //エラー表示の無効／有効を設定するメソッド
    $this->setDisplayErrors($dspErr);
    //初期化メソッド
    $this->initialize();
    //データベース接続メソッド
    $this->doDbConnection();
  }

  //--------initializeメソッド--------------
  //アプリケーションの初期化
  protected function initialize(){
    $this->_connectModel = new ConnectModel();
    $this->_router = new Router($this->getRouteDefinition());
    $this->_request = new Request();
    $this->_response = new Response();
    $this->_session = new Session();
  }

  //--------getRouteDefinitionメソッド--------------
  //オーバーライドを前提とした抽象メソッド
  abstract protected function getRouteDefinition();

  //--------setDisplayErrorsメソッド--------------
  //エラー表示の無効／有効を設定
  //フラグがtrueの場合はすべてエラーを出力する
  protected function setDisplayErrors($dspErr){
    if($dspErr){
      //エラー表示
      $this->_displayErrors = true;
      ini_set("display_errors", 1);
      ini_set("error_reporting", E_ALL);
    }else{
      //エラー表示しない
      $this->_displayErrors = false;
      ini_set("display_errors", 0);
    }
  }

  //--------isDisplayErrorsメソッド--------------
  //エラー表示の有無を返す
  public function isDisplayErrors(){
    return $this->_displayErrors;
  }

  //--------runメソッド--------------
  //リクエストに応答、レスポンスを送信
  public function run(){
    echo "run()起動";
    try{
      //ルーティング情報を取得
        //getRouteParams()->URL末尾のパス情報をルーティング用に加工
        //getPath()->URLに含まれるパス情報を取得
      $parameters = $this->_router->getRouteParams($this->_request->getPath());

      //ルーティング定義が存在しない場合例外を投げる
      if($parameters === false){
        throw new FileNotFoundException("NO ROUTE " . $this->_request->getPath());
      }
      //コントローラ名とアクション名のキーと値を変数に格納
      $controller = $parameters["controller"];
      $action = $parameters["action"];

      //getContent()実行
      $this->getContent($controller, $action, $parameters);

    }catch(FileNotFoundException $e){
      $this->dispErrorPage($e);
    }catch(AuthorizedException $e){
      //list->controllerとactionに_signinActionの配列をそれぞれ代入
      list($controller, $action) = $this->_signinAction;
      $this->getContent($controller, $action);
    }
    //レスポンス情報を送信
    $this->_response->send();
  }

  //--------getContentメソッド--------------
  //アクションの実行、コンテンツをレスポンス情報にセット
  public function getContent($controllerName, $action, $parameters = array()){
    //コントローラーのクラス名生成
    //ucfirst->最初の文字を大文字に
    $controllerClass = ucfirst($controllerName) . self::CONTROLLER;

    //コントローラークラスのインスタンス化
    //getControllerObject()でコントローラーサブクラスのインスタンス取得
    $controller = $this->getControllerObject($controllerClass);
    
    //指定されたコントローラークラスがない場合はエラーを投げる
    if($controller === false){
      throw new FileNotFoundException($controllerClass . " NOT FOUND");
    }

    //アクションの実行
    $content = $controller->dispatch($action, $parameters);

    //コンテンツをレスポンス情報にセット
    $this->_response->setContent($content);
  }

  //--------getControllerObjectメソッド--------------
  //コントローラークラスのインスタンス化する
  public function getControllerObject($controllerClass){
    //controllerClassが定義されていない or 読み込まれていない場合の処理
    if(!class_exists($controllerClass)){
      //コントローラーファイルパスを生成
      //getControllerDirectory()->コントローラーファイルが格納されているディレクトリパス取得
      $controllerFile = $this->getControllerDirectory() . "/" . $controllerClass . ".php";
      
      //生成したファイル名が見つからない場合はfalseを返す
      if(!is_readable($controllerFile)){
        return false;
      }else{

        //あればファイル読み込み
        require_once $controllerFile;

        //ファイルがあってもクラスが定義されていなければfalse
        if(!class_exists($controllerClass)){
          return false;
        }
      }
    }
    //コントローラークラスのインスタンス化
    //引数はAppBaseクラスのインスタンス
    $controller = new $controllerClass($this);
    return $controller;
  }

  //--------dispErrorPageメソッド--------------
  //エラー画面生成
  public function dispErrorPage($e){
    //ステータスコード404とステータスメッセージをヘッダー情報に登録
    $this->_response->setStatusCode(404, "FILE NOT FOUND");

    //エラー表示の有無を調べてtrueなら例外メッセージ取得、Falseなら独自メッセージ設定
    $errMessage = $this->isDisplayErrors() ? $e->getMessage() : "FILE NOT FOUND";
    //エスケープ処理
    $errMessage = htmlspecialchars($errMessage, ENT_QUOTES, "UTF-8");

    //コンテンツ生成
    $html = "
      <!DOCTYPE html>
      <html>
        <head>
          <meta charset='UTF-8'>
          <title>HTTP 404 Error</title>
        </head>
        <body>
          {$errMessage}
        </body>
      </html>
    ";
    //コンテンツ登録
    $this->_response->setContent($html);
  }

  //--------getRootDefinitionメソッド--------------
  //アプリケーションのルートディレクトリ取得
  //オーバーライド前提の抽象メソッド
  abstract public function getRootDefinition();


  //--------doDbConnectionメソッド--------------
  //データベース接続
  //実装はアプリケーション側
  protected function doDbConnection(){}

  //各インスタンスを取得するメソッド
  //--------getRequestObjectメソッド--------------
  public function getRequestObject(){
    return $this->_request;
  }
  //--------getResponseObjectメソッド--------------
  public function getResponseObject(){
    return $this->_response;
  }
  //--------getSessionObjectメソッド--------------
  public function getSessionObject(){
    return $this->_session;
  }
  //--------getConnectModelObjectメソッド--------------
  public function getConnectModelObject(){
    return $this->_connect_model;
  }

  //--------getRootDirectoryメソッド--------------
  //プロジェクトのルートディレクトリを返す
  //抽象メソッド
  abstract public function getRootDirectory();

  //それぞれのディレクトリのパスを返すメソッド
  //--------getViewDirectoryメソッド--------------
  public function getViewDirectory(){
    return $this->getRootDirectory() . self::VIEWSDIR;
  }

  public function getModelDirectory(){
    return $this->getRootDirectory() . self::MODELDIR;
  }

  //ドキュメントルート
  public function getDocDirectory(){
    return $this->getRootDirectory() . self::WEBDIR;
  }

  public function getControllerDirectory(){
    return $this->getRootDirectory() . self::CONTROLLERSDIR;
  }

}
?>
