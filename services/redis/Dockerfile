ARG REDIS_VERSION
FROM redis:${REDIS_VERSION}

ARG TZ
ARG CONTAINER_PACKAGE_URL


RUN if [ "${CONTAINER_PACKAGE_URL}" != "" ]; then \
        sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories; \
    fi

USER root
# disabled THP
RUN  sudo echo never > /sys/kernel/mm/transparent_hugepage/enabled;

# The TCP backlog setting
RUN sudo echo 511 > /proc/sys/net/core/somaxconn;
# 或者 打开ietc/sysctl.conf
# 在这里面添net.core.somaxconn= 1024 然后执行sysctl -p


ENTRYPOINT ["redis-server", "/etc/redis.conf"]