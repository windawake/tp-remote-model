<?php

namespace Windawake\TpRemoteModel\Remodel;

use think\db\Builder;
use think\db\Query;
use think\Exception;


class RemoteBuilder extends Builder
{
    protected $wheresArr = [];
    /**
     * 生成查询条件SQL
     * @access public
     * @param mixed     $where
     * @param array     $options
     * @return array
     */
    public function tpBuildWhere($where, $options)
    {
        if (empty($where)) {
            $where = [];
        }

        if ($where instanceof Query) {
            return $this->tpBuildWhere($where->getOptions('where'), $options);
        }

        foreach ($where as $boolean => $val) {
            foreach ($val as $field => $value) {
                if (is_int($field) && is_array($value)) {
                    $field = array_shift($value);
                }

                if ($value instanceof \Closure) {
                    // 使用闭包查询
                    $query = new Query($this->connection);
                    call_user_func_array($value, [ & $query]);
                    $this->tpBuildWhere($query->getOptions('where'), $options);
                    
                } elseif (strpos($field, '|')) {
                    // 不同字段使用相同查询条件（OR）
                    $array = explode('|', $field);
                    foreach ($array as $k) {
                        $this->tpParseWhereItem($k, $value, $boolean, $options);
                    }
                } elseif (strpos($field, '&')) {
                    // 不同字段使用相同查询条件（AND）
                    $array = explode('&', $field);
                    foreach ($array as $k) {
                        $this->tpParseWhereItem($k, $value, $boolean, $options);
                    }
                } else {
                    // 对字段使用表达式查询
                    $field = is_string($field) ? $field : '';
                    $this->tpParseWhereItem($field, $value, $boolean, $options);
                }
            }
        }

        return $this->wheresArr;
    }

    // where子单元分析
    protected function tpParseWhereItem($field, $val, $rule = '', $options = [], $binds = [], $bindName = null)
    {
        // 字段分析
        $key = $field ? $this->parseKey($field, $options) : '';

        // 查询规则和条件
        if (!is_array($val)) {
            $val = is_null($val) ? ['null', ''] : ['=', $val];
        }
        list($exp, $value) = $val;

        // 对一个字段使用多个查询条件
        if (is_array($exp)) {
            $item = array_pop($val);
            // 传入 or 或者 and
            if (is_string($item) && in_array($item, ['AND', 'and', 'OR', 'or'])) {
                $rule = $item;
            } else {
                array_push($val, $item);
            }
            foreach ($val as $k => $item) {
                $this->tpParseWhereItem($field, $item, $rule, $options);
            }
            
            return;
        }

        // 检测操作符
        if (!in_array($exp, $this->exp)) {
            $exp = strtolower($exp);
            if (isset($this->exp[$exp])) {
                $exp = $this->exp[$exp];
            } else {
                throw new Exception('where express error:' . $exp);
            }
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            // 对象数据写入
            $value = $value->__toString();
        }
        
        if (in_array($exp, ['=', '<>', '>', '>=', '<', '<='])) {
            // 比较运算
            if ($value instanceof \Closure) {
                $this->wheresArr[$rule][$field][] = [$exp, $this->tpParseClosure($value)];
            } else {
                $this->wheresArr[$rule][$field][] = [$exp, $value];
            }
        } elseif ('LIKE' == $exp || 'NOT LIKE' == $exp) {
            // 模糊匹配
            if (is_array($value)) {
                foreach ($value as $item) {
                    $array[] = [$exp, $item];
                }
                $logic = isset($val[2]) ? $val[2] : 'AND';
                $this->wheresArr[$rule][$field][] = [$logic, $array];
            } else {
                $this->wheresArr[$rule][$field][] = [$exp, $value];
            }
        } elseif ('EXP' == $exp) {
            // 表达式查询
            throw new \Exception('不支持exp表达式');
        } elseif (in_array($exp, ['NOT NULL', 'NULL'])) {
            // NULL 查询
            $this->wheresArr[$rule][$field][] = [$exp, ''];
        } elseif (in_array($exp, ['NOT IN', 'IN'])) {
            // IN 查询
            if ($value instanceof \Closure) {
                $this->wheresArr[$rule][$field][] = [$exp, $this->tpParseClosure($value)];
            } else {
                $value = array_unique(is_array($value) ? $value : explode(',', $value));
                $this->wheresArr[$rule][$field][] = [$exp, $value];
            }
        } elseif (in_array($exp, ['NOT BETWEEN', 'BETWEEN'])) {
            // BETWEEN 查询
            $data = is_array($value) ? $value : explode(',', $value);
            $between = $data;
            $this->wheresArr[$rule][$field][] = [$exp, $between];
        } elseif (in_array($exp, ['NOT EXISTS', 'EXISTS'])) {
            // EXISTS 查询
            if ($value instanceof \Closure) {
                $this->wheresArr[$rule][$field][] = [$exp, $this->tpParseClosure($value)];
            } else {
                $this->wheresArr[$rule][$field][] = [$exp ,$value];
            }
        } elseif (in_array($exp, ['< TIME', '> TIME', '<= TIME', '>= TIME'])) {
            $this->wheresArr[$rule][$field][] = [$exp ,$value];
        } elseif (in_array($exp, ['BETWEEN TIME', 'NOT BETWEEN TIME'])) {
            if (is_string($value)) {
                $value = explode(',', $value);
            }

            $this->wheresArr[$rule][$field][] = [$exp ,$value];
        }
    }

    protected function tpParseClosure($value){
        throw new \Exception('不支持子查询');
    }
}
