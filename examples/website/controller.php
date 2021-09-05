<?php 

class Controller{
    protected $db;
    protected $table;
    protected $pk;
    protected $rawData;

    public function __construct($table, $rawData)
    {
        $pkMap = [
            'product' => 'pid',
            'order' => 'oid',
            'order_detail' => 'od_id'
        ];
        $this->db = new SQLite3(__DIR__.'/test.db');
        $this->table = $table;
        $this->pk = $pkMap[$table];
        $this->rawData = $rawData;
    }
    
    public function index(){
        $whereStr = $this->getWhereStr();
        $sql = "select * from `{$this->table}` {$whereStr}";
        $this->sqlLog($sql);

        $ret = $this->db->query($sql);
        $results = [];
        if ($ret) {
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                $results[] = $row;
            }
        }
        

        return $results;
    }

    public function show($id){
        $whereStr = $this->getWhereStr($id);
        $sql = "select * from `{$this->table}` {$whereStr}";
        $this->sqlLog($sql);

        $ret = $this->db->query($sql);
        $results = null;
        if ($ret) {
            while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                $results = $row;
            }
        }

        return $results;
    }

    public function store(){

    }

    public function update($id){
        $whereStr = $this->getWhereStr($id);
        $updateStr = $this->getUpdateStr();

        $sql = "update `{$this->table}` set {$updateStr} {$whereStr}";
        $this->sqlLog($sql);

        $this->db->exec($sql);
        return $this->db->changes();
    }

    public function destory($id){

    }

    private function sqlLog($sql)
    {
        error_log('['.date('Y-m-d H:i:s').'] '.$sql.';'.PHP_EOL, 3, __DIR__.'/sql.log');
    }

    private function getWhereStr($id = 0){
        if ($id) {
            return "where {$this->pk} = {$id} limit 1";
        }

        $whereStr = '';
        $whereAndArr = $this->rawData['options']['where']['AND'] ?? [];
        foreach($whereAndArr as $field => $condList) {
            $sqlArr = [];
            foreach($condList as $cond){
                list($exp, $value) = $cond;
                if (in_array($exp, ['=', '<>', '>', '>=', '<', '<=', 'LIKE'])) {
                    $sqlArr[] = "`{$field}` {$exp} '{$value}'";
                }
                if (in_array($exp, ['IN', 'NOT IN'])) {
                    $value = implode(',', $value);
                    $sqlArr[] = "`{$field}` {$exp} ({$value})";
                }
            }

            $whereStr = implode(' AND ', $sqlArr);
        }
        if ($whereStr) $whereStr = 'where '.$whereStr;

        return $whereStr;
    }

    private function getUpdateStr() {
        $data = $this->rawData['options']['data'];
        $sqlArr = [];
        foreach ($data as $field => $value) {
            $sqlArr[] = "{$field}='{$value}'";
        }
        $whereStr = implode(',', $sqlArr);
        
        return $whereStr;
    }
    
}