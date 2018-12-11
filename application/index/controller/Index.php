<?php
namespace app\index\controller;
use think\Controller;
use think\facade\Session;

class Index extends Controller
{
    public function index()
    {
        $username = Session::get('username');
        $this->assign('username',$username);
        return $this->fetch();
    }

}
