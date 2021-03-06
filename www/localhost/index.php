<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sapi = php_sapi_name();

if ($sapi != 'cli') {
    echo '<h1 style="text-align: center;">欢迎使用DNMP！</h1>';
    echo '<h2>版本信息</h2>';

    echo '<ul>';
    echo '<li>PHP版本：', PHP_VERSION, '</li>';
    echo '<li>PHP运行环境：', php_sapi_name(); '</li>';
    echo '<li>Nginx版本：1.19.10</li>';
    echo '<li>MySQL服务器版本：', getMysqlVersion(), '</li>';
    echo '<li>Redis服务器版本：', getRedisVersion(), '</li>';
    echo '</ul>';
    
    echo '<h2>已安装扩展</h2>';
} else {
    echo "欢迎使用DNMP！\n";
    echo "版本信息\n";

    echo "PHP版本：", PHP_VERSION, "\n";
    echo "PHP运行环境：", php_sapi_name(), "\n";
    echo "Nginx版本：", $_SERVER['SERVER_SOFTWARE'], "\n";
    echo "MySQL服务器版本：", getMysqlVersion(), "\n";
    echo "Redis服务器版本：", getRedisVersion(), "\n";
    echo "MongoDB服务器版本：", getMongoVersion(), "\n";
    
    echo "已安装 PHP 扩展\n";
}

printExtensions($sapi);


/**
 * 获取MySQL版本
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
        return 'PDO_MYSQL 扩展未安装 ×';
    }

}

/**
 * 获取Redis版本
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
        return 'Redis 扩展未安装 ×';
    }
}

/**
 * 获取MongoDB版本
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
        return 'MongoDB 扩展未安装 ×';
    }
}

/**
 * 获取已安装扩展列表
 */
function printExtensions($sapi)
{
    if ($sapi != 'cli') {
        echo '<ol>';
        foreach (get_loaded_extensions() as $i => $name) {
            echo "<li>", $name, '=', phpversion($name), '</li>';
        }
        echo '</ol>';
    } else {
        foreach (get_loaded_extensions() as $i => $name) {
            echo $name, '=', phpversion($name), "\n";
        }
    }
}