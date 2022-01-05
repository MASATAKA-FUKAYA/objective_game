<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

//モンスター達格納用
$monsters = array();

require('class.php');

session_start();

//インスタンス生成
$human = new Human('勇者見習い', Sex::OKAMA, 500, 40, 120);
$monsters[] = new Monster( 'フランケン', 100, 'img/monster01.png', 20, 40 );
$monsters[] = new MagicMonster( 'フランケンNEO', 300, 'img/monster02.png', 20, 60, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュリー', 200, 'img/monster03.png', 30, 50 );
$monsters[] = new MagicMonster( 'ドラキュラ男爵', 400, 'img/monster04.png', 50, 80, mt_rand(60, 120) );
$monsters[] = new Monster( 'スカルフェイス', 150, 'img/monster05.png', 30, 60 );
$monsters[] = new Monster( '毒ハンド', 100, 'img/monster06.png', 10, 30 );
$monsters[] = new Monster( '泥ハンド', 120, 'img/monster07.png', 20, 30 );
$monsters[] = new Monster( '血のハンド', 180, 'img/monster08.png', 30, 50 );

function createMonster(){
    global $monsters;
    $monster = $monsters[mt_rand(0,7)];
    History::set($monster->getName().'が現れた!');
    $_SESSION['monster'] = $monster;
}
function createHuman(){
    global $human;
    $_SESSION['human'] = $human;
}
function init(){
    History::clear();
    History::set('初期化します。');
    $_SESSION['knockDownCount'] = 0;
    createHuman();
    createMonster();
}
function gameOver(){
    $_SESSION = array();
}

//POST送信されていた場合
if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    error_log('POSTされました。');

    if($startFlg){ //スタートボタンを押した場合
        History::set('ゲームスタート!');
        init();
    }else{
        if($attackFlg){ //攻撃するを押した場合

            //モンスターに攻撃を与える
            History::set($_SESSION['human']->getName().'の攻撃!');
            $_SESSION['human']->attack($_SESSION['monster']);
            $_SESSION['monster']->sayCry();

            //モンスターが攻撃する
            History::set($_SESSION['monster']->getName().'の攻撃!');
            $_SESSION['monster']->attack($_SESSION['human']);
            $_SESSION['human']->sayCry();

            //自分のhpが0以下になったらゲームオーバー
            if($_SESSION['human']->getHp() <= 0){
                gameOver();
            }else{
                //敵モンスターのhpが0以下になったら、別のモンスターを出現させる
                if($_SESSION['monster']->getHp() <=0){
                    History::set($_SESSION['monster']->getName().'を倒した!');  
                    $_SESSION['knockDownCount'] ++;
                    createMonster();
                }
            }
        }else{ //逃げるを押した場合
            History::set('逃げた!');
        }
    }
    $_POST = array();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 style="text-align:center; color:#333;">ゲーム「ドラ◯エ!!」</h1>
    <div style="background:black; padding:15px; position:relative;">
        <?php if(empty($_SESSION)): ?>
            <h2 style="margin-top:60px;">GAME START ?</h2>
            <form method="post">
                <input type="submit" name="start" value="▶ゲームスタート">
            </form>
        <?php else: ?>
            <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
            <div style="height: 150px;">
                <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
            </div>
            <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
            <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
            <form method="post">
                <input type="submit" name="attack" value="▶攻撃する">
                <input type="submit" name="escape" value="▶逃げる">
                <input type="submit" name="start" value="▶ゲームリスタート">
            </form>
        <?php endif; ?>
        <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>
</body>
</html>