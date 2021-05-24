#!/bin/sh

echo
echo "============================================"
echo "Kernel tuning from        : tuning.sh"
echo "PHP version               : ${PHP_VERSION}"
echo "PHP Runing                : ${PHP_RUNING}"
echo "Work directory            : ${PWD}"
echo "============================================"
echo

if [ "${PHP_RUNING}" = "cli" ]; then
    # 系统内核调优
    cp ./security/limits.d/* /etc/security/limits.d/
    # 打开文件数
    cp ./sysctl.d/* /etc/sysctl.d/
fi
ulimit -HSn 102400
