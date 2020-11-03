<?php
namespace Home\Controller;
use Think\Controller;
use Think\Log;

class UploadController extends Controller {
    private $isLogin = false;
    private $userInfo;
    public function __construct(){
        parent::__construct();
        $user = session('stgjsd_user');
        if($user){
            $this->isLogin = true;
            $this->userInfo = $user;
        }
    }


    public function commonUpload(){
        $upload = new \Think\Upload();
        $upload->maxSize   =     5242880;
        $upload->exts      =     array('jpg', 'bmp', 'png', 'jpeg');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();

        $info = $upload->upload();

        if(!$info) {
            $this->responseJsonStr(0,$upload->getError());
        }else{
            $path =  C('UPLOAD_PATH').$info['file']['savepath'].$info['file']['savename'];
            $index = $_GET['index'];
            switch ($index){
                case 1:
                    $this->uploadHead($path);
                    break;
                default:
            }
        }

    }
    public function uploadImg(){
        $upload = new \Think\Upload();
        $upload->maxSize   =     5242880;
        $upload->exts      =     array('jpg', 'png', 'jpeg');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();

        $info = $upload->upload();

        if(!$info) {
            $this->responseJsonStr(0,$upload->getError());
        }else{
            $path =  C('UPLOAD_PATH').$info['file']['savepath'].$info['file']['savename'];
            $this->responseJsonStr(1,'成功',$path);
        }
    }
    public function uploadHead($path){
        $header = array(
            'Content-Type'=> 'multipart/form-data;charset=UTF-8'
        );

        $result = curl_post(C('API_URL').'Customer/ModifyHeadUrl','',$this->userInfo['CusCode'],$header,array('head'=>new \CURLFile(realpath($path))));
        $row = json_decode($result,true);
        if($row['ResCode'] === '01'){
            $this->userInfo['CusHeadUrl'] = $row['Data'];
            session('stgjsd_user',$this->userInfo);
            $this->responseJsonStr(1,'头像修改成功!',$row['Data']);
        }else{
            $this->responseJsonStr(0,$row['ResMessage']);
        }
    }

     public function uploadCardFace(){
        $upload = new \Think\Upload();
        $upload->maxSize   =     5242880;
        $upload->exts      =     array('jpg', 'png', 'jpeg');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();

        $info = $upload->upload();

        if(!$info) {
            $this->responseJsonStr(0,$upload->getError());
        }else{
            $path =  C('UPLOAD_PATH').$info['file']['savepath'].$info['file']['savename'];
            $header = array(
                'Content-Type'=> 'multipart/form-data;charset=UTF-8'
            );

            $result = curl_post(C('API_URL').'OrderFront/ImageFaceOcr','','',$header,array('face'=>new \CURLFile(realpath($path))));
            $row = json_decode($result,true);
            if($row['ResCode'] === '01'){
                $this->responseJsonStr(1,$row['ResMessage'],$row['Data']);
            }else{
                $this->responseJsonStr(0,$row['ResMessage']);
            }
        }
    }

    public function uploadCardBack(){
        $upload = new \Think\Upload();
        $upload->maxSize   =     5242880;
        $upload->exts      =     array('jpg', 'png', 'jpeg');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();

        $info = $upload->upload();

        if(!$info) {
            $this->responseJsonStr(0,$upload->getError());
        }else{
            $path =  C('UPLOAD_PATH').$info['file']['savepath'].$info['file']['savename'];
            $header = array(
                'Content-Type'=> 'multipart/form-data;charset=UTF-8'
            );

            $result = curl_post(C('API_URL').'OrderFront/ImageBackOcr','','',$header,array('back'=>new \CURLFile(realpath($path))));
            $row = json_decode($result,true);
            if($row['ResCode'] === '01'){
                $this->responseJsonStr(1,$row['ResMessage'],$row['Data']);
            }else{
                $this->responseJsonStr(0,$row['ResMessage']);
            }
        }
    }



    private function responseJsonStr($status,$info,$data = []){
        $ret = array('status'=>$status,'info'=>$info);
        if($data){
            $ret['data'] = $data;
        }
        header('Content-Type:text/html; charset=utf-8');
        exit(json_encode($ret));
    }

}