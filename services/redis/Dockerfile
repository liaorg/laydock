ARG REDIS_VERSION
FROM redis:${REDIS_VERSION}

ARG TZ
ARG CONTAINER_PACKAGE_URL


RUN if [ "${CONTAINER_PACKAGE_URL}" != "" ]; then \
        sed -i "s/dl-cdn.alpinelinux.org/${CONTAINER_PACKAGE_URL}/g" /etc/apk/repositories; \
    fi

USER root

COPY ./sysctl.d/* /etc/sysctl.d/

# disabled THP
RUN sudo echo never > /sys/kernel/mm/transparent_hugepage/enabled; \
    sysctl -p;


ENTRYPOINT ["redis-server", "/etc/redis.conf"]