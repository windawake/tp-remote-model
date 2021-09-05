<?php

namespace TpRemoteModel\Remodel;

use think\db\Connection;

class RemoteConnection extends Connection
{
    // 数据库连接参数配置
    protected $config = [
        // 数据库类型
        'type'            => RemoteBuilder::class,
        'builder'         => RemoteBuilder::class,
    ];

    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param  array $config 数据库配置数组
     */
    // public function __construct(array $config = [])
    // {
    //     parent::__construct($config);

        
    //     if (!$this->builder) {
    //         $class = $this->config['builder'];
    //         // 创建Builder对象
    //         $this->builder = new $class($this);
    //     }
    // }

    /**
     * 解析pdo连接的dsn信息
     * @access protected
     * @param  array $config 连接信息
     * @return string
     */
    protected function parseDsn($config)
    {
        return '';
    }

    /**
     * 取得数据表的字段信息
     * @access public
     * @param  string $tableName
     * @return array
     */
    public function getFields($tableName)
    {
        return [];
    }

    /**
     * 取得数据库的表信息
     * @access public
     * @param  string $dbName
     * @return array
     */
    public function getTables($dbName = '')
    {
        return [];
    }

    /**
     * SQL性能分析
     * @access protected
     * @param  string $sql
     * @return array
     */
    protected function getExplain($sql)
    {

        return [];
    }


}
