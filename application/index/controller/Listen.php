<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/4
 * Time: 16:05
 */
namespace app\index\controller;
use think\Controller;
use think\facade\Session;
use app\index\model\Member;
use app\index\model\Singer;
use app\index\model\Singersong;

class Listen extends Controller
{
    public function listen()
    {
        $username_is_member = member::where('user_name',Session::get('username'))->find();
        if($username_is_member==NULL)
        {

            return $this->error('请先登录','sign/login');

        }
        $singer_name = $_GET['singer'];
        $singer = Singer::where('singer_name',$singer_name)->find();
        $singer_song = Singersong::where('singersong_singer',$singer_name)->select();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('singersong',$singer_song);
        $this->assign('singer',$singer);
        return $this->fetch();
    }
}