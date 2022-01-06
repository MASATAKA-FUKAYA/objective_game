<?php

require('function.php');
require('class.php');

session_start();

//モンスター達格納用
$monsters = array();

//インスタンス生成
$human = new Human('勇者見習い', Sex::OKAMA, 500, 40, 120);

$monsters[] = new God('神様','img/monster01.png');
$monsters[] = new Monster( 'フランケン', 100, 'img/monster01.png', 20, 40 );
$monsters[] = new MagicMonster( 'フランケンNEO', 300, 'img/monster02.png', 20, 60, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュリー', 200, 'img/monster03.png', 30, 50 );
$monsters[] = new MagicMonster( 'ドラキュラ男爵', 400, 'img/monster04.png', 50, 80, mt_rand(60, 120) );
$monsters[] = new Monster( 'スカルフェイス', 150, 'img/monster05.png', 30, 60 );
$monsters[] = new Monster( '毒ハンド', 100, 'img/monster06.png', 10, 30 );
$monsters[] = new Monster( '泥ハンド', 120, 'img/monster07.png', 20, 30 );
$monsters[] = new Monster( '血のハンド', 180, 'img/monster08.png', 30, 50 );
$monsters[] = new FlyMonster( 'ドラゴン', 200, 'img/monster09.png', 40, 70 );

//POST送信されていた場合
if(!empty($_POST)){
    error_log('POSTされました。');
    error_log(print_r($_SESSION['monster'],true));

    $startFlg = (!empty($_POST['start'])) ? true : false;

    if($startFlg){ //スタートボタン（ゲームリスタートボタン）を押した場合
        History::set('ゲームスタート!');
        init();
    }

    //神様が出現している場合
    if($_SESSION['monster'] instanceof God){
        
        $godRecoverFlg = (!empty($_POST['godRecover'])) ? true : false;
        $powerUpFlg = (!empty($_POST['powerUp'])) ? true : false;
        $maxhpUpFlg = (!empty($_POST['maxhpUp'])) ? true : false;

        if($godRecoverFlg){ 
            //回復してもらうを押した場合
            $_SESSION['monster']->recover($_SESSION['human']);
        }elseif($powerUpFlg){ 
            //強くしてもらうを押した場合
            $_SESSION['monster']->powerUp($_SESSION['human']);
        }elseif($maxhpUpFlg){ 
            //丈夫にしてもらうを押した場合
            $_SESSION['monster']->maxhpUp($_SESSION['human']);
            error_log(print_r($_SESSION['human']->getMaxHp(), true));
            error_log('神様がHPを上げてくれた');
        }

        //神様は一回行動したらいなくなる
        History::set($_SESSION['monster']->getName(). 'はいなくなってしまった…');
        createMonster();
    }

    //モンスターが出現している場合
    if($_SESSION['monster'] instanceof Monster){
        $attackFlg = (!empty($_POST['attack'])) ? true : false;
        $humanRecoverFlg = (!empty($_POST['humanRecover'])) ? true : false;
        $escapeFlg = (!empty($_POST['escape'])) ? true : false;

        if($attackFlg){ //攻撃するを押した場合
            //1.モンスターに攻撃を与える
            $_SESSION['human']->attack($_SESSION['monster']);            
            $_SESSION['monster']->sayCry();

            if($_SESSION['monster']->getHp() <=0){
                //2.1 敵モンスターのhpが0以下になったら、別のモンスターを出現させる
                History::set($_SESSION['monster']->getName().'を倒した!');  
                $_SESSION['knockDownCount'] ++;
                createMonster();
            }else{
                //2.2モンスターが攻撃する
                $_SESSION['monster']->attack($_SESSION['human']);
                $_SESSION['human']->sayCry();

                //2.2.1自分のhpが0以下になったらゲームオーバー
                if($_SESSION['human']->getHp() <= 0){
                    gameOver();
                }
            }
        }elseif($humanRecoverFlg){ //回復するを押した場合
            //1.自分のHPを回復する
            $_SESSION['human']->recover();

            //2.モンスターが攻撃する
            $_SESSION['monster']->attack($_SESSION['human']);
            $_SESSION['human']->sayCry();

            //2.1自分のhpが0以下になったらゲームオーバー
            if($_SESSION['human']->getHp() <= 0){
                gameOver();
            }
                
        }elseif($escapeFlg){ //逃げるを押した場合        
            History::set($_SESSION['monster']->getName(). 'から逃げた!');
            createMonster();
        }
    }

    $_POST = array();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
    <title>ゲーム「ドラ◯エ!!」</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ゲーム「ドラ◯エ!!」</h1>
    <div class="game-window">
        <?php if(empty($_SESSION)): ?>
            <h2 style="margin-top:60px;">GAME START ?</h2>
            <form method="post">
                <input type="submit" name="start" value="▶ゲームスタート">
            </form>
        <?php else: ?>
            <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
            <div style="height: 150px;">
                <img src="<?php echo $_SESSION['monster']->getImg(); ?>">
            </div>
            <?php if( !($_SESSION['monster'] instanceof God) ): ?>
                <p class="monster-hp">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
            <?php endif; ?>
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
            <p>勇者の回復回数：<?php echo $_SESSION['human']->getRecoverCount(); ?></p>
            <form method="post">
                <?php if($_SESSION['monster'] instanceof God): ?>
                    <input type="submit" name="godRecover" value="▶回復してもらう">
                    <input type="submit" name="powerUp" value="▶強くしてもらう">
                    <input type="submit" name="maxhpUp" value="▶丈夫にしてもらう">
                    <input type="submit" name="start" value="▶ゲームリスタート">
                <?php elseif($_SESSION['monster'] instanceof Monster): ?>
                    <input type="submit" name="attack" value="▶攻撃する">
                    <input type="submit" name="humanRecover" value="▶回復する">
                    <input type="submit" name="escape" value="▶逃げる">
                    <input type="submit" name="start" value="▶ゲームリスタート">
                <?php endif; ?>
            </form>
        <?php endif; ?>
        <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>
</body>
</html>