<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/6
 * Time: 17:35
 */
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;
use app\admin\model\Manager;

class Index extends Controller
{
    public function index()
    {
        $manager = Manager::where('ma_level',0)->select();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('manager',$manager);
        return $this->fetch();

    }
}