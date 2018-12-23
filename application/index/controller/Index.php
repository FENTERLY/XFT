<?php
namespace app\index\controller;
use think\Controller;
use think\facade\Session;
use app\index\model\Music;
use app\index\model\Playlist;
use app\index\model\Member;

class Index extends Controller
{
    public function index()
    {
        $username_is_member = member::where('user_name',Session::get('username'))->find();
        if($username_is_member==NULL)
        {

            return $this->error('请先登录','sign/login');

        }
        $playlist = Playlist::limit(12)->select();
        $username = Session::get('username');
        $this->assign('username',$username);
        $this->assign('playlist',$playlist);
        return $this->fetch();
    }
    public function getsmusic()
    {
        $pname = input('get.pname');  //接收前端Ajax传过来的数据
        $result = music::where('music_playlist',$pname)->select();
        $result_count = count($result);
        $result_start = music::where('music_playlist',$pname)->value('music_id');
        $music_number = rand($result_start,$result_start+$result_count);
        $music_address = music::where('music_id',$music_number)->value('music_address');
        $music_name = music::where('music_id',$music_number)->value('music_name');
        if($result)
        {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_address' => $music_address,
                'music_name' => $music_name,
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
     * 歌曲下一首播放
    **/
    public function music_next()
    {
        $mname = input('get.mname');//接收前端Ajax传过来的数据
        $music_atpresent_id = music::where('music_name',$mname)->value('music_id');
        $music_atpresent_id = $music_atpresent_id+1;
        $music_next_address = music::where('music_id',$music_atpresent_id)->value('music_address');
        $music_next_name = music::where('music_id',$music_atpresent_id)->value('music_name');
        if($music_next_name)
        {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_address' => $music_next_address,
                'music_name' => $music_next_name,
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
     * 歌曲上一首播放
    **/
    public function music_previous()
    {
        $mname = input('get.mname');//接收前端Ajax传过来的数据
        $music_atpresent_id = music::where('music_name',$mname)->value('music_id');
        $music_atpresent_id = $music_atpresent_id-1;
        $music_previous_address = music::where('music_id',$music_atpresent_id)->value('music_address');
        $music_previous_name = music::where('music_id',$music_atpresent_id)->value('music_name');
        if($music_previous_name)
        {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_address' =>  $music_previous_address,
                'music_name' => $music_previous_name,
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
