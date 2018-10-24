<?php
/*
*家庭作业
*by ruiping date 20161115
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class HomeworkController extends BaseController
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
        //$token = md5('/Api/Homework/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        //查询作业
        $user_id = $this->getUserId();
        $user_type = $this->getUserType();
        $Work = D('Homework');
        $pagesize = $this->PAGESIZE;
        $p = I('post.page', 1);
        $orderby = "h_id desc";
        $sdate = I('post.sdate');
        $edate = I('post.edate');
        $crs_id = I('post.crs_id');
        if (!empty($sdate) && !empty($edate)) {
            $where['create_time'] = array('between', array($sdate . ' 00:00:00', $edate . ' 23:59:59'));
        }
        if (!empty($crs_id)) {
            $where['crs_id'] = array('EQ', $crs_id);
        }
        if ($user_type == 3) {
            $where['user_id'] = $user_id;
            if (!empty(I('post.c_id'))) {
                $where['c_id'] = I('post.c_id');
            } else {
                //获取教师t_id
                $t_id = M("Teacher")->where("u_id = '{$user_id}'")->getField("t_id");
                $classInfo = $this->getTeacherClass($t_id);
                $c_str = $classInfo['c_id'];
                if ($c_str) {
                    $where['c_id'] = array("IN", $c_str);
                }
            }
        } elseif ($user_type == 4) {
            if (!empty(I('post.s_id'))) {
                $where['s_id'] = I('post.s_id');
            }
            if (!empty(I('post.a_id'))) {
                $where['a_id'] = I('post.a_id');
            }
            if (empty(I('post.g_id')) || empty(I('post.c_id'))) {
                $this->error('参数错误');
            }
            $where['g_id'] = I('post.g_id');
            $where['c_id'] = I('post.c_id');
        } else {
            $this->error('用户类型不正确,无权访问！');
        }
        $result = $Work->queryListForApp('*', $where, $orderby, $p, $pagesize);
        $workList = $result['list'];
        $workCount = $result['count'];
        $arr = array();
        $page_szie = ceil($workCount / $pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $workCount;
        $arr['page'] = $p;
        $arr['content'] = $workList;
        $this->success($arr);
    }

    //获取数据详情
    public function getDetail()
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
        $id = I('post.id');
        if($id <= 0){
            $this->error('参数错误');
        }
        $info = M('Homework')
            ->field('h_id,name,c_id,crs_id,work_content,finish_time,create_time,user_id')
            ->where("h_id = '{$id}'")
            ->find();
        if(empty($info)){
            $this->error('参数错误');
        }
        $class_info = getClassInfo($info['c_id']);
        $info['c_name'] = $class_info['c_name'];
        $info['g_name'] = $class_info['g_name'];
        $info['t_name'] = M('Teacher')->where("u_id = '{$info['user_id']}'")->getField('name');
        $info['crs_name'] = M('Course')->where("crs_id = '{$info['crs_id']}'")->getField('name');
        $info['file_path'] = M('HomeworkFile')->where("h_id = '{$info['h_id']}'")->getField('file_path',true);
        $this->success($info);
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
        //$token = md5('/Api/Homework/delete'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $h_id = I('post.h_id', '');
        if (empty($h_id)) {
            $this->error('参数不正确！');
        }
        $Work = D('Homework');
        $workFile = D('HomeworkFile');
        $where['h_id'] = $h_id;
        //检查权限
        $auth = $Work->where($where)->find();
        if (!empty($auth) && $auth['user_id'] != $this->getUserId()) {
            $this->error('没有权限删除');
        }
        $ret = $Work->where($where)->delete();

        if ($ret == 1) {
            $content = "删除成功";
            $dbfile = $workFile->where($where)->select();
            //删除资源文件
            foreach ($dbfile as $key => $value) {
                if (file_exists('.' . $value['file_path']) && is_file('.' . $value['file_path'])) {
                    unlink('.' . $value['file_path']);
                }
            }
            $workFile->where($where)->delete();

            $zt = 1;
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, $zt);
            $this->success('删除成功');
        } else {
            $content = "删除失败";
            $zt = 2;
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, $zt);
            $this->error('删除失败');
        }

    }

    /*
    *作业添加
    */
    public function add()
    {

        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        // $token = md5('/Api/Homework/add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $Work = D('Homework');
        $workFile = D('HomeworkFile');
        $data['h_id'] = I('post.h_id/d', 0);
        $data['name'] = I('post.name');
        $data['a_id'] = I('post.a_id');
        $data['s_id'] = I('post.s_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['crs_id'] = I('post.crs_id');
        $data['work_content'] = I('post.work_content');
        $data['finish_time'] = I('post.finish_time');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->getUserId();
        $Work->startTrans();
        $workFile->startTrans();
        if (!$Work->create($data)) {
            $this->error($Work->getError());
        } else {
            if ($data['h_id'] > 0) {
                $ret = $Work->save($data);
                $content = "编辑作业成功！";
                $id = $data['h_id'];
            } else {
                $ret = $Work->add($data);
                $content = "发布作业成功,ID:" . $ret;
                $id = $ret;
            }
            //班级ID=》学生id=》所有学生设备的imie号，=》循环task：imie=》（作业内容）
            //作业内容
            $con = utf8_unicode($data['work_content']);
            $con = iconv("utf-8","gb2312",$con);
            //imie
            $studentM = D('student');
            $imieList = $studentM->field('g_id,c_id,a_id,s_id,imei_id')->where(['g_id'=>$data['g_id'],'c_id'=>$data['c_id'],'a_id'=>$data['a_id'],'s_id'=>$data['s_id'],'devicetype'=>2])->select();
            foreach ($imieList as $value)
            {
                $num = ($id%3)+1;
                $Content = "HOMEWORK,".$con.",$num";
                $content_len_16 = get_len_16($Content);
//            [3G*5678901234*0020*HOMEWORK,597D003100320033,1]
                $command = '[3G*'.$value['imei_id'].'*'.$content_len_16.'*'.$Content.']';
                taskRedis($value['imei_id'],$command);
            }
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
                $this->error("发布作业失败");
            } else {
                $files = array();
                $retData = array();
                $newFilePath = array();

                //删除旧图片
                if ($data['h_id'] > 0) {
                    $where1['h_id'] = $data['h_id'];
                    $dbfile = $workFile->field('file_path')->where($where1)->select();
                    // var_dump($dbfile);die();

                    //删除资源文件
                    foreach ($dbfile as $key => $value) {
                        if (file_exists('.' . $value['file_path']) && is_file('.' . $value['file_path'])) {
                            unlink('.' . $value['file_path']);
                        }
                    }
                    $workFile->where($where1)->delete();
                }

                for ($i = 0; $i < count($_FILES); $i++) {
                    $files['fileupload']['name'] = $_FILES['pic' . $i]['name'];
                    $files['fileupload']['type'] = $_FILES['pic' . $i]['type'];
                    $files['fileupload']['size'] = $_FILES['pic' . $i]['size'];
                    $files['fileupload']['tmp_name'] = $_FILES['pic' . $i]['tmp_name'];

                    $upload = new \Think\Upload();// 实例化上传类
                    $upload->maxSize = 2097150;// 设置附件上传大小  2M
                    $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'amr');// 设置附件上传类型
                    $upload->rootPath = './Public/Uploads/app/'; // 设置附件上传根目录
                    $upload->savePath = 'imgage/'; // 设置附件上传（子）目录
                    $upload->stream = true;
                    // 上传文件
                    $info = $upload->upload($files);
                    if (!$info) {// 上传错误提示错误信息
                        $Work->rollback();
                        $workFile->rollback();
                        $this->error("上传文件失败");
                    } else {// 上传成功
                        $url = '/Public/Uploads/app/' . $info['fileupload']['savepath'] . $info['fileupload']['savename'];
                        // $this->success($url);
                        if ($data['h_id'] > 0) {
                            $data1['h_id'] = $data['h_id'];
                        } else {
                            $data1['h_id'] = $ret;
                        }
                        $data1['file_path'] = $url;
                        array_push($newFilePath, $url);
                        $workFile->add($data1);
                    }
                }

                $Work->commit();
                $workFile->commit();

                if ($data['h_id'] > 0) {
                    $retData['h_id'] = $data['h_id'];
                } else {
                    $retData['h_id'] = $ret;
                }
                $retData['path'] = $newFilePath;
                $retData['create_time'] = $data['create_time'];

                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);

                //app消息推送 By:Meng Fanmin 2017.06.26
                $parentId = getParentId(I('post.c_id'));
                $classInfo = getClassInfo(I('post.c_id'));
                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $parentId,
                    'ticker' => '作业消息',
                    'title' => $classInfo['g_name'] . $classInfo['c_name'] . I('post.name'),
                    'text' => $classInfo['g_name'] . $classInfo['c_name'] . I('post.name'),
                    'after_open' => 'go_custom',
                    'activity' => 'homework',
                    'id' => $id,
                );
                $this->sendAppMsg($pushArr);

                //将作业内容更新到作业语音表 By:Meng Fanmin 2017.07.04
                $Hook = new \Api\Controller\HookController();
                $Hook->getSchoolHomework($data['c_id']);
                $this->success($retData);
            }
        }

    }

}