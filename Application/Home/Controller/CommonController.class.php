<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;

class CommonController extends Controller {

    private $isLogin = false;
    private $userInfo;
    public function __construct()
    {
        parent::__construct();

        $user = session('stgjsd_user');
        if($user){
            $this->isLogin = true;
            $this->userInfo = $user;
        }
    }
    //根据先县区关键字查询
    public function searchByCounty(){
        if(IS_POST){
            $county = trim(I('county'));
            if(!$county){
                $this->error('参数错误');
            }
            $addressResult = curl_post(C('API_URL').'MdmDistrict/SearchByCountry',json_encode(array('DisName'=>$county)),$this->userInfo['CusCode']);
            $addressData = json_decode($addressResult,true);
            if($addressData['ResCode'] === '01'){
                $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$addressData['Data']));
            }else{
                $this->error('地址解析失败');
            }
        }
    }

    //根据商品条码或商品名称查询商品
    public function searchByTmOrMc(){
        if(IS_POST){
            $searchType = I('searchType');
            $params = [];
            $method = '';
            if(!$searchType){
                $this->error('查询类型错误');
            }
            if($searchType == 1){
                $params = ['SogBarcode'=>I('SogBarcode')];
                $method = 'SearchGoodsByCode';
            }else{
                $params = ['SogGoodsName'=>I('SogGoodsName')];
                $method = 'SearchGoodsByName';
            }
            $goodsResult = curl_post(C('API_URL').'OrderForBack/'.$method,json_encode($params),$this->userInfo['CusCode']);
            $goodsResult = json_decode($goodsResult,true);
            if($goodsResult['ResCode'] === '01'){
                $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$goodsResult['Data']));
            }else{
                $this->error('商品信息查询失败');
            }
        }
    }

    //获取首页banner
    public function getBanner(){
        $cache = S('banner');
        if($cache){
            return $cache;
        }
        $result = curl_post(C('API_URL').'WebBanner/Find');
        $retData = json_decode($result,true);
        if($retData['ResCode'] === '01'){
            S('banner',$retData['Data'],3600);
            return $retData['Data'];
        }else{
            return false;
        }
    }

    //获取网站基本信息
    public function getSiteInfo(){
        $cache = S('siteInfo');
        if($cache){
            return $cache;
        }
        $result = curl_post(C('API_URL').'WebBaseConfigure/GetBaseConfigure');
        $retData = json_decode($result,true);
        if($retData['ResCode'] === '01'){
            S('siteInfo',$retData['Data'],3600);
            return $retData['Data'];
        }
    }

    //获取理赔说明
    public function getLipei(){
        $cache = S('lipei');

        if($cache){
            return $cache;
        }

        $result = curl_post(C('API_URL').'WebLipei/GetLipeiDesc');
        $retData = json_decode($result,true);
        if($retData['ResCode'] === '01'){
            S('lipei',$retData['Data'],3600);
            return $retData['Data'];
        }
    }

    //获取分类说明
    public function getFenlei(){
        $cache = S('fenlei');
        if($cache){
            return $cache;
        }
        $result = curl_post(C('API_URL').'WebLipei/GetFenleiDesc');
        $retData = json_decode($result,true);
        if($retData['ResCode'] === '01'){
            S('fenlei',$retData['Data'],3600);
            return $retData['Data'];
        }
    }

}