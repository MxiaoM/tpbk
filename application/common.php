<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\db;
// 应用公共文件

//根据id获取用户名
if (!function_exists('getUserName'))
{
    function getUserName($id)
    {
        return Db::name('user')->where(['id'=>$id])->value('username');
    }
}
//文章内容截取
if (!function_exists('getDocBody'))
{
    function getDocBody($content)
    {
        return mb_substr(strip_tags($content),0,500);
    }
}
if (!function_exists('getCategoryName'))
{
    function getCategoryName($id)
    {
        return Db::name('document_category')->where(['id'=>$id])->value('name');
    }
}
// 匹配status的各种状态
if(!function_exists('getStatusTitle'))
{
    function getStatusTitle($id)
    {
        $list = config('STATUS_LIST');
        return $list[$id];
    }
}
//return[error_reporting(E_ERROR | E_WARNING | E_PARSE)];