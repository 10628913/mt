<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;

class ExportController extends Controller {

    private $isLogin = false;
    private $userInfo;
    public function __construct()
    {
        parent::__construct();

        $user = session('stgjsd_user');
        if($user){
            $this->isLogin = true;
            $this->userInfo = $user;
            $this->assign('userInfo',$user);
        }
        $this->assign('isLogin',$this->isLogin);
    }

    //条件导出数据
    public function exportOrderByCondition(){
        if(!IS_POST){
            return;
        }
        //订单状态
        $state = $_POST['state'];
        $this->assign('state',$state);

        //创建时间
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        //单号
        $orderSn = $_POST['order_sn'];
        //收件人
        $name = $_POST['name'];
        $mobile = $_POST['mobile'];
        $fName = $_POST['f_name'];
        $goods = $_POST['goods'];

        $params = array(
            'SorStatusStr' => $state ? $state : '',
            'SorCreateTime' => $startDate ? $startDate : '',
            'SorCreateEndTime' => $endDate ? $endDate : '',
            'MultipleWaybillNo' => $orderSn ? $orderSn : '',
            'SorReceiverName' => $name ? $name : '',
            'SorReceiverMobile' => $mobile ? $mobile : '',
            'SorShipperName' => $fName ? $fName : '',
            'GoodsName' => $goods ? $goods : '',
        );

        $orderResult = curl_post(C('API_URL').'OrderForBack/QueryExportExcel',json_encode($params),$this->userInfo['CusCode']);
        $orderResult = json_decode($orderResult,true);

        if($orderResult['ResCode'] === '01'){
            $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$orderResult['Data']));
        }else{
            $this->error($orderResult['ResMessage']);
        }
    }

    //条件导出面单
    public function exportPdfByCondition(){
        if(!IS_POST){
            return;
        }
        //订单状态
        $state = $_POST['state'];
        $this->assign('state',$state);

        //创建时间
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        //单号
        $orderSn = $_POST['order_sn'];
        //收件人
        $name = $_POST['name'];
        $mobile = $_POST['mobile'];
        $fName = $_POST['f_name'];
        $goods = $_POST['goods'];

        $params = array(
            'SorStatusStr' => $state ? $state : '',
            'SorCreateTime' => $startDate ? $startDate : '',
            'SorCreateEndTime' => $endDate ? $endDate : '',
            'MultipleWaybillNo' => $orderSn ? $orderSn : '',
            'SorReceiverName' => $name ? $name : '',
            'SorReceiverMobile' => $mobile ? $mobile : '',
            'SorShipperName' => $fName ? $fName : '',
            'GoodsName' => $goods ? $goods : '',
        );

        $orderResult = curl_post(C('API_URL').'OrderForBack/QueryPdf',json_encode($params),$this->userInfo['CusCode']);
        $orderResult = json_decode($orderResult,true);

        if($orderResult['ResCode'] === '01'){
            $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$orderResult['Data']));
        }else{
            $this->error($orderResult['ResMessage']);
        }
    }

    //勾选导出数据
    public function exportOrderByIds(){
        if(!IS_POST){
            return;
        }
        $params = array(
            'WaybillNoList' => I('ids'),
        );

        $orderResult = curl_post(C('API_URL').'OrderForBack/CheckExportExcel',json_encode($params),$this->userInfo['CusCode']);
        $orderResult = json_decode($orderResult,true);

        if($orderResult['ResCode'] === '01'){
            $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$orderResult['Data']));
        }else{
            $this->error($orderResult['ResMessage']);
        }
    }

    //勾选导出面单
    public function exportPdfByIds(){
        if(!IS_POST){
            return;
        }
        $params = array(
            'WaybillNoList' => I('ids'),
        );

        $orderResult = curl_post(C('API_URL').'OrderForBack/Pdf',json_encode($params),$this->userInfo['CusCode']);
        $orderResult = json_decode($orderResult,true);

        if($orderResult['ResCode'] === '01'){
            $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','data'=>$orderResult['Data']));
        }else{
            $this->error($orderResult['ResMessage']);
        }
    }

}