<?php
class View{
  //ビューファイルのディレクトリ保持
  //「ルートディレクトリ/views」的な情報
  protected $_baseUrl;
  //ビューファイルへ渡すデータ保持
  protected $_initialValue;
  //ページタイトル保持
  protected $_passValues = array();

  //----------construct-----------
  //渡されたデータをプロパティに保存
  public function __construct($baseUrl, $initialValue = array()){
    $this->_baseUrl = $baseUrl;
    $this->_initialValue = $initialValue;
  }

  //----------setPageTitleメソッド-----------
  //レイアウトファイルに渡すページタイトルを設定
  public function setPageTitle($name, $value){
    $this->_passValues[$name] = $value;
  }

  //filename -> ビューファイルのパスの基
  //            コントローラ名/アクション名
  //parameters -> アクションメソッドから渡された配列データ
  //template -> レイアウトファイルの指定があればファイル名を取得
  public function render($filename, $parameters = array(), $template = false){
    //ビューファイルへのパスを生成
    $view = $this->_baseUrl . "/" . $filename . ".php";

    //配列の結合、キー名で変数・値を変数の値とする処理
    extract(array_merge($this->_initialValue, $parameters));

    //ビューファイル読み込み（バッファリング）
    //ビューファイルの中身を読み込んで変数に格納
    ob_start();
    ob_implicit_flush(0);
    require $view;
    $content = ob_get_clean();

    //$templateにレイアウトファイル名があれば、自身のrender()を実行してレイアウトファイルを読み込む
    if($template){
      $content = $this->render($template, array_merge($this->_passValues, array("_content" => $content)));
    }
    //HTMLドキュメント返却
    return $content;
  }

  //HTMLエスケープ
  public function escape($string){
    return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
  }
}
?>
