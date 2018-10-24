<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学习服务后台管理
 */
namespace Admin\Controller\Learning;

use Admin\Controller\BaseController;

class LearningController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    //查询
    public function query()
    {
        $Learning = D("Learning");
        $s_id = trim(I('get.s_id_search'));//学校id
        $regionKeywords = trim(I('get.region_id_search'));//关键字搜索
        $page = I('get.page');
        $pagesize = I('get.pagesize');
        $order = "id";

        //根据城市名/学校名称搜索显示的轮播
        if (!empty($regionKeywords)) {
            //获取与关键字相关所有region_id
            $regionStr = M("AreaRegion")->where("region_name like '%{$regionKeywords}%'")->getField('region_code');
            $where['region_id'] = array('like', "%{$regionStr}%");
        }
        if (!empty($s_id)) {
            //与关键字相关的所有的s_id
            $schoolStr = M("SchoolInformation")->where("name like '%{$s_id}%'")->getField('s_id');
            $where['s_id'] = array('like', "%{$schoolStr}%");
        }

        $result = $Learning->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);

    }

    //新增/编辑
    public function edit()
    {
        $Learning = D("Learning");

        $id = I('post.learning_id');
        $data['region_id'] = I('post.prov_id');
        $data['s_id'] = I('post.s_id');
        $data['title'] = I('post.title', 1);//标题
        $data['web_url'] = I('post.web_url');//链接路径
        $data['icon_url'] = I('post.f_tb');//图片路径
        $data['status'] = I('post.status');//状态

        $data['prov_id'] = I("post.prov_id");
        $data['city_id'] = I("post.city_id");
        $data['county_id'] = I("post.county_id");
        $content = '新建';
        if (!$Learning->create($data)) {
            $this->error($Learning->getError());
        } else {
            if ($id) {
                $data['id'] = $id;
                $content = '编辑';
                $ret = $Learning->save($data);
            } else {
                $data['create_time'] = date("Y-m-d H:i:s");
                $ret = $Learning->add($data);
            }
        }
        if ($ret === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Learning->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content . '成功', U('/Admin/Learning/Learning/index'));
        }
    }

    //编辑获取
    public function get()
    {
        $id = I('get.learning_id');
        // var_dump($id);
        if (!$id) {
            $this->error('参数错误');
        }

        //banner详情
        $res = M('Learning')->where(" id = '{$id}' ")->find();

        if (!$res) {
            $this->error('操作错误');
        }
        $this->success($res);
    }

    //获取数据树
    public function get_tree()
    {
        //顶级树
        $regionTree = M("AreaRegion")->alias("a")->distinct(true)->field('a.region_code as id, a.parent_id as pId, a.region_name as name')
            ->join(" __SCHOOL_INFORMATION__ b ON a.region_code=b.region_id ")
            ->select();
        //子集树
        $schoolTree = M("SchoolInformation")->field("s_id as id, region_id as pId, name")->select();
        //数据总树
        $tree = array_merge($regionTree, $schoolTree);

        $id = I('get.learning_id');

        //banner详情
        if ($id) {
            $res = M('Learning')->where(" id = '{$id}' ")->find();
        } else {
            $res = array();
        }
        //banner中的城市id
        $regionArr = explode(',', $res['region_id']);
        $schoolArr = explode(',', $res['s_id']);
        foreach ($tree as $key => $value) {
            if ($value['pId'] == 0) {
                //判断顶级分类选中状态
                if (in_array($value['id'], $regionArr)) {
                    $tree[$key]['checked'] = 'true';
                } else {
                    $tree[$key]['checked'] = '';
                }
            } else {
                if (in_array($value['id'], $schoolArr)) {
                    $tree[$key]['checked'] = 'true';
                } else {
                    $tree[$key]['checked'] = '';
                }
            }
        }
        $this->success($tree);
    }

    //删除
    public function del()
    {
        $id = I('post.id');
        if (!$id) {
            $this->error("参数错误");
        }
        $info = M("Learning")->where("id in ({$id})")->getField('id', true);

        if (!$info) {
            $this->error("参数错误");
        }
        $res = M("Learning")->where("id in ({$id})")->delete();
        if ($res) {
            $content = "删除";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success('删除成功');
        }

    }

    // 上线/下线

    public function line()
    {
        $ids = I('post.id');
        $type = I('post.status');
        $arrIds = explode(',', $ids);
        if (empty($arrIds)) {
            $this->error('参数错误');
        }
        $Learning = D('Learning');
        $okCount = 0;
        foreach ($arrIds as $k => $v) {
            $where['id'] = $v;
            $ret = $Learning->where($where)->save(array('status' => $type));
            if ($type == 2) S($v, null);
            if ($ret !== false) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = (($type == 0) ? "上线" : "下线") . $okCount . "条信息:" . $ids;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success((($type == 0) ? "上线" : "下线") . '成功' . $okCount . '条记录');
    }
}