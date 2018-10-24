<?php
namespace Api\Controller;
use Think\Controller;
class UploadController extends Controller {
    public function index(){

        $this->display();
        
    }

    public function upload_image(){

        $fileName = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);
        if($fileName === false){
            $this->error('文件名称不存在');
        }
        $arrFileName = explode('.',$fileName);
        $tempFile = tempnam(sys_get_temp_dir(), 'emc1_img');
        rename($tempFile, $tempFile .= '.'.$arrFileName[1]);
        //保存临时文件
        $fp = @fopen($tempFile, "w");
        @fwrite($fp, file_get_contents('php://input'));
        fclose($fp);

        /*rename($tempFile,'./Public/Uploads/business/imgage/2016-10-14/58009ed63763b.jpg');
        unlink('./Public/Uploads/business/imgage/2016-10-14/58009ed63763b.jpg');
        exit;*/
        /*file_put_contents(
            $tempFile,
            file_get_contents('php://input')
        );*/

        /*$_FILES['myFile']['name'] 客户端文件的原名称

        $_FILES['myFile']['type'] 文件的 MIME类型，需要浏览器提供该信息的支持，例如"image/gif"

        $_FILES['myFile']['size'] 已上传文件的大小，单位为字节

        $_FILES['myFile']['tmp_name'] 文件被上传后在服务端储存的临时文件名*/
        $files = array();
        $files['fileupload']['name'] = $fileName;
        $files['fileupload']['type'] = $_SERVER["CONTENT_TYPE"];
        $files['fileupload']['size'] = $_SERVER["CONTENT_LENGTH"];
        $files['fileupload']['tmp_name'] = $tempFile;

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     2097150 ;// 设置附件上传大小  2M
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/Uploads/app/'; // 设置附件上传根目录
        $upload->savePath  =     'imgage/'; // 设置附件上传（子）目录
        $upload->stream    =     true;
        // 上传文件
        $info   =   $upload->upload($files);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功
            //var_dump($info);
            $url = '/Public/Uploads/app/'. $info['fileupload']['savepath'] . $info['fileupload']['savename'];
            $this->success($url);
        }
    }

}