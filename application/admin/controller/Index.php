<?php
/**
 * Created by PhpStorm.
 * User: mian
 * Time: 14:31
 */
namespace app\admin\controller;
use app\common\controller\Base;
use app\common\model\DocumentCategory;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $map = [
            'status' => ['gt' , -1],
        ];
        $this->assign('_list' ,$this->lists('user' ,$map));
        $this->assign('show_type' , 'user_list');
        $this->assign('title' , '用户管理');
        return $this->fetch();
    }
    // 文章列表
    public function document_list()
    {
        $map = [
            'status' => ['gt' , -1],
        ];
        $this->assign('_list' , $this->lists('document' ,$map));
        $this->assign('show_type' , 'document_list');
        $this->assign('title' , '文章管理');
        return $this->fetch();
    }
    // 分类管理
    public function category_list()
    {
        $map = [
            'status' => ['gt' , -1],
        ];
        $this->assign('_list' ,$this->lists('document_category' ,$map) );
        $this->assign('show_type' , 'category_list');
        $this->assign('title' , '文章分类管理');
        return $this->fetch();
    }


    // 修改用户的状态
    public function setUserStatus()
    {
        $map['id'] = input('param.id');
        // 超管无法修改自己的权限
        if($map['id'] == config('ADMIN_ID'))
        {
            $this->error('无法修改超级管理员的权限！');
        }

        // 登录用户无法删除自己
        if($map['id'] == USER_ID)
        {
            $this->error('无法删除已经登录的用户！');
        }

        $data['status'] = input('param.status');
        if(Db::name('user')->where($map)->update($data))
        {
            $this->success('修改状态成功！');
        }
        $this->error('修改状态失败！');
    }
    // 修改用户的状态
    public function setUserAdmin()
    {
        $map['id'] = input('param.id');
        if($map['id'] == config('ADMIN_ID'))
        {
            $this->error('无法修改超级管理员的权限！');
        }
        $data['is_admin'] = input('param.is_admin');
        if(Db::name('user')->where($map)->update($data))
        {
            $this->success('修改状态成功！');
        }
        $this->error('修改状态失败！');
    }
    // 新增分类
    public function add_category()
    {
        $this->assign('show_type' , 'category_list');
        $this->assign('title' , '文章分类管理');
        return $this->fetch();
    }

    // 编辑分类
    public function edit_category()
    {
        $id = input('param.id');
        $info = [];
        if($id)
        {
            // 编辑
            $map['id'] = $id;
            $info = Db::name('document_category')->where($map)->find();

        }
        $this->assign('info' , $info);
        $this->assign('show_type' , 'category_list');
        $this->assign('title' , '文章分类管理');
        return $this->fetch();
    }

    // 保存分类
    public function save_category()
    {
        if(request()->isPost())
        {
            $model = new DocumentCategory();
            $id = input('param.id');
            // 新增还是编辑 有id是编辑，否则是新增一条数据
            if($id != '')
            {
                $return = $model->allowField(true)
                    ->validate(true)
                    ->save(input('param.'),['id'=>$id]);
            }
            else
            {
                $return = $model->allowField(true)->validate(true)->save(input('param.'));
            }
            if($return !== false)
            {
                $this->success('保存成功！', url('admin/index/category_list'));
            }
            $this->error($model->getError());
        }
    }
    // 修改文章分类的状态
    public function setCategoryStatus()
    {
        return $this->setStatus(
            'document_category' ,
            input('param.id') ,
            input('param.status')
        );
    }
    // 修改文章的状态
    public function setDocumentStatus()
    {
        return $this->setStatus(
            'document' ,
            input('param.id') ,
            input('param.status')
        );
    }
    // 评论列表
    public function comment_list()
    {
        // 文章的id
        $id = input('param.id');
        // 验证
        if(!$id)
        {
            $this->error('参数错误！');
        }

        $this->assign('id' , $id);

        // 查询条件的构建
        $map = [
            'document_id'=> $id,
            'status'=>['>',-1]
        ];

        $this->assign('_list' , $this->lists('document_comments' , $map ));
        $this->assign('show_type' , 'document_list');
        $this->assign('title' , '文章评论管理');
        return $this->fetch();
    }
    // 修改文章评论的状态
    public function setDocumentCommentStatus()
    {
        return $this->setStatus(
            'document_comments' ,
            input('param.id') ,
            input('param.status')
        );
    }
}