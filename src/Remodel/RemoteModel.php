<?php

namespace TpRemoteModel\Remodel;


use think\Model;
class RemoteModel extends Model {
    protected $query = RemoteQuery::class;
    protected $connection = [
        'type' => RemoteConnection::class,
    ];

    protected $options = [];

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

}  