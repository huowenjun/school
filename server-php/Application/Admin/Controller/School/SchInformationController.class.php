<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学校信息
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class SchInformationController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    //查询数据
    public function query()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 's_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $School = D('SchoolInformation');
        $result = $School->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //保存
    public function edit()
    {

        $id = I('post.s_id/d', 0);
        $data['s_id'] = $id;

        $data['name'] = I('post.name');//学校名称
        $data['build_time'] = I('post.build_time'); //成立时间
        $data['main_zrr'] = I('post.main_zrr');//主责任人
        $data['sub_zzr'] = I('post.sub_zzr');//副责任人
        $data['tel'] = I('post.tel');//学校电话内线
        $data['memo'] = I('post.memo');//备注
        $data['main_phone'] = I('post.main_phone');//主责任人电话
        $data['sub_phone'] = I('post.sub_phone');//副负责电话
        $data['address'] = I('post.address');//学校地址
        $data['create_time'] = date('Y-m-d H:i:s');
        if (strtotime($data['build_time']) > strtotime($data['create_time'])) {
            $this->error("成立时间不能大于当前时间");
        }
        $School = D('SchoolInformation');
        if (!$School->create($data)) {
            $this->error($School->getError());
        } else {
            $ret = $School->save($data);
        }
        //  dump(self::getLastSql());
        $content = '编辑成功';
        if ($ret === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($School->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Dept/index'));
        }
    }
}

