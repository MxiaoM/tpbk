<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 17:57
 */

namespace app\common\validate;


use think\Validate;

class User extends Validate
{
    // 定义规则
    protected $rule = [
       'username' => ['require','length'=>'5,18'],
        'email' => ['require','email'],
        'mobile' => ['require','number','unique'=>'user','length'=>11],
        'password' => ['require','confirm','regex'=>'/^[A-Za-z0-9\_]{6,18}$/'],
    ];

    // 定义提示语（自定义）
    protected $message = [
        'username.require' => '用户名不能为空!',
        'username.length' => '用户名长度在5到18位之间',
        'mobile.unique' => '手机号已被注册',
        'password.regex' => '密码必须为6到18位之间的英文数字大小写和_符号',
    ];
}