<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-考勤管理
 */
namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class AtndManageController extends BaseController
{
    /**
     *考勤规则
     */
    public function index()
    {

        $this->display();
    }

    public function get()
    {
        $sId = I('get.s_id');//学校
        $aId = I('get.a_id');//校区
        $WorkRule = D('WorkRule');
        //       $page = I('get.page');
        //       $pagesize = I('get.$pagesize',10);
        //       $sort = I('get.sort','id');
        //       $order = $sort. ' ' . I('get.order','desc');
        if (!empty($sId)) {
            $where['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $where['a_id'] = $aId;
        }
        $workRuleInfo = $WorkRule->queryListEx('*', $where, $order, $page, $pagesize, '');
        if (empty($workRuleInfo)) {
            $this->error('该学校规则不存在');
        }
        $this->success($workRuleInfo['list']);
    }

    public function edit()
    {
        $WorkRule = D('WorkRule');
        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $authId = $this->authRule();
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['start_time'] = I('post.start_time');
        $data['end_time'] = I('post.end_time');
        if (!$WorkRule->create($data)) {
            $this->error($WorkRule->getError());
        } else {
            if ($id > 0) {
                $ret = $WorkRule->save($data);
            } else {
                $ret = $WorkRule->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Course->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Atnd/AtndManage/index'));
            }
        }
    }
}