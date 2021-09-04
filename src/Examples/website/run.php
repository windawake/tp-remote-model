<?php 
$sqlite = new SQLite3('test.db');

$sql =<<<EOF
    DROP TABLE IF EXISTS `warehouse`;

    CREATE TABLE warehouse(
        wid INT PRIMARY KEY NOT NULL,
        name varchar(32) NOT NULL DEFAULT '',
        status INT NOT NULL DEFAULT 0,
        delete_time INT
    );

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (1, '泰国海外仓', 1, 111);

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (2, '越南本地仓', 0, 222);

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (3, '国内福永仓', 1, null);

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (4, '日本平台仓', 2, 111);

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (5, '国内福田仓', 1, 333);

    INSERT INTO warehouse (wid, name, status, delete_time) VALUES (119, '香港国际仓', 1, 111);
EOF;

$ret = $sqlite->exec($sql);
if(!$ret){
    echo $sqlite->lastErrorMsg();
} else {
    echo "Records created successfully\n";
}
$sqlite->close();


$dir = dirname(__FILE__);
$serverCommand = "cd {$dir} && php -S 0.0.0.0:8888 -t ./";
echo "Listening on http://0.0.0.0:8888\n";
passthru($serverCommand, $status);

if ($status) {
    var_dump($status);exit;
}