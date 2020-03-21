<?php
/**
 * 1.   生产环境建议打开此注释
 *      并在单入口文件中定义diskroom变量
 *      只允许单入口文件访问该文件
 *2.  Notice default_socket_timeout 配置项需要放到connect之前 否则无法覆盖php.ini中的配置
 */
if(!defined(DISKROOMS)){
    exit('hacks');
}

class Hredis{
    private static $redis_config = [];
    private static $instance_redis = NULL; 
    
    public function __construct($ip = '127.0.0.1',$port = '6379',$pwd = ''){
        self::$redis_config = [
            'ip'=>$ip,
            'port'=>$port,
            'pwd'=>$pwd
        ];
        if(self::$instance_redis == null){
            self::$instance_redis = new Redis();
        }
        if(!self::$instance_redis instanceof Redis){
            throw new Exception('实例化未完成');
         }
    }
    
    /**
     * 连接 redis 服务器
    * @param integer $default_socket_timeout 客户端socket超时时间
     */
    private function _conncet($default_socket_timeout =  3){
        set_time_limit(0);
        ini_set('default_socket_timeout', $default_socket_timeout);
        self::$instance_redis->connect(self::$redis_config['ip'],self::$redis_config['port']);
        if(!empty(self::$redis_config['pwd'])){
            self::$instance_redis->auth(self::$redis_config['pwd']);
        }
    }
    
    /**
     * 测试redis服务 notify-keyspace-events 配置是否打开
     * 设置socket过期时间 2秒后还未收到消息即说明没有配置 notify-keyspace-events 参数
     */
    public function testNotifyKeyEvent(){
        $this->_conncet();
            //设置一秒过期
        self::$instance_redis->setex("testNotifyKeyEvent",1,"testNotifyKeyEvent");
           try{
        self::$instance_redis->subscribe(array('__keyevent@0__:expired'),function($instance,$channelName, $message){
            //var_dump($instance);
            //var_dump($channelName);
            //var_dump($message);
            if($message === 'testNotifyKeyEvent'){
                exit('ok, notify-keyspace-events 已配置');
                }
            });
           } catch (RedisException $e){
               echo $e->getMessage()."\r\n";
                exit('Redis服务配置 notify-keyspace-events 未打开,请将其设置为Ex 并重启Redis服务');
           }
        }
    
    /**
     * 注册监听频道函数
     */
    public function registerListenFunction($callbackFunc = '',$channelName = '__keyevent@0__:expired'){
        $this->_conncet(-1);
        self::$instance_redis->subscribe(array($channelName),$callbackFunc);
    }
    
    /**
     * 往频道中写入数据
     */
    public function publish($msg = '',$channelName = ''){
        self::$instance_redis->publish($channelName,$msg);
    }
    
    /**
     * 往Redis中写入数据并设置过期时间
     */
    public function setex($key = '',$value = '', $timeout = 3){
        self::$instance_redis->setex($key,$timeout,$value);
    }
}
