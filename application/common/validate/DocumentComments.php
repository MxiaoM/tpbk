<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 19:30
 */

namespace app\common\validate;



use think\Validate;

class DocumentComments extends Validate
{
    protected $rule = [
        'content' => ['require','length'=>'1,300'],
        'document_id' => ['require'],
        'uid' => ['require'],
    ];
    protected $message = [
        'content.require' => '评论内容不能为空',
        'document_id.require' => '文章id参数错误',
        'uid.require' => '未登录无法发布',

    ];
}