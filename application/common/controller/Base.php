<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 17:48
 */
namespace app\common\controller;
use think\Request;
use think\Controller;
use think\Db;

class Base extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        //前台
        define('USER_ID',session('user_id'));
        // 分类列表的变量置换
        $this->assign('menu',$this->showMenu());
        $this->assign('category_id',input('param.category_id'));
        $this->getDocTimeGroupList();
        //后台
        // 初始化一些常量
        define('CONTROLLER_NAME',Request::instance()->controller());
        define('MODULE_NAME',Request::instance()->module());
        define('ACTION_NAME',Request::instance()->action());
        // 检测已经登录的用户是否拥有后台访问的权限
        $this->checkAdminUserAuth();
        //网站设置变量置换
        $website = $this->getWebSiteInfo();
        $this->checkWebSiteOpen($website);
        $this->assign('website',$website);
    }
    // 获取列表的方法
    public function lists($model_name = '' , $map = [] , $order = 'create_time desc')
    {
        if($model_name && !empty($map))
        {
            return Db::name($model_name)
                ->where($map)
                ->order($order)
                ->paginate(config('PAGE_NUM_SET')
                );
        }
        return false;
    }
    // 修改状态通用方法
    public function setStatus($model_name , $id = 0 , $status = 0 )
    {
        if($model_name && $id)
        {
            $map['id'] = $id;
            $data['status'] = $status ;
            if(Db::name($model_name)->where($map)->update($data))
            {
                return $this->success('修改状态成功！');
            }
        }
        return $this->error('修改状态失败！');
    }
    //检查登录状态
    public function checkUserLogin()
    {
        if(!USER_ID){
            return $this->error('用户未登录：禁止访问',url('index/user/login'));
        }
    }
    public function checkLoginStatus()
    {
        if(USER_ID){
            return $this->error('用户已经登录请勿重复登录',url('index/User/index'));
        }
    }
    public function showMenu()
    {
        $map = ['status'=>1];
        return Db::name('document_category')
            ->where($map)
            ->order('sort','asc')
            ->select();
    }
    public function getDocTimeGroupList()
    {
        //最近30天
        $limit_time = time() - 30*24*60*60;
        $this->assign('doc_time_list',Db::name('document')
            ->field("FROM_UNIXTIME(CREATE_TIME,'%Y-%m-%d') as publish_date,COUNT(id) as doc_num")
            ->where(['status'=>1,'create_time'=>['>',$limit_time]])
            ->group('publish_date')
            ->order('publish_date','desc')
            ->select()
        );
    }
    // 如果是超管后台，则判断当前用户是否有权限
    private function checkAdminUserAuth()
    {
        if(MODULE_NAME == 'admin')
        {
            // 无需验证控制器User
            if(CONTROLLER_NAME =='User')
            {
                return true;
            }

            // 用户登录验证
            if(!USER_ID)
            {
                $this->redirect(url('admin/user/login'));
            }

            // 用户权限验证
            if(session('user_info.is_admin') != 1)
            {
                $this->error('没有管理后台的权限！' ,url('admin/user/login') );
            }
        }
        return true;
    }
    // 检测网站是否关闭
    private function checkWebSiteOpen($website)
    {
        if($website['is_open'] != 1)
        {
            if(MODULE_NAME != 'admin')
            {
                echo "网站已经关闭！";
                exit();
            }
        }
    }
    // 获取网站的基本配置信息
    public function getWebSiteInfo()
    {
        return Db::name('website')->find();
    }
}