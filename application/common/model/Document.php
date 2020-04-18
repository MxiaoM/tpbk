<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 17:52
 */
namespace app\common\model;
use think\Model;

class Document extends Model
{
    protected $autoWriteTimestamp = true;
    protected $insert = [
        'status' => 1,
    ];
    protected $update = [
        'update_time',
];
}