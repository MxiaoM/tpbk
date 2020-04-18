<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 14:53
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\User as UserM;
use Think\Db;
class User extends Base
{
    private $userModel = null;

    protected function _initialize()
    {
        parent::_initialize();
        if(!$this->userModel)
        {
            $this->userModel = new UserM();
        }
    }

    public function _empty()
    {
        $this->error('Admin访问错误！',url('admin/index/index'));
    }


    //用户登录
    public function login()
    {
        return $this->fetch();
    }

    // 执行用户登录的操作
    public function doLogin()
    {
        return $this->userModel->checkUserLogin(input('post.'));
    }


    // 执行退出登录操作
    public function logout()
    {
        session('user_id' , NULL);
        session('user_info' , NULL);
        $this->success('退出成功！',url('user/login'));
    }
}