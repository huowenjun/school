<?php
namespace Common\Model;
use Common\Model\BaseModel;
class GpsAttendanceModel extends BaseModel{

	protected $_validate =array(

         array('name','require','请输入围栏名称'),
         array('name', 'checklength', '名称长度必须在2-8位之间！', 0, 'callback', 3, array(2, 8)),
	);
	
     public function checklength($str, $min, $max) {
            preg_match_all("/./u", $str, $matches);
            $len = count($matches[0]);
            if ($len < $min || $len > $max) {
                return false;
            } else {
                return true;
            }
        }
    public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0) {//GPS考勤记录
        if ($page){
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->select();
        }else{
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
        }
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }
        $Student = D('Student')->getField('imei_id,stu_name,sex,stu_no');
        $stypeInfo = C('SCHOOL_TYPE');
        $sgtypeInfo = C('SIGNIB_TYPE');
        $nowTime = time();

        $normal = 0;
        $unusual = 0;

        foreach ($ret['list'] as $key => $value) {
            //获取学生详情
            $stu_info = M('Student')
                ->cache(true)
                ->field('a_id,stu_no,stu_name')
                ->where("imei_id = '{$value['imei']}'")
                ->find();

            //获取考勤区间
            $workInfo = M('WorkRule')
                ->cache(true)
                ->field('start_time,end_time')
                ->where("a_id = '{$stu_info['a_id']}'")
                ->find();

            $ret['list'][$key]['shijianduan'] = $workInfo['start_time'] . '-' . $workInfo['end_time'];//时间段
            $beganTime = strtotime($workInfo['start_time']);
            $endTime = strtotime($workInfo['end_time']);

            if ($beganTime < $nowTime && $nowTime < $endTime) {
                $value['signin_type'] = 0;//异常
                $unusual++;
            } else {
                $value['signin_type'] = 1;//正常
                $normal++;
            }

            $ret['list'][$key]['stu_name'] = $stu_info['stu_name'];
            $ret['list'][$key]['stu_no'] = $stu_info['stu_no'];
            $ret['list'][$key]['sign_type'] = $stypeInfo[$value['sign_type']];
            $ret['list'][$key]['signin_type'] = $sgtypeInfo[$value['signin_type']];
        }
        $ret['yichang'] = $unusual;
        $ret['zhengc'] = $normal;
        return $ret;
    }
	
}