<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/6
 * Time: 13:53
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;
use think\facade\Session;
use think\facade\Request;

class Login extends Controller
{
    public function login()
    {
        Session::delete('username');
        if(!empty($_POST))
        {
            $username = Request::param('username');
            $password = Request::param('password');
            $admin = Admin::where('admin_user',$username)->find();
            if($username!=$admin['admin_user']) {
                echo "1";
                exit;
            }
            if($password!=$admin['admin_password'])
            {
                echo "2";
                exit;
            }
            else
            {
                Session::set('username',$username);
                echo "3";
                exit;
            }
        }
        else{
            return $this->fetch();
        }


    }
}