<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/10
 * Time: 15:51
 */
namespace app\admin\controller;

use app\admin\model\Manager;
use app\admin\model\Member;
use think\facade\Session;
use think\Controller;
use think\facade\Request;

class Members extends Controller
{
    public function member()
    {
        if(Session::get('username')==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        else {

            $manager = Manager::where('ma_level',0)->select();
            $manager2 = Manager::where('ma_level',1)->select();
            $member = Member::select();
            $username = Session::get('username');
            $this->assign('member',$member);
            $this->assign('username',$username);
            $this->assign('manager2',$manager2);
            $this->assign('manager',$manager);
            return $this->fetch();

        }
    }

    //用户的添加
    public function add()
    {

    }

    //用户的编辑
    public function edit()
    {
        if(!empty($_POST))
        {
            $ids = Request::param('user_id');
            $names = Request::param('user_name');
            $emails = Request::param('user_email');
            $vips = Request::param('user_vip');
            //判断vips字样，是为1，否为0
            if($vips=='是')
            {
                $vips=1;
            }
            else{
                $vips=0;
            }

            //更改数据库中相同ID的数据
            $res = Member::where('user_id',$ids)->update([
                'user_name' => $names,
                'user_email' => $emails,
                'user_vip' => $vips
            ]);
            if($res)
            {
                echo '1';
                exit;
            }
            else{
                echo '2';
                exit;
            }


        }
        else{
            $me_id = Request::param('me');
            $members = Member::where('user_id',$me_id)->find();
            $this->assign('members',$members);
            return $this->fetch('member-edit');

        }

    }

    //用户的删除
    public function del()
    {
        if(Session::get('username')==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $result = Member::where('user_id',$id)->delete();  //删除此id对应的数据
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