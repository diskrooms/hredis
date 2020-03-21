<?php
define('DISKROOMS', 'DISKROOMS');
include 'redis.class.php';
$redis = new Hredis();
$redis->setex('gift-1');