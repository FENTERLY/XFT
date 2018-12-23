<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/23
 * Time: 14:08
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\Music;

class GetLyrics extends Controller
{
    /**
     * 将音乐歌词传回前端
    **/
    public function music_lyrics()
    {
        $mname = input('get.mname');//接收前端Ajax传过来的数据
        $mtime = input('get.mtime');
        $music_lyrics = Music::where('music_name',$mname)->value('music_lyrics');
        $music_lyrics = explode("\n",$music_lyrics);//分取每一排歌词,用逗号隔开每句歌词
        //将每句歌词的时间跟内容分开存放
        foreach($music_lyrics as $k=>$ml)
        {
            $music[$k]['times'] = substr($ml,1,5);
            $music[$k]['lyrics'] = substr($ml,10);
            //如果传进来的时间跟当前时间匹配，则返回歌词的下标
            if($mtime==$music[$k]['times'])
            {
                $target = $k;
            }
        }
        if($music_lyrics)
        {
            $response = array(
                'error' => 0,
                'errmsg' => 'success',
                'data' => true,
                'music_lyrics' => $music[$target]['lyrics'],
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