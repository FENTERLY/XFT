<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/3
 * Time: 13:50
 */
namespace app\api\controller;
use think\Controller;
use app\index\model\Newsong;

class Getnewmusic extends Controller
{
    /**
     * 获取最新的歌曲
    **/
    public function getnewmusic()
    {
        $url = 'www.kugou.com';
        $acc = new AccessPages();
        $output =  $acc->AccessPage($url);

        //获取到新歌li标签里面的所有歌曲的信息并存进数据库
        preg_match_all('/<div class="tabC" id="SongtabContent">(.*?)<\/div>/mis',$output,$match);
        preg_match_all('/<ul>(.*?)<\/ul>/mis',$match[0][0],$match);
        preg_match_all('/<li (.*?)>(.*?)<\/li>/mis',$match[0][0],$match);

        foreach($match[1] as $music_data)
        {
            preg_match_all("/data='(.*?)'/",$music_data,$music_data);
            $music_hash = json_decode($music_data[1][0])->Hash;//存放音乐的hash值

            $get_music_message = 'www.kugou.com/yy/index.php?r=play/getdata&hash=';
            $music_information_url = $get_music_message . $music_hash;

            $music_information = $acc->AccessPage($music_information_url);
            $music_information = json_decode($music_information)->data;

            $music_img = $music_information->img;//存放音乐照片
            $music_author_name = $music_information->author_name;//存放音乐人
            $music_song_name = $music_information->song_name;//存放音乐名字
            $music_lyrics = $music_information->lyrics;//存放音乐歌词

            //判断数据库表新歌里面没有此歌曲才存入
            $music_exist = Newsong::where('newsong_name',$music_song_name)->where('newsong_singer',$music_author_name)->find();
            if($music_exist==NULL)
            {
                $res = Newsong::insert(
                    [
                        'newsong_name' => $music_song_name,
                        'newsong_singer' => $music_author_name,
                        'newsong_img' => $music_img,
                        'newsong_address' => $music_hash,
                        'newsong_lyrics' => $music_lyrics
                    ]
                );
                if($res)
                {
                    echo '已经录入一首新歌';
                }
                else{
                    echo '暂无可以录入的新歌';
                }
            }
            else{
                continue;
            }
            break;
        }
    }
}