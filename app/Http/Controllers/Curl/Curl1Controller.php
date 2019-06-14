<?php

namespace App\Http\Controllers\Curl;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
class Curl1Controller extends Controller
{
    //使用guzzle   post提交方式
    public function guzzle(){
        $client = new Client();
        //加密
        //$data = 'holle word';
        //对称加密
//        $post_data = $this->encrypt($data);
        //非对称加密
        //$post_data = $this->Fencrypt($data);
        //验签
        $post_data = [
            'goods_name'=>'棒棒糖',
            'goods_price'=>'10',
            'add_time'=>time()
        ];
        ksort($post_data);
        $str = '';
        foreach ($post_data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str = rtrim($str,'&');
        //openssl_sign(数据字符串&分割，生成的签名，私钥路径)
        openssl_sign($str,$sign,openssl_get_privatekey("file://".'/wwwroot/blog/public/keys/priv.pem'));
        $sign = base64_encode($sign);
        $post_data['sign'] = $sign;
        //要发送的地址
        $url = "http://www.gwgw.com/curl/postData";
        //发送请求和数据
        $response = $client->request('post',$url,['form_params'=>$post_data]);
        $body = $response->getBody();
        echo $body;
    }
    //客户端get请求
    public function getCurl(){
        $url = 'https://www.baidu.com';
        //1、初始化
        $ch = curl_init($url);
        //2、设置参数
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
//        curl_setopt($ch,CURLOPT_URL,$url);
        //3、执行
        curl_exec($ch);
        //4、关闭
        curl_close($ch);
    }
    //客户端post请求
    public function postCurl(){
        $url = 'http://www.gwgw.com/curl/postData';
        //数组形式发送数据
        $post_data = [
            'name'=>'admin',
            'pwd'=>'admin'
        ];
        //www-x-form-urlencoded
        // $post_data = "name=admin&pwd=admin";
        //json模式发送数据
        //$post_data = json_encode($post_data);
        //raw字符串模式
        //$post_data = "abcdefg";
        //初始化
        $ch = curl_init($url);
        //设置参数
            //false是转化为字符串  true是直接输出
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
            //设置post方式  true的情况下是post提交
            curl_setopt($ch,CURLOPT_POST,true);
            //要发送的数据
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
        //执行会话
        $data = curl_exec($ch);
        //检测错误
        //curl_errno();
        //curl_error();
        //4、关闭会话
        curl_close($ch);
    }
    //对称加密
    public function encrypt($data){
        $key = "123";   // 加密密钥  和服务端商定好
        $iv = '1234567890zzzzzz';   //初始向量  16位  和服务端商定好
        //使用openssl_encrypt进行数据加密
        $post_data = openssl_encrypt($data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        //加密出来的数据是乱码  使用base64在加密
        $post_data = base64_encode($post_data);
        return $post_data;
    }
    //对称解密
    public function decrypt($data){
        //先使用base64解密
        $data = base64_decode($data);
        //接收的数据解密
        $key = '123';  //和发送端必须保持一致
        $iv = '1234567890zzzzzz';//和发送端必须保持一致
        //使用openssl_encrypt进行数据解密
        $data = openssl_decrypt($data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        return $data;
    }
    //非对称加密
    public function Fencrypt($data){
        //非对称加密
            //生成公钥私钥
            //  使用openssl genrsa -out priv.pem 2048生成私钥(在Xshell)
            //  根据私钥 使用openssl rsa -in priv.pem -pubout -out pub.key 生成公钥(在Xshell)
        //使用函数  根据私钥文件生成key 要根据公钥解密
        $priv_key = openssl_get_privatekey("file://".'/wwwroot/blog/public/keys/priv.pem');
        //使用函数加密  (要加密的数据   生成的加密数据   私钥key)
        openssl_private_encrypt($data,$post_data,$priv_key);
        return $post_data;
    }
    //非对称解密
    public function Fdecrypt($data){
        //非对称解密
        $pub_key = openssl_get_publickey("file://".'/wwwroot/blog/public/keys/pub.key');
        //使用函数解密  (要加密的数据   生成的加密数据   公钥key)
        $data = openssl_public_decrypt($data,$post_data,$pub_key);
        return $post_data;
    }
    //服务端post响应
    public function postData(){
        //$_POST接收数组和模式
        //验证签名
        $data = $_POST;
        $sign = base64_decode($data['sign']);
        unset($data['sign']);
        $str = '';
        foreach ($data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str = rtrim($str,'&');
        $pub_key = openssl_get_publickey("file://".'/wwwroot/blog/public/keys/pub.key');
        //openssl_verify(字符串数据，生成的签名，公钥路径)
        $res = openssl_verify($str,$sign,$pub_key);
        if ($res){
            echo 'OK';
        }else{
            echo 'NO';
        }
        //file_get_contents接收raw和json模式
        //$data = file_get_contents("php://input");
        //对称解密
        //$post_data = $this->dncrypt($data);
        //非对称解密
        //$post_data = $this->Fdecrypt($data);
        //echo $post_data;die;
    }
}