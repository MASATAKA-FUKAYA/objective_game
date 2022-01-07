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
    public function setAttackMin($num){
        $this->attackMin = $num;
    }
    public function getAttackMin(){
        return $this->attackMin;
    }
    public function setAttackMax($num){
        $this->attackMin = $num;
    }
    public function getAttackMax(){
        return $this->attackMin;
    }

    public function attack($targetObj){
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        History::set($this->name.'の攻撃!!');
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
    protected $maxHp;
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
    public function setMaxHp($num){
        $this->maxHp = $num;
    }
    public function getMaxHp(){
        return $this->maxHp;
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
                History::set('もっと!♡');
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
//魔法使いクラス
class Wizard extends Human{
    //魔法攻撃用。魔法攻撃を行うとMPが減る
    private $mp;

    //コンストラクタ
    public function __construct($name, $sex, $hp, $attackMin, $attackMax){
        parent::__construct($name, $sex, $hp, $attackMin, $attackMax);
        //MPは、インスタンス生成時に50-100の間でランダムに決定
        $this->mp = mt_rand(50, 100);
    }

    //セッター・ゲッター
    public function setMp($num){
        $this->mp = $num;
    }
    public function getMp(){
        return $this->mp;
    }

    //攻撃メソッド（オーバーライド）
    public function attack($targetObj){
        if($this->mp !==0 && !mt_rand(0,2)){ //MPが0ではない場合、1/3の確率で魔法攻撃

            //魔法攻撃を行うとMPが10減る
            $this->mp -= 10;

            History::set($this->name.'の魔法攻撃!!');

            if($targetObj instanceof FlyMonster){
                //モンスターが「空を飛べるモンスター」の場合、魔法攻撃のダメージは必ず1.5倍
                //「効果は抜群だ」の表示をする
                $attackPoint = mt_rand($this->attackMin, $this->attackMax);
                $attackPoint = $attackPoint * 1.5;
                $attackPoint = (int)$attackPoint;
                $targetObj->setHp($targetObj->getHp() - $attackPoint);
                History::set($attackPoint.'ポイントのダメージ!');
                History::set($attackPoint.'効果は抜群だ!!!');
            }else{
                //それ以外のモンスターの場合、魔法攻撃はランダムで通常攻撃の50%~200%のダメージになる
                $attackPoint = mt_rand($this->attackMin * 0.5, $this->attackMax * 2);
                $attackPoint = (int)$attackPoint;
                $targetObj->setHp($targetObj->getHp() - $attackPoint);
                History::set($attackPoint.'ポイントのダメージ!');
            }

        }else{ //MPが0の時には必ず通常攻撃、通常攻撃はHumanクラスと一緒
            parent::attack($targetObj);
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

    //攻撃はオーバーライドで魔法攻撃を追加
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
//空を飛べるモンスタークラス
class FlyMonster extends Monster{
    
    //攻撃はオーバーライドで空からの体当たりを追加
    public function attack($targetObj){
        if(!mt_rand(0,2)){ // 1/3の確率で空からの攻撃
            $attackPoint = mt_rand($this->attackMin, $this->attackMax);
            $attackPoint = $attackPoint * 1.2; //空中攻撃は攻撃力1.2倍
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'の空からの体当たり攻撃!');
            $targetObj->setHp($targetObj->getHp() - $attackPoint);
            History::set($attackPoint.'ポイントのダメージを受けた!');
            $this->setHp($this->getHp() - 20); //空中攻撃はモンスター自身もhpが減る
            History::set($this->name. 'も20ポイントのダメージを受けた!');
        }else{
            parent::attack($targetObj);
        }
    }

}//神様クラス
class God{
    private $name;
    Private $img;

    //コンストラクタ
    public function __construct($name, $img){
        $this->name = $name;
        $this->img = $img;
    }

    //ゲッター
    public function getName(){
        return $this->name;
    }
    public function getImg(){
        return $this->img;
    }

    //プレイヤーに以下のアクションを選択させる
    //1.回復
    public function recover($targetObj){
        $targetObj->setHp($targetObj->getMaxHp());
        History::set($this->name. 'がHPを全回復させてくれた!');
    }
    //2.攻撃力アップ
    public function powerUp($targetObj){
        $targetObj->setAttackMin($targetObj->getAttackMin() + 20);
        $targetObj->setAttackMax($targetObj->getAttackMax() + 20);
        History::set($this->name. 'が最小・最大攻撃力を20上げてくれた!');
    }
    //3.HPアップ
    public function maxhpUp($targetObj){
        $targetObj->setMaxHp($targetObj->getMaxHp() + 500);
        History::set($this->name. 'が最大HPを2倍にしてくれた!');
    }
}

//インターフェース
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