<?php
namespace Admin\Controller;

use Think\Controller;

class UploadController extends Controller
{
    public function index()
    {

        $this->display();

    }

    public function upload_image()
    {
        $result = array();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 2097150;// 设置附件上传大小  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'imgage/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            //var_dump($info);
            foreach ($info as $value) {

                $url = '/Public/Uploads/' . $value['savepath'] . $value['savename'];
                $result['url'] = $url;
//                //生成缩略图
//                $image = new \Think\Image();
//                $image->open($_SERVER['DOCUMENT_ROOT'] . $url);
//                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
//                $image->thumb(150, 150)->save($_SERVER['DOCUMENT_ROOT'] . '/Public/Uploads/' . $value['savepath'] . 's_' . $value['savename']);
            }
            $result['success'] = true;
        }


        $this->success($result);
    }

    /*
     * 支持多文件上传
     */
    public function upload_images()
    {
        $result = array();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 2097150;// 设置附件上传大小  2M
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录
        $upload->savePath = 'imgage/'; // 设置附件上传（子）目录
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            //var_dump($info);
            foreach ($info as $key=>$value) {

                $url = '/Public/Uploads/' . $value['savepath'] . $value['savename'];
                $result['url'][$key] = $url;
            }
            $result['success'] = true;
        }


        $this->success($result);
    }

}