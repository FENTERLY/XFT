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
use app\api\controller\AccessPages;
use think\facade\Cache;

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

    /**
     * 获取歌手歌曲
    **/
    public function getmusic()
    {
        $mname = input('get.mname');
        $music_number = Singersong::where('singersong_name',$mname)->value('singersong_id');
        $this->playmusic($music_number);
    }

    /**
     * 歌曲下一首播放
     **/
    public function music_next()
    {
        //读取缓存的音乐名
        $mname = Cache::store('redis')->get('music_name');
        $music_atpresent_id = Singersong::where('singersong_name', $mname)->value('singersong_id');
        $music_atpresent_id = $music_atpresent_id + 1;
        $this->playmusic($music_atpresent_id);
    }

    /**
     * 歌曲上一首播放
     **/
    public function music_previous()
    {
        //读取缓存的音乐名
        $mname = Cache::store('redis')->get('music_name');
        $music_atpresent_id = Singersong::where('singersong_name', $mname)->value('singersong_id');
        $music_atpresent_id = $music_atpresent_id - 1;
        $this->playmusic($music_atpresent_id);
    }

    /**
     * 音乐播放
     **/
    public function playmusic($music_number)
    {
        $music_address = Singersong::where('singersong_id',$music_number)->value('singersong_address');
        $music_name = Singersong::where('singersong_id',$music_number)->value('singersong_name');

        //设置音乐名字缓存
        Cache::store('redis')->set('music_name', $music_name);

        $get_music_message2 = 'http://m.kugou.com/app/i/getSongInfo.php?hash=';
        $get_music_message3 = '&cmd=playInfo';
        $get_music_mp3url = $get_music_message2 . $music_address . $get_music_message3;

        $music = new AccessPages();
        $music_all_output2 = $music->AccessPage($get_music_mp3url);

        $music_all_output2 = json_decode($music_all_output2);

        $music_data_address = $music_all_output2->url;
        if($music_name)
        {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_address' => $music_data_address,
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

}