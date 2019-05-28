<?php

/**
 * Mysql数据库操作类
 * Class MyPDO
 */

class MyPDO
{
    protected static $_instance = null;
    protected $dsn;
    protected $dbh;

    protected $db_host = '127.0.0.1';
    protected $db_port = 3306;
    protected $db_name = 'test';
    protected $db_user = 'root';
    protected $db_pass = 'root';
    protected $db_charset = 'utf8';

    /**
     * MyPDO 构造函数.
     * @param $config
     */
    private function __construct($config)
    {
        try {
            $this->db_host = $config['db_host'] ? $config['db_host'] : $this->db_host;
            $this->db_port = $config['db_port'] ? $config['db_port'] : $this->db_port;
            $this->db_name = $config['db_name'] ? $config['db_name'] : $this->db_name;
            $this->db_user = $config['db_user'] ? $config['db_user'] : $this->db_user;
            $this->db_pass = $config['db_pass'] ? $config['db_pass'] : $this->db_pass;
            $this->db_charset = $config['db_charset'] ? $config['db_charset'] : $this->db_charset;
            $this->dsn = 'mysql:host=' . $this->db_host . ';port=' . $this->db_port . ';dbname=' . $this->db_name;
            $this->dbh = new PDO($this->dsn, $this->db_user, $this->db_pass);
            $this->dbh->exec('SET character_set_connection=' . $this->db_charset . ', character_set_results=' . $this->db_charset . ', character_set_client=binary');
        } catch (PDOException $e) {
            $this->outPutError($e->getMessage());
        }
    }

