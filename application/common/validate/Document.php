<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 11:27
 */

namespace app\common\validate;


use think\Validate;

class Document extends Validate
{
    protected $rule = [
        'title' => ['require','length'=>'1,30'],
        'document_category_id' => ['require'],
        'content' => ['require','length'=>'1,30000'],
        'uid' => ['require'],
    ];
    protected  $message = [
        'title.require' => '文件标题不能为空',
        'document_category_id.require' => '请选择文章分类',
        'content' => '文件内容不能为空',
        'uid.require' => '未登录无法发布文章',
    ];
}