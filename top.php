<?php

require('function.php');
require('class.php');

session_start();
$_SESSION = array();

error_log('topページ');
error_log(print_r($_SESSION,true));
//POST送信されていた場合
if(!empty($_POST)){
    error_log('POSTされました。');

    $humanFlg = $_POST['human'];
    switch($humanFlg){
        case 'man':
            $_SESSION['human'] = new Human('男勇者', Sex::MAN, 500, 40, 120);
            error_log('man');
            break;
        case 'woman':
            $_SESSION['human'] = new Human('女勇者', Sex::WOMAN, 500, 40, 120);
            error_log('woman');
            break;
        case 'okama':
            $_SESSION['human'] = new Human('オカマ勇者', Sex::OKAMA, 500, 40, 120);
            error_log('okama');
            break;    

    }
    
    header("Location:index.php");
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
        <h2 style="margin-top:60px;">GAME START ?</h2>
        <form method="post">
            <input type="radio" id="man" name="human" value="man">
            <label for="man">男勇者</label>
            <input type="radio" id="woman" name="human" value="woman" checked>
            <label for="woman">女勇者</label>
            <input type="radio" id="okama" name="human" value="okama">
            <label for="okama">オカマ勇者</label>
            <input type="submit" value="▶ゲームスタート">
       </form>
    </div>
</body>
</html>