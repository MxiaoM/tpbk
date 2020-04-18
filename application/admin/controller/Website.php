<?php
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\Website as WebSiteSys;
use think\Db;

// 网站系统管理
class Website extends Base
{
    // 查询默认的网站设置
    public function index()
    {
        $this->assign('info' ,Db::name('website')->find());
        $this->assign('show_type' , 'website');
        return $this->fetch();
    }

    // 更新网站的设置
    public function save()
    {
        if(request()->isPost())
        {
            $model = new WebSiteSys();
            if($model->allowField(true)
                ->validate(true)
                ->save(input('param.') , ['id'=>input('param.id')]))
            {
                $this->success('更新成功！');
            }
            $this->error($model->getError());
        }
        $this->error('参数错误！');
    }
}