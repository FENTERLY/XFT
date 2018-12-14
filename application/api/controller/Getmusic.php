<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/13
 * Time: 16:56
 */
namespace app\api\controller;
use think\Controller;

class Getmusic extends Controller
{
    /**
     *采集歌单音乐(酷狗)
     * */
    public function getMusic()
    {
        //初始curl会话
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,"www.kugou.com");

        //参数是否返回请求结果
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //头部是否以数据流输出
        curl_setopt($ch,CURLOPT_HEADER,0);
        //设置请求超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,10);

        $index_output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        //获取歌单列表链接
        preg_match_all('/<a target="_blank" href="(.*?)" class="more fr">.*?<\/a>/',$index_output,$album_url);

        //替换掉https
        $album_url = preg_replace('/https/','http',$album_url[1][0],1);


        /**
         * 读取歌单列表里面的内容
         **/
        //初始curl会话
        $album_ch = curl_init();

        curl_setopt($album_ch,CURLOPT_URL,$album_url);

        //参数是否返回请求结果
        curl_setopt($album_ch,CURLOPT_RETURNTRANSFER,1);
        //头部是否以数据流输出
        curl_setopt($album_ch,CURLOPT_HEADER,0);
        //设置请求超时时间
        curl_setopt($album_ch,CURLOPT_TIMEOUT,10);

        $album_output = curl_exec($album_ch);
        //释放curl句柄
        curl_close($album_ch);


        /**
         * 获取所有歌单的链接
         **/
        preg_match_all('/<ul id="ulAlbums">.*?<\/ul>/mis',$album_output,$album_ul);
        preg_match_all('/<a title=".*?" href="(.*?)">.*?<\/a>/',$album_ul[0][0],$album_list_url);

        foreach($album_list_url as $k => $a)
        {
            $album_list['url'] = $a;
        }

        print_r($album_list['url']);




    }

}