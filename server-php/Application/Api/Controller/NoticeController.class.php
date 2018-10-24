<?php
/*
*活动公告
*by ruiping date 20161117
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class NoticeController extends BaseController
{
    /*
    *数据查询
    *@param
    */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        //查询公告
        $user_id = $this->getUserId();
        $messSub = D('MymessageSub');
        $Mess = D('Mymessage');
        $pagesize = $this->PAGESIZE;
        $p = I('post.page', 1);
        $orderby = " m_id desc ";
        $sdate = I('post.sdate');
        $class = I('post.c_id');
        $edate = I('post.edate');
        $type = I('post.type'); //1接收的公告  2是发送的公告
        if (!empty($sdate) && !empty($edate)) {
            $where['create_time'] = array('between', array($sdate . ' 00:00:00', $edate . ' 23:59:59'));
        }
        if (!empty($class)) {
            $where['c_id'] = array("like", "%" . $class . "%");
        }

        $where2['user_id'] = array('EQ', $user_id);
        //获取有关登录用户接收的所有信息列表
        $messSubId = $messSub->where($where2)->getField("m_id", true);
        if (!$messSubId) {
            $messSubId = ' ';
        }

        if (!empty($type)) {
            if ($type == 1) {//接收的
                $where['m_id'] = array('in', $messSubId);
            } else {//发送的
                $where['user_id'] = $user_id;
            }
        } else {
            //接收的m_id
            $where1['m_id'] = array('in', $messSubId);
            //发送的user_id
            $where1['user_id'] = $user_id;

            $where1['_logic'] = 'or';
            $where['_complex'] = $where1;
        }

        $where['m_type'] = array('EQ', 2);
        $where['del_state'] = array('EQ', 0);
