#!/bin/sh
echo "*** 启动 ssh ***"
service ssh start
service ssh status

echo "================================"

echo "*** 启动 docker ***"
service docker start

echo "================================"

echo "*** 启动应用 ***"
docker-compose up -d

echo "================================"

# 查看启动的容器
docker ps --format "table {{.ID}}\t{{.Names}}"