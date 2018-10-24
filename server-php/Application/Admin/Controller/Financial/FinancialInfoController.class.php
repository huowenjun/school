<?php
/*
 * 金融资讯
 */

namespace Admin\Controller\Financial;

use Admin\Controller\BaseController;


class FinancialInfoController extends BaseController
{

    /*
  * 金融资讯
  */
    public function index()
    {
        $this->display();
    }

    /*
     * 添加add与编辑updata
     * post请求
     * url：/index.php/Admin/Financial/FinancialInfo/edit
     * 接收参数：
     * add   ：title    信息标题 类型为公告时必填
                content 消息内容
                m_type   int() 消息类型  :  1 系统消息2 公告3私信，4数海公告  5.新品推荐 6致富财经
                images_path 缩略图
       up  ：title    信息标题 类型为公告时必填
            content 消息内容
            m_type   int() 消息类型  :  1 系统消息2 公告3私信，4数海公告  5.新品推荐 6致富财经
            images_path 缩略图
            m_id
     */
    public function edit()
    {
        //  根据 m_id 判断添加入库还是编辑
        $m_id = I('post.m_id');
        if ($m_id) {//修改
            $data['title'] = I('post.title');
            $data['content'] = I('post.content');
            $data['m_type'] = I('post.m_type');
            $data['user_id'] = $this->getUserId();
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['describe'] = I('post.describe');
            $data['images_path'] = I('post.images_path');
            $bool = D('mymessage')->where(['m_id' => $m_id])->save($data);
            if ($bool) {
                $this->success('编辑成功');
            } else {
                $this->error('网络错误，请稍后');
            }
        } else {//添加
            $data['title'] = I('post.title');
            $data['content'] = I('post.content');
            $data['m_type'] = I('post.m_type');
            $data['user_id'] = $this->getUserId();
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['describe'] = I('post.describe');
            $images_path = I('post.images_path');
            if ($images_path) {
                $data['images_path'] = I('post.images_path');
            }
            $bool = D('mymessage')->add($data);
            if ($bool) {
                $this->success('添加成功');
            } else {
                $this->error('网络错误，请稍后');
            }
        }
    }

    /*
     * 编辑展示数据
     * 参数：
     *  m_id int（）
     * get 请求
     * url：/index.php/Admin/Financial/FinancialInfo/get
     */
    public function get()
    {
        $m_id = I('get.m_id');
        $data = D('mymessage')->where(['m_id' => $m_id])->find();
        if ($data) {
            $this->success($data);
        } else {
            $this->error('网络错误，请稍后');
        }
    }

    /*
     *展示数据
     * 参数：
     *      page
     *      pagesize
     *      keyword
     * get 请求
     * url： /index.php/Admin/Financial/FinancialInfo/query
     */
    public function query()
    {
        $page = I('get.page');
        $pagesize = I('get.pagesize', 10);
        //消息类型 1 系统消息2 公告 3私信，4数海公告  5.新品推荐 6致富财经 7推送通知类消息
        if (I('get.m_type')) {
            $where['m_type'] = I('get.m_type');
        } else {
            $where['m_type'] = array('in', array(4, 5, 6));
        }
        $myMessageD = D('mymessage');
        $data['list'] = $myMessageD->where($where)->page($page, $pagesize)->order('m_id desc')->select();//分页展示数据
        foreach ($data['list'] as &$value) {
            $value['images_path'] = '<img src="' . $value['images_path'] . '" width="120px" height="90px">';
            if ($value['m_type'] == 1) {
                $value['m_type'] = '系统消息';
            } elseif ($value['m_type'] == 2) {
                $value['m_type'] = '数海公告';
            } elseif ($value['m_type'] == 3) {
                $value['m_type'] = '私信';
            } elseif ($value['m_type'] == 4) {
                $value['m_type'] = '数海公告';
            } elseif ($value['m_type'] == 5) {
                $value['m_type'] = '新品推荐';
            } elseif ($value['m_type'] == 6) {
                $value['m_type'] = '致富财经';
            }
        }
        $data['count'] = $myMessageD->where($where)->count();//总记录条数
        $this->success($data);
    }

    /**
     * 删除数据
     * 参数：m_id 例如：1,2,3,4
     * post 请求
     * url：/index.php/Admin/Financial/FinancialInfo/del
     */
    public function del()
    {
        $m_id = I('post.m_id', '0', 'string');
        if (!$m_id) {
            $this->error('参数错误!');
        }
        $myMessageD = D('mymessage');
        $bool = $myMessageD->where(array('m_id' => array('in', $m_id)))->delete();
        if ($bool) {
            $this->success('删除成功');
        } else {
            $this->error('网络错误，请稍后');
        }

    }

    /*
     * 撤回数据（修改状态）
     * 参数：
     *  m_id
     *  is_effect=0
     * post 请求
     * url：/index.php/Admin/Financial/FinancialInfo/withdraw
     */
    public function withdraw()
    {
        $where['m_id'] = I('post.m_id');
        $data['is_effect'] = 0;
        $bool = D('mymessage')->where($where)->save($data);
        if ($bool) {
            $this->success('撤回成功');
        } else {
            $this->error('网络错误，请稍后');
        }
    }

    /*
     * 发布数据
     * 参数：
     *      m_id
     *      is_effect=1
     * post 请求
     * url：/index.php/Admin/Financial/FinancialInfo/release
     */
    public function release()
    {
        set_time_limit(0);
        //修改状态
        $where['m_id'] = I('post.m_id');
        $data['is_effect'] = 1;
        $data['release_time'] = date('Y-m-d H:i:s');
        $m_type = I('post.m_type');
        $UserD = M('user', 'think_');
        $bool = D('mymessage')->where($where)->save($data);
        $releaseData = D('mymessage')->where($where)->find();//推送的数据
        if ($bool) {
            //根据判断m_type的类型，匹配user表的type来找到user_id,
            //4数海公告  5.新品推荐 6致富财经
            //0会员 9第三方代理公司 10分销代理商 11区县代理商
            if ($m_type == 5 || $m_type == 6) {//type   == 0
                $userIdList = $UserD->where(['type' => 0])->getField('user_id', true);//二维数组，user_id
                $pushArr['u_id'] = $userIdList;
                if ($m_type == 5) {
                    $pushArr['ticker'] = '新品推荐';
                } else {
                    $pushArr['ticker'] = '致富财经';
                }
                $pushArr['title'] = $releaseData['title'];
                $pushArr['text'] = $releaseData['describe'];
                $pushArr['fsdx'] = '0,0,1';
                $pushArr['after_open'] = 'go_custom';
                $pushArr['activity'] = 'financial';
                $pushArr['id'] = $where['m_id'];
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    sendAppMsg($pushArr);
                }

            } else if ($m_type == 4) {//type  == 9 & 10 & 11
                $userIdList = $UserD->field('user_id')->where(array('m_id' => array('in', '9,10,11')))->select();//二维数组，user_id
                $pushArr['u_id'] = $userIdList;
                $pushArr['ticker'] = '数海公告';
                $pushArr['title'] = $releaseData['title'];
                $pushArr['text'] = $releaseData['describe'];
                $pushArr['fsdx'] = '0,0,1';
                $pushArr['after_open'] = 'go_custom';
                $pushArr['activity'] = 'financial';
                $pushArr['id'] = $where['m_id'];
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    sendAppMsg($pushArr);
                }
            }
            $this->success('发布成功');
            //组合数据，调用推送集成接口
        } else {
            $this->error('网络错误，请稍后');
        }

    }


}