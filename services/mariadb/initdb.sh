#!/bin/sh

if [ ! -d "/run/mysqld" ]; then
  mkdir -p /run/mysqld
fi

if [ -d /data/mysql ]; then
  echo "** [mariadb] [i] MariaDB directory already present, skipping DB creation."
else
  echo "** [mariadb] [i] MySQL data directory is not found, creating initial DB(s)..."

  mysql_install_db --user=mysql --basedir=/usr --defaults-file=/etc/mysql/my.cnf >/dev/null

  if [ "$MYSQL_ROOT_PASSWORD" = "" ]; then
    MYSQL_ROOT_PASSWORD=abc12345
    echo "[i] MySQL root Password: $MYSQL_ROOT_PASSWORD"
  fi

  MYSQL_DATABASE=${MYSQL_DATABASE:-""}
  MYSQL_USER=${MYSQL_USER:-""}
  MYSQL_PASSWORD=${MYSQL_PASSWORD:-""}

  tfile=`mktemp`
  if [ ! -f "$tfile" ]; then
      return 1
  fi

# GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
  cat > "$tfile" <<-EOSQL
USE mysql;
FLUSH PRIVILEGES;
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY "$MYSQL_ROOT_PASSWORD" WITH GRANT OPTION;
ALTER USER 'root'@'localhost' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD';
DROP DATABASE IF EXISTS test ;
EOSQL

  if [ "$MYSQL_DATABASE" != "" ]; then
    echo "[i] Creating database: $MYSQL_DATABASE"
    echo "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\` CHARACTER SET utf8 COLLATE utf8_general_ci;" >> "$tfile"

    if [ "$MYSQL_USER" != "" ]; then
      echo "[i] Creating user: $MYSQL_USER with password $MYSQL_PASSWORD"
      echo "GRANT ALL ON \`$MYSQL_DATABASE\`.* to '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';" >> "$tfile"
    fi
  fi
  
  echo 'FLUSH PRIVILEGES ;' >> "$tfile"
  /usr/bin/mysqld --defaults-file=/etc/mysql/my.cnf --console --user=mysql --init-file="$tfile"
  rm -f "$tfile"
fi

exec /usr/bin/mysqld --user=mysql --defaults-file=/etc/mysql/my.cnf --innodb-flush-method=fsync --console
# exec tail -f /var/lib/mysql/mariadb.error.log
# exec /usr/bin/mysqld --user=mysql --console
# exec /usr/bin/mysqld_safe --datadir='/data' --console
# tail -f /dev/null
