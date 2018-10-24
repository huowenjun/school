<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-考勤管理
 */

namespace Admin\Controller\App;

use Admin\Controller\BaseController;

class AppHomesetController extends BaseController
{
    //首页配置新增/编辑
    public function edit()
    {
        $type = I('post.type');//1.教育培训  2.投资理财  3.生活缴费
        $id = I('post.id');
        $title = I('post.title');
        $url = I('post.url');
        $mark = I('post.mark');//备注
        $sort = I('post.sort');
        $is_effect = I('post.is_effect');
        $is_top = I('post.is_top');
        $image = I('post.image');

        if ($id) {
            $data['id'] = $id;
        }
        $data['title'] = $title;
        $data['url'] = htmlspecialchars_decode($url);
        $data['mark'] = $mark;
        $data['sort'] = $sort;
        $data['is_effect'] = $is_effect;
        $data['is_top'] = $is_top;
        $data['modif_time'] = date('Y-m-d H:i:s');
        $data['ios_v'] = I('post.ios_v');

        if ($image) {
            $data['image'] = $image;
        }

        $rules = array(
            array('title', 'require', '标题不能为空！'),
            array('url', 'require', '跳转地址不能为空！'),
            array('is_top', 'require', '请选择是否首页展示！'),
            array('ios_v', 'require', 'ios版本号不能为空！'),
        );

        $model = '';
        switch ($type) {
            case 1:
                $model = M('OnlineVideo');//在线教育
                break;
            case 2:
                $model = M('Invest');//投资理财
                break;
            case 3:
                $model = M('LivingPayment');//生活缴费
                break;
            case 4:
                $model = M('ChildrenGoods');//快乐成长
                break;
            default:
                $this->error('参数错误');
        }
        if (!$model->validate($rules)->create($data)) {
            $this->error($model->getError());
        }
        if (!$id) {
            $result = $model->add($data);
        } else {
            $result = $model->where("id = '{$id}'")->save($data);
        }
        if (!$result) {
            $this->error('操作失败,请稍后再试');
        } else {
            $this->success('操作成功');
        }
    }


    public function get()
    {
        $id = I('get.id/d');
        $type = I('get.type');
        if (!$id || !$type) {
            $this->error('参数错误,请重试.');
        }
        $model = '';
        switch ($type) {
            case 1:
                $model = M('OnlineVideo');//在线教育
                break;
            case 2:
                $model = M('Invest');//投资理财
                break;
            case 3:
                $model = M('LivingPayment');//生活缴费
                break;
            case 4:
                $model = M('ChildrenGoods');//快乐成长
                break;
            default:
                $this->error('参数错误');
        }
        $info = $model->where(array('id' => $id))->find();

        if (empty($info)) {
            $this->error('网络延迟,请稍后再试.');
        }
        $this->success($info);
    }

    //首页配置 删除
    public function delete()
    {
        $type = I('post.type');//1.教育培训  2.投资理财  3.生活缴费
        $ids = I('post.id');
        if (empty($ids) || empty($ids)) {
            $this->error('参数错误!');
        }
        $model = '';
        switch ($type) {
            case 1:
                $model = M('OnlineVideo');//在线教育
                break;
            case 2:
                $model = M('Invest');//投资理财
                break;
            case 3:
                $model = M('LivingPayment');//生活缴费
                break;
            case 4:
                $model = M('ChildrenGoods');//快乐成长
                break;
            default:
                $this->error('参数错误');
        }
        $arrIds = explode(',', $ids);
        foreach ($arrIds as $key => $value) {
            $where['id'] = $value;
            $ret = $model->where($where)->delete();
        }

        $this->success('删除成功!');

    }

    //首页请求查询
    public function query()
    {
        $type = I('get.type', 1);
        $page = I('get.page');
        $pagesize = I('get.pagesize', 20);

        $model = '';
        switch ($type) {
            case 1:
                $model = M('OnlineVideo');
                break;
            case 2:
                $model = M('Invest');
                break;
            case 3:
                $model = M('LivingPayment');
                break;
            case 4:
                $model = M('ChildrenGoods');
                break;
            default:
                $this->error('参数错误');
        }
        $list = $model->page($page, $pagesize)->order('sort asc')->select();
        $count = $model->count();
        $IS_EFFECT = C('IS_EFFECT');
        foreach ($list as $key => $value) {
            $list[$key]['is_effect'] = $IS_EFFECT[$value['is_effect']];
            $list[$key]['is_top'] = $IS_EFFECT[$value['is_top']];
            $list[$key]['image'] = "<img width='100' src='" . $value['image'] . "'>";
        }
        $result['list'] = $list;
        $result['count'] = $count;
        $this->success($result);
    }
}