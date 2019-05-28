<?php

/**
 * redis操作类
 * 说明，任何为false的串，存在redis中都是空串。
 * 只有在key不存在时，才会返回false。
 * 这点可用于防止缓存穿透
 */

Class RedisDriver
{
    private $redis;
    protected $dbId = 0; //当前数据库ID号
    static private $_instance = array();    //实例化的对象,单例模式.
    private $k;
    protected $attr = array(
        'timeout' => 30,    //连接超时时间，redis配置文件中默认为300秒
        'db_id' => 0    //选择单数据库
    );  //连接属性数组
    protected $expireTime;  //什么时候重新建立连接
    protected $host;    //服务器地址
    protected $port;    //端口号
    protected $auth;    //当前权限验证码

    /**
     * RedisDriver constructor.
     * @param $config
     * @param array $attr
     */
    private function __construct($config, $attr = array())
    {
        $this->attr = array_merge($this->attr, $attr);
        $this->redis = new Redis();
        $this->host = $config['host'] ? $config['host'] : '127.0.0.1';
        $this->port = $config['port'] ? $config['port'] : 6379;
        $this->redis->connect($this->host, $this->port, $this->attr['timeout']);
        if ($config['auth']) {
            $this->auth($config['auth']);
            $this->auth = $config['auth'];
        }
        $this->expireTime = time() + $this->attr['timeout'];
    }

    /**
     * 获取Redis实例
     * @param $config
     * @param array $attr
     * @return mixed
     */
    public static function getInstance($config, $attr = array())
    {
        //如果是一个字符串，将其认为是数据库单ID号，以简化写法。
        if (!is_array($attr)) {
            $dbId = $attr;
            $attr = array();
            $attr['db_id'] = $dbId;
        }
        //如果是数组,如array('db_id'=>1)
        @$attr['db_id'] = $attr['db_id'] ? $attr['db_id'] : 0;
        $k = md5(implode('', $config) . $attr['db_id']);
        if (!(@static::$_instance[$k] instanceof self)) {
            static::$_instance[$k] = new self($config, $attr);
            static::$_instance[$k]->k = $k;
            static::$_instance[$k]->dbId = $attr['db_id'];

            //如果不是0号库，选择一下数据库。
            if ($attr['db_id'] != 0) {
                static::$_instance[$k]->select($attr['db_id']);
            }
        } elseif (time() > static::$_instance[$k]->expireTime) {
            static::$_instance[$k]->close();
            static::$_instance[$k] = new self($config, $attr);
            static::$_instance[$k] = $k;
            static::$_instance[$k]->dbId = $attr['db_id'];

            //如果不是0号库，选择一下数据库。
            if ($attr['db_id'] != 0) {
                static::$_instance[$k]->select($attr['db_id']);
            }
        }
        return static::$_instance[$k];
    }

    /**
     * 私有化克隆函数，防止类外部克隆对象
     */
    private function __clone()
    {
    }

    /**
     * 执行原生的redis操作
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /*************redis字符串操作命令*****************/

    /**
     * 设置一个key
     * @param $key
     * @param $value
     * @return bool
     */
    public function set($key, $value)
    {
        return $this->redis->set($key, $value);
    }

    /**
     * 得到一个key
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * 设置一个有过期时间的key
     * @param $key
     * @param $expire,单位为秒
     * @param $value
     * @return bool
     */
    public function setex($key, $expire, $value)
    {
        return $this->redis->setex($key, $expire, $value);
    }

    /**
     * 设置一个key，如果key存在，不做任何操作
     * @param $key
     * @param $value
     * @return bool
     */
    public function setnx($key, $value)
    {
        return $this->redis->setnx($key, $value);
    }

    /**
     * 批量设置key
     * @param $arr
     * @return int
     */
    public function mset($arr)
    {
        return $this->redis->mset($arr);
    }

    /*****************hash表操作*******************/

    /**
     * 为hash表设定一个字段的值
     * @param $key 缓存key
     * @param $field 字段
     * @param $value 值
     * @return bool
     */
    public function hSet($key, $field, $value)
    {
        return $this->redis->hSet($key, $field, $value);
    }

    /**
     * 得到hash表中一个字段的值
     * @param $key 缓存key
     * @param $field 字段
     * @return string|false
     */
    public function hGet($key, $field)
    {
        return $this->redis->hGet($key, $field);
    }

    /**
     * 判断hash表中，指定field是不是存在
     * @param $key 缓存key
     * @param $field 字段
     * @return bool
     */
    public function hExists($key, $field)
    {
        return $this->redis->hExists($key, $field);
    }

    /**
     * 删除hash表中指定字段，支持批量删除
     * @param $key 缓存key
     * @param $field 字段
     * @return int
     */
    public function hDel($key, $field)
    {
        $fieldArr = explode(',', $field);
        $delNum = 0;
        foreach ($fieldArr as $row) {
            $row = trim($row);
            $delNum += $this->redis->hDel($key, $row);
        }
        return $delNum;
    }

    /**
     * 返回hash表中元素个数
     * @param $key 缓存key
     * @return int|bool
     */
    public function hLen($key)
    {
        return $this->redis->hLen($key);
    }

    /**
     * 为hash表设定一个字段单值，如果字段存在，返回false
     * @param $key 缓存key
     * @param $field 字段
     * @param $value 值
     * @return bool
     */
    public function hSetNx($key, $field, $value)
    {
        return $this->redis->hSetNx($key, $field, $value);
    }

    /**
     * 为hash表多个字段设定值
     * @param $key
     * @param $value
     * @return bool
     */
    public function hMset($key, $value)
    {
        if (!is_array($value)) {
            return false;
        }
        return $this->redis->hMset($key, $value);
    }

    /**
     * 获取hash表多个字段的值
     * @param $key
     * @param $field |string string以','号分隔字段
     * @return array|bool
     */
    public function hMget($key, $field)
    {
        if (!is_array($field))
            $field = explode(',', $field);
        return $this->redis->hMGet($key, $field);
    }

    /**
     * 为hash表设置累加，可以负数
     * @param $key
     * @param $field
     * @param $value
     * @return int
     */
    public function hIncrBy($key, $field, $value)
    {
        $value = intval($value);
        return $this->redis->hIncrBy($key, $field, $value);
    }

    /**
     * 返回所有hash表的所有字段
     * @param string $key
     * @return array|bool
     */
    public function hKeys($key)
    {
        return $this->redis->hKeys($key);
    }

    /**
     * 返回所有hash表单字段值，为一个索引数组
     * @param string $key
     * @return array|bool
     */
    public function hVals($key)
    {
        return $this->redis->hVals($key);
    }

    /**
     * 返回所有hash表的字段值，为一个关联数组
     * @param string $key
     * @return array|bool
     */
    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    /*********************队列操作命令************************/

    /**
     * 在队列尾部插入一个元素
     * @param $key 缓存key
     * @param $value 值
     * @return int 队列长度
     */
    public function rPush($key, $value)
    {
        return $this->redis->rPush($key, $value);
    }

    /**
     * 在队列尾部插入一个元素，如果key不存在，什么也不做
     * @param $key 缓存key
     * @param $value 值
     * @return int 队列长度
     */
    public function rPushx($key, $value)
    {
        return $this->redis->rPushx($key, $value);
    }

    /**
     * 在队列头部插入一个元素
     * @param $key
     * @param $value
     * @return int 返回队列长度
     */
    public function lPush($key, $value)
    {
        return $this->redis->lPush($key, $value);
    }

    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param $key
     * @param $value
     * @return int 返回队列长度
     */
    public function lPushx($key, $value)
    {
        return $this->redis->lPushx($key, $value);
    }

    /**
     * 返回队列长度
     * @param $key
     * @return int
     */
    public function lLen($key)
    {
        return $this->redis->lLen($key);
    }

    /**
     * 返回队列指定区间单元素
     * @param $key
     * @param $start 索引开始值
     * @param $end 索引结束值，数字大于当前最大索引值，则返回所有
     * @return array
     */
    public function lRange($key, $start, $end)
    {
        return $this->redis->lRange($key, $start, $end);
    }

    /**
     * 返回队列中指定索引的元素
     * @param $key
     * @param $index
     * @return String
     */
    public function lIndex($key, $index)
    {
        return $this->redis->lIndex($key, $index);
    }

    /**
     * 设定队列中指定index的值
     * @param $key
     * @param $index
     * @param $value
     * @return bool
     */
    public function lSet($key, $index, $value)
    {
        return $this->redis->lSet($key, $index, $value);
    }

    /**
     * 删除值为vaule的count个元素
     *count<0 从尾部开始
     * >0　从头部开始
     * =0　删除全部
     * @param $key
     * @param $count
     * @param $value
     * @return int
     */
    public function lRem($key, $count, $value)
    {
        return $this->redis->lRem($key, $value, $count);
    }

    /**
     * 删除并返回队列中的头元素
     * @param $key
     * @return string
     */
    public function lPop($key)
    {
        return $this->redis->lPop($key);
    }

    /**
     * 删除并返回队列中的尾元素
     * @param $key
     * @return string
     */
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    /*********************有序集合操作*********************/

    /**
     * 给当前集合添加一个元素，如果value已经存在，会更新order值
     * @param $key
     * @param $order 序号
     * @param $value 值
     * @return int
     */
    public function zAdd($key, $order, $value)
    {
        return $this->redis->zAdd($key, $order, $value);
    }

    /**
     * 给$value成员的order值，增加$num,可以为负数
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public function zIncrBy($key, $num, $value)
    {
        return $this->redis->zIncrBy($key, $num, $value);
    }

    /**
     * 删除值为value的元素
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function zRem($key, $value)
    {
        return $this->redis->zRem($key, $value);
    }

    /**
     * 集合以order递增排序，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRange($key, $start, $end)
    {
        return $this->redis->zRange($key, $start, $end);
    }

    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRevRange($key, $start, $end)
    {
        return $this->redis->zRevRange($key, $start, $end);
    }

    /**
     * 返回有序集key中，所有order值介于min和max之间(包括等于 min 或 max )的成员。
     * 有序集成员按 order 值递增(从小到大)次序排列。
     * @param string $key
     * @param int $start
     * @param int $end
     * @param array $option 参数
     * withscores=>true，表示数组下标为Order值，默认返回索引数组
     * limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRangeByScore($key, $start = '-inf', $end = '+inf', $option = array())
    {
        return $this->redis->zRangeByScore($key, $start, $end, $option);
    }

    /**
     * 返回有序集key中，所有order值介于min和max之间(包括等于 min 或 max )的成员。
     * 有序集成员按 order 值递减(从大到小)次序排列。
     * @param string $key
     * @param int $start
     * @param int $end
     * @param array $option 参数
     * withscores=>true，表示数组下标为Order值，默认返回索引数组
     * limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRevRangeByScore($key, $start = '-inf', $end = '+inf', $option = array())
    {
        return $this->redis->zRevRangeByScore($key, $start, $end, $option);
    }

    /**
     * 返回order值在start end之间的数量
     * @param $key
     * @param $start
     * @param $end
     * @return int
     */
    public function zCount($key, $start, $end)
    {
        return $this->redis->zCount($key, $start, $end);
    }

    /**
     * 返回值为value的order值
     * @param $key
     * @param $value
     * @return float|bool
     */
    public function zScore($key, $value)
    {
        return $this->redis->zScore($key, $value);
    }

    /**
     * 返回有序集key中成员member的排名。其中有序集成员按order值递增(从小到大)顺序排列
     * 排名以 0 为底，也就是说， score 值最小的成员排名为 0
     * @param $key
     * @param $value
     * @return int
     */
    public function zRank($key, $value)
    {
        return $this->redis->zRank($key, $value);
    }

    /**
     * 返回有序集key中成员member的排名。其中有序集成员按order值递减(从大到小)顺序排列
     * @param $key
     * @param $value
     * @return int
     */
    public function zRevRank($key, $value)
    {
        return $this->redis->zRevRank($key, $value);
    }

    /**
     * 删除集合中，score值在start end之间的元素,包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * @param $key
     * @param $start
     * @param $end
     * @return int 删除成员的数量
     */
    public function zRemRangeByScore($key, $start, $end)
    {
        return $this->redis->zRemRangeByScore($key, $start, $end);
    }

    /**
     * 返回集合元素个数。
     * @param $key
     * @return int
     */
    public function zCard($key)
    {
        return $this->redis->zCard($key);
    }

    /**********************无序集合操作命令*****************/

    /**
     * 返回集合中所有元素
     * @param $key
     * @return array
     */
    public function sMembers($key)
    {
        return $this->redis->sMembers($key);
    }

    /**
     * 求2个集合的差集
     * @param $key1
     * @param $key2
     * @return array
     */
    public function sDiff($key1, $key2)
    {
        return $this->redis->sDiff($key1, $key2);
    }

    /**
     * 添加集合。由于版本问题，扩展不支持批量添加。这里做了封装
     * @param $key
     * @param $value
     */
    public function sAdd($key, $value)
    {
        if (!is_array($value))
            $arr = array($value);
        else
            $arr = $value;
        foreach ($arr as $row) {
            $this->redis->sAdd($key, $row);
        }
    }

    /**
     * 返回无序集合的元素个数
     * @param $key
     * @return int
     */
    public function sCard($key)
    {
        return $this->redis->sCard($key);
    }

    /**
     * 移除并返回集合中的一个随机元素
     * @param $key
     * @return string
     */
    public function sPop($key){
        return $this->redis->sPop($key);
    }

    /**
     * 从集合中删除一个元素
     * @param $key
     * @param $value
     * @return int
     */
    public function sRem($key, $value)
    {
        return $this->redis->sRem($key, $value);
    }

    /*************redis管理操作命令*****************/

    /**
     * 选择数据库
     * @param $dbId 数据库ID号
     * @return bool
     */
    public function select($dbId)
    {
        $this->dbId = $dbId;
        return $this->redis->select($dbId);
    }

    /**
     * 清空当前数据库
     * @return bool
     */
    public function flushDB()
    {
        return $this->redis->flushDB();
    }

    /**
     * 返回当前库状态
     * @return string
     */
    public function info()
    {
        return $this->redis->info();
    }

    /**
     * 同步保存数据到磁盘
     * @return bool
     */
    public function save()
    {
        return $this->redis->save();
    }

    /**
     * 异步保存数据到磁盘
     * @return bool
     */
    public function bgSave()
    {
        return $this->redis->bgsave();
    }

    /**
     * 返回最后保存到磁盘的时间
     * @return int
     */
    public function lastSave()
    {
        return $this->redis->lastSave();
    }

    /**
     * 返回key，支持*多个字符，？表示一个字符
     * @param $key
     * @return array
     */
    public function keys($key)
    {
        return $this->redis->keys($key);
    }

    /**
     * 删除指定key
     * @param $key
     * @return int
     */
    public function del($key)
    {
        return $this->redis->del($key);
    }

    /**
     * 判断一个key值是不是存在
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 为一个key设定过期时间 单位为秒
     * @param $key
     * @param $expire
     * @return bool
     */
    public function expire($key, $expire)
    {
        return $this->redis->expire($key, $expire);
    }

    /**
     * 返回一个key还有多久过期，单位秒
     * @param $key
     * @return int
     */
    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

    /**
     * 设置一个key什么时候过期，time为一个时间戳
     * @param $key
     * @param $time
     * @return bool
     */
    public function expireAt($key, $time)
    {
        return $this->redis->expireAt($key, $time);
    }

    /**
     * 关闭服务器连接
     */
    public function close()
    {
        return $this->redis->close();
    }

    /**
     * 关闭所有连接
     */
    public static function closeAll()
    {
        foreach (static::$_instance as $o) {
            if ($o instanceof self)
                $o->close();
        }
    }

    /**
     * 返回当前数据库key数量
     * @return int
     */
    public function dbSize()
    {
        return $this->redis->dbSize();
    }

    /**
     * 返回一个随机key
     * @return string
     */
    public function randomKey()
    {
        return $this->redis->randomKey();
    }

    /**
     * 得到当前数据库ID
     * @return int
     */
    public function getDbId()
    {
        return $this->dbId;
    }

    /**
     * 返回当前密码
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * 返回服务器ip
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * 返回服务器端口号
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * 返回连接信息
     * @return array
     */
    public function getConnInfo()
    {
        return array(
            'host' => $this->host,
            'port' => $this->port,
            'auth' => $this->auth
        );
    }

    /****************事务的相关方法**************/

    /**
     * 监控key，就是一个或多个key添加一个乐观锁
     * 在此期间如果key的值发生的改变，则不能为key设定值
     * 可以重新取得key的值。
     * @param $key
     */
    public function watch($key)
    {
        return $this->redis->watch($key);
    }

    /**
     * 取消当前链接对所有key的watch
     * EXEC命令或DISCARD命令先被执行了的话，那么就不需要再执行UNWATCH了
     */
    public function unwatch()
    {
        return $this->redis->unwatch();
    }

    /**
     * 开启一个事务
     * 事务的调用有两种模式Redis::MULTI和Redis::PIPELINE，
     * 默认是Redis::MULTI模式，
     * Redis::PIPELINE管道模式速度更快，但没有任何保证原子性有可能造成数据的丢失
     * @param int $type
     * @return Redis
     */
    public function multi($type = Redis::MULTI)
    {
        return $this->redis->multi($type);
    }

    /**
     * 执行一个事务
     * 收到 EXEC 命令后进入事务执行，事务中任意命令执行失败，其余的命令依然被执行
     */
    public function exec()
    {
        return $this->redis->exec();
    }

    /**
     * 回滚一个事务
     */
    public function discard()
    {
        return $this->redis->discard();
    }

    /**
     * 测试当前连接是否失效
     * @return string 没有失效返回PONG 失效返回false
     */
    public function ping()
    {
        return $this->redis->ping();
    }

    /**
     * auth验证
     * @param $auth
     * @return bool
     */
    public function auth($auth)
    {
        return $this->redis->auth($auth);
    }

    /************自定义的方法,用于简化操作************/

    /**
     * 存储数组
     * @param string $key
     * @param array $value
     * @return bool
     */
    public function setArr($key, $value)
    {
        $value = json_encode($value);
        return $this->set($key, $value);
    }

    /**
     * 根据key获取数组
     * @param $key
     * @return mixed
     */
    public function getArr($key)
    {
        return json_decode($this->get($key), true);
    }

    /**
     * 得到一组的ID号
     * @param $prefix
     * @param $ids
     * @return array|bool
     */
    public function hashAll($prefix, $ids)
    {
        if ($ids == false)
            return false;
        if (is_string($ids))
            $ids = explode(',', $ids);
        $arr = array();
        foreach ($ids as $id) {
            $key = $prefix . '.' . $id;
            $res = $this->hGetAll($key);
            if ($res != false)
                $arr[] = $res;
        }
        return $arr;
    }

    /**
     * 生成一条消息，放到redis数据库中，使用0号库
     * @param $lkey
     * @param $msg
     * @return string
     */
    public function pushMessage($lkey, $msg)
    {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $key = md5($msg);
        $this->lPush($lkey, $key);
        $this->setex($key, 3600, $msg);
        return $key;
    }

    /**
     * 得到条批量删除key的命令
     * @param $keys
     * @param $dbId
     * @return string
     */
    public function delKeys($keys, $dbId)
    {
        $redisInfo = $this->getConnInfo();
        $cmdArr = array(
            'redis-cli',
            '-a',
            $redisInfo['auth'],
            '-h',
            $redisInfo['host'],
            '-p',
            $redisInfo['port'],
            '-n',
            $dbId,
        );
        $redisStr = implode(' ', $cmdArr);
        $cmd = "{$redisStr} KEYS \"{$keys}\" | xargs {$redisStr} del";
        return $cmd;
    }

}