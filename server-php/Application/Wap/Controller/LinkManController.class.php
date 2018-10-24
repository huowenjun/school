<?php
/*
 * 常用联系人类
 * 理想化的数据，得正则验证
 */

namespace Wap\Controller;

use Think\Controller;

class LinkManController extends Controller
{
    /*查询联系人
     *参数：user_id 用户id
     * get请求
     * a.php/wap/LinkMan/queryLinkMan
     */
    public function queryLinkMan()
    {
        $user_id = I('get.user_id');
        $data = D('linkman')->field('id,name,mobile,idcard')->where(['user_id' => $user_id])->select();
        $this->success($data);
    }

    /*增加联系人
     * 参数：user_id 用户id
     *      name 姓名
     *      mobile 手机
     *      idcard 身份证
     * 请求方式 post
     *url:/a.php/wap/LinkMan/addLinkMan
     */
    public function addLinkMan()
    {
        $id = I('post.id', '0', 'int');
        if ($id == 0) {
            $data['user_id'] = I('post.user_id', 0, 'int');
            $data['name'] = I('post.name', '0', 'string');
            $data['mobile'] = I('post.mobile', '0', 'string');
            $data['idcard'] = I('post.idcard', '0', 'string');
            $data['create_time'] = date('Y-m-d H:i:s');
            $bool = D('linkman')->add($data);
            if ($bool) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        } else {
            $name = I('post.name', '0', 'string');
            if ($name) {
                $data['name'] = $name;
            }
            $mobile = I('post.mobile', '0', 'string');
            if ($mobile) {
                $data['mobile'] = $mobile;
            }
            $idcard = I('post.idcard', '0', 'string');
            if ($idcard) {
                $data['idcard'] = $idcard;
            }
            $data['create_time'] = date('Y-m-d H:i:s');
            $bool = D('linkman')->where(['id' => $id])->save($data);
            if ($bool) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }


    }

    /*删除联系人
     *参数 id 联系人ID
     * post 请求
     * /a.php/wap/LinkMan/delLinkMan
     */
    public function delLinkMan()
    {
        $id = I('post.id', 0, 'int');
        if (empty($id)) {
            $this->error('请选择联系人');
        }
        $bool = D('linkman')->where(['id' => $id])->delete();
        if ($bool) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

}