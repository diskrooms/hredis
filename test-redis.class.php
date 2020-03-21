<?php
define('DISKROOMS', 'DISKROOMS');
include 'redis.class.php';
$redis = new Hredis();
$redis->testNotifyKeyEvent(); //测试是否打开 notify-keyspace-events 配置,强烈建议使用此方法进行测试.测试完毕后再关闭
$redis->registerListenFunction('gift'); //注册监听函数

//业务逻辑函数
function gift($instance,$channelName, $message){
    //这里编写你的业务逻辑 根据$message里包含的信息(红包、优惠券id等)来修改数据库中对应的数据
    var_dump($message);
}