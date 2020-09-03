<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1 style="text-align: center;">��ӭʹ��DNMP��</h1>';
echo '<h2>�汾��Ϣ</h2>';

echo '<ul>';
echo '<li>PHP�汾��', PHP_VERSION, '</li>';
echo '<li>Nginx�汾��', $_SERVER['SERVER_SOFTWARE'], '</li>';
echo '<li>MySQL�������汾��', getMysqlVersion(), '</li>';
echo '<li>Redis�������汾��', getRedisVersion(), '</li>';
echo '<li>MongoDB�������汾��', getMongoVersion(), '</li>';
echo '</ul>';

echo '<h2>�Ѱ�װ��չ</h2>';
printExtensions();


/**
 * ��ȡMySQL�汾
 */
function getMysqlVersion()
{
    if (extension_loaded('PDO_MYSQL')) {
        try {
            $dbh = new PDO('mysql:host=mysql;dbname=mysql', 'root', '123456');
            $sth = $dbh->query('SELECT VERSION() as version');
            $info = $sth->fetch();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        return $info['version'];
    } else {
        return 'PDO_MYSQL ��չδ��װ ��';
    }

}

/**
 * ��ȡRedis�汾
 */
function getRedisVersion()
{
    if (extension_loaded('redis')) {
        try {
            $redis = new Redis();
            $redis->connect('redis', 6379);
            $info = $redis->info();
            return $info['redis_version'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    } else {
        return 'Redis ��չδ��װ ��';
    }
}

/**
 * ��ȡMongoDB�汾
 */
function getMongoVersion()
{
    if (extension_loaded('mongodb')) {
        try {
            $manager = new MongoDB\Driver\Manager('mongodb://root:123456@mongodb:27017');
            $command = new MongoDB\Driver\Command(array('serverStatus'=>true));

            $cursor = $manager->executeCommand('admin', $command);

            return $cursor->toArray()[0]->version;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    } else {
        return 'MongoDB ��չδ��װ ��';
    }
}

/**
 * ��ȡ�Ѱ�װ��չ�б�
 */
function printExtensions()
{
    echo '<ol>';
    foreach (get_loaded_extensions() as $i => $name) {
        echo "<li>", $name, '=', phpversion($name), '</li>';
    }
    echo '</ol>';
}