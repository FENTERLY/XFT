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
use app\admin\model\Playlist;

class Getmusic extends Controller
{
    /**
     *采集歌单音乐(酷狗)
     * */
    public function getMusic()
    {
        /**
         * 获取到音乐的mp3文件地址('将所有歌曲录入数据库 一次录入5首音乐')
         **/
        //查看是否还有需要爬取的歌单
        $crawl_exits = Playlist::where('playlist_gets', 0)->find();
        if ($crawl_exits) {
            $hash = $crawl_exits['playlist_mp3url'];
            //剩余要爬取的音乐数量
            $surplus = $crawl_exits['playlist_count'] - $crawl_exits['playlist_surplus'];

            //将字符串转换为数组
            $album_list['music'] = explode(',', $hash);

            $num = 0;//统计收录歌曲的数目


            for ($i = $surplus; $i < $surplus + 5; $i++) {
                //去掉|之后包括|后面的所有数据
                $get_music_data = substr($album_list['music'][$i], 0, strpos($album_list['music'][$i], '|'));
                $get_music_message = 'www.kugou.com/yy/index.php?r=play/getdata&hash=';

                //将歌曲的data码跟获取音乐所有信息的地址进行拼接
                $get_music_allurl[$i] = $get_music_message . $get_music_data;


                /**
                 * 获取到歌曲信息json
                 **/

                $music_all_output = $this->AccessPage($get_music_allurl[$i]);

                $music_all_output = json_decode($music_all_output);

                $music_all_output = $music_all_output->data;

                //获取歌曲的歌词
                $music_data_lyrics = $music_all_output->lyrics;

                //获取歌曲的明星的图片
                $music_data_img = $music_all_output->img;

                //获取歌曲的明星的名字
                $music_data_singer = $music_all_output->author_name;

                //获取歌曲的名字
                $music_data_name = $music_all_output->song_name;

                //查询数据库里还未爬取到完整歌单的名字
                $playlist_name = Playlist::where('playlist_gets', 0)->value('playlist_name');

                //录入数据库
                $res1 = Music::insert([
                    'music_name' => $music_data_name,
                    'music_singer' => $music_data_singer,
                    'music_img' => $music_data_img,
                    'music_address' => $get_music_data,
                    'music_playlist' => $playlist_name,
                    'music_lyrics' => $music_data_lyrics,
                ]);
                $res2 = Playlist::where('playlist_name', $playlist_name)->setDec('playlist_surplus');

                //查看歌单数据库中是否已经完全爬取成功,是就更改gets属性为1
                Playlist::where('playlist_surplus', 0)->where('playlist_name', $playlist_name)->setInc('playlist_gets');

                if ($res1 && $res2) {
                    $num = $num+1;
                }
            }
            echo "已经成功收录该歌单的".$num."歌曲";
        }
        else{
            echo '暂无歌单需要收录';
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