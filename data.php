<?php

require('function.php');
require('class.php');

//モンスター達格納用
$monsters = array();

//インスタンス生成
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

?>