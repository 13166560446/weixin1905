<?php

namespace App\Admin\Controllers;

use App\Model\GoodsModel;
use App\Model\WxUsermodel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use GuzzleHttp\Client;


class WxmsgController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';
    public function sendmsg(){
        $openid_arr=WxUsermodel::select('openid')->get()->toArray();
        $openid=array_column($openid_arr,'openid');
//        print_r($openid);
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=28_qO-eB_2RpFNc4c1G4tDrzd0UxEMRCZiM7CeuES2FAKX2Q7HRt1-0u0SoU5NnS5ATqJbWPRW0FruMA2Q2QtcbBlSou7H-s0GIIV_yA2tOB7P3XfaHOBIUXpA4pNouZiJ09iQ1jWq3n_hWjowECNGiAGAPAT';
        $msg=date('Y-m-d H:i:s').'哈哈哈哈哈';
        $data=[
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>['content'=>$msg]
        ];
        $client=new Client();
        $response=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        echo $response->getBody();
    }


}
