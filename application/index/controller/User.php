<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 20:00
 */

namespace app\index\controller;



use app\common\Controller\Base;
use app\common\model\Document;
use app\common\model\User as UserModel;
use think\Db;
class User extends Base
{
    /**
     * 用户注册界面
     */
    public function reg()
    {
        $website = Db::name('website')->find();
        if(!$website['is_reg'])
        {
            $this->error('注册功能已关闭',url('index/index'));
        }
        $this->assign('title','用户注册');
        return  $this->fetch();
    }
    /**
     * ajax处理验证
     */
    public function add()
    {
        if (request()->isAjax())
        {
            $user = new UserModel();
            if($user->allowField(true)->validate(true)->save(input('post.'))){
                return['status'=>1 ,'msg'=>'注册成功'];
            }else{
                return['status'=>0 ,'msg'=>$user->getError()];
            }
        }
        return['status'=>0 ,'msg'=>'未知错误'];
    }
    /**
     * 用户登录界面
     */
    public function login()
    {
        $this->checkLoginStatus();
        return $this->fetch();
    }
    /**
     * 用户登录方法
     */
    public function doLogin()
    {
        if (request()->isAjax())
        {
            $user = new UserModel();
            return $user->checkUserLogin(input('param.'));
        }
        return ['status' =>0];

    }
    /**
     * 用户中心
     */
    public function index()
    {
        $uid = session('user_id');

        $this->assign('user_list',Db::name('user')
        ->where(['id'=>$uid])
        ->select());
        $this->assign('user_document',Db::name('document')
            ->where(['uid'=>$uid])
            ->select());
       $fav = Db::table('db_document_fav')
           ->alias('f')
           ->join('db_document d','f.document_id = d.id')
           ->where('db_document_fav.uid','=',$uid)
           ->select();
       $this->assign('fav',$fav);
        $this->checkUserLogin();
        return $this->fetch();
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        session('user_id',null);
        session('user_info',null);
        //return $this->redirect('User/login');
        return $this->success('注销成功',url('User/login'));
    }
}