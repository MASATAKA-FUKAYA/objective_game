<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

function createMonster(){
    global $monsters;
    $monster = $monsters[mt_rand(0,2)];
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
?>