    /**
     * Singleton instance
     * @param $config
     * @return MyPDO|null
     */
    public static function getInstance($config)
    {
        if (self::$_instance === null) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    /**
     * 私有化克隆方法
     */
    private function __clone()
    {
    }

    /*************************基本操作**********************/
    /**
     * Query 查询
     * @param $sql
     * @param string $queryMode (All or Row)
     * @param bool $debug
     * @return array|mixed|null
     */
    public function query($sql, $queryMode = 'All', $debug = false)
    {
        if ($debug === true) $this->debug($sql);
        $record_set = $this->dbh->query($sql);
        $this->getPDOError();
        $result = null;
        if ($record_set) {
            $record_set->setFetchMode(PDO::FETCH_ASSOC);
            if ($queryMode == 'All') {
                $result = $record_set->fetchAll();
            } elseif ($queryMode == 'Row') {
                $result = $record_set->fetch();
            }
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Insert 插入
     * @param $table
     * @param $arrValue
     * @param bool $debug
     * @return int
     */
    public function insert($table, $arrValue, $debug = false)
    {
        $this->checkFields($table, $arrValue);
        $strSql = "INSERT INTO `$table` (`" . implode('`,`', array_keys($arrValue)) . "`) VALUE ('" . implode("','", $arrValue) . "')";
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 覆盖方式插入
     * @param $table
     * @param $arrValue
     * @param bool $debug
     * @return int
     */
    public function replace($table, $arrValue, $debug = false)
    {
        $this->checkFields($table, $arrValue);
        $strSql = "REPLACE INTO `$table` (`" . implode('`,`', array_keys($arrValue)) . "`) VALUES ('" . implode("','", $arrValue) . "')";
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 删除
     * @param $table
     * @param string $where
     * @param bool $debug
     * @return int
     */
    public function delete($table, $where = '', $debug = false)
    {
        if ($where == '') {
            $this->outPutError("'WHERE is Null'");
        } else {
            $strSql = "DELETE FROM `$table` WHERE $where";
            if ($debug === true) $this->debug($strSql);
            $result = $this->dbh->exec($strSql);
            $this->getPDOError();
            return $result;
        }
    }

    /**
     * update 更新
     * @param $table
     * @param $arrValue
     * @param string $where
     * @param bool $debug
     * @return int
     */
    public function update($table, $arrValue, $where = '', $debug = false)
    {
        $this->checkFields($table, $arrValue);
        if ($where) {
            $strSql = '';
            foreach ($arrValue as $key => $val) {
                $strSql .= ", `$key` = '$val'";
            }
            $strSql = substr($strSql, 1);
            $strSql = "UPDATE `$table` SET $strSql WHERE $where";
        } else {
            $strSql = "REPLACE INTO `$table` (`" . implode('`,`', array_keys($arrValue)) . "`) VALUE ('" . implode("','", $arrValue) . "')";
        }
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 执行sql语句
     * @param $strSql
     * @param bool $debug
     * @return int
     */
    public function execSql($strSql, $debug = false)
    {
        if ($debug === true) $this->debug($strSql);
        $result = $this->dbh->exec($strSql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 获取字段最大值
     * @param $table
     * @param $filed_name
     * @param string $where
     * @param bool $debug
     * @return int|mixed
     */
    public function getMaxValue($table, $filed_name, $where = '', $debug = false)
    {
        $sql = "SELECT MAX(" . $filed_name . ") AS MAX_VALUE FROM $table";
        if ($where != '') $sql .= "WHERE $where";
        if ($debug === true) $this->debug($sql);
        $arrTmp = $this->query($sql, 'Row');
        $maxValue = $arrTmp['MAX_VALUE'];
        if ($maxValue == "" || $maxValue == null) {
            $maxValue = 0;
        }
        return $maxValue;
    }

    /**
     * 获取指定列的数量
     * @param $table
     * @param $field_name
     * @param string $where
     * @param bool $debug
     * @return mixed
     */
    public function getCount($table, $field_name, $where = '', $debug = false)
    {
        $sql = "SELECT COUNT($field_name) AS NUM FROM $table";
        if ($where != '') $sql .= "WHERE $where";
        if ($debug === true) $this->debug($sql);
        $arrTmp = $this->query($sql, 'Row');
        return $arrTmp['NUM'];
    }

    /**
     * 获取表引擎
     * @param $dbName
     * @param $tableName
     * @return mixed
     */
    public function getTableEngine($dbName, $tableName)
    {
        $sql = "SHOW TABLE STATUS FROM $dbName WHERE NAME='" . $tableName . "'";
        $arrTabInfo = $this->query($sql);
        $this->getPDOError();
        return $arrTabInfo[0]['Engine'];
    }

    /*************************事务处理**********************/

    /**
     * 事务开始
     */
    public function beginTrans()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        $this->dbh->commit();
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->dbh->rollBack();
    }

    /**
     * 通过事务处理多条SQL语句
     * 调用前需通过getTableEngine判断表引擎是否支持事务
     * @param $arrSql
     * @return bool
     */
    public function execTrans($arrSql)
    {
        $retval = 1;
        $this->beginTrans();
        foreach ($arrSql as $strSql) {
            if ($this->execSql($strSql) == 0) $retval = 0;
        }
        if ($retval == 0) {
            $this->rollback();
            return false;
        } else {
            $this->commit();
            return true;
        }
    }

    /*************************其他函数**********************/

    /**
     * 检查指定字段是否在指定数据库表中存在
     * @param $table
     * @param $arrFields
     * @return bool
     */
    private function checkFields($table, $arrFields)
    {
        $tableFields = $this->getFields($table);
        foreach ($arrFields as $key => $val) {
            if (!in_array($key, $tableFields)) {
                $this->outPutError("Unknow column `$key` in field list.");
            }
        }
        return true;
    }

    /**
     * 获取指定数据表中的全部字段名
     * @param $table
     * @return array
     */
    private function getFields($table)
    {
        $fields = array();
        $record_set = $this->dbh->query("SHOW COLUMNS FROM $table");
        $this->getPDOError();
        $record_set->setFetchMode(PDO::FETCH_ASSOC);
        $res = $record_set->fetchAll();
        foreach ($res as $rows) {
            $fields[] = $rows['Field'];
        }
        return $fields;
    }

    /**
     * 捕获PDO错误信息
     */
    private function getPDOError()
    {
        if ($this->dbh->errorCode() != '00000') {
            $arrayError = $this->dbh->errorInfo();
            $this->outPutError($arrayError[2]);
        }
    }

    /**
     * 调试模式打印sql
     * @param $debugInfo
     */
    private function debug($debugInfo)
    {
        var_dump($debugInfo);
        exit();
    }

    /**
     * 输出错误信息
     * @param $strErrMsg
     * @throws Exception
     */
    private function outPutError($strErrMsg)
    {
        throw new Exception('MySQL Error: ' . $strErrMsg);
    }

    /**
     * 关闭数据库连接
     */
    public function destruct()
    {
        $this->dbh = null;
    }
}