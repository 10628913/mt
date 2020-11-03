<?php
namespace Admin\Controller;
use Think\Log;
class ContentController extends BaseController {
    public function __construct(){
        parent::__construct();
    }

    //新闻列表
    public function news(){
		$pageSize = I('pageSize') ? I('pageSize') : 20;
        $pageIndex = I('p') ? I('p') : 1;
        $params = array(
            'PageIndex' => $pageIndex - 1,
            'PageSize' => $pageSize
        );
        $result = curl_post(C('API_URL').'NewsFront/FindPage',json_encode($params));
        $rows = json_decode($result,true);
        if($rows['ResCode'] === '01'){
        	$data = $rows['Data'];
        	$totalCount = $data['TotalRecords'];
        	$pageIndex = $data['PageIndex'];
        	$Page = new \Think\Page($totalCount,$pageSize);

            $this->assign('list',$data['DataBody']);
            $this->assign('page',$Page->show());
        }
        $this->display('news_index');
    }
    //添加新闻
    public function newsAdd(){
    	if(IS_POST){
    		$params = I('info');
	        $result = curl_post(C('API_URL').'NewsFront/Insert',json_encode($params));

	        $rows = json_decode($result,true);
	        if($rows['ResCode'] === '01'){
	        	$this->success('操作成功');
	        }else{
	        	$this->error($ret['ResMessage']);
	        }
    	}else{
    		$editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
    		$this->display('news_add');
    	}
    }
    //新闻修改
    public function newsEdit(){
		if(IS_POST){
			$params = I('info');
			$params['NewId'] = intval($params['NewId']);
			$params['NewIsScroll'] = intval($params['NewIsScroll']);
	        $result = curl_post(C('API_URL').'NewsFront/Update',json_encode($params));

	        $ret = json_decode($result,true);
	        if($ret && $ret['ResCode'] === '01'){
	            $this->success('操作成功');
	        }else{
	            $this->error($ret['ResMessage']);
	        }
    	}else{
    		$nid = I('NewId');
	        if(!trim($nid)){
	            $this->error('数据错误!');
	        }
	        $params = array(
	            'NewId' => $nid
	        );
	        $result = curl_post(C('API_URL').'NewsFront/GetById',json_encode($params));
	        $ret = json_decode($result,true);
	        if($ret && $ret['ResCode'] === '01'){
	            $this->assign($ret['Data']);
	        }else{
	            $this->assign('msg',$ret['ResMessage']);
	        }
    		$editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
    		$this->display('news_edit');
    	}
    }

    //新闻删除
    public function newsDelete(){
        if(IS_POST){
            $newsId = I('NewId');
            $params = array(
            	'NewId' => $newsId
            );
            $result = curl_post(C('API_URL').'NewsFront/Delete',json_encode($params));
	        $rows = json_decode($result,true);
	        if($rows['ResCode'] === '01'){
	        	$this->success("操作成功");
	        }else{
	        	$this->error($ret['ResMessage']);
	        }
        }
    }
    //分类说明
    public function fenlei(){
    	if(IS_POST){
    		$params = I('info');
	        $result = curl_post(C('API_URL').'WebLipei/SaveFenleiDesc',json_encode($params));

	        $rows = json_decode($result,true);
	        if($rows['ResCode'] === '01'){
	        	$this->success('操作成功');
	        }else{
	        	$this->error($ret['ResMessage']);
	        }
        }else{
        	$result = curl_post(C('API_URL').'WebLipei/GetFenleiDesc');
	        $ret = json_decode($result,true);
	        if($ret && $ret['ResCode'] === '01'){
	            $this->assign('LipFenleiDesc',$ret['Data']);
	        }
    		$editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
        	$this->display();
        }
    }

    //理赔
    public function lipei(){
    	if(IS_POST){
    		$params = I('info');
	        $result = curl_post(C('API_URL').'WebLipei/SaveLipeiDesc',json_encode($params));

	        $rows = json_decode($result,true);
	        if($rows['ResCode'] === '01'){
	        	$this->success('操作成功');
	        }else{
	        	$this->error($ret['ResMessage']);
	        }
        }else{

        	$result = curl_post(C('API_URL').'WebLipei/GetLipeiDesc');
	        $ret = json_decode($result,true);
	        if($ret && $ret['ResCode'] === '01'){
	            $this->assign('LipLiPeiDesc',$ret['Data']);
	        }
    		$editor = new \Org\Editor\Editor;
            $this->assign('editor',$editor);
        	$this->display();
        }
    }

}