//var_dump($where);die;
        $result = $Mess->queryListForApp('a.*', $where, $orderby, $p, $pagesize);


        foreach ($result['list'] as $key => $value) {
            if ($type == 2) {//发出
                $zt = 1;//发送的
            } elseif ($type == 1) {//接收
                $zt = 2;//接收
            } else {//获取全部消息(发出+接收)
                if ($value['user_id'] == $user_id) {
                    $zt = 1;//发送的
                } else {
                    $zt = 2;//接收
                }
            }
            $result['list'][$key]['fs_type'] = $zt;
            //过滤模糊查询不存在的结果集
            if ($class) {
                //过滤模糊查询不正确的结果集
                $cIdArr = explode(',', $value['c_id']);
                if (!in_array($class, $cIdArr)) {
                    unset($result['list'][$key]);
                }
            }

        }
        rsort($result['list']);
        $messList = $result['list'];


        foreach ($messList as $key => $value) {
            $messList[$key]['m_type'] = isset($mtypeInfo[$value['m_type']]) ? $mtypeInfo[$value['m_type']] : $value['m_type'];
            $messList[$key]['user_name'] = isset($userInfo[$value['user_id']]) ? $userInfo[$value['user_id']] : $value['user_id'];
            $newStr = array();

            if (!empty($class)) {
                //含有单个班级
                $info = getClassInfo($class);
                $newStr[0]['c_id'] = $class;
                $newStr[0]['c_name'] = $info['c_name'];
                //获取年级gid
                $newStr[0]['g_id'] = $info['g_id'];
                //获取g_name
                $newStr[0]['g_name'] = $info['g_name'];
            } else {
                $cidTmpStr = $value['c_id'];
                $cidStr = explode(',', trim($cidTmpStr, ','));

                foreach ($cidStr as $k => $v) {
                    $info = getClassInfo($v);
                    $newStr[$k]['c_id'] = $v;
                    $newStr[$k]['c_name'] = $info['c_name'];
                    //获取年级gid
                    $newStr[$k]['g_id'] = $info['g_id'];
                    //获取g_name
                    $newStr[$k]['g_name'] = $info['g_name'];
                }
            }
            $messList[$key]['classInfo'] = $newStr;
        }
        $messCount = count($messList);
        $arr = array();
        $page_szie = ceil($messCount / $pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $messCount;
        $arr['page'] = $p;
        $arr['content'] = $messList;
        $this->success($arr);
    }

    /*
    *删除
    */
    public function delete()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Notice/delete'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $m_id = I('post.m_id', '');
        if (empty($m_id)) {
            $this->error('参数不正确！');
        }
        $Mess = D('Mymessage');
        $where['m_id'] = $m_id;
        //检查权限
        $auth = $Mess->where($where)->find();
        if (!empty($auth) && $auth['user_id'] != $this->getUserId()) {
            $this->error('没有权限删除');
        }
        $data['del_state'] = 1;
        $ret = $Mess->where($where)->save($data);
        if ($ret === false) {
            $content = "删除失败";
            $zt = 2;
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, $zt);
            $this->error('删除失败');
        } else {
            $content = "删除成功";
            $zt = 1;
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, $zt);
            $this->success('删除成功');
        }

    }

    /*
    *公告添加
    */
    public function add()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $Mess = D('Mymessage');
        $messSub = D('MymessageSub');
        $data['m_id'] = I('post.m_id/d', 0);
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['m_type'] = 2;//2公告
        $data['title'] = I('post.title');
        $data['content'] = I('post.content');
        $data['m_tzfs'] = '1,2';
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->getUserId();
        if (empty($data['c_id'])) {
            $this->error('没有选择班级！');
        }

        $data['g_id'] = ',' . I('post.g_id') . ',';
        $data['c_id'] = ',' . I('post.c_id') . ',';

        if (!$Mess->create($data)) {
            $this->error($Mess->getError());
        } else {
            if ($data['m_id'] > 0) {
                $ret = $Mess->save($data);
                $content = "编辑公告成功！";
                $id = $data['m_id'];
            } else {
                $ret = $Mess->add($data);
                $id = $ret;
                if ($ret > 0) {
                    $where['g_id'] = I('post.g_id');
                    if (!empty(I('post.c_id'))) {
                        $where['c_id'] = I('post.c_id');
                    }
                    //根据所在班级的学生id查询家长
                    $student = D('Student')->where($where)->select();
                    $sutId = array();
                    foreach ($student as $key => $value) {
                        $sutId[] = $value['stu_id'];
                    }
                    $where2['stu_id'] = array('in', implode(',', $sutId));
                    $parentId = D('StudentGroupAccess')->where($where2)->select();
                    $spId = array();
                    foreach ($parentId as $key => $value) {
                        $spId[] = $value['sp_id'];
                    }
                    $where3['sp_id'] = array('in', implode(',', $spId));
                    $parent = D('StudentParent')->where($where3)->select();
                    $parentId = array();
                    foreach ($parent as $key => $value) {
                        $parentId[] = $value['u_id'];
                    }
                    $parentId = array_unique($parentId);
                    if (!empty($parentId)) {
                        foreach ($parentId as $key => $value) {
                            $data1['m_id'] = $ret;
                            $data1['user_id'] = $value;
                            $messSub->add($data1);
                        }
                    }
                }
                $content = "发布公告成功,ID:" . $ret;
            }
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
                $this->error($Mess->getError());
            } else {
                //app消息推送 By:Meng Fanmin 2017.06.22
                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $parentId,
                    'ticker' => '班级公告',
                    'title' => I('post.title'),
                    'text' => I('post.title'),
                    'after_open' => 'go_custom',
                    'activity' => 'notice',
                    'id' => $id,
                );

                $this->sendAppMsg($pushArr,1);


                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
                $this->success('操作成功！');
            }
        }


    }

    //获取公告详情
    public function getDetail(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $id = I('post.id');

        if(empty($id)){
            $this->error('参数错误');
        }
        $info = M('Mymessage')->field('c_id,title,content,create_time')->where(array('m_id'=>$id))->find();
        $cids = $info['c_id'];
        $cidStr = explode(',', trim($cids, ','));
        foreach ($cidStr as $k => $v) {
            $cinfo = getClassInfo($v);
            $newStr[$k]['c_id'] = $v;
            $newStr[$k]['c_name'] = $cinfo['c_name'];
            //获取年级gid
            $newStr[$k]['g_id'] = $cinfo['g_id'];
            //获取g_name
            $newStr[$k]['g_name'] = $cinfo['g_name'];
        }
        $info['classInfo'] = $newStr;
        if(empty($info)){
            $this->error('参数错误');
        }
        $this->success($info);
    }


}