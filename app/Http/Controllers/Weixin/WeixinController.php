<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WxUsermodel;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class WeixinController extends Controller
{
        protected $access_token;

        public function __construct(){
            //获取access_token
            $this->access_token=$this->getAccessToken();
        }
        public function getAccessToken(){
            $key=   'wx_access_token';

            $access_token=Redis::get($key);
            if($access_token){
                return $access_token;
            }
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
            $data_json = file_get_contents($url);
            $arr = json_decode($data_json,true);
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,3600);
            return $arr['access_token'];
        }
        public function weixin()
        {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            $token = 'acb5as840asd316asd268asd';
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );

            if( $tmpStr == $signature ){
                echo $_GET['echostr'];
            }else{
                die('not ok');
            }
        }
        /*
        * 接收微信推送事件
         */

            public  function  receiv(){
                $log_file="wx.log";
                $xml_str=file_get_contents("php://input");
                //将接收的"数据记录到日志文件
                $data=date("Y-m-d H:i:s").">>>>>>>\n".$xml_str."\n\n";
                file_put_contents($log_file,$data,FILE_APPEND);
                //处理xml数据
                $xml_obj=simplexml_load_string($xml_str);
                $event=$xml_obj->Event; //类型
                if($event=='subscribe'){
                    $openid=$xml_obj->FromUserName;    //获取用户的openid
                    //判断用户是否已经存在
                    $u=WxUsermodel::where(['openid'=>$openid])->first();
                    if($u){
                        $msg='欢迎回来';
                        $xml='<xml>
                              <ToUserName><![CDATA['.$openid.']]></ToUserName>
                              <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
                              <CreateTime>'.time().'</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA['.$msg.']]></Content>
                            </xml>';
                        echo $xml;
                    }else{

                        //获取用户信息
                        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
                        $user_info=file_get_contents($url);
                        $u=json_decode($user_info,true);
                        $user_data=[
                            'openid'    => $openid,
                            'nickname'  =>$u['nickname'],
                            'sex'        =>$u['sex'],
                            'headimgurl'=>$u['headimgurl'],
                            'subscribe_time'=>$u['subscribe_time']
                        ];
//                        $log_content=data('Y-m-d H:i:s').">>>>>".$user_info."\n";
//                        file_put_contents("wx_user.log",$user_info,FILE_APPEND);

                        //openid 入库
                        $uid= WxUsermodel::insertGetId($user_data);
                        //回复用户关注
                        $msg="谢谢关注";
                        $xml='<xml>
                              <ToUserName><![CDATA['.$openid.']]></ToUserName>
                              <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
                              <CreateTime>'.time().'</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA['.$msg.']]></Content>
                            </xml>';
                        echo $xml;
                    }

                }
                //判断消息类型
                $msg_type = $xml_obj->MsgType;
                $touser = $xml_obj->FromUserName;           //接收消息得到用户openid
                $fromuser = $xml_obj->ToUserName;           //自己开发的公众号的id
                $time = time();
                $media_id=$xml_obj->MediaId;
                if($msg_type=='text'){
                    $content = date('Y-m-d H:i:s').$xml_obj->Content;
                    $response_text = '<xml>
                <ToUserName><![CDATA['.$touser.']]></ToUserName>
                <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
                <CreateTime>'.$time.'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA['.$content.']]></Content>
                </xml>
                ';
                    echo $response_text;        //回复用户消息
                    // TODO消息入库

                }elseif($msg_type=='image'){  //图片消息
                    //TODO 下载图片
                    $this->getMedia2($media_id,$msg_type);
                    //TODO 回复图片
                    $response='<xml>
                  <ToUserName><![CDATA['.$touser.']]></ToUserName>
                  <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
                  <CreateTime>'.time().'</CreateTime>
                  <MsgType><![CDATA[image]]></MsgType>
                  <Image>
                    <MediaId><![CDATA['.$media_id.']]></MediaId>
                  </Image>
                </xml>';
                echo $response;

                }elseif($msg_type=='voice'){ //语音消息
                    //下载语音
                    $this->getmedia2($media_id,$msg_type);
                    //回复语音
                    $response='<xml>
  <ToUserName><![CDATA['.$touser.']]></ToUserName>
  <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[voice]]></MsgType>
  <Voice>
    <MediaId><![CDATA['.$media_id.']]></MediaId>
  </Voice>
</xml>';
                    echo $response;

                }elseif($msg_type=='video'){
                    //下载视频
                    $this->getmedia2($media_id,$msg_type);
                    //回复视频
                    $response='<xml>
  <ToUserName><![CDATA['.$touser.']]></ToUserName>
  <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[video]]></MsgType>
  <Video>
    <MediaId><![CDATA['.$media_id.']]></MediaId>
    <Title><![CDATA[测试]]></Title>
    <Description><![CDATA[不可描述]]></Description>
  </Video>
</xml>';
                    echo $response;
                }

            }

        //获取用户基本信息
        public function getuserinfo(){
            $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
        }
        //获取素材
        public function getmedia(){
            $media_id='ANonSAFO0SR31M6LYQGz-j-8gXsfR3xBemj4Lha_vXyZnJlVkA5-yqx162NbyyHz';
            $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
            $img=file_get_contents($url);
            file_put_contents('cat.jpg',$img);
            echo "下载成功";
        }
        public function getmedia2($media_id,$media_type){
            $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
            //获取素材内容
            $client = new Client();
            $response=$client->request('GET',$url);

            //获取文件拓展名
            $f = $response->getHeader('Content-disposition')[0];
            $extension = substr(trim($f,'"'),strpos($f,'.'));
            //获取文件内容
            $file_content= $response->getBody();


            //  保存文件
            $save_path='wx_media';
            if($media_type=='image'){
                $file_name = date('YmdHis').mt_rand(11111,99999) . $extension;
                $save_path = $save_path . '/imgs/' . $file_name;
            }elseif($media_type=='voice'){  //保存语音文件
                $file_name = date('YmdHis').mt_rand(11111,99999) . $extension;
                $save_path = $save_path . '/voice/' . $file_name;
            }elseif($media_type=='video')
            {
                $file_name = date('YmdHis').mt_rand(11111,99999) . $extension;
                $save_path = $save_path . '/video/' . $file_name;
            }
            file_put_contents($save_path,$file_content);
        }
    /**
     * 刷新 access_token
     */
    public function flushAccessToken()
    {
        $key = 'wx_access_token';
        Redis::del($key);
        echo $this->getAccessToken();
    }
}
