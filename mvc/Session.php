<?php
class Session{
  //静的変数なので関数外にも値が引き継がれる
  //セッションを保持
  protected static $_session_flag = false;
  //セッションIDが生成済みかどうか保持
  protected static $_generated_flag = false;

  //------------コンストラクタ----------------
  //セッション確認・開始、セッション情報保持
  public function __construct(){
    if(!self::$_session_flag){
      session_start();
      self::$_session_flag = true;
    }
  }

  //------------setメソッド---------------
  //セッション変数の設定
  public function set($key, $value){
    $_SESSION[$key] = $value;
  }

  //------------getメソッド---------------
  //セッション変数取得
  public function get($key, $par = null){
    if(isset($_SESSION[$key])){
      return $_SESSION[$key];
    }
    return $par;
  }

  //------------generateSessionメソッド---------------
  //セッションID生成
  //del->session_regenerate_idメソッドの引数
  public function generateSession($del = true){
    //セッションIDが生成されていなければ
    if(!self::$_generated_flag){
      //セッションID生成、古いのは削除(引数trueで)
      session_regenerate_id($del);
      self::$_generated_flag = true;
    }
  }

  //------------setAuthenticateStatusメソッド---------------
  //セッション開始時に呼び出し、サインイン中と示すためセッション変数に格納
  public function setAuthenticateStatus($flag){
    //$_SESSION["_authenticated"]=>flag となる
    $this->set("_authenticated", (bool)$flag);
    $this->generateSession();
  }

  //------------isAuthenticatedメソッド---------------
  //サインイン状態を確認
  public function isAuthenticated(){
    return $this->get("_authenticated", false);
  }

  //------------clearメソッド---------------
  //セッション変数の初期化
  public function clear(){
    $_SESSION = array();
  }
}
?>
