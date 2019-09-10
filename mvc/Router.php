<?php
class Router{
  //ルーティング情報を保持
  protected $_convertedRoutes;

  //-------コンストラクター----------
  //URL解析用メソッド呼び出し、プロパティに格納
  //routedef->ルーティングを定義する配列
  public function __construct($routedef){
    $this->_convertedRoutes = $this->routeConverter($routedef);
  }

  //-------routeConverterメソッド----------
  //ルーティングの定義を内部用に変換
  //よくわからん
  public function routeConverter($routedef){
    //分割したコントローラ、アクション名を配列で保持
    $converts = array();

    //urlにパス情報、parにコントローラー、アクションのパラメータ
    foreach($routedef as $url => $par){

      //ltrimで先頭の/を取り除く
      //explodeで/で分割した文字列をconvertsに格納
      $converts = explode("/", ltrim($url, "/"));
      foreach($converts as $i => $convert){

        //先頭が:なら:以降をbarへ格納。convertに正規表現パターンとしてbarと名前をつけて、
        if(0 === strpos($convert, ":")){
          $bar = substr($convert, 1);
          $convert = "(?<" . $bar . ">[^/]+)";
        }
        $converts[$i] = $convert;
      }
      $pattern = "/" . implode("/", $converts);
      $converted[$pattern] = $par;
    }
    return $converted;
  }

  //-------getRouteParamsメソッド----------
  //そのリクエストはルーティング定義にマッチしているか
  //よくわからん
  public function getRouteParams($path){
    //リクエストURLのパス情報の先頭に/がなければ/を付ける
    if("/" !== substr($path, 0, 1)){
      $path = "/" . $path;
    }
    foreach($this->_convertedRoutes as $pattern => $par){
      //pathがpatternと一致するか確認（p_matchに格納される）
      if(preg_match("#^" . $pattern . "$#", $path, $p_match)){
        //配列結合
        $par = array_merge($par, $p_match);
        return $par;
      }
    }
    return false;
  }

  

}
?>
