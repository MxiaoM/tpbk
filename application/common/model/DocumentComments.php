<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 19:26
 */

namespace app\common\model;


use think\Model;

class DocumentComments extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $insert = [
        'status' => 1,
    ];
    protected $update = [
        'update_time',
    ];
}