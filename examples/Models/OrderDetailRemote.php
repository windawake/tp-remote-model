<?php
namespace TpRemoteModel\Examples\Models;

use TpRemoteModel\Examples\Demo;
use TpRemoteModel\Remodel\RemoteModel;

class OrderDetailRemote extends RemoteModel {
    protected $pk = 'od_id';
    protected $table = 'order_detail';

    public function selectHandle()
    {
        $demo = new Demo();
        $list = $demo->getGuzzleHttpContent('GET', $this->table, $this->options);
        return $list;
    }

    public function findHandle($data)
    {
        $list = $this->selectHandle();
        if (!$list) return null;
        $item = current($list);
        
        return $item;
    }

    public function updateHandle($data)
    {
        $demo = new Demo();
        $this->options['data'] = $data;
        $numRows = $demo->getGuzzleHttpContent('PUT', $this->table, $this->options);

        return $numRows;
    }

    public function product()
    {
        return $this->belongsTo(ProductRemote::class, 'pid');
    }
    
}