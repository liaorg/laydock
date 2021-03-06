DNMPR（Docker + Nginx + MySQL + PHP7 + Redis）是一款全功能的LNMP一键安装程序。
特点：

1. 在 [**[DNMP]**](https://gitee.com/yeszao/dnmp) 的基础上删减和改进
安装
git
docker
docker-compose
```bash
curl -L "https://github.com/docker/compose/releases/download/1.26.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
```
2. 支持快速安装扩展命令 `install-php-extensions apcu`

## 1.目录结构

```
/
├── data                        数据库数据目录
│   └── composer                Composer 数据目录
│   └── mysql                   MySQL 数据目录
│   └── redis                   Redis 数据目录
├── services                    服务构建文件和配置文件目录
│   ├── mysql                   MySQL 配置文件目录
│   ├── nginx                   Nginx 配置文件目录
│   ├── php                     PHP7.4 配置目录
│   └── redis                   Redis 配置目录
├── logs                        日志目录
│   ├── mysql                   MySQL日志目录
│   ├── nginx                   Nginx日志目录
│   └── php-fpm                 PHP-FPM日志目
├── docker-compose.sample.yml   Docker 服务配置示例文件
├── env.smaple                  环境配置示例文件
└── www                         站点根目录
```

## 2.快速使用

1. 拷贝并命名配置文件（Windows系统请用`copy`命令），启动：
    ```
    $ cd laydock                                        # 进入项目目录
    $ cp env.sample .env                                # 复制环境变量文件
    $ cp docker-compose.sample.yml docker-compose.yml   # 复制 docker-compose 配置文件。默认启动4个服务：
                                                        # Nginx、PHP7、Redis和MySQL。
    $ docker-compose up -d                              # 启动
    ```

2. 在浏览器中访问：`http://localhost`或`https://localhost`(自签名HTTPS演示)就能看到效果，PHP代码在文件`./www/localhost/index.php`。

## 3.PHP和扩展
### 3.1 如果有改 nginx 配置

**重启 Nginx** 生效
```bash
$ docker exec -it nginx nginx -s reload
```
这里两个`nginx`，第一个是容器名，第二个是容器中的`nginx`程序。

### 3.2 安装PHP扩展

如果要安装更多扩展，请打开你的`.env`文件修改如下的PHP配置，
```bash
PHP_EXTENSIONS=pdo_mysql,opcache,redis       # PHP 要安装的扩展列表，英文逗号隔开
```
然后重新build PHP镜像。
```bash
docker-compose build php
```
可用的扩展请看同文件的`env.sample`注释块说明。

### 3.3 快速安装php扩展
1.进入容器:
```sh
docker exec -it php /bin/sh
install-php-extensions apcu 
```

### 3.4 Host中使用php命令行（php-cli）
1. 参考[bash.alias.sample](bash.alias.sample)示例文件，将对应 php cli 函数拷贝到主机的 `~/.bashrc`文件。
2. 让文件起效：
    ```bash
    source ~/.bashrc
    ```
3. 然后就可以在主机中执行php命令了：
    ```bash
    ~ php -v
    ```

### 3.5 使用composer
**方法1：主机中使用composer命令**
1. 确定composer缓存的路径。`~/laydock/data/composer`。
2. 参考[bash.alias.sample](bash.alias.sample)示例文件，将对应 php composer 函数拷贝到主机的 `~/.bashrc`文件。
   
    > 这里需要注意的是，示例文件中的`~/laydock/data/composer`目录需是第一步确定的目录。
3. 让文件起效：
    ```bash
    source ~/.bashrc
    ```
4. 在主机的任何目录下就能用composer了：
    ```bash
    cd ~/laydock/www/
    composer create-project yeszao/fastphp project --no-dev
    ```
5. （可选）第一次使用 composer 会在 `~/laydock/data/composer` 目录下生成一个**config.json**文件，可以在这个文件中指定国内仓库，例如：
    ```json
    {
        "config": {},
        "repositories": {
            "packagist": {
                "type": "composer",
                "url": "https://packagist.laravel-china.org"
            }
        }
    }
    
    ```
    **方法二：容器内使用composer命令**

还有另外一种方式，就是进入容器，再执行`composer`命令，以PHP7容器为例：
```bash
docker exec -it php /bin/sh
cd /www/localhost
composer update
```

## 4.管理命令
### 4.1 服务器启动和构建命令
如需管理服务，请在命令后面加上服务器名称，例如：
```bash
$ docker-compose up                         # 创建并且启动所有容器
$ docker-compose up -d                      # 创建并且后台运行方式启动所有容器
$ docker-compose up nginx php mysql         # 创建并且启动nginx、php、mysql的多个容器
$ docker-compose up -d nginx php  mysql     # 创建并且已后台运行的方式启动nginx、php、mysql容器
$ docker-compose up --force-recreate        # 强制重建
$ docker-compose up --remove-orphans        # 清理独立的容器

$ docker-compose start php                  # 启动服务
$ docker-compose stop php                   # 停止服务
$ docker-compose restart php                # 重启服务
$ docker-compose build php                  # 构建或者重新构建服务

$ docker-compose rm php                     # 删除并且停止php容器
$ docker-compose down --volumes             # 停止并删除容器，网络，图像和挂载卷

# 镜像导出
$ docker save -o laydock.tar nginx:1.17.10-alpine redis:6.0.3-alpine mysql:5.7.30 php:7.4.6-fpm-alpine laydock_nginx:latest laydock_php:latest

# 重启容器
$ docker restart php
$ docker-compose restart php

# 压缩解压
$ tar -zcvf oracle_instantclient_11_2.tar.gz oracle_instantclient_11_2
$ cd /usr/local/src/
$ tar -zxvf /tmp/extensions/oracle_instantclient_11_2.tar.gz

```

### 4.2 添加快捷命令
在开发的时候，我们可能经常使用`docker exec -it`进入到容器中，把常用的做成命令别名是个省事的方法。

首先，在主机中查看可用的容器：
```bash
$ docker ps           # 查看所有运行中的容器
$ docker ps -a        # 所有容器

# docker 常用命令
#镜像
docker images #列出本地镜像
docker rmi training/sinatra #删除（在删除镜像之前要先用 docker rm 删掉依赖于这个镜像的所有容器）
docker run -t -i ubuntu:14.04 /bin/bash #
docker commit -m "Added json gem" -a "Docker Newbee" 0b2616b0e5a8 ouruser/sinatra:v2 #更新镜像
docker tag 5db5f8471261 ouruser/sinatra:devel #修改标签
docker build ${dockerfile_dir} #Dockerfile 构建
docker save -o ubuntu_14.04.tar ubuntu:14.04 #保存
docker load --input ubuntu_14.04.tar #导入
#容器
docker ps #查看容器信息
docker rm #删掉容器（-f 删除运行中）
docker inspect #查看指定容器详细信息（可获取ip，pid等信息）
docker logs insane_babbage #查看容器log
docker port CONTAINER [PRIVATE_PORT[/PROTO]] #查看端口映射
docker start|stop|restart insane_babbage #启动终止重启
docker attach insane_babbage #进入后台运行的容器 -d（推荐nsenter）
docker export 7691a814370e > ubuntu.tar #导出快照
cat ubuntu.tar | sudo docker import - test/ubuntu:v1.0 #导入快照
## docker hub 
docker search #搜索镜像
docker pull #下载
docker push #推送（需登录）
```
输出的`NAMES`那一列就是容器的名称，如果使用默认配置，那么名称就是`nginx`、`php`、`php56`、`mysql`等。

然后，打开`~/.bashrc`或者`~/.zshrc`文件，加上：
```bash
source $HOME/alias.sh
# 其内容为如下：
alias dnginx='docker exec -it nginx /bin/sh'
alias dmysql='docker exec -it mysql /bin/bash'
alias dredis='docker exec -it redis /bin/sh'

alias dphpcli='docker exec -it php-cli /bin/sh'
alias dphp='docker exec -it php-fpm /bin/sh'
```
下次进入容器就非常快捷了，如进入php容器：
```bash
$ dphp
```

### 4.3 查看docker网络

```sh
ifconfig docker0
```
用于填写`extra_hosts`容器访问宿主机的`hosts`地址

## 5. 使用Log

Log文件生成的位置依赖于conf下各log配置的值。

### 5.1 Nginx日志
Nginx日志是我们用得最多的日志，所以我们单独放在根目录`log`下。

`log`会目录映射Nginx容器的`/var/log/nginx`目录，所以在Nginx配置文件中，需要输出log的位置，我们需要配置到`/var/log/nginx`目录，如：
```
error_log  /var/log/nginx/nginx.localhost.error.log  warn;
```


### 5.2 PHP-FPM日志
大部分情况下，PHP-FPM的日志都会输出到Nginx的日志中，所以不需要额外配置。

另外，建议直接在PHP中打开错误日志：
```php
error_reporting(E_ALL);
ini_set('error_reporting', 'on');
ini_set('display_errors', 'on');
```

如果确实需要，可按一下步骤开启（在容器中）。

1. 进入容器，创建日志文件并修改权限：
    ```bash
    $ docker exec -it php /bin/sh
    $ mkdir /var/log/php
    $ cd /var/log/php
    $ touch php-fpm.error.log
    $ chmod a+w php-fpm.error.log
    ```
2. 主机上打开并修改PHP-FPM的配置文件`conf/php-fpm.conf`，找到如下一行，删除注释，并改值为：
    ```
    php_admin_value[error_log] = /var/log/php/php-fpm.error.log
    ```
3. 重启PHP-FPM容器。

### 5.3 MySQL日志
因为MySQL容器中的MySQL使用的是`mysql`用户启动，它无法自行在`/var/log`下的增加日志文件。所以，我们把MySQL的日志放在与data一样的目录，即项目的`mysql`目录下，对应容器中的`/var/lib/mysql/`目录。
```bash
slow-query-log-file     = /var/lib/mysql/mysql.slow.log
log-error               = /var/lib/mysql/mysql.error.log
```
以上是mysql.conf中的日志文件的配置。

## 6. 构建 PHP-CLI 环境

### 6.1 安装

```shell
$ cd laydock                                        # 进入项目目录
$ cp env.sample .env                                # 复制环境变量文件
$ cp docker-compose.cli.sample.yml docker-compose.cli.yml   # 复制 docker-compose 配置文件。默认启动3个服务：
                                                    # PHP7、Redis和MySQL。
$ docker-compose -f docker-compose.cli.yml up -d    # 启动
$ docker exec -it php-cli /bin/sh
$ dphpcli
```

