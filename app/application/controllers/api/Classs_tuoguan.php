<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/7/24
 * Time: 上午10:47
 */
class Classs_tuoguan extends MY_Controller
{
    public function log($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $student_id = $this->input->get('student_id');
        $day = $this->input->get('day');


        $this->db->where(array('class_id'=>$class_id));

        if(!empty($student_id)){
            $this->db->where(array('class_id'=>$class_id));
        }
        if(!empty($day)){
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("time > {$today_start} and time < {$today_end}");
        }else{
            $day = date("Y-m-d",time());
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("time > {$today_start} and time < {$today_end}");
        }
        $data['count'] = $this->db->count_all_results('wxt_class_tuoguan_log');


        $this->db->select("wxt_student.name as student_name,wxt_class_tuoguan_log.*,wxt_user.name as teacher_name");
        $this->db->where(array('wxt_class_tuoguan_log.class_id' => $class_id));
        if(!empty($student_id)){
            $this->db->where(array('class_id'=>$class_id));
        }
        if(!empty($day)){
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("time > {$today_start} and time < {$today_end}");
        }
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_tuoguan_log.user_id");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_tuoguan_log.student_id");
        $this->db->limit($limit,$page);
        $data['tuoguan'] = $this->db->get("wxt_class_tuoguan_log")->result_array();

        $this->output_json("ok","托管记录",$data);


    }

    public function month($class_id = 0,$month = 0)
    {


        if(empty($month)){
            $time = time();
        }else{
            $time = strtotime($month.'-01');
        }


        $start_time = mktime(0,0,1,date('m',$time),1,date('Y',$time));
        $end_time = mktime(23,59,59,date('m',$time),date('t',$time),date('Y',$time));


        $this->db->select("time");
        $this->db->where("class_id = $class_id and time > $start_time and time < $end_time");

        $tuoguan_log = $this->db->get("wxt_class_tuoguan_log")->result_array();

        $data = array();
        foreach ($tuoguan_log as $item){
            $i = date("d",$item['time']);
            $data[$i]= 'true';
        }
        $this->output_json("ok","课表日历",$data);

    }

    public function today($class_id = 0)
    {

        $this->is_my_school($class_id,'class');

        $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.phone");
        $this->db->where(array('wxt_class_student.class_id' => $class_id));
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");

        $data['student'] = $this->db->get("wxt_class_student")->result_array();

        $day = date("Y-m-d",time());
        $today_start = strtotime($day);
        $today_end = strtotime($day) + 86400;

        foreach ($data['student'] as &$item){
            $this->db->select("status,time,remark");
            $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['student_id']));
            $this->db->order_by('id','DESC');
            $item['now'] = $this->db->get("wxt_class_tuoguan_log")->row_array();


            $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['student_id'],'time > '=>$today_start,'time < '=>$today_end,'status'=>'1'));
            $item['count']['qiandao'] = $this->db->count_all_results('wxt_class_tuoguan_log');

            $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['student_id'],'time > '=>$today_start,'time < '=>$today_end,'status'=>'2'));
            $item['count']['qiantui'] = $this->db->count_all_results('wxt_class_tuoguan_log');

            $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['student_id'],'time > '=>$today_start,'time < '=>$today_end,'status'=>'3'));
            $item['count']['qingjia'] = $this->db->count_all_results('wxt_class_tuoguan_log');

        }


        $this->output_json("ok","今日记录",$data);
        
    }

    public function attence($class_id = 0 , $student_id = 0)
    {

        $this->is_my_school($class_id,'class');
        $this->is_my_school($student_id,'student');

        $this->form_validation->set_rules('status', '签到状态', 'trim|required|in_list[0,1,2,3]');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }


        $this->db->where(array('class_id'=>$class_id,'student_id'=>$student_id));
        $this->db->order_by('id','DESC');
        $student = $this->db->get("wxt_class_tuoguan_log")->row_array();

        if(empty($student) and $this->input->post('status') == '2'){
            $this->output_json('error', '未签到课程不能执行签退操作！');
        }

        if($student['status'] == '1' and $this->input->post('status') <> '2'){
            $this->output_json('error', '已签到课程请签退后再执行其他操作！');
        }

        if($student['status'] == '2' and $this->input->post('status') == '2'){
            $this->output_json('error', '已签退课程请勿重复签退！');
        }
        if($student['status'] == '3' and $this->input->post('status') == '2'){
            $this->output_json('error', '未签到课程不能执行签退操作！');
        }



        $log['class_id'] = $class_id;
        $log['student_id'] = $student_id;
        $log['time'] = time();
        $log['status'] = $this->input->post('status');
        $log['user_id'] = $this->session->user['id'];
        if($this->input->post('status') == '2'){
            $log['remark'] = ($this->input->post('status_info') == '1')?"送达学校":"已离校";
        }else{
            $log['remark'] = $this->input->post('remark');
        }


        $this->db->insert('wxt_class_tuoguan_log',$log);


        //签到成功

