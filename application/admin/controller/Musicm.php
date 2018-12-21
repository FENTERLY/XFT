<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/21
 * Time: 12:09
 */
namespace app\admin\controller;
use think\Controller;
use think\facade\Session;
use think\facade\Request;
use app\admin\model\Music;
use app\admin\model\Admin;
use app\admin\model\Manager;
use app\admin\model\Playlist;

class MusicM extends Controller
{
    /**
     * 歌单管理页面
    **/
    public function playlist()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        else{
            $playlist = Playlist::paginate(10);
            // 获取分页显示
            $page = $playlist->render();
            $manager = Manager::where('ma_level',0)->select();
            $manager2 = Manager::where('ma_level',1)->select();
            $username = Session::get('username');
            $this->assign('manager2',$manager2);
            $this->assign('manager',$manager);
            $this->assign('username',$username);
            $this->assign('playlist',$playlist);
            $this->assign('page',$page);
            return $this->fetch();
        }
    }

    /**
     * 歌单删除
    **/
    public function pldel()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $result = Playlist::where('playlist_id',$id)->delete();//删除id对应的数据
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

    /**
     * 批量删除歌单数据
    **/
    public function delall()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $result = Playlist::where('playlist_id',$id)->delete();//删除id对应的数据
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



    /**
     * 歌曲管理页面
    **/
    public function music()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        else{
            $music = Music::paginate(10);
            // 获取分页显示
            $page = $music->render();
            $manager = Manager::where('ma_level',0)->select();
            $manager2 = Manager::where('ma_level',1)->select();
            $username = Session::get('username');
            $this->assign('manager2',$manager2);
            $this->assign('manager',$manager);
            $this->assign('username',$username);
            $this->assign('music',$music);
            $this->assign('page', $page);
            return $this->fetch();
        }
    }

    /**
     * 歌单删除
     **/
    public function mdel()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $result = Music::where('music_id',$id)->delete();//删除id对应的数据
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

    /**
     * 批量删除歌单数据
     **/
    public function mp3delall()
    {
        //拿到session的name属性对管理员的表进行筛选查看是否有这个管理员
        $username_is_manager = admin::where('admin_user',Session::get('username'))->find();
        if($username_is_manager==NULL)
        {

            return $this->error('请先登录','login/login');

        }
        $id = input('get.id');  //接收前端Ajax传过来的数据
        $result = Music::where('music_id',$id)->delete();//删除id对应的数据
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