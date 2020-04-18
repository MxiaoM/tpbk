<?php
namespace app\index\controller;


use app\common\controller\Base;
use app\common\model\Document;
use app\common\model\DocumentComments;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $map = ['status'=>1];
        $page_header = "全部文章";
        $keywords = input('param.keywords','','trim');
        if ($keywords !='')
        {
            $map['title'] = ['like','%'.$keywords.'%'];
            $page_header = '关键字：'.$keywords;
        }
        $category_id = input('param.category_id','');
        if ($category_id !='')
        {
            $map['document_category_id'] = $category_id;
            $page_header = '分类名称：'.getCategoryName($category_id);
        }
        $publish_date = input('param.publish_date');
        if ($publish_date)
        {
            $map['create_time'] = ['between',[strtotime($publish_date),strtotime($publish_date) + 60*60*24]];
            $page_header = '时间：'.$publish_date;
        }
        $lists = Db::name('document')
            ->where($map)
            ->order('create_time','desc')
            ->paginate();
        $this->assign('_list',$lists);
        $this->assign('title','博客文章首页列表');
        $this->assign('page_header',$page_header);
        return $this->fetch();
    }
    public function add()
    {
        $this->checkUserLogin();
        $map = ['status'=>1];
        $catesLists = Db::name('document_category')->where($map)->select();
        $this->assign('title','文章发布');
        $this->assign('catesLists',$catesLists);
        return $this->fetch();
    }
    public function save()
    {
        if (request()->isPost()){
            $data = input('post.');
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('img_path');
            $map = [
                'size' => 1024*1024,
                'ext' => 'jpg,jpeg,png,gif'
            ];
             $validate = validate('Document');
             if (!$validate->check($data)){
                 $this->error($validate->getError());
             }
            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->validate($map)->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    $data['img_path'] = $info->getSaveName();
                }else{
                    // 上传失败获取错误信息
                    $this->error($file->getError());
                }
                $document =new Document();
                if (!$document->allowField(true)->validate(true)->save($data)){
                    $this->error($document->getError());
                }
            }
            $this->success('保存成功',url('index/index/index'));
        }
        $this->error('参数错误');
    }
    public function detail()
    {
        $id = input('param.id');
        if (!$id)
        {
            $this->error('参数错误');
        }
        $map = [
          'id'=>$id,
            'status' =>1,
        ];
        $info = Db::name('document')->where($map)->find();
        if ($info === null)
        {
            $this->error('文章已经被删除');
        }
        $is_fav = 0;
        if (USER_ID)
        {
            $fav_map = [
                'document_id' => $info['id'],
                'uid' =>USER_ID,
            ];
            $fav_id = Db::name('document_fav')->where($fav_map)->value('id');
            if ($fav_id)
            {
                $is_fav = 1;
            }
        }
        $is_support = 0;
        if (USER_ID)
        {
            $support_map = [
                'document_id' => $info['id'],
                'uid' =>USER_ID,
            ];
            $support_id = Db::name('document_support')->where($support_map)->value('id');
            if ($support_id)
            {
                $is_support = 1;
            }
        }
        $page_set = [
            'type'      => 'bootstrap',
            'var_page'  => 'page',
            'list_rows' => 2,
        ];
        $this->assign('is_support',$is_support);
        $this->assign('is_fav',$is_fav);
        Db::name('document')->where($map)->setInc('pv');
        $this->assign('comments_list',Db::name('document_comments')
            ->where(['document_id'=>$info['id'],'status'=>1])
            ->order('create_time','desc')
            ->paginate($page_set));
        $this->assign('info',$info);
        $this->assign('title','详情页'.$info['title']);
        return $this->fetch();
    }
    public function fav()
    {
        if (!request()->isAjax())
        {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        $document_id = input('param.document_id');
        $uid = input('param.uid');
        if (!$document_id || !$uid)
        {
            return ['status'=>0,'msg'=>'用户未登录'];
        }
        $map = [
            'document_id' => $document_id,
            'uid' =>$uid
        ];
        $type = 0;
        $fav_id = Db::name('document_fav')->where($map)->value('id');
        if ($fav_id)
        {
            Db::name('document_fav')->delete($fav_id);
            $type = 0;
        }
        else
        {
            Db::name('document_fav')->insert(['document_id'=>$document_id,'uid'=>$uid]);
            $type = 1;
        }
        return ['status'=>1,'msg'=>'操作成功','type'=>$type];
    }
    public function support()
    {
        if (!request()->isAjax())
        {
            return ['status'=>0,'msg'=>'参数错误'];
        }
        $document_id = input('param.document_id');
        $uid = input('param.uid');
        if (!$document_id || !$uid)
        {
            return ['status'=>0,'msg'=>'用户未登录'];
        }
        $map = [
            'document_id' => $document_id,
            'uid' =>$uid
        ];
        $type = 0;
        $fav_id = Db::name('document_support')->where($map)->value('id');
        if ($fav_id)
        {
            Db::name('document_support')->delete($fav_id);
            $type = 0;
        }
        else
        {
            Db::name('document_support')->insert(['document_id'=>$document_id,'uid'=>$uid]);
            $type = 1;
        }
        return ['status'=>1,'msg'=>'操作成功','type'=>$type];
    }
    public function add_comment()
    {
        $model =new DocumentComments();
        $c_name='doc_'.USER_ID.'_'.input('param.document_id');
        if (cookie($c_name))
        {
            if (time()-cookie($c_name)<10)
            {
                return ['status'=>0,'msg'=>'发布评论时间间隔太短！'];
            }
            else
                {
                    cookie($c_name,time(),time()+60);
                }
        }
        else
        {
            cookie($c_name,time(),time()+60);
        }
        if($model->allowField(true)->validate(true)->save(input('post.')))
        {
            return ['status'=>1,'msg'=>'发布成功'];
        }
        else
        {
            return ['status'=>0,'msg'=>$model->getError()];
        }
    }
}
