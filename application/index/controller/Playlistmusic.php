<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/4
 * Time: 10:51
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Music;
use app\api\controller\AccessPages;
use think\facade\Cache;

class Playlistmusic extends Controller
{
    /**
     * 随机获取歌单里面的音乐
     **/
    public function getsmusic()
    {
        $pname = input('get.pname');  //接收前端Ajax传过来的数据
        $result = music::where('music_playlist', $pname)->select();
        $result_count = count($result);
        $result_start = music::where('music_playlist', $pname)->value('music_id');
        $music_number = rand($result_start, $result_start + $result_count);
        $this->playmusic($music_number);
    }

    /**
     * 歌曲下一首播放
     **/
    public function music_next()
    {
        //读取缓存的音乐名
        $mname = Cache::store('redis')->get('music_name');
        $music_atpresent_id = music::where('music_name', $mname)->value('music_id');
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
        $music_atpresent_id = music::where('music_name', $mname)->value('music_id');
        $music_atpresent_id = $music_atpresent_id - 1;
        $this->playmusic($music_atpresent_id);
    }

    /**
     * 音乐播放
     **/
    public function playmusic($music_number)
    {
        $music_address = music::where('music_id', $music_number)->value('music_address');
        $music_name = music::where('music_id', $music_number)->value('music_name');
        //设置音乐名字缓存
        Cache::store('redis')->set('music_name', $music_name);

        $get_music_message2 = 'http://m.kugou.com/app/i/getSongInfo.php?hash=';
        $get_music_message3 = '&cmd=playInfo';
        $get_music_mp3url = $get_music_message2 . $music_address . $get_music_message3;

        $music = new AccessPages();
        $music_all_output2 = $music->AccessPage($get_music_mp3url);

        $music_all_output2 = json_decode($music_all_output2);

        $music_data_address = $music_all_output2->url;
        if ($music_name) {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_address' => $music_data_address,
                'music_name' => $music_name,
            );
        } else {
            $response = array(
                'error' => -1,
                'errmsg' => 'fail',
                'data' => false
            );
        }

        echo json_encode($response);  //返回json数据

    }
}