#!/bin/sh

echo
echo "============================================"
echo "Kernel tuning from        : tuning.sh"
echo "PHP version               : ${PHP_VERSION}"
echo "PHP Runing                : ${PHP_RUNING}"
echo "Work directory            : ${PWD}"
echo "============================================"
echo

# set -x

if [ "${PHP_RUNING}" = "cli" ]; then
    # 系统内核调优
    mkdir -p /etc/security/limits.d/
    cp -rf /tmp/etc/security/limits.d/* /etc/security/limits.d/
    # 打开文件数
    cp -rf /tmp/etc/sysctl.d/* /etc/sysctl.d/
fi
ulimit -HSn 102400
