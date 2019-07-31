<?php
namespace App;

function P($arr) {
    var_dump($arr, 1, '', 0);
    exit;
}

/**
 * 判断当前用户是否是超级管理员
 * @param string $uid
 * @return bool
 * @author zhaoxiang <zhaoxiang051405@gmail.com>
 */
function isAdministrator($uid = '') {
    if (!empty($uid)) {
        $adminConf = \PhalApi\DI()->config->get('app.USER_ADMINISTRATOR');
        if (is_array($adminConf)) {
            if (is_array($uid)) {
                $m = array_intersect($adminConf, $uid);
                if (count($m)) {
                    return true;
                }
            } else {
                if (in_array($uid, $adminConf)) {
                    return true;
                }
            }
        } else {
            if (is_array($uid)) {
                if (in_array($adminConf, $uid)) {
                    return true;
                }
            } else {
                if ($uid == $adminConf) {
                    return true;
                }
            }
        }
    }
    return false;
}