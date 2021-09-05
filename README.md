# tp-remote-model
- thinkphp调用的model代码无需修改，可以直接实现服务化。
- 原理是将远程api接口，转为tp5 model，兼容80%以上的model方法。
- 目前仅支持tp5.0，tp5.1。

# 快速预览

### composer require windawake/tp-remote2model:dev-main

### 安装phpunit，把phpunit.xml复制到项目根目录下

### cd windawake/tp-remote2model/examples/website && php run.php

### 在项目根目录下，执行单元测试
- ./vendor/bin/phpunit --filter=testFind
- ./vendor/bin/phpunit --filter=testWhereIn
- ./vendor/bin/phpunit --filter=testWith
- ./vendor/bin/phpunit --filter=testUpdate
