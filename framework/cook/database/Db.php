<?php

namespace cook\database;

use cook\core\Config;
use cook\log\Log;
use PDO;
use Throwable;
use PDOStatement;
use cook\database\ORM\Query as ORM;

/**
 * 数据库类
 * @author cookphp <admin@cookphp.org>
 */
class Db {

    /**
     * 表前缀
     * @var string
     */
    public $dbprefix = '';

    /**
     * 字段标识
     * @var string
     */
    public $identifier = '';

    /**
     * 具体驱动的连接选项
     * @var array
     */
    public $options = [];

    /**
     * 是否记录日志
     * @var bool
     */
    public $logging = false;

    /**
     * 错误
     * @var array
     */
    public $errorinfo = null;

    /**
     * 写入服务器
     * @var PDO
     */
    public $linkWrite;

    /**
     * 读取服务器
     * @var PDO
     */
    public $linkRead;

    /**
     * 配制
     * @var Config
     */
    public $config;

    /**
     * 日志
     * @var Log
     */
    public $log;

    /**
     * ORM
     * @var ORM
     */
    public $ORM;

    public function __construct(Config $config, Log $log, ORM $orm) {
        $this->config = $config;
        $this->log = $log;
        $this->ORM = $orm;
        $this->initialize();
    }

    /**
     * 初始化
     * @param array $config
     * @return $this
     */
    public function initialize(array $config = null): Db {
        !empty($config) || ($config = $this->config->db);
        $this->identifier = $config['identifier'] ?? '``';
        $this->dbprefix = $config['dbprefix'] ?? '';
        $this->options = $config['options'] ?? '';
        $this->logging = $config['logging'] ?? false;
        if ($config['separate']) {
            $read = $config['read'][array_rand($config['read'])];
            $write = $config['write'][array_rand($config['write'])];
            $this->linkRead = $this->connect($read['dsn'] ?? null, $read['username'] ?? null, $read['password'] ?? null);
            $this->linkWrite = $this->connect($write['dsn'] ?? null, $write['username'] ?? null, $write['password'] ?? null);
        } else {
            $read = $config['read'][array_rand($config['read'])];
            $this->linkWrite = $this->linkRead = $this->connect($read['dsn'] ?? null, $read['username'] ?? null, $read['password'] ?? null);
        }
        return $this;
    }

    /**
     * 连接数据库
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @return PDO
     */
    protected function connect($dsn, $username, $password) {
        try {
            return new PDO($dsn, $username, $password, $this->options + [PDO::ATTR_PERSISTENT => true, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * 返回驱动名称
     * @return string
     */
    public function getDriveName(): string {
        return $this->linkRead->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * 返回客户端库的版本信息
     * @return string
     */
    public function getClientVersion(): string {
        return $this->linkRead->getAttribute(PDO::ATTR_CLIENT_VERSION);
    }

    /**
     * 返回数据库服务的版本信息
     * @return string
     */
    public function getServerVersion(): string {
        return $this->linkRead->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * 启动事务
     * @return bool
     */
    public function beginTransaction(): bool {
        if ($this->linkWrite->beginTransaction()) {
            $this->logging && $this->log->sql('[DB]启动事务');
            return true;
        }
        return false;
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit(): bool {
        if ($this->linkWrite->commit()) {
            $this->logging && $this->log->sql('[DB]提交事务');
            return true;
        }
        return false;
    }

    /**
     * 回滚事务
     * @return bool
     */
    public function rollBack(): bool {
        if ($this->linkWrite->rollBack()) {
            $this->logging && $this->log->sql('[DB]回滚事务');
            return true;
        }
        return false;
    }

    /**
     * 检查是否在事务内
     * @return bool
     */
    public function inTransaction(): bool {
        return $this->linkWrite->inTransaction();
    }

    /**
     * 返回最后插入行的ID或序列值
     * @return string
     */
    public function lastInsertId() {
        return $this->linkWrite->lastInsertId();
    }

    /**
     * 确定查询是否为“写入”类型
     * @param	string SQL
     * @return	bool
     */
    public function isWriteType($sql): bool {
        return (bool) preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX|MERGE)\s/i', $sql);
    }

    /**
     * 查询SQL语句
     * @param string $statement SQL语句
     * @param string $parameters 绑定的参数
     * @return \PDOStatement|false
     */
    public function query(string $statement, array $parameters = []) {
        $sth = $this->prepareExecute($statement, $parameters);
        return $sth instanceof PDOStatement ? $sth : false;
    }

    /**
     * 执行的SQL语句 DELETE、INSERT、UPDATE
     * 返回受影响的行数
     * @param string $statement SQL语句
     * @param string $parameters 绑定的参数
     * @return int
     */
    public function exec(string $statement, array $parameters = []): int {
        //自动启动事务
        !$this->inTransaction() && $this->beginTransaction();
        $sth = $this->prepareExecute($statement, $parameters);
        return $sth instanceof PDOStatement ? $sth->rowCount() : 0;
    }

    /**
     * 执行语句并返回语句对象
     * @param string $statement SQL语句
     * SQL语句可以（:name）或问号（?）做参数标记
     * 有效防止SQL注入攻击
     * @param string $parameters 绑定的参数
     * @return \PDOStatement|false
     * @throws PDOException
     * @throws Exception
     */
    public function prepareExecute(string $statement, array $parameters = []) {
        $pdo = $this->isWriteType($statement) ? $this->linkWrite : $this->linkRead;
        $start = microtime(true);
        $sth = $pdo->prepare($statement);
        if ($pdo->errorCode() === PDO::ERR_NONE) {
            if ($sth->execute($parameters) !== false) {
                $this->setPrepareLog($start, $statement);
                return $sth;
            }
            $this->inTransaction() && $this->rollBack();
        } else {
            $this->errorinfo = ['code' => $pdo->errorCode(), 'message' => $pdo->errorInfo()[2]];
            //print_r($this->errorinfo);exit;
        }
        return false;
        //$sth->debugDumpParams();
    }

    /**
     * 引用用于查询的字符串
     * 返回在理论上安全传入SQL语句的引用字符串
     * @param string $string
     * @return string
     */
    public function quote(string $string): string {
        return $this->linkRead->quote($string);
    }

    /**
     * 处理字段和表名转义标识符
     * PDO不提供此功能
     * @param string|array $string
     * @return string
     */
    public function identifier($string) {
        if (!is_array($string)) {
            $string = preg_split('/\s*,\s*/', trim($string), -1, PREG_SPLIT_NO_EMPTY);
        }
        $strings = [];
        foreach ($string as $value) {
            if (preg_match('/(.*) AS (.*)/i', $value, $matches)) {
                $strings[] = $this->name($matches[1]) . ' AS ' . $this->name($matches[2]);
            } else {
                $strings[] = $this->name($value);
            }
        }
        return implode(',', $strings);
    }

    /**
     * 转义字段
     * @param string $string
     * @return string
     */
    public function name($string): string {
        if (preg_match('/^[A-Za-z\-\_]+$/', $string) && stristr($string, '*') === false) {
            $string = $this->identifier && strlen($this->identifier) === 2 ? $this->identifier[0] . trim($string) . $this->identifier[1] : trim($string);
        } else {
            $string = trim($string);
        }
        return $string;
    }

    private function setPrepareLog($start, $message) {
        $this->logging && $this->log->sql('[SQL]' . number_format(microtime(true) - $start, 6) . ' ' . $message);
    }

    public function __destruct() {
        //自动提交事务
        $this->inTransaction() && $this->commit();
    }

}
