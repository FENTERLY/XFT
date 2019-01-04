<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/4
 * Time: 15:04
 */
namespace app\api\controller;
use think\Controller;
use app\index\model\Singersong;
use app\index\model\Singer;

class Getsingersong extends Controller
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
        $singer_exits = Singer::where('singer_music_gets', 0)->find();
        if ($singer_exits) {
            $hash = $singer_exits['singer_music'];
            //剩余要爬取的音乐数量
            $surplus = $singer_exits['singer_music_count'] - $singer_exits['singer_music_surplus'];

            //将字符串转换为数组
            $album_list['music'] = explode(',', $hash);

            $num = 0;//统计收录歌曲的数目


            for ($i = $surplus; $i < $surplus + 5; $i++) {
                //去掉|之后包括|后面的所有数据
                $get_music_data = $album_list['music'][$i];
                $get_music_message = 'www.kugou.com/yy/index.php?r=play/getdata&hash=';

                //将歌曲的data码跟获取音乐所有信息的地址进行拼接
                $get_music_allurl[$i] = $get_music_message . $get_music_data;


                /**
                 * 获取到歌曲信息json
                 **/

                $acc = new AccessPages();
                $music_all_output = $acc->AccessPage($get_music_allurl[$i]);

                $music_all_output = json_decode($music_all_output);

                $music_all_output = $music_all_output->data;

                //获取歌曲的歌词
                $music_data_lyrics = $music_all_output->lyrics;

                //获取歌曲的明星的图片
                $music_data_img = $music_all_output->img;

                //获取歌曲的名字
                $music_data_name = $music_all_output->song_name;

                //查询数据库里还未爬取到完整歌单的名字
                $singer_name = Singer::where('singer_music_gets', 0)->value('singer_name');

                //录入数据库
                $res1 = Singersong::insert([
                    'singersong_name' => $music_data_name,
                    'singersong_singer' => $singer_name,
                    'singersong_img' => $music_data_img,
                    'singersong_address' => $get_music_data,
                    'singersong_lyrics' => $music_data_lyrics,
                ]);
                $res2 = Singer::where('singer_name', $singer_name)->setDec('singer_music_surplus');

                //查看歌单数据库中是否已经完全爬取成功,是就更改gets属性为1
                Singer::where('singer_music_surplus', 0)->where('singer_name', $singer_name)->setInc('singer_music_gets');

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


}