//        //生成动态信息
//        $feed = array(
//            "student_id" => $student_id,
//            "time" => time(),
//            "type" => "5",
//            "source" => $this->session->user['name'] . '老师',
//            "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
//            "title" => '托管状态提醒',
//            "msg" => "信息",
//            "url" => "#/Apps/ClassRecord/"
//        );
//        $this->db->insert("wxt_feed", $feed);
        //获取模版id
        $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM406074911"));
        $temp = $this->db->get('wxt_weixin_temp')->row_array();


        //获取学生信息
        $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
        $this->db->where("wxt_student_weixin.uid = {$student_id}");
        $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
        $student = $this->db->get("wxt_student_weixin")->result_array();


        //发送模版消息
        foreach ($student as $item) {
            $msg['wecha_id'] = $item['wecha_id'];
            $msg['template_id'] = $temp['temp_id'];
            if($log['status'] == '1'){
                $first = "{$item['name']}同学家长您好，{$item['name']}已安全到达托管班，请您放心!";
                $status = "签到";
            }else if($log['status'] == '2'){
                if($this->input->post('status_info') == '1'){
                    $first = "{$item['name']}同学家长您好，{$item['name']}已由托管老师送达学校，请您放心!";
                }else{
                    $first = "{$item['name']}同学家长您好，{$item['name']}已离开托管班，请您留意!";
                }
                $status = "签退";

            }
            else if($log['status'] == '3'){
                $first = "{$item['name']}同学家长您好，{$item['name']}的请假信息老师已确认!";
                $status = "请假";
            }
            $msg['first'] = $first;
            $msg['keyword1'] = $item['name'];
            $msg['keyword2'] = $status;
            $msg['keyword3'] = date('m月d日 H:i', time());
            $msg['keyword4'] = empty($log['remark'])?'暂无':$log['remark'];//内容
            $msg['remark'] = "点击查看详情。";//内容
            $msg['url'] = "http://wx.eduwxt.com/student/#/?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

            $this->load->model('queue');
            $this->queue->post($this->session->school['pigcms_token'],$msg);
            //erm_post($url, $msg);
        }

        $this->output_json('ok', '考勤信息添加成功！');

        
    }

    public function notice($class_id = 0 , $student_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($student_id,'student');

        $this->form_validation->set_rules('type', '消息类型', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        if($this->input->post('type') == 'jstz'){
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM406074911"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();


            //获取学生信息
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
            $this->db->where("wxt_student_weixin.uid = {$student_id}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
            $student = $this->db->get("wxt_student_weixin")->result_array();


            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];

                $msg['first'] = "{$item['name']}同学家长您好，{$item['name']}的托管课程已结束，请您按时前来接送!";;
                $msg['keyword1'] = $item['name'];
                $msg['keyword2'] = "接送通知";
                $msg['keyword3'] = date('m月d日 H:i', time());
                $msg['keyword4'] = "如需延时接送请提前与托管老师联系";//内容
                $msg['remark'] = "";//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }
        }


        $this->output_json('ok', '通知信息已发送！');

    }

    public function extime($class_id = 0 , $student_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($student_id,'student');

        if($this->input->method() == 'post'){
            $this->form_validation->set_rules('end_time', '到期时间', 'trim|required');
            $this->form_validation->set_rules('remark', '调课原因', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->output_json('error', validation_errors(' ', ' '));
            }
            $end_time = $this->input->post('end_time');

            if($end_time < time()){
                $this->output_json('error', "到期时间不能小与当前时间");
            }

            $this->db->where(array("class_id"=>$class_id,"student_id"=>$student_id));
            $this->db->update("wxt_class_student",array('end_time'=>$end_time));

            $log['class_id'] = $class_id;
            $log['student_id'] = $student_id;
            $log['action'] = '调整课次';
            $log['time'] = time();
            $log['detail'] = $this->input->post('remark');
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);

            $this->output_json('ok', '调整成功！',$end_time);


        }else{
            $this->db->select('start_time,end_time');
            $this->db->where(array("class_id"=>$class_id,"student_id"=>$student_id));
            $data['student'] = $this->db->get("wxt_class_student")->row_arry();

            $this->output_json('ok', '课程信息！',$data);

        }


        
    }

    /**
     * 转班
     * @param int $class_id
     * @param int $student_id
     */
    public function change($class_id = 0, $student_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($student_id,'student');

        $this->form_validation->set_rules('new_class_id', '转入班级', 'trim|required|numeric');
        $this->form_validation->set_rules('end_time', '到期时间', 'trim|required|numeric');
        $this->form_validation->set_rules('remark', '转班原因', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $this->output_json('error',validation_errors(' ',' '));
        }
        $this->db->select("name");
        $this->db->where("id = {$student_id}");
        $student = $this->db->get("wxt_student")->row_array();

        $this->db->where("id = {$class_id}");
        $class = $this->db->get("wxt_class")->row_array();
//        $this->db->where("class_id = {$class_id} and student_id = {$sid}");
//        $data['class_student'] = $this->db->get("wxt_class_student")->row_array();

        $new_class_id = $this->input->post('new_class_id');


        $this->db->where(array('campus_id'=>$class['campus_id'],'class_type'=>'1'));
        $new_class = $this->db->get("wxt_class")->row_array();

        if(empty($new_class)){
            $this->output_json('error', '不允许跨校区或跨类型转班！');
        }



        $end_time = $this->input->post('end_time');

        $this->db->where("class_id = {$new_class_id} and student_id = {$student_id}");
        if (empty($this->db->count_all_results("wxt_class_student"))) {
            $this->db->where("class_id = {$class_id} and student_id = {$student_id}");
            $this->db->update("wxt_class_student", array('class_id' => $new_class_id, 'end_time' => $end_time, 'reg_time' => time(), 'status' => '1'));

            $this->autolog("班级学员管理","将学生 {$student['name']} 从 {$class['class_name']} 班级转入 {$new_class['class_name']} ");

            $log['class_id'] = $class_id;
            $log['student_id'] = $student_id;
            $log['action'] = '转班';
            $log['time'] = time();
            $log['detail'] = "从 {$class['class_name']} 班级转入 {$new_class['class_name']} ,备注：".$this->input->post('remark');;
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);


            $this->output_json('ok', '操作成功！');

        } else {
            $this->output_json('error', '此学生在当前班级已有报名信息！');
        }
    }

    /**
     * 续费
     * @param int $sid
     * @param int $class_id
     */
    public function renewal($sid = 0,$class_id =0)
    {
        $this->is_my_school($sid,'student');
        $this->form_validation->set_rules('quantity', '需费月数', 'trim|required|numeric');
        $this->form_validation->set_rules('end_time', '到期时间', 'trim|required|numeric');
        $this->form_validation->set_rules('actual', '收费金额', 'trim|required|numeric');
        $this->form_validation->set_rules('payee_time', '收费时间', 'trim|required');
        $this->form_validation->set_rules('account', '收费账户', 'trim|required');
        $this->form_validation->set_rules('fee_mode', '收费模式', 'trim|required');

        if ($this->form_validation->run() == FALSE){

            $this->output_json('error',validation_errors(' ',' '));
        }

        $class = $this->db->where('id',$class_id)->get("wxt_class")->row_array();


        $quantity = $this->input->post('quantity');
        $actual = $this->input->post('actual');
        $end_time = $this->input->post('end_time');


        $this->db->where("class_id = $class_id and student_id = $sid");
        $student = $this->db->get("wxt_class_student")->row_array();

        $buy_quantity = $student['buy_quantity'] + $quantity;

        //获取历史订单号
        $this->db->select("ordersid");
        $this->db->where("id",$this->session->school['id']);
        $sc = $this->db->get("wxt_school")->row_array();
        //生成新订单号
        $ordersid = $sc['ordersid']+1;
        //更新历史订单号
        $this->db->where("id",$this->session->school['id']);
        $this->db->update("wxt_school",array('ordersid'=>$ordersid));
        $ordersid = date("Ymd").$this->session->school['id'].$ordersid;

        $orders['id'] = $ordersid;

        //学校ID
        $orders['school_id'] = $this->session->school['id'];
        //校区ID
        $orders['campus_id'] = $class['campus_id'];
        //学生ID
        $orders['student_id'] = $sid;
        //收费时间
        $orders['payee_time'] = $this->input->post('payee_time');
        //记录生成时间
        $orders['reg_time'] = time();
        //备注
        $orders['remark'] = $this->input->post('remark');
        //收款人
        $orders['payee'] = $this->session->user['name'];
        $orders['payee_id'] = $this->session->user['id'];
        //收款账户
        $orders['payee_account_id'] = $this->input->post('account');
        $this->db->where("id",$orders['payee_account_id']);
        $school_account = $this->db->get("wxt_school_account")->row_array();
        $orders['payee_account'] =  $school_account['account_name'];

        //开始事务
        $this->db->trans_start();

        $this->db->insert('wxt_finance_orders', $orders);

        //$orders_id = $this->db->insert_id();
        $orders_id = $ordersid;

        $recordss['school_id'] = $this->session->school['id'];
        $recordss['orders_id'] = $orders_id;
        $recordss['fee_name'] = '班级续费-' . $class['class_name'];
        $recordss['fee_mode'] = $this->input->post('fee_mode');
        $recordss['quantity'] = $quantity;
        $recordss['unit'] = '月';
        $recordss['price'] = $class['price'];
        $recordss['preferential'] = ($quantity * $class['price']) - $actual;
        $recordss['actual'] = $actual;
        $this->db->insert('wxt_finance_recordss', $recordss);

        $this->db->where("id = {$student['id']}");
        $this->db->update('wxt_class_student', array('buy_quantity' => $buy_quantity,'end_time'=>$end_time));

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            $this->output_json('error', '失败！');
        } else {

            $this->db->where("id = {$sid}");
            $student2 = $this->db->get("wxt_student")->row_array();

            //生成动态信息
            $feed = array(
                "student_id" => $sid,
                "time" => time(),
                "type" => "2",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '缴费成功通知',
                "msg" => "{$student2['name']}同学您好，您已经缴费成功。",
                "url" => "#/My/Orders/{$ordersid}"
            );
            $this->db->insert("wxt_feed", $feed);


            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM400962287"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();

            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            //获取学生信息
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
            $this->db->where("wxt_student_weixin.uid = {$sid}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
            $student = $this->db->get("wxt_student_weixin")->result_array();
            foreach ($student as $item)
            {
                $msg['template_id'] =  $temp['temp_id'];//
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['first']    = "{$item['name']}同学您好，您已续费成功。";//标题
                $msg['keyword1'] = $item['name'];//
                $msg['keyword2'] = $item['phone'];//老师
                $msg['keyword3'] = '#'.$orders_id;//时间
                $msg['keyword4'] = $orders['payee_account'];//内容
                $msg['keyword5'] = $recordss['actual'].'元';//内容
                $msg['remark'] = '点击查看详细信息。';//内容
                $msg['url'] = 'http://wx.eduwxt.com/student/#/My/Orders/'.$orders_id."?token=".$this->session->school['pigcms_token']."&wecha_id=".$item['wecha_id'];//内容

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
            }

            $log['class_id'] = $class_id;
            $log['student_id'] = $sid;
            $log['action'] = '续费';
            $log['time'] = time();
            $log['detail'] = "到期时间延长到：".date("Y-m-d",$end_time);
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);


            $this->output_json('ok', '操作成功！',array('orders_id'=>$orders_id));
        }

    }

}