<?php
namespace app\common\validate;

use think\Validate;

/**
 * 系统设置验证类
 * Class Document
 * @package app\common\validate
 */
class Website extends Validate{

    // 验证规则
    protected $rule = [
        'webname' => ['require','length'=>'1,30'],
        'webkeywords' => ['require','length'=>'1,300'],
    ];
}