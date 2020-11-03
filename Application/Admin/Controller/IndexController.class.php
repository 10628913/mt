<?php
namespace Admin\Controller;
//use Think\Controller;
class IndexController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    //后台入口
    public function index(){
        layout(false);
        // $adminMenus = parent::getMenus();
        // $this->assign('admin_menus',$adminMenus);
        $this->display('index');
    }
    // 后台首页
    public function main(){
        $this->display('main');
    }

    //登录
    public function login(){
        if(IS_POST){
            $username = $_POST['form-username'];
            $password = $_POST['form-password'];
            if(!$username || !$password){
                $this->error("请输入完整信息");
            }
            $path = 'Application/'.MODULE_NAME .'/Conf/admin.php';
            $file = include $path;
            if($username != $file['user_name'] || $password != $file['password']){
                $this->error("用户名或密码错误!");
            }else{
                session('admin_uid',1);
                session('admin_username',$username);
                $this->success('登录成功',''.__MODULE__.'');
            }
        }else{
            layout(false);
            $this->display('login');
        }
    }
    //退出
    public function logout(){
        cookie('admin_uid',null);
        session('admin_uid',null);
        session('admin_username',null);
        session('admin_realname',null);
        session('ADMIN_MENU_LIST',null);
        layout(false);
        // $this->success('退出成功');
        $this->redirect('Index/login');
    }
    public function verify(){
        $config = array(
            'fontSize'=>30,    // 验证码字体大小
            'length'=>4,     // 验证码位数
            'useNoise'=>false, // 关闭验证码杂点
        );
        ob_clean();
        $verify = new \Think\Verify($config);
        $verify->codeSet = '0123456789';
        $verify->entry(1);
    }
    public function getCurrentPos(){
        $menuId = I('menuId');
        echo self::currentPos($menuId);
        exit;
    }

}