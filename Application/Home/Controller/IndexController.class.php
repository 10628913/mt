<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;

class IndexController extends Controller {

    private $isLogin = false;
    private $userInfo;
    public function __construct()
    {
        parent::__construct();
    }

    //首页
    public function index(){

        $commonController = new CommonController();
        $this->assign('banner',$commonController->getBanner());

        $newsResult = curl_post(C('API_URL').'News/FindScroll');

        $news = json_decode($newsResult,true);
        if($news['ResCode'] === '01'){
            $this->assign('news',$news['Data']);
        }

        $params = array(
            'PageIndex' => 0,
            'PageSize' => 7
        );
        $result = curl_post(C('API_URL').'NewsFront/FindPage',json_encode($params));
        $rows = json_decode($result,true);
        if($rows['ResCode'] === '01'){
            $this->assign('data',$rows['Data']);
        }
        $this->display();
    }

    //最新资讯
    public function news(){
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $pageIndex = I('pageIndex') ? I('pageIndex') : 0;
        $params = array(
            'PageIndex' => $pageIndex,
            'PageSize' => $pageSize
        );
        $result = curl_post(C('API_URL').'NewsFront/FindPage',json_encode($params));
        $rows = json_decode($result,true);
        if($rows['ResCode'] === '01'){
            $this->assign('data',$rows['Data']);
            $this->assign('pageSize',$pageSize);
            $this->assign('pageIndex',$pageIndex);
            $this->assign('pageIndex',$pageIndex+1);
        }
        $this->display();
    }

    //身份证上传
    public function upload(){
        if(IS_POST){
            $type = I('type');
            $SorWaybillNo = I('SorWaybillNo');
            $SorReceiverMobile = I('SorReceiverMobile');
            if($type != 1 && $type != 2){
                $this->error('提交身份信息错误!');
            }
            $idcard1 = I('idcard1');
            $idcard2 = I('idcard2');
            if(!$idcard1){
                $this->error('请上传身份证正面照片!');
            }
            if(!$idcard2){
                $this->error('请上传身份证背面照片!');
            }
            $params = [
                'SorReceiverName' => I('SorReceiverName'),
                'SorReceiverCardNo' => I('SorReceiverCardNo'),
                'SorCardPositiveUrl' => $idcard1,
                'SorCardNegativeUrl' => $idcard2
            ];

            $method = 'UploadIdByWaybillNo';
            if($type == 1){
                if(!$SorWaybillNo){
                    $this->error('请输入运单号!');
                }
                $params['SorWaybillNo'] = $SorWaybillNo;
            }else{
                if(!$SorReceiverMobile){
                    $this->error('请输入手机号码!');
                }
                $method = 'UploadIdByMobile';
                $params['SorReceiverMobile'] = $SorReceiverMobile;
            }

            $result = curl_post(C('API_URL').'OrderFront/'.$method,json_encode($params));
            $ret = json_decode($result,true);
            if($ret && $ret['ResCode'] === '01'){
                $this->success('身份信息上传成功!');
            }else{
                $this->error($ret['ResMessage']);
            }
        }else{
            $this->display('upload_id');
        }
    }



    //运单查询
    public function courier(){
        if(IS_POST){
            $sn = trim(I('sn'));
            if(!$sn){
                $this->error('运单号错误');
            }
            $params = array(
                'WaybillNoList' => $sn
            );
            $result = curl_post(C('API_URL').'OrderFront/MoreTrack1',json_encode($params));
            $row = json_decode($result,true);
            if($row['ResCode'] === '01'){
                $this->ajaxReturn(['status'=>1,'info'=>$row['ResMessage'],'data'=>$row['Data']]);
            }else{
                $this->ajaxReturn(['status'=>0,'info'=>$row['ResMessage'],'data'=>$row['Data']]);
            }
        }else{
            $sn = I('sn');
            if(trim($sn)){
                $params = array(
                    'SorWaybillNo' => $sn
                );
                $result = curl_post(C('API_URL').'OrderFront/Track1',json_encode($params));
                $ret = json_decode($result,true);
                if($ret && $ret['ResCode'] === '01'){
                    $this->assign('data',array_reverse($ret['Data']));
                }else{
                    $this->assign('msg','未找到 '.$sn.' 相关信息!');
                }
                $this->assign('sn',$sn);
            }
            $this->display();
        }
    }
    //运单查询页
    public function courierSearch(){


    }


    //新闻详情
    public function newsDetail(){
        $nid = I('nid');
        if(!trim($nid)){
            $this->error('数据错误!');
        }
        $params = array(
            'NewId' => $nid
        );
        $result = curl_post(C('API_URL').'NewsFront/GetById',json_encode($params));
        $ret = json_decode($result,true);
        if($ret && $ret['ResCode'] === '01'){
            $ret['data']['NewContent'] = htmlspecialchars_decode($ret['data']['NewContent']);
            $this->assign('data',$ret['Data']);
        }else{
            $this->assign('msg',$ret['ResMessage']);
        }
        $this->display('news_detail');
    }


    //理赔
    public function claim_provision(){
        $commonController = new CommonController();

        $this->assign('content',htmlspecialchars_decode($commonController->getLipei()));
        $this->display();
    }

    //分类说明
    public function shipping_instructions(){
        $commonController = new CommonController();

        $this->assign('content',htmlspecialchars_decode($commonController->getFenlei()));
        $this->display();
    }
}