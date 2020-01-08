<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use App\Model\WxUsermodel;

class TestController extends Controller
{
    public function hello(){
       phpinfo();
    }

    public function adduser(){

        $pass ='123';
        $email='asdas';
        //使用秘钥函数
        $password = password_hash($pass,PASSWORD_BCRYPT);

        $data=[
            'user_name'=>'yyp',
            'password'=>$password,
            'email'=>$email
        ];
       $res=UserModel::insertGetId($data);
        var_dump($res);
    }



    //加密
    public function ord(){
        $char='Hello Word';
        $length=strlen($char);
        echo $length;echo '</br>';

        $pass= "";
        $pass2= "";
        for($i=0;$i<$length;$i++){
            echo $char[$i].'---'.ord($char[$i]);echo '</br>';
            echo chr(ord($char[$i])+3).'---'.(ord($char[$i])+3);echo '</br>';
            echo '<hr>';
             $pass.=$char[$i];
             $pass2.=chr(ord($char[$i])+3);
        }
        echo $pass;
        echo '<br>';
        echo $pass2;

    }
    //解密
    public function dec(){

    }
}
