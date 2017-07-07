<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    /***************************************************微信支付*****************************************************/

    public function wxPay(Request $request)
    {
        \Pingpp\Pingpp::setApiKey(env('PING_TEST_API_KEY'));

        // \Pingpp\Pingpp::setPrivateKeyPath(Storage::disk('local')->get('rsa_private_key.pem'));
        \Pingpp\Pingpp::setPrivateKeyPath(public_path() . '/rsa_private_key.pem');

        $charge = \Pingpp\Charge::create([
            'order_no'  =>  time().rand(1000,9999),
            'amount'    => '100',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
            'app'       => ['id' => env('PING_APP_ID')],
            'channel'   => 'wx_pub',
            'currency'  => 'cny',
            'client_ip' => $request->ip(),
            'subject'   => 'class-demo-weixinpay',
            'body'      => '1_32',
            'extra'     => ['open_id' => 'osfNct43WQbeULEFiVm5YZa2nIMA']
        ]);

        // return $charge;
        return view('payment.wxpay',compact('charge'));
    }

    public function wxPaySuccess(Request $request)
    {
        \Session::flash('paid_success','微信付款成功啦！');
        return redirect('/');
        // dd('weixin paid');
    }




/*******************************************************支付宝支付*******************************************************/

    public function aliPay(Request $request)
    {
        \Pingpp\Pingpp::setApiKey(env('PING_TEST_API_KEY'));

        \Pingpp\Pingpp::setPrivateKeyPath(public_path('rsa_private_key.pem'));

        $charge = \Pingpp\Charge::create([
            'order_no'  =>  time().rand(1000,9999),
            'amount'    => '100',//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
            'app'       => ['id' => env('PING_APP_ID')],
            'channel'   => 'alipay_pc_direct',
            'currency'  => 'cny',
            'client_ip' => $request->ip(),
            'subject'   => 'class-demo-alipay',
            'body'      => '1_32',
            'extra'     => array('success_url' => 'http://pay.dev/pay/alipay/success')
        ]);

        // return $charge; 
        return view('payment.alipay',compact('charge'));
    }

    public function aliPaySuccess(Request $request)
    {
        // if($this->isFromAlipay($request->get('notify_id'))){

            //验证通过的代码逻辑
            \Session::flash('paid_success','支付宝付款成功啦！');
            return redirect('/');
        // }

        // return 'failed';

        // dd($request->all());
    }

    /*验证是否是支付宝发来的通知*/
    protected function isFromAlipay($notifyId)
    {
        $url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . trim(env('ALIPAY_PID')) . '&notify_id=' . $notifyId;
        $response = $this->httpGet($url);
        return (bool) preg_match("/true$/i",$response);
    } 

    protected function httpGet($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        // curl_setopt($curl, CURLOPT_CAINFO, public_path() . '/cacert.pem');//证书地址(还没下载)
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
