<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Model\UserPubKeyModel;
class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function addSSHKey1(){
        return view('user.addkey');
    }
    //用户添加公钥
    public function addSSHKey2(){
        $key=trim($_POST['sshkey']);
        $uid=Auth::id();
        $user_name=Auth::user()->name;
        $data=[
            'uid'=>$uid,
            'name'=>$user_name,
            'pubkey'=>trim($key)
        ];
        //如果有记录则删除
        UserPubkeyModel::where(['uid'=>$uid])->delete();
        //添加新纪录
        $kid=UserPubkeyModel::insertGetId($data);
        if($kid){
            //页面跳转
            header('Refresh: 3;url='.env('APP_URL').'/home');
            echo '添加成功 公钥内容:   <br>'.$key;
            echo '</br>';
            echo "页面跳转中...";
        }
    }
}
