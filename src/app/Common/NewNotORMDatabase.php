<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/11
 *+----------------------------------------------------------------------
 *| Time: 14:01
 *+----------------------------------------------------------------------
 */


namespace App\Common;

use PDO;
use PhalApi\Database\NotORMDatabase;

class NewNotORMDatabase extends NotORMDatabase
{
    protected function createPDOBy($dbCfg)
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s;port=%d', $dbCfg['name'], isset($dbCfg['host']) ? $dbCfg['host'] : 'localhost', isset($dbCfg['port']) ? $dbCfg['port'] : 3306);
        $charset = isset($dbCfg['charset']) ? $dbCfg['charset'] : 'UTF8';
        $pdo = new PDO( $dsn, $dbCfg['user'],$dbCfg['password']);
        $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->exec("SET NAMES '{$charset}'");
        return $pdo;
    }
}
