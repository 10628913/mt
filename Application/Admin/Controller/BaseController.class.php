<?php
//定义后台,判断管理员
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;
class BaseController extends Controller {

	public function __construct(){
		parent::__construct();
	}
	public function _initialize() {
		self::check_admin();
	}
//	final public function checkRule(){
//		$auth = new Auth();
//		$methodName = CONTROLLER_NAME.'/'.ACTION_NAME;
//		$authRuleDb = M('Auth_rule');
//		$where['name'] = $methodName;
//		if($authRuleDb->where($where)->find()){
//			if(!$auth->check($methodName,session('admin_uid')) && session('admin_uid')!= 1 && ACTION_NAME != 'login' && ACTION_NAME != 'logout' && ACTION_NAME != 'verify'&& ACTION_NAME != 'index'){
//				$this->display('ErrorPage/error404');exit;
//			}
//		}
//	}
	//验证登录
    final public function check_admin() {
    	if(MODULE_NAME == 'Admin' && CONTROLLER_NAME == 'Index' && in_array(ACTION_NAME, array('login','verify','logout'))) {
			return true;
		} else {
			//验证管理员
			$admin_uid = session('admin_uid');
			if(!$admin_uid){
                $this->redirect('Index/login');
                return;
			}
            // $this->getMenus();
//			$this->checkRule();
		}
    }
    final public function getMenus(){
        $admin_uid = session('admin_uid');

        if($admin_uid){

            $cacheKey = 'admin_menu_'.$admin_uid;
            $menuList = S($cacheKey);
            if($menuList){
                return $menuList;
            }else{

                $menuDb = M('Admin_menu');
                $groupDb = M('Auth_group');
                $groupAccessDb = M('Auth_group_access');

                $whereAdmin['uid'] = $admin_uid;
                $group_id = $groupAccessDb->where($whereAdmin)->getField("group_id");
                $whereGroup['id'] = $group_id;

                $menu_ids = $groupDb->where($whereGroup)->getField("menu_ids");
                $tree = new \Org\Tree\Tree;
                $whereMenu['display'] = 1;
                if($admin_uid != 1){
                    $whereMenu['id'] = array('IN',$menu_ids);
                }
                $data = $menuDb->where($whereMenu)->order('sort asc,id asc')->select();
                $menuList = $tree->makeTree($data);
                S($cacheKey,$menuList);
                return $menuList;
            }
        }
    }
    final public function currentPos($id){
    	$menudb = M('Admin_menu');
    	$where['id'] = $id;
		$r =$menudb->where($where)->find();
		$str = '';
		if($r['parent_id']) {
			$str = self::currentPos($r['parent_id']);
		}
		return $str.L($r['name']).' > ';
    }
}