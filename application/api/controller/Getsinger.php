<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/2
 * Time: 14:18
 */
namespace app\api\controller;
use think\Controller;
use app\index\model\Singer;

class Getsinger extends Controller
{
    /**
     * 获取歌手页面的信息
    **/
    public function getsinger()
    {
        $acc = new AccessPages();
        $music_singer_html = $acc->AccessPage('www.kugou.com/yy/html/singer.html');
        preg_match_all('/<ul id="list_head" class="clear_fix">(.*?)<\/ul>/mis',$music_singer_html,$music_match);
        //获取到歌手的信息页面在信息页面进行爬取歌手的资料
        preg_match_all('/<li .*?>(.*?)<\/li>/mis',$music_match[0][0],$music_singer);

        $singer_count = count($music_singer[0]);

        for($i=0;$i<$singer_count;$i++)
        {
            preg_match_all('/<a .*? href="(.*?)">(.*?)<\/a>/mis', $music_singer[0][$i], $music_singer_url);
            $music_singer_url = str_replace('https', 'http', $music_singer_url[1][0]);

            $singer_html = $acc->AccessPage($music_singer_url);
            preg_match_all('/<div class="clear_fix">(.*?)<\/div>/mis',$singer_html,$singer_name);
            //$singer_name存入歌手名字
            $singer_name = trim(strip_tags($singer_name[1][0]));

            preg_match_all('/<ul id="song_container">(.*?)<\/ul>/mis',$singer_html,$singer_li);
            preg_match_all('/<li>(.*?)<\/li>/mis',$singer_li[0][0],$singer_li);

            //统计歌曲数量
            $music_count = count($singer_li[0]);

            //将歌手的所有歌曲的hash值都存放到数据库表里
            foreach($singer_li[0] as $k => $singer_music)
            {
                preg_match_all('/<input (.*?) value="(.*?)">/mis',$singer_music,$singer_li);
                //获取|第一次出现的地方
                $changes_number = strpos($singer_li[2][0],'|');
                $singer_music_hash = substr($singer_li[2][0],$changes_number+1);
                $changes_number2 = strpos($singer_music_hash,'|');
                //截取后把最后的歌曲的hash值传入到$singer_music_hash
                $singer_music_hash = substr($singer_music_hash,0,$changes_number2);
                $music['url'][$k] = $singer_music_hash;
            }
            //将获取到的歌手歌曲转换为字符串
            $music_hash = implode(",",$music['url']);

            preg_match_all('/<div class="top">.*?<\/div>/mis',$singer_html,$singer_img);
            preg_match_all('/<img .* _src="(.*?)" .*?>/mis',$singer_img[0][0],$singer_img);
            //$singer_img存入歌手照片
            $singer_img = strip_tags($singer_img[1][0]);

            preg_match_all('/<p>(.*?)<\/p>/mis',$singer_html,$singer_information);
            //$singer_information存入歌手介绍
            $singer_information = strip_tags($singer_information[0][0]);

            //查询数据库表是否有这个歌手
            $singer_exits = Singer::where('singer_name',$singer_name)->find();
            if($singer_exits)
            {
                continue;
            }
            else
            {
                //存入数据库
                $res = Singer::insert(
                    [
                        'singer_name' => $singer_name,
                        'singer_img' => $singer_img,
                        'singer_information' => $singer_information,
                        'singer_music' => $music_hash,
                        'singer_music_count' => $music_count,
                        'singer_music_surplus' => $music_count,
                    ]
                );
                if($res)
                {
                    echo '成功录入一位歌手';
                }
            }
            exit;
        }

    }

}