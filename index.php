<?php

require('data.php');
session_start();

error_log('index.php');
error_log(print_r($_SESSION['human'], true));

//TOPページから遷移してきた時にはここでモンスター生成、ノックダウンカウントも初期化
if(empty($_SESSION['monster'])){
    createMonster();
}
if(is_null($_SESSION['knockDownCount'])){
    $_SESSION['knockDownCount'] = 0;
}

//POST送信されていた場合
if(!empty($_POST)){
    error_log('POSTされました。');

    $startFlg = (!empty($_POST['start'])) ? true : false;

    if($startFlg){ //スタートボタン（ゲームリスタートボタン）を押した場合
        //History::set('ゲームスタート!');
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
                    header("Location:gameOver.php");
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
                header("Location:gameOver.php");
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
            <p>自分のHP：<?php echo $_SESSION['human']->getHp(); ?>/<?php echo $_SESSION['human']->getMaxHp(); ?></p>
            <p>自分の回復回数：<?php echo $_SESSION['human']->getRecoverCount(); ?></p>
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