#!/bin/sh

# set -x

echo "--- 启动 ssh ---"
flag=`ps aux|grep "sshd"|grep -v "grep"|wc -l`
if [ "$flag" = "0" ]; then
    service ssh start
fi
service ssh status

echo "================================"

echo "--- 启动 docker ---"
flag=`ps aux|grep "dockerd"|grep -v "grep"|wc -l`
if [ "$flag" = "0" ]; then
    service docker start
else
    echo " * docker is running"
fi

echo "================================"

echo "--- 启动应用 ---"
if [ "$1" = "phpcli" ]; then
    docker-compose -f docker-compose.cli.yml up -d
else
    docker-compose up -d
fi

echo "================================"

# 查看启动的容器
docker ps --format "table {{.ID}}\t{{.Names}}"
