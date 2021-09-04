<?php

namespace Windawake\TpRemoteModel\Remodel;

use app\modelRemote\RemoteBuidler;
use think\db\Connection;

/**
 * mysql数据库驱动
 */
class RemoteConnection extends Connection
{

    protected $builder = RemoteBuidler::class;
    protected $builderClassName = RemoteBuidler::class;

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
