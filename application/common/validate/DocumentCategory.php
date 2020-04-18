<?php
namespace app\common\validate;

use think\Validate;

class DocumentCategory extends Validate{

    protected $rule = [
        'name' => ['require','length'=>'1,300','unique'=>'document_category'],
        'uid' => ['require']
    ];

    protected $message = [

        'name.unique' => '分类已经存在',
    ];
}