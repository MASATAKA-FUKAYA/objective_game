<?php

//性別クラス（人間用）
class Sex{
    const MAN = 1;
    const WOMAN = 2;
    const OKAMA = 3;
}

//抽象クラス（生き物クラス、人間・モンスターへ派生）
abstract class Creature{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;
    
    abstract public function sayCry(); //攻撃された時に悲鳴をあげる
    
    //セッター・ゲッター
    public function setName($str){
        $this->name = $str;
    }
    public function getName(){
        return $this->name;
    }
    public function setHp($num){
        $this->hp = $num;
    }
    public function getHp(){
        return $this->hp;
    }

    public function attack($targetObj){
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        if(!mt_rand(0,9)){ //　1/10の確率でクリティカルヒット
            $attackPoint = $attackPoint * 1.5;
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'のクリティカルヒット!!');
        }
        $targetObj->setHp($targetObj->getHp() - $attackPoint);
        History::set($attackPoint.'ポイントのダメージ!');
    }
}

//人クラス
class Human extends Creature{
    protected $sex;
    protected $maxHp; //回復する時の判定で使用。HPの初期値以上には回復できない。
    protected $recoverCount = 0;

    public function __construct($name, $sex, $hp, $attackMin, $attackMax){
        $this->name = $name;
        $this->sex = $sex;
        $this->hp = $hp;
        $this->maxHp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }

    //セッター・ゲッター
    public function getSex(){
        return $this->sex;
    }
    public function getRecoverCount(){
        return $this->recoverCount;
    }

    public function sayCry(){
        History::set($this->name.'が叫ぶ!');
        switch($this->sex){
            case Sex::MAN :
                History::set('ぐはっ!');
                break;
            case Sex::WOMAN :
                History::set('きゃっ!');
                break;
            case Sex::OKAMA :
                History::set('もっと!');
                break;
        }
    }

    //回復する
    public function recover(){
        //回復は3回まで。現在の回復回数を判定
        if($this->recoverCount < 3){ //今までに3回以下なら回復行動
            
            $recoverPoint = mt_rand(10,100); //10〜100ポイントの間でHP回復

            if( ($this->getHp() + $recoverPoint) <= $this->maxHp){ //回復してもHPが満タンにならない場合
                $this->setHp($this->getHp() + $recoverPoint);
                History::set($this->name. 'は'. $recoverPoint. 'ポイントのHPを回復!');
                $this->recoverCount ++;
            }else{ //回復するとHPが満タンになる場合
                $recoverPoint = $this->maxHp - $this->getHp();
                $this->setHp($this->maxHp);
                History::set($this->name. 'は'. $recoverPoint. 'ポイントのHPを回復!');
                $this->recoverCount ++;
            } 
        }else{ //既に3回回復している場合
            History::set($this->name. 'はもう回復できない!!');
        }
    }
}
//モンスタークラス
class Monster extends Creature{
    protected $img;

    public function __construct($name, $hp, $img, $attackMin, $attackMax){
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    //ゲッター
    public function getImg(){
        return $this->img;
    }
    
    public function sayCry(){
        History::set($this->name.'が叫ぶ!');
        History::set('はうっ!');
    }
}
//魔法を使えるモンスタークラス
class MagicMonster extends Monster{
    private $magicAttack;

    function __construct($name, $hp, $img, $attackMin, $attackMax, $magicAttack){
        parent::__construct($name, $hp, $img, $attackMin, $attackMax);
        $this->magicAttack = $magicAttack;
    }

    //ゲッター
    public function getMagicAttack(){
        return $this->magicAttack;
    }

    //オーバーライド
    public function attack($targetObj){
        if(!mt_rand(0,4)){ // 1/5の確率で魔法攻撃
            History::set($this->name.'の魔法攻撃!!');
            $targetObj->setHp( $targetObj->getHp() - $this->magicAttack);
            History::set($this->magicAttack.'ポイントのダメージを受けた!');
        }else{
            parent::attack($targetObj);
        }
    }
}

interface HistoryInterface{
    public static function set($str);
    public static function clear();
}
//履歴管理クラス（インスタンス化して複数に増殖させる必要がないクラスなので、staticにする）
class History implements HistoryInterface{
    public static function set($str){
        //セッションhistoryが作られていなければ作る
        if(empty($_SESSION['history'])) $_SESSION['history'] = '';
        //文字列をセッションhistoryへ格納
        $_SESSION['history'] .= $str.'<br>';
    }
    public static function clear(){
        unset($_SESSION['history']);
    }
}

?>