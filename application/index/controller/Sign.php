<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/9
 * Time: 15:23
 */
namespace app\index\controller;
use think\Controller;
use think\facade\Request;
use app\index\model\Member;
use think\facade\Session;

class Sign extends Controller
{
    public function login()
    {
        Session::delete('username');
        if(!empty($_POST))
        {
            $username = Request::param('username');
            $password = Request::param('password');
            $find_name = Member::where('user_name',$username)->find();
            if($find_name==NULL)
            {
                echo '1';
                exit;
            }
            else if(md5($password)!=$find_name['user_password'])
            {
                echo '2';
                exit;
            }
            else{
                Session::set('username',$username);
                echo '3';
                exit;
            }
        }
        else{
            return $this->fetch();
        }
    }

    public function register()
    {
        if(!empty($_POST))
        {
            $username = Request::param('username');
            $password = Request::param('password');
            $password2 = Request::param('password2');
            $email = Request::param('email');
            $find_name = Member::where('user_name',$username)->find();
            if($find_name!=NULL)
            {
                echo '1';
                exit;
            }
            else if($password!=$password2)
            {
                echo '2';
                exit;
            }
            else
            {
                $res = Member::insert([
                    'user_name'=>$username,
                    'user_email'=>$email,
                    'user_password'=>md5($password),
                    'user_create'=>date('Y-m-d H:i:s',time()),
                ]);
                if($res)
                {
                    echo '3';
                    exit;
                }
                else{
                    echo '4';
                    exit;
                }
            }
        }
        else{
            return $this->fetch();
        }
    }

}