<?php

namespace App\Http\Controllers\Sign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\UserPubKeyModel;

class IndexController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    //在线验签
    public function signOnline(){
        return view('sign.online');
    }
    public function signOnline1(){
        unset($_POST['_token']);
        echo '<pre>';print_r($_POST);echo'</pre>';
        //接收POST参数
        $sign=base64_decode($_POST['sign']);
        unset($_POST['sign']);

        $params=[];
        foreach($_POST['k'] as $k=>$v){
            if(empty($v)){
                continue;   //跳过空字段
            }
            $params[$v]=$_POST['v'][$k];
        }
        echo '<pre>';print_r($params);echo'</pre>';
        //拼接参数
        $str='';
        foreach($params as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        $str=rtrim($str,'&');
        echo '<hr>';
        echo $str;
        echo '<hr>';
        //验签
        $uid=Auth::id();  //获取登录用户的uid
//        echo $uid;die;
        $u=UserPubKeyModel::where(['uid'=>$uid])->first();
//        echo $u;die;

        $status = openssl_verify($str,$sign,$u->pubkey,OPENSSL_ALGO_SHA256);
        if($status){
            echo '验签成功';
        }else{
            echo '验签失败';
        }
    }
}
