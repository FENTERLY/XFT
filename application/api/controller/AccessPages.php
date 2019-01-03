<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2019/1/3
 * Time: 13:53
 */
namespace app\api\controller;
use think\Controller;

class AccessPages extends Controller
{
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