<?php

namespace App\Http\Controllers\pay;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayContrller extends Controller
{
    public function pay(){
        $request_data = [
            'out_trade_no'=>'1810_'.rand(100000,999999),//订单号
            'product_code'=>'FAST_INSTANT_TRADE_PAY',//	销售产品码
            'total_amount'=>rand(1000,100000),//价格
            'subject'=>'测设订单'//订单标题
        ];
        $data = [
            'app_id'=>'2016092600603244',
            'method'=>'alipay.trade.page.pay',//接口名称
            'charset'=>'utf-8',//请求编码
            'sign_type'=>'RSA2',//签名类型
            'timestamp'=>date('Y-m-d h:i:s'),
            'version'=>'1.0',
            'biz_content'=>json_encode($request_data)
        ];
        ksort($data);

        //echo '<pre>';print_r($data);
        $str = '';
        foreach ($data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        $str = rtrim($str,'&');
        //echo $str;die;
        $priv_key = storage_path('pay_key/private.pem');
        //openssl_sign(数据字符串&分割，生成的签名，私钥路径)
        openssl_sign($str,$sign0,openssl_get_privatekey("file://".$priv_key));
        $sign = base64_encode($sign0);
        //$data['sign'] = $sign;
        //$client = new Client();
        $url = "https://openapi.alipaydev.com/gateway.do?";
        $req_url = $url.$str .'&sign='.$sign;
        //echo $req_url;
        //$response = $client->request('post',$url,['form_params'=>$data]);
        //$body = $response->getBody();
        header("Location:".$req_url);
    }
}
