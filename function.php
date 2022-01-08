<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

function createMonster(){
    global $monsters;
    //５体目までモンスターを倒すと、以降はランダムでボス出現
    if($_SESSION['knockDownCount'] >= 5){
        $monster = $monsters[mt_rand(0,10)];
        History::set($monster->getName().'が現れた!');
    }else{
        $monster = $monsters[mt_rand(0,9)];
        History::set($monster->getName().'が現れた!!!!!!!!!');
    }
    
    $_SESSION['monster'] = $monster;
}
function init(){
    $_SESSION = array();
    header("Location:top.php");
}
?>