<?php
namespace TpRemoteModel\Tests;

use PHPUnit\Framework\TestCase;
use TpRemoteModel\Examples\Models\OrderDetailRemote;
use TpRemoteModel\Examples\Models\OrderRemote;
use TpRemoteModel\Examples\Models\ProductRemote;

class Tp50Test extends TestCase {
    protected function setUp(): void
    {
        if (!defined('THINK_VERSION')) {
            $class = '\think\Container';
            $class::get('app')->initialize();
        }
    }

    public function testFind()
    {
        $item = ProductRemote::where('pid', 1)->find();
        var_dump($item->toJson());

        $item = OrderRemote::where('oid', 1)->find();
        var_dump($item->toJson());

        $item = OrderDetailRemote::where('od_id', 1)->find();
        var_dump($item->toJson());
    }

    public function testWhereIn()
    {
        $list = OrderRemote::whereIn('oid', [1,2])->select();
        foreach($list as $item){
            var_dump($item->toJson());
        }
    }

    public function testWith()
    {
        $item = OrderRemote::with(['orderDetails.product'])->where('oid', 1)->find();
        var_dump($item->toJson());
    }
}