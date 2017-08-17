<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/21
 * Time: 下午4:46
 */
class Workbench extends MY_Controller
{
    /**
     * 套餐信息
     */
    public function pack_info()
    {
        $this->db->select('id,name,re_time,exp_time,pack_id,campus_quota,student_quota');
        $this->db->where('id',$this->session->school['id']);
        $school = $this->db->get('wxt_school')->row_array();

        $this->db->where('id',$school['pack_id']);
        $pack_basic = $this->db->get('wxt_pack_basic')->row_array();

        $school['pv_quota'] = $pack_basic['pv_limit'];
        $school['pack_name'] = $pack_basic['pack_name'];

        $this->db->where('school_id',$this->session->school['id']);
        $school['campus_opened'] = $this->db->count_all_results('wxt_campus');

        $this->db->where('school_id',$this->session->school['id']);
        $school['student_opened'] = $this->db->count_all_results('wxt_student');

        $this->db->db_select('wxt_pigcms');

        $this->db->select('access_count');
        $this->db->where('id',$school['pack_id']);
        $school['pv_quota'] = $this->db->get('pigcms_user_group')->row_array()['access_count'];

        //php获取本周起始时间戳和结束时间戳
        $beginWeek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
        $endWeek=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));

        $this->db->select('sum(count) as count');
        $this->db->where('token',$this->session->school['pigcms_token']);
        $this->db->where('module','wap');
        $this->db->where("create_time > $beginWeek and update_time < $endWeek");
        $school['pv_opened'] = $this->db->get('pigcms_access_count')->row_array()['count'];
        $this->db->db_select('wxt_admin');

        if(empty($school['pv_opened'])){
            $school['pv_opened'] = 0;
        }

        $this->output_json("ok","套餐信息",$school);
    }

    /**
     * 校园通知
     */
    public function campus_notice()
    {
        //校园通知
        $this->db->select("wxt_notice.*,wxt_campus.name as campus_name");
        $this->db->where("wxt_notice.school_id = {$this->session->school['id']} and wxt_notice.type = 'campus'");
        $this->db->join("wxt_campus","wxt_campus.id = wxt_notice.campus_id");
        $this->db->order_by('wxt_notice.id', 'desc');
        $this->db->limit(20);  // Produces: LIMIT 10
        $data['campus_notice'] = $this->db->get("wxt_notice")->result_array();
        $this->output_json("ok","校园通知",$data);

    }



    /**
     * 今日课程
     */
    public function today_curriculum()
    {

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;


        //今日课程
        $today_start = strtotime(date('Y-m-d',time()));
        $today_end = strtotime(date('Y-m-d',time())) + 86400;

        //统计总数
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'wxt_class.school_id'=>$this->session->school['id'],
                'wxt_class.campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'wxt_class.school_id'=>$this->session->school['id']
            );
        }
        $this->db->where($where);
        $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $data['count'] = $this->db->count_all_results("wxt_class_curriculum");



        $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");
        $this->db->where($where);
        $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
        $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");
        $this->db->order_by('wxt_class_curriculum.start_time', 'asc');
        $this->db->limit($limit,$page);
        $data['today_curriculum'] = $this->db->get("wxt_class_curriculum")->result_array();
        $this->output_json("ok","今日课程",$data);

    }

    /**
     * 我的今日课程
     */
    public function my_today_curriculum()
    {

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;


        //今日课程
        $today_start = strtotime(date('Y-m-d',time()));
        $today_end = strtotime(date('Y-m-d',time())) + 86400;


        $this->db->where("wxt_class.school_id  = {$this->session->school['id']} and wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end} and (wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']})");
        $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $data['count'] = $this->db->count_all_results("wxt_class_curriculum");

        $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");



        $this->db->where("wxt_class.school_id  = {$this->session->school['id']} and wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end} and (wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']})");

        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
        $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");
        $this->db->order_by('wxt_class_curriculum.start_time', 'asc');
        $this->db->limit($limit,$page);
        $data['today_curriculum'] = $this->db->get("wxt_class_curriculum")->result_array();
        $this->output_json("ok","我的今日课程",$data);

    }


    /**
     * 预约报名
     * @param int $id
     * @param string $action
     */
    public function reservation($id = 0,$action ='')
    {
        //获取
        if($this->input->method() == 'get'){

            $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
            $page = empty($this->input->get('page'))?1:$this->input->get('page');
            $page = ($page-1)*$limit;
            $data['limit'] = $limit;

            $where = array(
                'wxt_class.school_id'=>$this->session->school['id']
            );
            $this->db->where($where);
            $this->db->where("wxt_class.staus = '2' and wxt_class_reservation.status != '2'");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_reservation.class_id");
            $data['count'] = $this->db->count_all_results("wxt_class_reservation");

            //预约报名
            $this->db->select("wxt_class_reservation.id,wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.number as student_number,wxt_student.face,wxt_student.phone as student_phone,wxt_class.class_name,wxt_class_reservation.time,wxt_class_reservation.status");
            $this->db->where($where);
            $this->db->where("wxt_class.staus = '2' and wxt_class_reservation.status != '2'");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_reservation.class_id");
            $this->db->join("wxt_student","wxt_student.id = wxt_class_reservation.student_id");
            $this->db->limit($limit,$page);
            $data['reservation'] = $this->db->get("wxt_class_reservation")->result_array();

            $this->output_json("ok","预约报名",$data);
        }else{

            //编辑
            if($action == 'edit'){
                $this->form_validation->set_rules('status', '状态', 'trim|required');
                //$this->form_validation->set_rules('remark', '备注', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error','表单信息不正确',$this->form_validation->error_array());
                }

                $this->db->where('id',$id);
                $this->db->update('wxt_class_reservation',array('status'=>$this->input->post('status')));
                $this->output_json("ok","修改成功");
                //删除
            }elseif ($action == 'del'){
                $this->db->where('id',$id);
                $this->db->delete('wxt_class_reservation');
                $this->output_json("ok","删除成功");
            }



        }

    }

    /**
     * 投诉建议
     * @param int $id
     * @param string $action
     */
    public function suggest($id = 0,$action ='')
    {
        //获取
        if($this->input->method() == 'get'){

            $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
            $page = empty($this->input->get('page'))?1:$this->input->get('page');
            $page = ($page-1)*$limit;
            $data['limit'] = $limit;


            $this->db->where("wxt_suggest.school_id = {$this->session->school['id']} ");
            if(!empty($this->input->get('status'))){
                $this->db->where("wxt_suggest.status = '{$this->input->get('status')}' ");
            }
            $data['count'] = $this->db->count_all_results("wxt_suggest");


            $this->db->select("wxt_suggest.*,wxt_student.id as student_id,wxt_student.name as student_name ,wxt_student.face as student_face");
            $this->db->where("wxt_suggest.school_id = {$this->session->school['id']} ");
            if(!empty($this->input->get('status'))){
                $this->db->where("wxt_suggest.status = '{$this->input->get('status')}' ");
            }
            $this->db->join("wxt_student","wxt_student.id = wxt_suggest.student_id");
            $this->db->order_by('wxt_suggest.id', 'desc');
            $this->db->limit($limit,$page);
            $data['suggest'] = $this->db->get("wxt_suggest")->result_array();
            $this->output_json("ok","投诉建议",$data);
        }else{

            $this->is_my_school($id,'suggest');

            //编辑
            if($action == 'reply'){
                //$this->form_validation->set_rules('status', '状态', 'trim|required');
                $this->form_validation->set_rules('reply', '答复内容', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error','表单信息不正确',$this->form_validation->error_array());
                }

                $this->db->where('id',$id);
                $this->db->update('wxt_suggest',array('reply'=>$this->input->post('reply'),'reply_time'=>time(),'reply_user'=>$this->session->user['id']));
                $this->output_json("ok","修改成功");
                //删除
            }elseif ($action == 'del'){


                $this->db->where('id',$id);
                $this->db->delete('wxt_suggest');
                $this->output_json("ok","删除成功");
            }



        }

    }

    /**
     * 请假记录
     * @param int $id
     * @param string $action
     */
    public function leave($id = 0,$action ='')
    {

        //获取
        if($this->input->method() == 'get'){

            $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
            $page = empty($this->input->get('page'))?1:$this->input->get('page');
            $page = ($page-1)*$limit;
            $data['limit'] = $limit;

            $this->db->where("wxt_class_leave.status = '0' and (wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']})");
            $this->db->join("wxt_class_curriculum","wxt_class_curriculum.id = wxt_class_leave.curriculum_id");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
            $data['count'] = $this->db->count_all_results("wxt_class_leave");


            $this->db->select("wxt_class_leave.id,wxt_student.id,student_id,wxt_student.name as student_name,wxt_student.face,wxt_class_leave.info,wxt_class_leave.status,wxt_class_leave.time,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject,wxt_class_curriculum.id as curriculum_id,wxt_class_curriculum.start_time as curriculum_start_time");

            $this->db->where("wxt_class_leave.status = '0' and (wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']})");

            $this->db->join("wxt_student","wxt_student.id = wxt_class_leave.student_id");
            $this->db->join("wxt_class_curriculum","wxt_class_curriculum.id = wxt_class_leave.curriculum_id");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
            $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
            $this->db->order_by('wxt_class_leave.time', 'asc');
            $this->db->limit($limit,$page);
            $data['leave'] = $this->db->get("wxt_class_leave")->result_array();
            $this->output_json("ok","请假记录",$data);
        }else{


//            //编辑
//            if($action == 'ratify'){
//                $this->db->where('id',$id);
//                $this->db->update('wxt_class_leave',array('status'=>'1','ratify_time'=>time(),'ratify_user'=>$this->session->user['id']));
//
//                $this->output_json("ok","批复成功");
//                //删除
//            }elseif ($action == 'del'){
//
//                $this->db->where('id',$id);
//                $this->db->delete('wxt_class_leave');
//                $this->output_json("ok","删除成功");
//            }



        }

    }


    /**
     * 今日跟进
     */
    public function tody_intention()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $today_start = strtotime(date('Y-m-d',time()));
        $today_end = strtotime(date('Y-m-d',time())) + 86400;

        $this->db->where("wxt_intention.next_user = {$this->session->user['id']} and wxt_intention.next_time >= $today_start and wxt_intention.next_time <= $today_end and wxt_intention.last_time < wxt_intention.next_time");
        $data['count'] = $this->db->count_all_results("wxt_intention");

        $this->db->select('wxt_intention.id,wxt_intention.name,wxt_intention.age,wxt_intention.phone,wxt_intention_status.name as status');
        $this->db->where("wxt_intention.next_user = {$this->session->user['id']} and wxt_intention.next_time >= $today_start and wxt_intention.next_time <= $today_end and wxt_intention.last_time < wxt_intention.next_time");
        $this->db->join('wxt_intention_status',"wxt_intention_status.id = wxt_intention.status_id",'left');
        $this->db->limit($limit,$page);
        $data['intention'] = $this->db->get("wxt_intention")->result_array();

        $this->output_json("ok", "今日跟进", $data);
    }

    /**
     * 未按时上课
     */
    public function late_curriculum()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("teacher_id = {$this->session->user['id']} and start_time < ".time()." and status = '0'");
        $data['count'] = $this->db->count_all_results("wxt_class_curriculum");
        $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");

        $this->db->where("wxt_class_curriculum.teacher_id = {$this->session->user['id']} and wxt_class_curriculum.start_time < ".time()." and wxt_class_curriculum.status = '0'");

        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
        $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");

        $this->db->order_by('wxt_class_curriculum.start_time', 'asc');
        $this->db->limit($limit,$page);
        $data['curriculum'] = $this->db->get('wxt_class_curriculum')->result_array();
        $this->output_json("ok","未按时上课",$data);
        
    }

    /**
     * 未按时下课
     */
    public function timeout_curriculum()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("teacher_id = {$this->session->user['id']} and end_time < ".time()." and status = '1'");
        $data['count'] = $this->db->count_all_results("wxt_class_curriculum");
        $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");

        $this->db->where("wxt_class_curriculum.teacher_id = {$this->session->user['id']} and wxt_class_curriculum.end_time < ".time()." and wxt_class_curriculum.status = '1'");

        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
        $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");

        $this->db->order_by('wxt_class_curriculum.start_time', 'asc');
        $this->db->limit($limit,$page);
        $data['curriculum'] = $this->db->get('wxt_class_curriculum')->result_array();
        $this->output_json("ok","未按时下课",$data);
        
    }

    /**
     * 等待点评课程
     */
    public function wait_evaluation()
    {
        //开始时间7天前
        $sql_time = time() - (60*60*24*7);

        //找出最近7天的课程
        $this->db->select('id');
        $this->db->where("teacher_id = {$this->session->user['id']} and end_time > {$sql_time} and status = '2'");
        $my_curriculum = $this->db->get("wxt_class_curriculum")->result_array();

        $wait_curriculum = array();
        //检查签到人数与已点评人数差
        foreach ($my_curriculum as $item){
            $this->db->where("curriculum_id = {$item['id']} and status = '1'");
            $attence_count = $this->db->count_all_results('wxt_class_attence');

            $this->db->where("curriculum_id = {$item['id']}");
            $evaluation_count = $this->db->count_all_results('wxt_class_evaluation');

            //导出点评人数少于签到人数的课程
            if($attence_count > $evaluation_count ){
                $wait_curriculum[]= array(
                    'id'=> $item['id'],
                    'attenct'=>$attence_count,
                    'evaluation'=>$evaluation_count,
                    'wait_evaluation'=>$attence_count - $evaluation_count
                );
            }

        }

        //查询签到人数不足的课程信息
        foreach ($wait_curriculum as &$item){
            $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");
            $this->db->where("wxt_class_curriculum.id = {$item['id']}");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
            $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
            $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
            $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
            $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");
            $item+= $this->db->get('wxt_class_curriculum')->row_array();
        }

        $data['count'] = count($wait_curriculum);
        $data['curriculum'] = $wait_curriculum;
        $this->output_json("ok","等待点评课程",$data);

        
    }

    /**
     * 遗漏考勤的课程
     */
    public function wait_attence()
    {
        //开始时间7天前
        $sql_time = time() - (60*60*24*7);

        //找出最近7天的课程
        $this->db->select('id,class_id');
        $this->db->where("teacher_id = {$this->session->user['id']} and end_time > {$sql_time} and status = '2'");
        $my_curriculum = $this->db->get("wxt_class_curriculum")->result_array();

        $wait_curriculum = array();
        //检查签到人数与班级总人数
        foreach ($my_curriculum as $item){

            $this->db->where("class_id = {$item['class_id']} and status = '1'");
             $class_student_count = $this->db->count_all_results('wxt_class_student');

            $this->db->where("curriculum_id = {$item['id']} and status in('1','2','3')");
             $attence_count = $this->db->count_all_results('wxt_class_attence');

            //导出点评人数少于签到人数的课程
            if($class_student_count > $attence_count ){
                $wait_curriculum[]= array(
                    'id'=> $item['id'],
                    'attenct'=>$attence_count,
                    'class_student'=>$class_student_count,
                    'wait_attence'=>$class_student_count - $attence_count
                );
            }

        }

        //查询考勤人数不足的课程信息
        foreach ($wait_curriculum as &$item){
            $this->db->select("wxt_class_curriculum.id as curriculum_id,wxt_class.id as class_id,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_classroom.room_name,wxt_user.name as curriculum_teacher,class_teacher.name as class_teacher,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time");
            $this->db->where("wxt_class_curriculum.id = {$item['id']}");
            $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
            $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
            $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
            $this->db->join("wxt_user class_teacher","class_teacher.id = wxt_class.teacher_id");
            $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");
            $item+= $this->db->get('wxt_class_curriculum')->row_array();
        }

        $data['count'] = count($wait_curriculum);
        $data['curriculum'] = $wait_curriculum;
        $this->output_json("ok","遗漏考勤的课程",$data);
        
    }

    /**
     * 已延误的跟进计划
     */
    public function wait_track()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("next_user = {$this->session->user['id']} and next_time < ".time()." and next_time > last_time");
        $data['count'] = $this->db->count_all_results("wxt_intention");

        $this->db->select('wxt_intention.id,wxt_intention.name,wxt_intention.age,wxt_intention.phone,wxt_intention_status.name as status');
        $this->db->where("wxt_intention.next_user = {$this->session->user['id']} and wxt_intention.next_time < ".time()." and wxt_intention.next_time > wxt_intention.last_time");
        $this->db->join('wxt_intention_status',"wxt_intention_status.id = wxt_intention.status_id");
        $this->db->order_by('wxt_intention.next_time', 'asc');
        $this->db->limit($limit,$page);
        $data['intention'] = $this->db->get("wxt_intention")->result_array();
        $this->output_json("ok", "已延误的跟进计划", $data);
        
    }

    /**
     * 新意向学员
     */
    public function new_intention()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("wxt_gtd_new_intention.user_id = {$this->session->user['id']} and wxt_gtd_new_intention.status = '0'");
        $this->db->join('wxt_intention',"wxt_intention.id = wxt_gtd_new_intention.intention_id");
        $data['count'] = $this->db->count_all_results("wxt_gtd_new_intention");

        $this->db->select('wxt_intention.id,wxt_intention.name,wxt_intention.age,wxt_intention.phone,wxt_intention_status.name as status_name,wxt_user.name as sender');
        $this->db->where("wxt_gtd_new_intention.user_id = {$this->session->user['id']} and wxt_gtd_new_intention.status = '0'");
        $this->db->join('wxt_intention',"wxt_intention.id = wxt_gtd_new_intention.intention_id");
        $this->db->join('wxt_intention_status',"wxt_intention_status.id = wxt_intention.status_id",'left');
        $this->db->join('wxt_user',"wxt_user.id = wxt_gtd_new_intention.sender",'left');

        $this->db->order_by('wxt_gtd_new_intention.id', 'asc');
        $this->db->limit($limit,$page);
        $data['intention'] = $this->db->get("wxt_gtd_new_intention")->result_array();
        $this->output_json("ok", "新意向学员", $data);

    }

    /**
     * 新跟进动态
     */
    public function new_intention_log()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("user_id = {$this->session->user['id']} and is_view = '0'");
        $data['count'] = $this->db->count_all_results("wxt_gtd_intention_log");

        $this->db->select('wxt_intention.id,wxt_intention.name,wxt_intention.gw_id,wxt_intention.zy_id,wxt_user.id as teacher_id,wxt_user.name as teacher_name,wxt_intention_log.time,wxt_intention_log.content');
        $this->db->where("wxt_gtd_intention_log.user_id = {$this->session->user['id']} and wxt_gtd_intention_log.is_view = '0'");
        $this->db->join('wxt_intention',"wxt_intention.id = wxt_gtd_intention_log.intention_id");
        $this->db->join('wxt_intention_log',"wxt_intention_log.id = wxt_gtd_intention_log.log_id");
        $this->db->join('wxt_user',"wxt_user.id = wxt_intention_log.user_id");
        $this->db->order_by('wxt_gtd_intention_log.id', 'asc');
        $this->db->limit($limit,$page);
        $data['intention'] = $this->db->get("wxt_gtd_intention_log")->result_array();
        $this->output_json("ok", "新跟进动态", $data);
    }


    /**
     * 新回评
     */
    public function new_evaluation()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("teacher_id = {$this->session->user['id']} and status = '2' and is_view = '0'");
        $data['count'] = $this->db->count_all_results("wxt_class_evaluation");

        $this->db->select('wxt_class_evaluation.id as evaluation_id,wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.face as student_face,wxt_class.class_name,wxt_class_evaluation.reply_time,wxt_class_evaluation.reply_info,wxt_class_evaluation.teacher_score,wxt_subject.subject,wxt_class_curriculum.start_time as curriculum_start_time');
        $this->db->where("wxt_class_evaluation.teacher_id = {$this->session->user['id']} and wxt_class_evaluation.status = '2' and wxt_class_evaluation.is_view = '0'");
        $this->db->join('wxt_class_curriculum',"wxt_class_curriculum.id = wxt_class_evaluation.curriculum_id");
        $this->db->join('wxt_class',"wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join('wxt_student',"wxt_student.id = wxt_class_evaluation.student_id");
        $this->db->join('wxt_subject',"wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->order_by('wxt_class_evaluation.id', 'asc');
        $this->db->limit($limit,$page);
        $data['intention'] = $this->db->get("wxt_class_evaluation")->result_array();
        $this->output_json("ok", "新回评", $data);

    }

    /**
     * 修改学生回浏览状态
     * @param $id
     */
    public function evaluation_viwe($id)
    {
        $this->db->where('id',$id);
        $this->db->update('wxt_class_evaluation',array('is_view'=>'1'));
        $this->output_json("ok", "评价已查看");
        
    }


    

}