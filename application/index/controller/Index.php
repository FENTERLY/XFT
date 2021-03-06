<?php
namespace app\index\controller;
use app\index\model\Newsong;
use think\Controller;
use think\facade\Session;
use app\index\model\Playlist;
use app\index\model\Member;
use app\index\model\Singer;

class Index extends Controller
{
    /**
     * 主页面展示
    **/
    public function index()
    {
        $username_is_member = member::where('user_name',Session::get('username'))->find();
        if($username_is_member==NULL)
        {

            return $this->error('请先登录','sign/login');

        }
        $playlist = Playlist::limit(12)->order('playlist_id','desc')->select();
        $singer = Singer::limit(8)->select();
        $newsong = Newsong::limit(7)->order('newsong_id','desc')->select();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('singer',$singer);
        $this->assign('playlist',$playlist);
        $this->assign('newsong',$newsong);
        return $this->fetch();
    }



}
