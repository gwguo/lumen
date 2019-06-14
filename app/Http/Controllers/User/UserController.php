<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\UserModel;
class UserController extends Controller
{
    //注册
    public function reg(){
        echo 1;
    }
    public function regD(Request $request){
        $data = $request->input();
        $uid = UserModel::insertGetId($data);
        dd($uid);
    }
    //登录
    public function loginD(Request $request){
        $username = $request->username;
        $pwd = $request->pwd;
        $user = UserModel::where('username',$username)->first();
        if ($user){
            if ($pwd!=$user->pwd){
                echo '登录失败';
            }else{
                echo '登录成功';
            }
        }else{
            echo '登录失败';
        }
    }
    //修改密码
    public function passwordD(Request $request){
        $username = $request->username;
        $pwd1 = $request->pwd;//原密码
        $pwd2 = $request->pwd;//新密码
        $pwd = $request->pwd;//确认新密码
        if ($pwd2!=$pwd){
            echo '新密码不一致';
        }
        $user = UserModel::where('username',$username)->first();
        if ($user){
            $res = UserModel::where('username',$username)->update(['pwd'=>$pwd]);
            dd($res);
        }else{
            echo '没有此用户';
        }
    }
    //天气
    public function weather(Request $request){
        $weather = $request->weather;
        $url = "http://api.k780.com/?app=weather.future&weaid={$weather}&&appkey=42248&sign=7ae9418fd68085357aff895f390c3bd0&format=json";
        $data = file_get_contents($url);
        $data = json_decode($data,true);
        dd($data['result'][0]);
    }
}
