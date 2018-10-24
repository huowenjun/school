<?php
namespace Common\Model;
use Common\Model\BaseModel;
class OnlineQuModel extends BaseModel{

	protected $_validate =array(
	    array('title','require','请输入提问标题！'),
//	    array('content','require','请输入提问内容！'),
	    array('form_user','require','请选择接收老师！'),
	);
	
	public function queryListEXForApp($type,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
       
        $sqlQuery = self::alias('qst')->field('u_send.name as sender_name,u_send.sex as sender_sex,u_form.name as receiver_name,u_form.sex as receiver_sex,'
                    .'gr.name as g_name,cl.name as c_name,qst.id,qst.title,qst.content,qst.create_time')
                    ->join('sch_grade gr on qst.g_id = gr.g_id')
                    ->join('sch_class cl on qst.c_id = cl.c_id')
                    ->join('think_user u_send on u_send.user_id = qst.send_user')
                    ->join('think_user u_form on u_form.user_id = qst.form_user');

        if($type==4){
            //家长
            $sqlQuery = $sqlQuery->join('sch_teacher tch on tch.u_id = qst.form_user');
        }

        $ret['list'] = $sqlQuery->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
    
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }

        // var_dump(self::getLastSql());die();

        return $ret;
    }

    public function queryListEX($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
        if ($page){ 
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }else{
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }
    
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }

        return $ret;
    }


    public function queryDetialByQid($condition){
        // $condition['qst.id']=$q_id;
        //gr.name as g_name,cl.name as c_name,
        $ret = self::alias('qst')->field(''
            .'qst.id,qst.send_user as sender_id,qst.form_user as receiver_id,qst.title,qst.content,qst.create_time')
            // ->join('sch_grade gr on qst.g_id = gr.g_id')
            // ->join('sch_class cl on qst.c_id = cl.c_id')
            ->where($condition)->select();
        //var_dump(self::getLastSql());die();
        
        return $ret;
    }
	
}