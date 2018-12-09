<?php
/**
 * Created by PhpStorm.
 * User: 11491
 * Date: 2018/12/9
 * Time: 15:23
 */
namespace app\index\controller;
use think\Controller;

class Sign extends Controller
{
    public function login()
    {
        return $this->fetch();
    }
    public function register()
    {
        return $this->fetch();
    }
}