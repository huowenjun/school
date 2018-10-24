<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {

    public function get_list(){
        $arrRet = array();
        $type = I("get.type");
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }

      //   if($authId['s_id']>0){
      //     $authId = $this->authRule();
      //     $where['s_id'] = $authId['s_id'];
      //     $where['a_id'] = $authId['a_id'];
      //     $where['g_id'] = $authId['g_id'];
      //     $where['c_id'] = $authId['c_id'];
      // }


        switch ($type){
            case 'group': //角色
                $arrRet = D('AdminGroup')->getField('id as id,title as value');
                break;
            case 'region': //地区
                $Index = new BaseController();
                $arr = $Index->getTreeList('county','all');
                $arrRet = $arr;
                break;
            case 's_region': //学校地区
                $regionList = D('RegionCode')->getField('');
                $arrRet = D('Surface')->where($where)->getField('s_id as id , s_jc,s_mc','-');
                break;
            case 'schoolarea': //校区
                $arrRet = D('SchoolArea')->where($where)->getField('a_id as id,name as value');
                // var_dump($arrRet);
                break;
            case 'dept': //部门
                $arrRet = D('Dept')->where($where)->getField('d_id as id , name as value');
                break;
            case 'grade': //年级
                $arrRet = D('Grade')->where($where)->getField('g_id as id , name as  value');
                break;
            case 'class': //班级
                $arrRet = D('Class')->where($where)->getField('c_id as id , name as value');
                break;
            case 'course': //学科
                if (!empty($a_id)) {
                    $where['a_id'] = $a_id;
                }
                if (!empty($g_id)) {
                    $where['g_id'] = $g_id;
                }
                if (!empty($c_id)) {
                    $where['c_id'] = $c_id;
                }
                $arrRet = D('Course')->where($where)->getField('crs_id as id , name as value');
                break;
            case 'teacher': //教师
                $arrRet = D('Teacher')->where($where)->getField('t_id as id , name as value');
                break;
            case 'student': //学生
                $arrRet = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
                break;
            case 'teachergroup': //组名
                $arrRet = D('TeacherGroup')->where($where)->getField('id , group_name as value');
                break;
            case 'examname': //单考名
                $arrRet = D('Exam')->where($where)->getField('ex_id as id ,start_time, name','-');
                break;
            case 'education': //学历
                $arrRet = C('EDUCATION');
                break;
            case 'leave': //请假类型
                $arrRet = C('LEAVE_TYPE');
                break;
            case 'divide_show': //分隔方式显示
                $arrRet = C('DIVIDE_SHOW');
                break;
            case 'open': //开启方式
                $arrRet = C('OPEN_MODE');
                break;
            case 'handle_open'://产品开启方式
                $arrRet = C('HANDLE_OPEN_MODE');
                break;
            case 'open_show'://开启方式显示数据
                $arrRet = C('OPEN_SHOW');
                break;
            case 'price': //计价方式
                $arrRet = C('PRICE_MODE');
                break;
            case 'product_type': //产品类别
                $arrRet = D('ProductType')->getField('pt_id as id , pt_mc as value');
                break;
            case 'user_status': //用户状态
                $arrRet = C('USER_STATUS');
                break;
            case 'user'://用户列表
                $arrRet = D('AdminUser')->getField('user_id as id , username as value');
                break;
            case 'group'://用户角色
                $arrRet = D('AdminGroup')->getField('id , title as value');
                break;
            case 'price_type'://价格表示方法
                $arrRet = C('PRICE_TYPE');
                break;
            case 'price_mode'://价格表示方法
                $arrRet = C('PRICE_MODE');
                break;
            case 'order_ostatus'://订单状态
                $arrRet = C('OSTATUS');
                break;
            default:
                $arrRet = D('Material')->getField('m_id as id,m_jc,m_mc ','-');
                break;
        }

        $this->success($arrRet);
    }
	
	   public function upload_excel(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     2097150 ;// 设置附件上传大小  2M
        $upload->exts      =     array('xls',);// 设置附件上传类型
        $upload->rootPath  =     './Public/Uploads/'; // 设置附件上传根目录
        $upload->savePath  =     'excel/'; // 设置附件上传（子）目录
        //$upload->saveName  = '';
        $upload->autoSub = false;
        // 上传文件
        $info   =   $upload->upload();
        // var_dump($info);
        if(!$info) {// 上传错误提示错误信息
            \Response::json(\Base_Error::ERROR_DATA, $upload->getError());
        }else{// 上传成功
            //var_dump($info);
            $url = '/Public/Uploads/'. $info['upload_files']['savepath'] . $info['upload_files']['savename'];
            // \Response::json(\Base_Error::SUCCESS,  \Base_Error::getMessage(\Base_Error::SUCCESS),$url);
             // var_dump($url);
             $this->success($url);

        }
    }

    //获取班级
    public function get_class(){
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        if (empty($g_id)) {
            $this->error('请选择年级！');
        }
        $where['g_id'] = $g_id;
        if (!empty($c_id)) {
            $where['c_id'] = $c_id;
        }
        $arrRet = D('Class')->where($where)->getField('c_id as id , name as value');
        $this->success($arrRet);

    }

}