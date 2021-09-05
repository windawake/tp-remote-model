<?php

namespace TpRemoteModel\Remodel;

use think\db\Query;

class RemoteQuery extends Query {
    protected $lastInsID;

    /**
     * @var RemoteModel
     */
    protected $model;
    protected $builder;

    public function select($data = null)
    {
        $options = $this->parseExpress();
        if (!is_null($data)) {
            // 主键条件分析
            $this->parsePkWhere($data, $options);
        }

        $result = $this->model->setOptions($options)->selectHandle($data);
        if (!$result) return [];

        foreach ($result as $index => $item) {
            $model = $this->model->newInstance($item);
            $model->isUpdate(true);
            $result[$index] = $model;
        }

        if (!empty($options['with'])) {
            // 预载入
            $this->model->eagerlyResultSet($result, $options['with']);
        }
        
        return $result;
    }

    public function find($data = null)
    {
        // 分析查询表达式
        $options = $this->parseExpress();
          if (!is_null($data)) {
            // 主键条件分析
            $this->parsePkWhere($data, $options);
        }

        $result = $this->model->setOptions($options)->findHandle($data);
        if (!$result) return null;

        $model = $this->model->newInstance($result);
        $model->isUpdate(true);

        // 预载入查询
        if (!empty($options['with'])) {
            $this->model->eagerlyResult($model, $options['with']);
        }

        return $model;
    }

    public function update(array $data = [])
    {
        $options = $this->parseExpress();

        return $this->model->setOptions($options)->updateHandle($data);
    }

    /**
     * 插入记录
     * @access public
     * @param mixed   $data         数据
     * @param boolean $replace      是否replace
     * @param boolean $getLastInsID 返回自增主键
     * @param string  $sequence     自增序列名
     * @return integer|string
     */
    public function insert(array $data = [], $replace = false, $getLastInsID = false, $sequence = null)
    {
        // 分析查询表达式
        $options = $this->parseExpress();
        $data    = array_merge($options['data'], $data);

        $this->lastInsID = $this->model->setOptions($options)->insertHandle($data);

        // 执行操作
        return $this->lastInsID;
    }

    public function getLastInsID($sequence = null)
    {
        return $this->lastInsID;
    }

    /**
     * 删除记录
     * @access public
     * @param mixed $data 表达式 true 表示强制删除
     * @return int
     * @throws Exception
     * @throws PDOException
     */
    public function delete($data = null)
    {
        // 分析查询表达式
        $options = $this->parseExpress();
        if (!is_null($data)) {
            // 主键条件分析
            $this->parsePkWhere($data, $options);
        }
        
        $ret = $this->model->setOptions($options)->deleteHandle($data);
        return $ret;
    }

    public function value($field, $default = null, $force = false)
    {
        // 分析查询表达式
        $options = $this->parseExpress();
        $options['field'] = [$field];

        $result = $this->model->setOptions($options)->findHandle();
        if ($pos = strpos($field, ' AS ')) {
            $field = trim(substr($field, $pos+4), '` ');
        }
        
        $value = $result[$field] ?? $default;

        return $value;
    }

    public function column($field, $key = '')
    {
        // 分析查询表达式
        $options = $this->parseExpress();
        $options['field'] = [$field];

        $result = $this->model->setOptions($options)->selectHandle();
        $field = $field == '*' ? null : $field;
        if ($pos = strpos($field, ' AS ')) {
            $field = trim(substr($field, $pos+4), '` ');
        }
        
        $columns = array_column($result, $field, $key);

        return $columns;
    }

    /**
     * COUNT查询
     * @access public
     * @param string $field 字段名
     * @return integer|string
     */
    public function count($field = '*')
    {
        return $this->value('COUNT(' . $field . ') AS tp_count', 0, true);
    }

    /**
     * SUM查询
     * @access public
     * @param string $field 字段名
     * @return float|int
     */
    public function sum($field)
    {
        return $this->value('SUM(' . $field . ') AS tp_sum', 0, true);
    }

    /**
     * MIN查询
     * @access public
     * @param string $field 字段名
     * @param bool   $force   强制转为数字类型
     * @return mixed
     */
    public function min($field, $force = true)
    {
        return $this->value('MIN(' . $field . ') AS tp_min', 0, $force);
    }

    /**
     * MAX查询
     * @access public
     * @param string $field 字段名
     * @param bool   $force   强制转为数字类型
     * @return mixed
     */
    public function max($field, $force = true)
    {
        return $this->value('MAX(' . $field . ') AS tp_max', 0, $force);
    }

    /**
     * AVG查询
     * @access public
     * @param string $field 字段名
     * @return float|int
     */
    public function avg($field)
    {
        return $this->value('AVG(' . $field . ') AS tp_avg', 0, true);
    }

    protected function parseExpress()
    {
        $options = $this->getOptions();

        if ($this->builder) {
            $builder = $this->builder;
        } else {
            $builder = $this->connection->getBuilder();

        }
        $options['where'] = $builder->tpBuildWhere($options['where'], $options);

        return $options;
    }
}  