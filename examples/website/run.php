<?php
include __DIR__.'/../Demo.php';
use TpRemoteModel\Examples\Demo;

$sqlite = new SQLite3('test.db');

$sql =<<<EOF
    DROP TABLE IF EXISTS `product`;
    CREATE TABLE product(
        pid INT PRIMARY KEY NOT NULL,
        name varchar(32) NOT NULL DEFAULT '',
        status INT NOT NULL DEFAULT 0,
        delete_time INT
    );

    INSERT INTO product (pid, name, status, delete_time) VALUES (1, 'computer', 1, 111);
    INSERT INTO product (pid, name, status, delete_time) VALUES (2, '火锅底料', 0, 222);
    INSERT INTO product (pid, name, status, delete_time) VALUES (3, 'まんが', 1, null);
    INSERT INTO product (pid, name, status, delete_time) VALUES (4, '携帯電話', 2, 111);
    INSERT INTO product (pid, name, status, delete_time) VALUES (5, '风扇', 1, 333);
    INSERT INTO product (pid, name, status, delete_time) VALUES (119, '玩具', 1, 111);

    DROP TABLE IF EXISTS `order`;
    CREATE TABLE `order`(
        oid INT PRIMARY KEY NOT NULL,
        order_number varchar(32) NOT NULL DEFAULT '',
        status INT NOT NULL DEFAULT 0,
        created_date DATETIME
    );

    INSERT INTO `order` (oid, order_number, status, created_date) VALUES (1, 'no001', 1, '2021-09-01 08:40:50');
    INSERT INTO `order` (oid, order_number, status, created_date) VALUES (2, 'no002', 2, '2021-09-02 09:00:00');
    INSERT INTO `order` (oid, order_number, status, created_date) VALUES (3, 'no003', 3, '2021-09-05 10:00:00');

    DROP TABLE IF EXISTS `order_detail`;
    CREATE TABLE `order_detail`(
        od_id INT PRIMARY KEY NOT NULL,
        oid INT NOT NULL DEFAULT 0,
        pid INT NOT NULL DEFAULT 0
    );

    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (1, 1, 1);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (2, 1, 2);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (3, 2, 1);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (4, 2, 2);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (5, 3, 3);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (6, 3, 4);
    INSERT INTO `order_detail` (od_id, oid, pid) VALUES (7, 4, 5);
EOF;

$ret = $sqlite->exec($sql);
if(!$ret){
    echo $sqlite->lastErrorMsg();
} else {
    echo "Records created successfully\n";
}
$sqlite->close();


$dir = dirname(__FILE__);
$demo = new Demo();
$port = $demo->getListenPort();
$serverCommand = "cd {$dir} && php -S 0.0.0.0:{$port} -t ./";
echo "Listening on http://0.0.0.0:{$port}\n";
passthru($serverCommand, $status);

if ($status) {
    var_dump($status);exit;
}