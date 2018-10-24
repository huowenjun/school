<?php
/*
*在线提问
*by ruiping date 20161117
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class QuestionController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Question/query'.date('Ymd').'097eea0ec4ee11e688eb00163e006cec');
    	//校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
//            $this->error('接口访问不合法');
        }
        //查询提问列表
        $user_id = $this->getUserId();
        $user_type = $this->getUserType();
        $Online = D('OnlineQu');
        $pagesize = $this->PAGESIZE;
        $p = I('post.page',1);
        $orderby = "qst.id desc";
        $sdate = I('post.sdate');
        $edate = I('post.edate');

        if ($user_type==3) {
            //老师
            if (!empty(I('post.c_id'))) {
                $where['qst.c_id'] = I('post.c_id');
            }
            //接收者id=当前老师id
            $where['qst.form_user'] = $user_id;
        }elseif ($user_type==4) {
            //家长
            $g_id = I('post.g_id');
            $c_id = I('post.c_id');

            if (empty($g_id)||empty($c_id)) {
                $this->error('参数错误');
            }
            $where['qst.g_id'] = $g_id;
            $where['qst.c_id'] = $c_id;
            //发送者id=当前用户id
            $where['qst.send_user'] = $user_id;

        }else{
            $this->error('用户类型不正确');
        }

        if (!empty($sdate) && !empty($edate)) {
            $where['qst.send_date'] = array('between',$sdate.",".$edate);
        }

        $result = $Online->queryListEXForApp($user_type,$where,$orderby,$p,$pagesize);

        $onlineList = $result['list'];
        foreach ($onlineList as $key => $value) {
            $onlineList[$key]['content'] = strip_tags(htmlspecialchars_decode(str_replace('&amp;nbsp;',' ',$value['content'])));
        }
        $onlineCount = $result['count'];
        $arr = array();
        $page_szie = ceil($onlineCount/$pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $onlineCount;
        $arr['page'] = $p;
        $arr['content'] = $onlineList;
        $this->success($arr);
    }

    /*
    *信息查看
    */
    public function view(){
        $id = I('post.id/d','79');
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Question/view'.date('Ymd').'097eea0ec4ee11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法');
        }
        if (empty($id)) {
            $this->error('参数不正确！');
        }
        $orderby = "";//id ase
        $where['p_id'] = $id;
        $where['id'] = $id;
        $where['_logic'] = 'or';

        //var_dump($where);die();

        $Online = D('OnlineQu');
        $qaInfo = $Online->queryDetialByQid($where);
        foreach ($qaInfo as $key => $value) {
            $qaInfo[$key]['content'] = strip_tags(htmlspecialchars_decode(str_replace('&amp;nbsp;',' ',$value['content'])));
        }

        $this->success($qaInfo);
    }
    /*
    *删除
    */
    public function delete(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Question/delete'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法');
        }
        $id = I('post.id','');
        if (empty($id)) {
            $this->error('参数不正确！');
        }
        $Online = D('OnlineQu');
        $where['id'] = $id;
        //检查权限
        $auth = $Online->where($where)->find();
        if (!empty($auth) && $auth['send_user']!=$this->getUserId()) {
            $this->error('没有权限删除');
        }
        $ret = $Online->where($where)->delete();
        if ($ret!=1) {
            $content = "删除失败";
            $zt = 2;
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,$zt);
            $this->error('删除失败');
        }else{
            $content = "删除成功";
            $zt = 1;
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,$zt);
            $this->success('删除成功');
        }
        
    }

    /*
    *回复
    */
    public function add(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Question/add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法');
        }
        $Online = D('OnlineQu');
        $id = I('post.id/d',0);
        if (empty($id)) {
            $this->error('参数不正确！');
        }
        $where['id'] = $id;
        $onlineInfo = $Online->where($where)->find();
        $data['p_id'] = $id;
        //$data['title'] = $onlineInfo['title'];
        $data['content'] = I('post.content');
        $data['send_user'] = $this->getUserId();
        $data['form_user'] = $onlineInfo['send_user'];
        $data['send_date'] = date('Y-m-d');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['a_id'] = $onlineInfo['a_id'];
        $data['s_id'] = $onlineInfo['s_id'];
        $data['c_id'] = $onlineInfo['c_id'];
        $data['g_id'] = $onlineInfo['g_id'];
        if(!$Online->create($data)){
            $this->error($Online->getError());
        }else{
            $ret = $Online->add($data);
            $content = "回复成功,ID:".$ret;
            if($ret === false){
                D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,2);
                $this->error($Online->getError());
            }else{
                D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,1);
                $this->success('回复成功');
            }
        }
    }

    //创建问题 家长端功能
    public function new_add(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Question/new_add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法');
        }
        $Online = D('OnlineQu');
        $data['send_user'] = $this->getUserId();
        $data['title'] = I('post.title');
        $data['content'] = I('post.content');
        $data['form_user'] = I('post.form_user');
        if (empty(I('post.form_user'))) {
            $this->error('请选择接收人！');
        }
        if (empty(I('post.title'))) {
            $this->error('请输入标题！');
        }
        if (empty(I('post.content'))) {
            $this->error('请输入内容！');
        }
        $data['send_date'] = date('Y-m-d');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['p_id'] = I('post.p_id',0);
        $data['a_id'] = I('post.a_id',0);
        $data['s_id'] = I('post.s_id',0);
        $data['c_id'] = I('post.c_id',0);
        $data['g_id'] = I('post.g_id',0);
        $ret = $Online->add($data);
        $content = "提问问题成功,ID:".$ret;
        if($ret === false){
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,2);
            $this->error($Online->getError());
        }else{
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getUserType(),$this->getUserType(),1,1);
            $this->success('提问问题成功');
        }

    }


}