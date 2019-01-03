<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/28
 * Time: 15:08
 */
namespace app\api\controller;
use think\Controller;
use app\admin\model\Playlist;

class Getplaylist extends Controller
{
    /**
     *采集歌单音乐(酷狗)
     * */
    public function getplaylist()
    {
        $index_url = 'www.kugou.com';
        $acc = new AccessPages();
        $index_output = $acc->AccessPage($index_url);

        //获取歌单列表链接
        preg_match_all('/<a target="_blank" href="(.*?)" class="more fr">.*?<\/a>/',$index_output,$album_url);

        $album_urls = preg_replace('/https/','http',$album_url[1][0],1);


        //读取歌单列表里面的内容
        $album_output = $acc->AccessPage($album_urls);

        //获取所有歌单的链接
        preg_match_all('/<ul id="ulAlbums">.*?<\/ul>/mis',$album_output,$album_ul);
        preg_match_all('/<a title=".*?" href="(.*?)">.*?<\/a>/',$album_ul[0][0],$album_list_url);

        foreach($album_list_url as $k => $a)
        {
            $a = preg_replace('/https/','http',$a,1);
            $album_list['url'] = $a;
        }

        /**
         * 获取到所有歌单列表里的所有音乐('存储歌单信息到数据库表')
         **/
        foreach($album_list['url'] as $k => $album_list_music)
        {

            $album_li_output = $acc->AccessPage($album_list_music);

            //获取到各个歌单里面的歌曲
            preg_match_all('/<a title="(.*?)" hidefocus="true" href="javascript:;" data="(.*?)">(.*?)<\/a>/m',$album_li_output,$album_li_a);

            //把获取到的歌单里面音乐的地址传进参数
            $album_list['music'][$k] = $album_li_a[2];

            //将获取到的歌单里面的歌曲hash值数组转换成字符串
            $album_list['music'][$k] = implode(",",$album_list['music'][$k]);

            //获取到歌单的详情
            preg_match_all('/<img alt="(.*?)" src="(.*?)" _src="(.*?)"(.*?)>/m',$album_li_output,$album_li_b);

            //获取到歌单的图片
            $album_list['img'][$k] = $album_li_b[3][0];

            //获取到歌单的名称
            $album_list['name'][$k] = $album_li_b[1][0];

            //查询歌单数据库表是否有此歌单
            $playlist_is_exist = Playlist::where('playlist_name',$album_list['name'][$k])->find();

            if($playlist_is_exist)
            {
                continue;
            }
            else{
                Playlist::insert([
                    'playlist_name' => htmlspecialchars_decode($album_list['name'][$k]),
                    'playlist_img' => $album_list['img'][$k],
                    'playlist_count' => count($album_li_a[2]),
                    'playlist_surplus' => count($album_li_a[2]),
                    'playlist_mp3url' => $album_list['music'][$k],
                ]);

                echo "已经成功录入1个歌单";
            }

            exit;

        }
    }

}