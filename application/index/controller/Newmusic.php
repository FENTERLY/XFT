<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/3
 * Time: 19:14
 */
namespace app\index\controller;
use app\index\model\Newsong;
use think\Controller;
use think\Cache;
use app\api\controller\AccessPages;

class Newmusic extends Controller
{
    /**
     *获取前端传来的新歌歌名
    **/
    public function getnewsong()
    {
        $mname = input('get.mname');  //接收前端Ajax传过来的数据
        $music_number = Newsong::where('newsong_name',$mname)->value('newsong_id');
        $this->playmusic($music_number);
    }

    /**
     * 音乐播放
     **/
    public function playmusic($music_number)
    {
        $music_address = Newsong::where('newsong_id',$music_number)->value('newsong_address');
        $music_name = Newsong::where('newsong_id',$music_number)->value('newsong_name');

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