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
        $manager2 = Manager::where('ma_level',1)->select();
        $managers = Manager::select();
        $username = Session::get('username');
        $this->assign('managers',$managers);
        $this->assign('username',$username);
        $this->assign('manager2',$manager2);
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
                    'ma_level'=>0,
                ]);
                //父类的son字段自增1
                Manager::where('ma_id',$ma_father_id)->setInc('ma_son');
                if($res['ma_father']!=0)
                {
                    Manager::where('ma_father',$res['ma_father'])->update(['ma_level'=>1]);
                }
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
            $manager2 = Manager::where('ma_level',0)->select();
            $this->assign('manager2',$manager2);
            return $this->fetch('manager-add');
        }

    }

    //管理模块的编辑
    public function edit()
    {
        if(!empty($_POST))
        {
            $name = Request::param('ma_name');
            $address = Request::param('ma_address');
            $father = Request::param('ma_father');
            //查看输入的管理名称是否是最高等级
            $level = Manager::where('ma_name',$name)->value('ma_level');
            if($level==0&&$father!='无父类'){
                echo '1';
                exit;
            }
        }
        else{
            $ma_id = Request::param('ma');
            $ma = Manager::where('ma_id',$ma_id)->find();
            $manager = Manager::where('ma_level',0)->select();
            $this->assign('ma',$ma);
            $this->assign('manager',$manager);
            return $this->fetch('manager-edit');
        }
    }

    //管理模块的删除
    public function del()
    {
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $d_data = Manager::where('ma_id',$id)->find(); //查询准备要删除的数据
        $result = Manager::where('ma_id',$id)->delete();  //删除此id对应的数据
        Manager::where('ma_id',$d_data['ma_father'])->setDec('ma_son'); //删除数据的对应父类ma_son字段自减1
        if($result)
        {
            $response = array(
              'error' => 0,
              'errmsg' => 'success',
              'data' => true
            );
        }
        else{
            $response = array(
                'error' => -1,
                'errmsg' => 'fail',
                'data' => false
            );
        }

        echo json_encode($response);  //返回json数据
    }
}