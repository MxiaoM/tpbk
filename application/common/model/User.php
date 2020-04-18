<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 17:50
 */
namespace app\common\model;

use think\Model;

class User extends Model
{
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 自动完成 新增和更新都执行
    protected $auto = [
        'password',
    ];

    // 只是新增的时候
    protected $insert = [
        'status' => 1,
    ];

    // 只是更新的时候
    protected $update = [
        'update_time',
    ];

    // 密码md5序列化
    public function setPasswordAttr($value , $data)
    {
        if($data['password'])
        {
            return md5($data['password']);
        }
        return '';
    }
    //判断用户登录名密码
    public function checkUserLogin($data)
    {
        $map = [
            'mobile' => $data['mobile'],
            'password' => md5(md5($data['password'])),
        ];
        $userinfo = self::get($map);
        if ($userinfo === null)
        {
            return ['status' => 0, 'msg'=>'手机号密码错误'];
        }
        //判断用户登录状态
        if($userinfo['status'] < 1)
        {
           // return ['status' => [0,-1], 'msg'=>'用户名不存在'];
        }
        //记录用户状态
        session('user_id',$userinfo->data['id']);
        session('user_info',$userinfo->data);
        return ['status' => 1, 'msg'=>'登录成功'];
    }
}