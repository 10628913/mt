<?php
namespace Admin\Controller;
class SysController extends BaseController{
    public function loginList(){
        $count = M('Log')->count();
        $Page = new \Think\Page($count,20);

        $list = M('Log')->limit($Page->firstRow,$Page->listRows)->order('login_time desc')->select();

        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display('login_list');
    }

    public function changePassword(){
        if(IS_POST){
            $info = I('info');
            $oldPassword = $info['old_password'];
            $newPassword = $info['new_password'];
            $rePassword = $info['re_password'];
            if(empty($oldPassword)){
                $this->error('旧密码不能为空');
            }
            if(empty($newPassword)){
                $this->error('新密码不能为空');
            }
            if(empty($rePassword) || $newPassword != $rePassword){
                $this->error('两次输入的密码不一致');
            }
            $path = 'Application/'.MODULE_NAME .'/Conf/admin.php';
            $file = include $path;
            if($oldPassword != $file['password']){
                $this->error("旧密码错误!");
            }else{
                 $res = array_merge($file, array('password'=>$newPassword));

                   $str = '<?php return array(';

                   foreach ($res as $key => $value){
                       // '\'' 单引号转义
                       $str .= '\''.$key.'\''.'=>'.'\''.$value.'\''.',';
                   };
                   $str .= '); ?>';

                   //写入文件中,更新配置文件
                   if(file_put_contents($path, $str)){
                       $this->success('修改成功');
                   }else {
                       $this->error('修改失败');
                   }
            }

        }else{
            $this->assign('username',session('admin_username'));
            $this->display('change_password');
        }
    }
    public function ipConfig(){
        if(IS_POST){
            $info = I('info');
            if(!explode(',', $info['ips'])){
                $this->error('多个ip请用英文,分隔');
            }

            if(M('Ip')->save(I('info'))){
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
        }else{
            $info = M('Ip')->find();
            $this->assign($info);
            $this->display('ip_config');
        }
    }
}