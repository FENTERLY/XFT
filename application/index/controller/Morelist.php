<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/26
 * Time: 16:26
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Playlist;
use app\index\model\Member;
use app\index\model\Newsong;
use think\facade\Session;

class Morelist extends Controller
{
    public function morelist()
    {
        $username_is_member = member::where('user_name',Session::get('username'))->find();
        if($username_is_member==NULL)
        {

            return $this->error('请先登录','sign/login');

        }
        $playlist = Playlist::paginate(18);
        // 获取分页显示
        $page = $playlist->render();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('playlist',$playlist);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function newsong()
    {
        $username_is_member = member::where('user_name',Session::get('username'))->find();
        if($username_is_member==NULL)
        {

            return $this->error('请先登录','sign/login');

        }
        $newsong = Newsong::paginate(12);
        //获取分页显示
        $page = $newsong->render();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('newsong',$newsong);
        $this->assign('page',$page);
        return $this->fetch();

    }
}