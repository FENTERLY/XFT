<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/7
 * Time: 16:00
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Manager;
use think\facade\Session;
use think\facade\Request;

class Managers extends Controller
{
    //管理列表
    public function manager()
    {
        $manager = Manager::where('ma_level',0)->select();
        $managers = Manager::select();
        $username = Session::get('username');
        $this->assign('managers',$managers);
        $this->assign('username',$username);
        $this->assign('manager',$manager);
        return $this->fetch();

    }

    //管理模块的添加
    public function add()
    {
        if(!empty($_POST))
        {
            $ma_name = Request::param('ma_name');
            $ma_address = Request::param('ma_address');
            $ma_father = Request::param('ma_father');
            //查询这个管理名称是否存在,就返回管理名称已存在
            $em = Manager::where('ma_name',$ma_name)->find();
            if($em!=NULL)
            {
                echo '1';
                exit;
            }
            else {
                $ma_father_id = Manager::where('ma_name',$ma_father)->value('ma_id');
                $res = Manager::create([
                    'ma_name'=>$ma_name,
                    'ma_address'=>$ma_address,
                    'ma_father'=>$ma_father_id,
                    'ma_level'=>1,
                ]);
            }
            //查看添加是否成功,是的话返回2,失败返回3
            if(!empty($res))
            {
                echo '2';
                exit;
            }
            else{
                echo '3';
                exit;
            }
        }
        else{
            $manager = Manager::select();
            $this->assign('manager',$manager);
            return $this->fetch('manager-add');
        }

    }

    //管理模块的编辑
    public function edit()
    {
        $ma_id = $_GET['ma'];
        $ma = Manager::where('ma_id',$ma_id)->find();
        $this->assign('ma',$ma);
        return $this->fetch('manager-edit');
    }
}