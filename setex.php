<?php
define('DISKROOMS', 'DISKROOMS');
include 'redis.class.php';
$redis = new Hredis('127.0.0.1');
$redis->setex('gift-1');