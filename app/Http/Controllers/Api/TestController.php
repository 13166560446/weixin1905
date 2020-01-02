<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\ApiloginModel;
use Illuminate\Support\Str;
class TestController extends Controller
{
    public function test(){
        $user_info=[
            'name'=>'王大锤',
            'tel'=>'7451',
            'email'=>'2256006378'
        ];
        echo json_encode($user_info);
    }
    public function reg(request $request){
        $pass1=$request->input('pass1');
        $pass2=$request->input('pass2');
        if($pass1!=$pass2){
            die('输入的两次密码不一致');
        }
        $password=password_hash($pass1,PASSWORD_BCRYPT);
        $data=[
            'email' =>$request->input('email'),
            'name'  =>$request->input('name'),
            'password'=> $password,
            'last_login'=>time(),
            'last_ip'=>$_SERVER['REMOTE_ADDR'],
        ];
        $res=ApiloginModel::insertGetId($data);
        echo $res;
//     echo   json_encode($data);

    }
    public function login(request $request){
        $name=$request->input('name');
        $password=$request->input('password');
        $a=ApiloginModel::where(['name'=>$name])->first();
        if($a){
            if(password_verify($password,$a->password)){
                echo '登陆成功';
                //生成token
                $token=Str::random(32);
                $response=[
                    'errno'=>0,
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token
                    ]
                ];
//                return $response;
            }else{
                $response=[
                    'errno'=>40002,
                    'msg'=>'密码不正确'
                ];
            }

        }else{
            $response=[
                'errno'=>40003,
                'msg'=>'用户不存在'
            ];
        }
        return $response;

    }
}
