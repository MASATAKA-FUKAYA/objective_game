<?php

error_log('gameoverページ');
error_log(print_r($_SESSION,true));
error_log(print_r('POSTの中身：'. $_POST,true));
//POST送信されていた場合
if(!empty($_POST)){
    error_log('POSTされました。');
    //セッションを終わり、トップページへ
    $_SESSION = array();
    header("Location:top.php");
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
        <h2 style="margin-top:60px;">GAME OVER...</h2>
        <form method="post">
            <input type="submit" value="▶もう一度チャレンジする">
       </form>
    </div>
</body>
</html>