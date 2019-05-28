<?php

if (!defined('INI_SYS')) {
    exit;
}

//mysql数据库基类
class mysql
{
    public $link;
    public $output = FALSE;//是否输出错误信息
    public $conn;

    public  $sql        = '';//sql语句，主要用于输出构造成的sql语句

    //连接数据库
    public function connect($db) {
        $dbsn = 'mysql:host=' . $db['db_host'] . ';dbname=' . $db['db_name'] . ';port=' . $db['db_port'] . ';charset=utf8';
        try {
            $this->conn = new PDO($dbsn, $db['db_user'], $db['db_pass']);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * 执行 mysql_query 并返回其结果.
     */
    public function query($sql) {
        $result = $this->conn->exec($sql);

        return $result;
    }

    public function getSql() {
        return $this->sql;
    }

    /**
     * 执行 SQL 语句, 返回结果的第一条记录(是一个对象).
     */
    public function get($sql) {
        $this->sql = $sql;
        $select = $this->conn->query($sql);
        if (!$select) {
            return FALSE;
        }
        $row = $select->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    /**
     * 返回查询结果集, 以 key 为键组织成关联数组, 每一个元素是一个对象.
     * 如果 key 为空, 则将结果组织成普通的数组.
     */
    public function find($sql, $key = NULL) {
        
        $this->sql = $sql;

        $select = $this->conn->query($sql);
        if (!$select) {
            return FALSE;
        }
        $data = $select->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public function last_insert_id() {
        return $this->conn->lastInsertId();
    }

    /**
     * 保存一条记录
     * @param object $row
     */
    public function save($table, &$row) {
        $sqlA = '';
        if(is_array($row) && !empty($row)){
            foreach ($row as $k => $v) {
                $sqlA .= "`$k` = '" . $v . "',";
            }
        }else{
            return FALSE;
        }
        $sqlA = substr($sqlA, 0, -1);
        $this->sql  = "INSERT INTO {$table} SET $sqlA";


        if ($this->query($this->sql)) {
            $id = $this->last_insert_id();
            if ($id) return $id;

            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 更新$arr[id]所指定的记录.
     * @param array $row 要更新的记录, 键名为id的数组项的值指示了所要更新的记录.
     * @return int 影响的行数.
     * @param string $field 字段名, 默认为'id'.
     */
    public function update($table, &$row, $field = 'id') {
        $sqlF = '';
        foreach ($row as $k => $v) {
            $sqlF .= "`$k` = '$v',";
        }
        $sqlF = substr($sqlF, 0, -1);
        if (is_object($row)) {
            $id = $row->{$field};
        } else {
            $id = $row[ $field ];
        }

        $this->sql = "UPDATE {$table} SET $sqlF WHERE `{$field}`='$id'";

        return $this->query($this->sql);
    }

    /**
     * 删除一条记录.
     * @param int $id 要删除的记录编号.
     * @return int 影响的行数.
     * @param string $field 字段名, 默认为'id'.
     */
    public function remove($table, $id, $field = 'id') {
        $sql = "DELETE FROM {$table} WHERE `{$field}`='{$id}'";

        return $this->query($sql);
    }

    /**
     * 开始事务
     */
    public function begin() {
        $this->query('begin');
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->query('commit');
    }

    /**
     * 事务回滚
     */
    public function rollback() {
        $this->query('rollback');
    }

}

//继承基类扩展
class DbMysql extends mysql
{
    public  $pre        = '';//表前缀，主要用于在其他地方获取表前缀
    private $data       = array();// 数据信息
    private $options    = array(); // 查询表达式参数
    private $comparison = array(
        'eq'   => '=', 'neq' => '!=', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE',
        'like' => 'LIKE'
    );//数据库表达式

    public function __construct() {
        $this->pre               = defined('DB_PREFIX') ? DB_PREFIX : '';//数据库表前缀,优先级defined
        $this->options['_field'] = '*';//默认查询字段
    }

    //设置表，$$ignore_prefix为true的时候，不加上默认的表前缀
    public function table($table, $ignore_prefix = FALSE) {
        if ($ignore_prefix) {
            $this->options['_table'] = $this->_parseSpecialField($table);
        } else {
            $table                   = $this->pre . $table;
            $this->options['_table'] = $this->_parseSpecialField($table);
        }

        return $this;
    }

    //回调方法，连贯操作的实现
    public function __call($method, $args) {
        $method = strtolower($method);
        if (in_array($method, array('field', 'data', 'where', 'group', 'having', 'order', 'limit'))) {
            $this->options[ '_' . $method ] = $args[0];//接收数据

            return $this;//返回对象，连贯查询
        } else {
            //$this->halt($method.'方法在类中没有定义');
        }
    }
    //执行原生sql语句，如果sql是查询语句，返回二维数组
    /*  public function query($sql){
          if(empty($sql)){
              return false;
          }
          $this->sql = $sql;
          //判断当前的sql是否是查询语句
          if(strpos(trim(strtolower($sql)),'select')===0){
              return $this->getAll($this->sql);
          }else{
              return $query = $this->Q($this->sql);//不是查询条件，执行之后，直接返回
          }
      }*/
    //统计行数
    public function count($field = '') {
        $table = $this->options['_table'];//当前表
        if ($field == '') {//查询的字段
            $field = 'count(*)';
        } else {
            $field = 'count(' . $field . ')';
        }
        $where     = $this->_parseCondition();//条件
        $this->sql = "SELECT $field FROM $table $where";
        $query     = $this->conn->query($this->sql);
        $count     = $query->fetchColumn();

        return $count;
    }

    //只查询一条信息，返回一维数组
    public function finds() {
        $table = $this->options['_table'];//当前表
        $field = $this->options['_field'];//查询的字段
        $field = $this->_parseField($field);
        if (!isset($this->options['_limit'])) {
            $this->options['_limit'] = '0,1';//只查询一条
        }
        $where                   = $this->_parseCondition();//条件
        $this->options['_field'] = '*';//设置下一次查询时，字段的默认值
        $this->sql               = "SELECT $field FROM $table $where";
        //$query = $this->db->Q($this->sql);
        $data = $this->get($this->sql);

        return $data;
    }

    //查询多条信息，返回数组
    public function selects() {
        $table                   = $this->options['_table'];//当前表
        $field                   = $this->options['_field'];//查询的字段
        $field                   = $this->_parseField($field);
        $where                   = $this->_parseCondition();//条件
        $this->options['_field'] = '*';//设置下一次查询时，字段的默认值
        $this->sql               = "SELECT $field FROM $table $where";

        return mysql::find($this->sql);
    }

    //插入数据
    public function insert() {
        $table = $this->options['_table'];//当前表
        $data  = $this->_parseData('add');//要插入的数据
        if (empty($data)) return FALSE;
        $this->save($table, $data);

        return FALSE;
    }

    //替换数据
    public function replace() {
        $table     = $this->options['_table'];//当前表
        $data      = $this->_parseData('add');//要插入的数据
        $this->sql = "REPLACE INTO $table $data";
        $query     = $this->Q($this->sql);
        if ($this->affected_rows()) {
            return $this->last_insert_id();
        }

        return FALSE;
    }

    //修改更新
    public function updates() {
        $table = $this->options['_table'];//当前表
        $data  = $this->_parseData('save');//要更新的数据
        $where = $this->_parseCondition();//更新条件
        if (empty($data)) return FALSE;
        //修改条件为空时，则返回false，避免不小心将整个表数据修改了
        if (empty($where)) return FALSE;
        $this->sql = "UPDATE $table $data $where";

        return $this->query($this->sql);
    }

    //删除
    public function delete() {
        $table = $this->options['_table'];//当前表
        $where = $this->_parseCondition();//条件
        //删除条件为空时，则返回false，避免数据不小心被全部删除
        if (empty($where)) {
            return FALSE;
        }
        $this->sql = "DELETE FROM $table $where";

        return $this->query($this->sql);
    }

    ///////////////////////////////////////// 以下为分析数组的function //////////////////////////////////////////////////////////////////
    //解析数据,添加数据时$type=add,更新数据时$type=save
    private function _parseData($type) {
        if ((!isset($this->options['_data'])) || (empty($this->options['_data']))) {
            unset($this->options['_data']);

            return FALSE;
        }
        //如果数据是字符串，直接返回
        if (is_string($this->options['_data'])) {
            $data = $this->options['_data'];
            unset($this->options['_data']);

            return $data;
        }
        switch ($type) {
            case 'add':
                foreach ($this->options['_data'] as $key => $value) {
                    $value = $this->_parseValue($value);
                    if ($value === FALSE || $value === TRUE) continue;//过滤恒为false和true
                    if (is_scalar($value)) { // 过滤非标量数据
                        $values[] = $value;
                        $fields[] = $this->_parseSpecialField($key);
                    }
                }
                unset($this->options['_data']);

                return ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
                break;
            case 'save':
                foreach ($this->options['_data'] as $key => $value) {
                    $value = $this->_parseValue($value);
                    if ($value === FALSE || $value === TRUE) continue;//过滤恒为false和true
                    if (is_scalar($value)) // 过滤非标量数据
                        $set[] = $this->_parseSpecialField($key) . '=' . $value;
                }
                unset($this->options['_data']);

                return ' SET ' . implode(',', $set);
                break;
            default:
                unset($this->options['_data']);

                return FALSE;
        }
    }

    //解析sql查询条件
    private function _parseCondition() {
        $condition = "";
        //解析where()方法
        if (!empty($this->options['_where'])) {
            $where = $this->options['_where'];
            $where = $this->_parseWhere($where);
            if ($where) {
                $condition .= ' WHERE ' . $where;
            }
            unset($this->options['_where']);
        }

        if (!empty($this->options['_group']) && is_string($this->options['_group'])) {
            $condition .= " GROUP BY " . $this->options['_group'];
            unset($this->options['_group']);
        }

        if (!empty($this->options['_having']) && is_string($this->options['_having'])) {
            $condition .= " HAVING " . $this->options['_having'];
            unset($this->options['_having']);
        }

        if (!empty($this->options['_order']) && is_string($this->options['_order'])) {
            $condition .= " ORDER BY " . $this->options['_order'];
            unset($this->options['_order']);
        }
        if (!empty($this->options['_limit']) && (is_string($this->options['_limit']) || is_numeric($this->options['_limit']))) {
            $condition .= " LIMIT " . $this->options['_limit'];
            unset($this->options['_limit']);
        }
        if (empty($condition)) return "";

        return $condition;
    }

    //where条件分析
    private function _parseWhere($where) {
        $whereStr = '';
        if (is_string($where)) {
            // 直接使用字符串条件
            $whereStr = $where;
        } else { // 使用数组条件表达式
            if (array_key_exists('_logic', $where)) {
                // 定义逻辑运算规则 例如 OR XOR AND NOT
                $operate = ' ' . strtoupper($where['_logic']) . ' ';
                unset($where['_logic']);
            } else {
                // 默认进行 AND 运算
                $operate = ' AND ';
            }
            foreach ($where as $key => $val) {
                if (is_array($val) && empty($val)) continue;
                $whereStr .= "( ";
                if (0 === strpos($key, '_')) {
                    // 解析特殊条件表达式
                    $whereStr .= $this->_parseSpecialWhere($key, $val);
                } else {
                    $key = $this->_parseSpecialField($key);
                    if (is_array($val)) {
                        if (is_string($val[0])) {
                            if (preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE)$/i', $val[0])) { // 比较运算
                                $whereStr .= $key . ' ' . $this->comparison[ strtolower($val[0]) ] . ' ' . $this->_parseValue($val[1]);
                            } elseif ('exp' == strtolower($val[0])) { // 使用表达式
                                $whereStr .= ' (' . $key . ' ' . $val[1] . ') ';
                            } elseif (preg_match('/IN/i', $val[0])) { // IN 运算
                                if (is_string($val[1])) {
                                    $val[1] = explode(',', $val[1]);
                                }
                                $zone     = implode(',', $this->_parseValue($val[1]));
                                $whereStr .= $key . ' ' . strtoupper($val[0]) . ' (' . $zone . ')';
                            } elseif (preg_match('/BETWEEN/i', $val[0])) { // BETWEEN运算
                                $data     = is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                                $whereStr .= ' (' . $key . ' BETWEEN ' . $data[0] . ' AND ' . $data[1] . ' )';
                            } else {
                                //$this->halt($val[0]);
                            }
                        } else {
                            $count = count($val);
                            if (is_string($val[ $count - 1 ]) && in_array(strtoupper(trim($val[ $count - 1 ])), array(
                                    'AND', 'OR', 'XOR'
                                ))
                            ) {
                                $rule  = strtoupper(trim($val[ $count - 1 ]));
                                $count = $count - 1;
                            } else {
                                $rule = 'AND';
                            }
                            for ($i = 0; $i < $count; $i++) {
                                $data = is_array($val[ $i ]) ? $val[ $i ][1] : $val[ $i ];
                                if ('exp' == strtolower($val[ $i ][0])) {
                                    $whereStr .= '(' . $key . ' ' . $data . ') ' . $rule . ' ';
                                } else {
                                    $op       = is_array($val[ $i ]) ? $this->comparison[ strtolower($val[ $i ][0]) ] : '=';
                                    $whereStr .= '(' . $key . ' ' . $op . ' ' . $this->_parseValue($data) . ') ' . $rule . ' ';
                                }
                            }
                            $whereStr = substr($whereStr, 0, -4);
                        }
                    } else {
                        $whereStr .= $key . " = " . $this->_parseValue($val);
                    }
                }
                $whereStr .= ' )' . $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }

        return empty($whereStr) ? '' : $whereStr;
    }

    //特殊条件分析
    private function _parseSpecialWhere($key, $val) {
        $whereStr = '';
        switch ($key) {
            case '_string':
                // 字符串模式查询条件
                $whereStr = $val;
                break;
            case '_complex':
                // 复合查询条件
                $whereStr = $this->_parseWhere($val);
                break;
            case '_query':
                // 字符串模式查询条件
                parse_str($val, $where);
                if (array_key_exists('_logic', $where)) {
                    $op = ' ' . strtoupper($where['_logic']) . ' ';
                    unset($where['_logic']);
                } else {
                    $op = ' AND ';
                }
                $array = array();
                foreach ($where as $field => $data) $array[] = $this->_parseSpecialField($field) . ' = ' . $this->_parseValue($data);
                $whereStr = implode($op, $array);
                break;
        }

        return $whereStr;
    }

    //field分析
    private function _parseField($fields) {
        if (is_array($fields)) {
            // 完善数组方式传字段名的支持
            // 支持 'field1'=>'field2' 这样的字段别名定义
            $array = array();
            foreach ($fields as $key => $field) {
                if (!is_numeric($key)) $array[] = $this->_parseSpecialField($key) . ' AS ' . $this->_parseSpecialField($field); else
                    $array[] = $this->_parseSpecialField($field);
            }
            $fieldsStr = implode(',', $array);
        } elseif (is_string($fields) && !empty($fields)) {
            $fieldsStr = $this->_parseSpecialField($fields);
        } else {
            $fieldsStr = '*';
        }

        return $fieldsStr;
    }

    //value分析
    private function _parseValue($value) {
        if (is_string($value)) {
            $value = '\'' . $value . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $value[1];
        } elseif (is_array($value)) {
            $value = array_map(array($this, '_parseValue'), $value);
        } elseif (is_null($value)) {
            //$value = 'null';
            $value = '\'\'';
        }

        return $value;
    }
    //* 字段和表名添加`
    //* 保证指令中使用关键字不出错 针对mysql
    private function _parseSpecialField(&$value) {
        $value = trim($value);
        if (FALSE !== strpos($value, ' ') || FALSE !== strpos($value, ',') || FALSE !== strpos($value, '*') || FALSE !== strpos($value, '(') || FALSE !== strpos($value, '.') || FALSE !== strpos($value, '`')) {
            //如果包含* 或者 使用了sql方法 则不作处理
        } else {
            $value = '`' . $value . '`';
        }

        return $value;
    }

}//类定义结束
