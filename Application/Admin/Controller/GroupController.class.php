<?php
namespace Admin\Controller;
class GroupController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    /**
    * 管理组列表
    */
    public function index(){
        $count = M("Auth_group")->count();
        $Page = new \Think\Page($count,20);
        $list = M("Auth_group")->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id desc')->select();
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        adminLog("查看管理组列表");
        $this->display("group_list");
    }
    /**
    * 管理组增加
    */
    public function groupAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            if(!$data['title']){
                $this->error("请输入管理组名称");
            }else{
                $data['type'] = 1;
                $result = M("Auth_group")->data($data)->add();
                if($result){
                    adminLog("增加管理员：".$data["title"]);
                    $this->success("操作成功");
                }else{
                    $this->error("操作失败");
                }
            }
        }else{
            layout(false);
            $this->display("group_add");
        }
    }

    /**
    * 管理组编辑
    */
    public function groupEdit(){
        if(IS_POST){
            $id = $where['id'] = I('id');
            $data = $_POST['info'];
            $result = M("Auth_group")->where($where)->save($data);
            if($result){
                adminLog("修改管理组".$data["title"]);
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $id = $where['id'] = I('id');
            layout(false);
            $groupInfo = M("Auth_group")->where($where)->find();
            $this->assign($groupInfo);
            $this->display("group_edit");
        }

    }
    /**
    *管理组删除
    */
    public function groupDelete(){
        if(IS_POST){
            $id = intval($_POST['id']);
            if($id == '1') $this->error("当前管理组不允许删除");
            $whereData['id'] = $id;
            // 判断当前管理组是否有会员
            $whereGroupAccess['group_id'] = $id;
            $groupAccessData = $this->groupAccessDb->where($whereGroupAccess)->find();
            if($groupAccessData){
                $this->error('当前管理组存在管理员');
            }
            M("Auth_group")->data($whereData)->delete();
            adminLog("删除管理组，ID为".$id);
            $this->success('删除成功');
        }
    }
    // 管理组设置规则
    public function groupSettingRule(){
        if(IS_POST){
            $whereGroup['id'] = $id = I("groupId");
            $ids = I('ids');
            // var_dump($whereGroup);
            // var_dump($ids);die;
            $result = M("Auth_group")->where($whereGroup)->setField("rules",$ids);
            if($result){
                adminLog("管理组规则设置");
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $whereGroup['id'] = $id = I("groupId");
            $groupData = M("Auth_group")->where($whereGroup)->find();
            $groupData['rules']  =  explode(',',$groupData['rules']);
            // var_dump($groupData);die;
            $this->assign($groupData);
            // 获取
            $tree = new \Org\Tree\Tree;
            $where["is_delete"] = 0;
            $where["status"] = 1;
            $data =M('Auth_rule')->where($where)->order('sort desc,id desc')->select();
            $ruleList = $tree->makeTree($data);
            $this->assign("ruleList",$ruleList);
            // layout(false);
            $this->display("group_rule_list");
        }
    }
    // 管理组设置菜单
    public function groupSettingMenu(){
        if(IS_POST){
            $whereGroup['id'] = $id = I("groupId");
            $ids = I('ids');
            if($ids){
                M("Auth_group")->where($whereGroup)->setField("menu_ids",$ids);
                adminLog("管理组菜单设置");
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $whereGroup['id'] = $id = I("groupId");
            $groupData = M("Auth_group")->where($whereGroup)->find();
            $groupData['menu_ids']  =  explode(',',$groupData['menu_ids']);
            // dump($groupData);
            $this->assign($groupData);
            // 获取
            $tree = new \Org\Tree\Tree;
            $where["is_delete"] = 0;
            $where["is_show"] = 1;
            $data = M('Admin_menu')->where($where)->order('sort desc,id desc')->select();
            $menuList = $tree->makeTree($data);
            $this->assign("menuList",$menuList);
            $this->display("group_menu_list");
        }
    }

}