<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/14
 * Time: 15:57
 */
namespace app\admin\controller;
use think\facade\Request;
use app\admin\model\Member;
use app\admin\model\Manager;
use app\admin\model\Admin;
use think\facade\Session;
use think\Controller;

class Sendmail extends Controller
{
    public function sendmail()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user', Session::get('username'))->find();
        if ($username_is_manager == NULL) {

            return $this->error('请先登录', 'login/login');

        }
        if(!empty($_POST))
        {

        }
        else{

            $manager = Manager::where('ma_level',0)->select();
            $manager2 = Manager::where('ma_level',1)->select();
            $members = Member::select();
            $managers = Manager::select();
            $username = Session::get('username');
            $this->assign('managers',$managers);
            $this->assign('username',$username);
            $this->assign('manager2',$manager2);
            $this->assign('manager',$manager);
            $this->assign('members',$members);
            return $this->fetch('email/sendemail');
        }
    }
}