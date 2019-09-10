<?php
class Loader{

  //オートロードするディレクトリを保持
  protected $_directories = array();

  //$dir -> オートロード対象フォルダー（配列）
  //$_directoriesへ格納
  public function regDirectory($dir){
    $this->_directories[] = $dir;
  }

  //クラスを読み込むメソッドをコールバックとして登録
  //自身(Loaderクラス)のrequireClsFileを登録
  //new Class名で、まだ読み込まれていないクラス名だった場合、requireClsFileを実行する
  public function register(){
    spl_autoload_register(array($this, "requireClsFile"));
  }

  //読み込まれていないクラスが呼び出された時にコールバックされるメソッド
  //呼び出すクラス名がパラメータに渡される
  //$_directoriesからクラスのパスを生成、読み込み可能であれば読み込む
  public function requireClsFile($class){ 
    foreach ($this->_directories as $dir){
      $file = $dir . "/" . $class . ".php";
      if (is_readable($file)){
        require $file;
        return;
      }
    }
  }
}
