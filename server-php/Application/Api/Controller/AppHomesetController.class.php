<?php
/*
*我的班级
*by Mengfanmin date 20170323
*/

namespace Api\Controller;

use Api\Controller\BaseController;

class AppHomesetController extends BaseController
{
    /*
     * 重写父类方法
     * */
    protected function init()
    {

    }

    /*
     *首页数据查询
     *
     * */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        $system_model = I('post.system_model');
        $version_name = I('post.version_name');

        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }

        $where['is_effect'] = 1;
        $where['is_top'] = 1;
        $product_name = C('PRODUCT_NAME');
        //教育培训
        $online_video = M('OnlineVideo')->field('id,title,url,mark,image')->where($where)->order('sort asc')->select();
        //快乐成长
        $children_goods = M('ChildrenGoods')->field('id,title,url,mark,image')->where($where)->limit(6)->order('sort asc')->select();
        //投资理财
        $invest = M('Invest')->where($where)->field('id,title,url,mark,image')->limit(3)->order('sort asc')->select();
        //生活缴费
        $living_payment = M('LivingPayment')->field('id,title,url,mark,image')->where($where)->order('sort asc')->select();
        //校园购
        /*$shopping = M('GoodsDetail')
            ->field("id,url_pc_short as url,address as mark,image,`describe` as title,price")
            ->limit(3)
            ->order('sort asc')
            ->select();*/
        $shopping = array();
        foreach ($online_video as $key => $value) {
            $online_video[$key]['product_name'] = $product_name[0];
        }
        foreach ($invest as $key => $value) {
            $invest[$key]['product_name'] = $product_name[1];
        }
        foreach ($living_payment as $key => $value) {
            $living_payment[$key]['product_name'] = $product_name[2];
        }
        foreach ($children_goods as $key => $value) {
            $children_goods[$key]['product_name'] = $product_name[3];
        }
        foreach ($shopping as $key => $value) {
            $shopping[$key]['product_name'] = $product_name[4];
        }

        //教育新闻
        $cates = C('ARTICLE_CATE');
        $host_name = C('ONLINE_NAME');
        $Archives = M('Archives', 'dede_', 'DB_CONFIG2');
        $news_list = $Archives
            ->field('id,typeid,title,litpic as image,senddate,source')
            ->where('arcrank=0 and litpic != ""')
            ->order("id desc")
            ->limit(3)
            ->select();

        foreach ($news_list as $key => $value) {
            $time = date('Y', $value['senddate']) . '/' . date('md', $value['senddate']);
            unset($news_list[$key]['typeid']);
            unset($news_list[$key]['senddate']);
            $news_list[$key]['url'] = $host_name . '/edunews/' . $cates[$value['typeid']] . '/' . $time . '/' . $value['id'] . '.html';
            $news_list[$key]['product_name'] = $product_name[5];
        }


        $result['children_goods'] = $children_goods;
        $result['living_payment'] = $living_payment;
        $result['shopping'] = $shopping;
        $result['edu_news'] = $news_list;
        if (($system_model == 0) && ($version_name == '2.1.7')) {
            $result['invest'] = array();
            $result['online_video'] = array();
        } else {
            $result['invest'] = $invest;
            $result['online_video'] = $online_video;
        }


        $this->success($result);
    }


    //更多-列表页
    public function getMore()
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
        $type = I('post.type');
        $page = I('post.page');
        $pagesize = I('post.pagesize', 20);
        $model = '';
        switch ($type) {
            case 'online_video':
                $model = M('OnlineVideo');//在线教育
                break;
            case 'invest':
                $model = M('Invest');//投资理财
                break;
            case 'living_payment':
                $model = M('LivingPayment');//生活缴费
                break;
            case 'children_goods':
                $model = M('ChildrenGoods');//快乐成长
                break;
            default:
                $this->error('参数错误');
        }
        $where['is_effect'] = 1;
        $result = $model->where($where)->order('sort asc')->page($page, $pagesize)->select();
        $this->success($result);
    }

    /*
     *  栏目标题
     */
    public function titleQuery()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        $system_model = I('post.system_model');
        $version_name = I('post.version_name');

        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }

        $where['is_effect'] = 1;
        $where['is_top'] = 1;
        $product_name = C('PRODUCT_NAME');
        $product_path = C('PRODUCT_PATH');
        //快乐成长
        $children_goods = M('ChildrenGoods')->field('id,title,url,mark,image')->where($where)->order('sort asc')->select();
        foreach ($children_goods as $key => $value) {
            $children_goods[$key]['product_name'] = $product_name[3];
            $children_goods[$key]['product_path'] = $product_path[3];
        }
        //投资理财
        if ($system_model == 0) {
            $where['ios_v'] = array('neq', $version_name);
            $invest = M('Invest')->where($where)->field('id,ios_v,title,url,mark,image')->order('sort asc')->select();
            foreach ($invest as $key => $value) {
                if ($value['ios_v'] == '1') {
                    $invest[$key]['product_name'] = "惠民生活";
                } else {
                    $invest[$key]['product_name'] = $product_name[1];
                }
                $invest[$key]['product_path'] = $product_path[1];

            }
        } else {
            $invest = M('Invest')->where($where)->field('id,title,url,mark,image')->order('sort asc')->select();
            foreach ($invest as $key => $value) {
                $invest[$key]['product_name'] = $product_name[1];
                $invest[$key]['product_path'] = $product_path[1];
            }
        }

        $result['children_goods'] = $children_goods;
        $result['invest'] = $invest;
        $this->success($result);
    }

    //添加版本返回
    public function appVersion(){
        $data = D('app_version')->field('v_code,v_upgrade,v_path,v_memo')->order('v_id desc')->find();
        $this->success($data);
    }

}