<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/13
 * Time: 16:56
 */
namespace app\api\controller;
use think\Controller;
use app\admin\model\Music;

class Getmusic extends Controller
{
    /**
     *采集歌单音乐(酷狗)
     * */
    public function getMusic()
    {

        $index_url = 'www.kugou.com';
        $index_output = $this->AccessPage($index_url);

        //获取歌单列表链接
        preg_match_all('/<a target="_blank" href="(.*?)" class="more fr">.*?<\/a>/',$index_output,$album_url);

        $album_urls = preg_replace('/https/','http',$album_url[1][0],1);


        //读取歌单列表里面的内容
        $album_output = $this->AccessPage($album_urls);

        //获取所有歌单的链接
        preg_match_all('/<ul id="ulAlbums">.*?<\/ul>/mis',$album_output,$album_ul);
        preg_match_all('/<a title=".*?" href="(.*?)">.*?<\/a>/',$album_ul[0][0],$album_list_url);

        foreach($album_list_url as $k => $a)
        {
            $a = preg_replace('/https/','http',$a,1);
            $album_list['url'] = $a;
        }

        /**
         * 获取到所有歌单列表里的所有音乐
        **/
        foreach($album_list['url'] as $k => $album_list_music)
        {

            $album_li_output = $this->AccessPage($album_list_music);

            //获取到各个歌单里面的歌曲
            preg_match_all('/<a title="(.*?)" hidefocus="true" href="javascript:;" data="(.*?)">(.*?)<\/a>/m',$album_li_output,$album_li_a);

            //把获取到的歌单里面音乐的地址传进参数
            $album_list['music'][$k] = $album_li_a[2];
        }


        /**
         * 获取到音乐的mp3文件地址
        **/
        foreach($album_list['music'] as $k => $alm)
        {
            //把获取到的音乐地址后缀去掉
            foreach($alm as $k2 => $alm_value)
            {
                //去掉|之后包括|后面的所有数据
                $get_music_data = substr($alm_value,0,strpos($alm_value, '|'));
                $get_music_message = 'www.kugou.com/yy/index.php?r=play/getdata&hash=';

                //将歌曲的data码跟获取音乐所有信息的地址进行拼接
                $get_music_allurl[$k][$k2] = $get_music_message.$get_music_data;


                /**
                 * 获取到mp3文件
                 **/

                $music_all_output = $this->AccessPage($get_music_allurl[$k][$k2]);

                $music_all_output = json_decode($music_all_output);

                $music_all_output = $music_all_output->data;

                //获取歌曲的明星的图片
                $music_data_img =  $music_all_output->img;

                //获取歌曲的明星的名字
                $music_data_singer =  $music_all_output->author_name;

                //获取歌曲的mp3播放源
                $music_data_address =  $music_all_output->play_url;

                //获取歌曲的名字
                $music_data_name = $music_all_output->song_name;

                //查询数据库判断是否有这首歌存在
                $music_is_exist = Music::where('music_name',$music_data_name)->where('music_singer',$music_data_singer)->find();
                //如果存在就跳出本次循环
                if($music_is_exist)
                {
                    continue;
                }
                //如果不存在就录入数据库
                else
                {
                    $res = Music::insert([
                        'music_name' => $music_data_name,
                        'music_singer' => $music_data_singer,
                        'music_img' => $music_data_img,
                        'music_address' => $music_data_address,
                        'music_playlist' => $k+1,
                    ]);
                    if($res)
                    {
                        echo '数据录入成功';
                    }
                    exit;
                }

            }
        }



    }

    /**
     * 获取页面
    **/
    public function AccessPage($url)
    {
        //初始curl会话
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);

        //参数是否返回请求结果
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //头部是否以数据流输出
        curl_setopt($ch,CURLOPT_HEADER,0);
        //设置请求超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,100);

        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        return $output;
    }

}