<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/31
 * Time: 上午11:41
 */
class Classs_curriculum extends MY_Controller
{
    /**
     * 班级排课
     * @param int $class_id
     */
    public function index($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $type = $this->input->get('type');
        if($type == 'wait'){
            $this->db->where("status <> '2'");
        }elseif ($type == 'end'){
            $this->db->where("status = '2'");
        }

        $this->db->where(array('class_id'=>$class_id));

        $day = $this->input->get('day');
        if(!empty($day)){
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        }


        $data['count'] = $this->db->count_all_results('wxt_class_curriculum');


        $this->db->select('wxt_class_curriculum.id,wxt_subject.subject,wxt_user.name as teacher_name,wxt_classroom.room_name,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time,wxt_class_curriculum.notification');

        $this->db->where("wxt_class_curriculum.class_id = {$class_id} ");

        if(!empty($day)){
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        }

        if($type == 'wait'){
            $this->db->where("wxt_class_curriculum.status <> '2'");
        }elseif ($type == 'end'){
            $this->db->where("wxt_class_curriculum.status = '2'");
        }

        $this->db->join('wxt_subject', 'wxt_subject.id = wxt_class_curriculum.subject_id', 'left');
        $this->db->join('wxt_user', 'wxt_user.id = wxt_class_curriculum.teacher_id', 'left');
        $this->db->join('wxt_classroom', 'wxt_classroom.id = wxt_class_curriculum.room_id', 'left');

        if($type == 'wait'){
            $this->db->order_by('wxt_class_curriculum.start_time', 'ase');
        }elseif ($type == 'end'){
            $this->db->order_by('wxt_class_curriculum.start_time', 'desc');
        }else{
            $this->db->order_by('wxt_class_curriculum.start_time', 'ase');
        }

        $this->db->limit($limit,$page);
        $data['curriculum'] = $this->db->get("wxt_class_curriculum")->result_array();

        $this->output_json("ok","班级排课",$data);
        
    }


    /**
     * 检查月份课程
     * @param int $class_id 班级id
     * @param int $month 月份时间戳
     */
    public function month($class_id = 0,$month = 0)
    {


        if(empty($month)){
            $time = time();
        }else{
            $time = strtotime($month.'-01');
        }


        $start_time = mktime(0,0,1,date('m',$time),1,date('Y',$time));
        $end_time = mktime(23,59,59,date('m',$time),date('t',$time),date('Y',$time));


        $this->db->select("start_time");
        $this->db->where("class_id = $class_id and start_time > $start_time and end_time < $end_time");
        $type = $this->input->get('type');
        if($type == 'wait'){
            $this->db->where("status <> '2'");
        }elseif ($type == 'end'){
            $this->db->where("status = '2'");
        }
        $curriculum = $this->db->get("wxt_class_curriculum")->result_array();

        $data = array();
        foreach ($curriculum as $item){
            $i = date("d",$item['start_time']);
            $data[$i]= 'true';
        }
        $this->output_json("ok","课表日历",$data);

    }

    /**
     * 排课统计
     * @param int $class_id
     */
    public function count($class_id = 0)
    {
        $this->is_my_school($class_id,'class');

        //待上课程
        $this->db->where(array('class_id'=>$class_id,'status <>'=>'2'));
        $data['wait'] = $this->db->count_all_results('wxt_class_curriculum');

        //已上课程
        $this->db->where(array('class_id'=>$class_id,'status'=>'2'));
        $data['end'] = $this->db->count_all_results('wxt_class_curriculum');

        //全部课程
        $this->is_my_school($class_id,'class');
        $this->db->where(array('class_id'=>$class_id));
        $data['count'] = $this->db->count_all_results('wxt_class_curriculum');

        $this->output_json("ok","排课统计",$data);


    }

    /**
     * 课程详情
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function view($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');

        $this->db->select("wxt_class_curriculum.*,wxt_subject.subject as subject_name,wxt_user.name as teacher_name,wxt_classroom.room_name");
        $this->db->where("wxt_class_curriculum.id = {$curriculum_id} ");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_curriculum.teacher_id");
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_class_curriculum.room_id");

        $data['curriculum'] = $this->db->get("wxt_class_curriculum")->row_array();
        $this->output_json("ok","课程详情",$data);

    }

    /**
     * 添加排课
     * @param int $class_id
     */
    public function add($class_id = 0)
    {
        $this->is_my_school($class_id,'class');

        $this->form_validation->set_rules('subject_id', '课程', 'trim|required');
        $this->form_validation->set_rules('teacher_id', '代课老师', 'trim|required');
        $this->form_validation->set_rules('room_id', '上课教室', 'trim|required');
        $this->form_validation->set_rules('start_time', '上课时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '下课时间', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $curriculum = array(
            'class_id'=> $class_id,
            'school_id'=> $this->session->school['id'],
            'subject_id' => $this->input->post('subject_id'),
            'teacher_id' => $this->input->post('teacher_id'),
            'room_id' => $this->input->post('room_id'),
            'start_time' => $this->input->post('start_time'),
            'end_time' => $this->input->post('end_time')
        );

        $this->db->insert("wxt_class_curriculum", $curriculum);

        $this->output_json('ok', '课程信息已添加!');

    }

    /**
     * 检测排课冲突
     * @param $class_id
     */
    public function is_conflict($class_id)
    {
        $this->is_my_school($class_id,'class');

        $this->form_validation->set_rules('subject_id', '课程', 'trim|required');
        $this->form_validation->set_rules('teacher_id', '代课老师', 'trim|required');
        $this->form_validation->set_rules('room_id', '上课教室', 'trim|required');
        $this->form_validation->set_rules('start_time', '上课时间', 'trim|required');
        $this->form_validation->set_rules('end_time', '下课时间', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $curriculum = array(
            'class_id'=> $class_id,
            'school_id'=> $this->session->school['id'],
            'subject_id' => $this->input->post('subject_id'),
            'teacher_id' => $this->input->post('teacher_id'),
            'room_id' => $this->input->post('room_id'),
            'start_time' => $this->input->post('start_time'),
            'end_time' => $this->input->post('end_time')
        );


        $this->db->where("class_id = {$class_id}");
        $this->db->where("((start_time >= {$curriculum['start_time']} and start_time <=  {$curriculum['end_time']}) or (end_time >= {$curriculum['start_time']} and end_time <=  {$curriculum['end_time']}) or (start_time <= {$curriculum['start_time']} and end_time >=  {$curriculum['end_time']}))");
        if($this->db->count_all_results("wxt_class_curriculum") > 0){

            $this->output_json('error','当前课程与其他课程时间冲突',array('type'=>'subject'));
        }

        $this->db->where("room_id = {$curriculum['room_id']} and ((start_time >= {$curriculum['start_time']} and start_time <=  {$curriculum['end_time']}) or (end_time >= {$curriculum['start_time']} and end_time <=  {$curriculum['end_time']}) or (start_time <= {$curriculum['start_time']} and end_time >=  {$curriculum['end_time']}))");
        $this->db->where("school_id = {$this->session->school['id']}");
        if($this->db->count_all_results("wxt_class_curriculum") > 0){
            $this->output_json('error','当前教室与其他课程时间冲突',array('type'=>'room'));
        }


        $this->db->where("teacher_id = {$curriculum['teacher_id']} and ((start_time >= {$curriculum['start_time']} and start_time <=  {$curriculum['end_time']}) or (end_time >= {$curriculum['start_time']} and end_time <=  {$curriculum['end_time']}) or (start_time <= {$curriculum['start_time']} and end_time >=  {$curriculum['end_time']}))");
        $this->db->where("school_id = {$this->session->school['id']}");
        if($this->db->count_all_results("wxt_class_curriculum") > 0){
            $this->output_json('error','当前代课老师与其他课程时间冲突',array('type'=>'teacher'));
        }

        $this->output_json('ok', '当前课程与其他课程不冲突!');
        
    }

    /**
     * 调整课程
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function edit($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');

        $this->form_validation->set_rules('subject_id', '课程', 'trim|required');
        $this->form_validation->set_rules('teacher_id', '代课老师', 'trim|required');
        $this->form_validation->set_rules('room_id', '上课教室', 'trim|required');
        $this->form_validation->set_rules('sdate', '上课日期', 'trim|required');
        $this->form_validation->set_rules('time', '上课时间', 'trim|required');
        $this->form_validation->set_rules('sc', '时长', 'trim|required');
        $this->form_validation->set_rules('change_info', '调课原因', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        } else {

            $curriculum = array(
                'subject_id' => $this->input->post('subject_id'),
                'teacher_id' => $this->input->post('teacher_id'),
                'room_id' => $this->input->post('room_id'),
                'start_time' => strtotime($this->input->post('sdate') . ' ' . $this->input->post('time')),
                'end_time' => strtotime($this->input->post('sdate') . $this->input->post('time')) + ($this->input->post('sc') * 60),
                'is_change' => '1',
                'change_info' => $this->input->post('change_info')
            );


            $this->db->where("id = {$curriculum_id}");
            $this->db->update("wxt_class_curriculum", $curriculum);


            //载入相关model

            $this->db->select('wxt_class_curriculum.*,wxt_subject.subject,wxt_user.name as teacher_name,wxt_classroom.room_name');

            $this->db->where("wxt_class_curriculum.id = {$curriculum_id}");

            $this->db->join('wxt_subject', 'wxt_subject.id = wxt_class_curriculum.subject_id', 'left');
            $this->db->join('wxt_user', 'wxt_user.id = wxt_class_curriculum.teacher_id', 'left');
            $this->db->join('wxt_classroom', 'wxt_classroom.id = wxt_class_curriculum.room_id', 'left');

            $curriculum = $this->db->get('wxt_class_curriculum')->row_array();

            //生成动态信息
            $feed = array(
                "class_id" => $curriculum['class_id'],
                "time" => time(),
                "type" => "14",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '调课通知',
                "msg" => "您的{$curriculum['subject']}上课时间有所调整，请及时查看。",
                "url" => "#/Apps/ClassRecord/{$curriculum_id}"
            );
            $this->db->insert("wxt_feed", $feed);

            //if ($curriculum['notification'] == '1') {
            //获取班级信息
            $this->db->where('id',$curriculum['class_id']);
            $classs = $this->db->get('wxt_class')->row_array();

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM205990150"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();

            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
            $this->db->where("wxt_class_student.class_id = {$curriculum['class_id']}");
            $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_class_student.student_id");
            $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");

            //获取班级学生
            $student = $this->db->get("wxt_class_student")->result_array();

            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "{$item['name']}同学，您的{$curriculum['subject']}上课时间有所调整，请及时查看。";//标题
                $msg['keyword1'] = $classs['class_name'];//班级
                $msg['keyword2'] = $curriculum['change_info'];//
                $msg['keyword3'] = date('m月d日 H:i', $curriculum['start_time']);//时间
                $msg['remark'] = '具体内容请点击查看通知详情。';//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/ClassRecord/{$curriculum_id}?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }

            $this->output_json('ok', '课程信息已修改!');

        }

    }

    /**
     * 删除排课
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function del($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');


        $curriculum = $this->db->where("id = $curriculum_id")->get("wxt_class_curriculum")->row_array();

        if($curriculum['status'] == '0'){
            $this->db->where("id = $curriculum_id")->delete("wxt_class_curriculum");
            $this->output_json('ok', '排课信息已被删除!');
        }else{
            $this->output_json('error', '当前课程已开课不能删除!');
        }

    }


    /**
     * 课程状态调整
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function status($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');


        $action = $this->input->post("status");
        $curriculum = $this->db->where("id = $curriculum_id")->get("wxt_class_curriculum")->row_array();

        //上课
        if ($action == 'start') {

            //检查当前是否有其他课程在上课
            $this->db->where("class_id = {$class_id} and status = '1'");
            if ($this->db->count_all_results("wxt_class_curriculum") == '0') {

                $curriculum_time = $curriculum['end_time'] - $curriculum['start_time'];
                $this->db->where("id = {$curriculum_id}");
                $this->db->update("wxt_class_curriculum", array('status' => '1', 'start_time' => time(),'end_time'=>time() + $curriculum_time));
                $this->output_json('ok', '课程状态已修改为上课中!');
            } else {
                $this->output_json('error', '您的班级有其他课程还未结束!');
            }

            //下课
        } elseif ($action == 'end') {
            if ($curriculum['status'] == '1') {
                $this->db->where("id = {$curriculum_id}");
                $this->db->update("wxt_class_curriculum", array('status' => '2', 'end_time' => time()));

                $this->db->select('wxt_class_curriculum.*,wxt_subject.subject,wxt_user.name as teacher_name,wxt_classroom.room_name');
                $this->db->where("wxt_class_curriculum.id = {$curriculum_id}");
                $this->db->join('wxt_subject', 'wxt_subject.id = wxt_class_curriculum.subject_id', 'left');
                $this->db->join('wxt_user', 'wxt_user.id = wxt_class_curriculum.teacher_id', 'left');
                $this->db->join('wxt_classroom', 'wxt_classroom.id = wxt_class_curriculum.room_id', 'left');

                $curriculum = $this->db->get('wxt_class_curriculum')->row_array();

                //生成动态信息
                $feed = array(
                    "class_id" => $curriculum['class_id'],
                    "time" => time(),
                    "type" => "6",
                    "source" => $this->session->user['name'] . '老师',
                    "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                    "title" => '下课提醒',
                    "msg" => "本次的{$curriculum['subject']}课程已结束，点击查看详情。",
                    "url" => "#/Apps/ClassRecord/{$curriculum_id}"
                );
                $this->db->insert("wxt_feed", $feed);


                //获取模版id
                $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM406387705"));
                $temp = $this->db->get('wxt_weixin_temp')->row_array();

                //api 地址
                $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

                $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
                $this->db->where("wxt_class_student.class_id = {$curriculum['class_id']} and wxt_class_student.status = '1'");
                $this->db->where("wxt_class_attence.status = '1'");
                $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_class_student.student_id");
                $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
                $this->db->join("wxt_class_attence","wxt_class_attence.student_id = wxt_class_student.student_id and wxt_class_attence.curriculum_id = {$curriculum['id']}");

                //获取班级学生
                $student = $this->db->get("wxt_class_student")->result_array();

                //发送模版消息
                foreach ($student as $item) {
                    $msg['wecha_id'] = $item['wecha_id'];
                    $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                    $msg['first'] = "尊敬的{$item['name']}同学家长您好，您的孩子本次课程已经下课，请您留意孩子回家时间!";//标题
                    $msg['keyword1'] = $curriculum['subject'];//课程
                    $msg['keyword2'] = date('m月d日 H:i', time());//时间
                    $msg['remark'] = "如有疑问请联系{$curriculum['teacher_name']}老师，点击查看详情。";//内容
                    $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/ClassRecord/{$curriculum['id']}?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

                    $this->load->model('queue');
                    $this->queue->post($this->session->school['pigcms_token'],$msg);
                    //erm_post($url, $msg);
                }

                $this->output_json('ok', '课程状态已修改为已完课!');
            } else {
                $this->output_json('error', '您提交的数据有误!');
            }

        } else {
            $this->output_json('error', '您提交的数据有误!');
        }

    }

    /**
     * 课程考勤状态
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function attence($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');


        $this->db->where("id = {$curriculum_id}");
        $attence['curriculum'] = $this->db->get("wxt_class_curriculum")->row_array();


        $this->db->select("wxt_student.id as sid,wxt_student.name,wxt_student.number,wxt_student.phone,wxt_student.face,wxt_class_attence.status as attence_status,wxt_class_attence.is_xk,wxt_class_attence.time as attence_time,wxt_class_leave.info as leave_info,wxt_class_leave.time as leave_time");
        $this->db->where("wxt_class_student.class_id = {$class_id} ");
        $this->db->where("wxt_class_student.status = '1' ");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
        $this->db->join("wxt_class_attence", "wxt_class_attence.student_id = wxt_class_student.student_id and wxt_class_attence.curriculum_id={$curriculum_id}", 'left');
        $this->db->join("wxt_class_leave", "wxt_class_leave.student_id = wxt_class_student.student_id and wxt_class_leave.curriculum_id={$curriculum_id}", 'left');
        $this->db->order_by('wxt_class_attence.status', 'desc');
        $this->db->order_by('wxt_class_attence.time', 'desc');

        //获取班级学生
        $data['student'] = $this->db->get("wxt_class_student")->result_array();
        $this->output_json('ok', 'ok!',$data);

    }

    /**
     * 学生签到
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function student_attence($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');

        $this->form_validation->set_rules('student_id', '学生', 'trim|required');
        $this->form_validation->set_rules('type', '签到状态', 'trim|required|in_list[0,1,2,3]');
        $this->form_validation->set_rules('xk', '是否消课', 'trim|required|in_list[0,1]');


        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }
        $student_id = $this->input->post('student_id');
        $type = $this->input->post('type');
        $xk = $this->input->post('xk');

        $class = $this->db->where('id',$class_id)->get('wxt_class')->row_array();

        if($class['fee_method'] == 'frequency'){

            $this->db->select('buy_quantity,status');
            $this->db->where(array('student_id' =>$student_id,'class_id'=>$class_id));
            $student_info = $this->db->get('wxt_class_student')->row_array();

            $this->db->where("class_id = {$class_id} and student_id = {$student_id} and is_xk = '1'");
            $student_info['kx'] = $this->db->count_all_results("wxt_class_attence");

            if($student_info['kx'] >= $student_info['buy_quantity']){
                $this->output_json('error', '已无可用课时！');
            }


        }

        //获取课程信息
        $this->db->select('wxt_class_curriculum.*,wxt_subject.subject,wxt_user.name as teacher_name,wxt_classroom.room_name');
        $this->db->where("wxt_class_curriculum.id = {$curriculum_id}");

        $this->db->join('wxt_subject', 'wxt_subject.id = wxt_class_curriculum.subject_id', 'left');
        $this->db->join('wxt_user', 'wxt_user.id = wxt_class_curriculum.teacher_id', 'left');
        $this->db->join('wxt_classroom', 'wxt_classroom.id = wxt_class_curriculum.room_id', 'left');

        $curriculum = $this->db->get('wxt_class_curriculum')->row_array();


        $attence = $this->db->where("curriculum_id = {$curriculum_id} and student_id={$student_id}")
            ->get("wxt_class_attence")
            ->row_array();


        //没有考勤记录的话创建一个新的记录
        if (empty($attence)) {
            $this->db->insert("wxt_class_attence", array(
                'school_id' => $curriculum['school_id'],
                'class_id' => $curriculum['class_id'],
                'curriculum_id' => $curriculum_id,
                'student_id' => $student_id,
                'status' => $type,
                "time" => time(),
                'is_xk' => $xk
            ));

            //有记录的话更新状态
        } else {
            $this->db->where("curriculum_id = {$curriculum_id} and student_id={$student_id}")
                ->update("wxt_class_attence", array(
                    'status' => $type,
                    'is_xk' => $xk,
                    "time" => time()
                ));
        }

        if ($type == '2') {
            $leave = $this->db->where("curriculum_id = {$curriculum_id} and student_id={$student_id}")->get("wxt_class_leave")->row_array();
            if (empty($leave)) {
                $this->db->insert("wxt_class_leave", array(
                    'status' => '1',
                    'ratify_time' => time(),
                    'time' => time(),
                    'curriculum_id' => $curriculum_id,
                    'student_id' => $student_id,
                    'info' => $this->session->user['name'] . '老师代操作'
                ));

            } else {
                $this->db->where("curriculum_id = {$curriculum_id} and student_id = {$student_id}");
                $this->db->update("wxt_class_leave", array(
                    'status' => '1',
                    'ratify_time' => time()
                ));
            }


        }


        if ($type == '1') {
            //签到成功

            //生成动态信息
            $feed = array(
                "student_id" => $student_id,
                "time" => time(),
                "type" => "5",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '签到确认提醒',
                "msg" => "今天的{$curriculum['subject']}课程您已签到，点击查看详情。",
                "url" => "#/Apps/ClassRecord/{$curriculum_id}"
            );
            $this->db->insert("wxt_feed", $feed);
            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM206165014"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();
            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            //获取学生信息
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
            $this->db->where("wxt_student_weixin.uid = {$student_id}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
            $student = $this->db->get("wxt_student_weixin")->result_array();


            //发送模版消息
            foreach ($student as $item2) {
                $msg['wecha_id'] = $item2['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "尊敬的{$item2['name']}同学家长您好，您的孩子{$item2['name']}已经到达学校并开始上课，请您放心!";//尊敬的(学生姓名)同学家长您好，
                $msg['keyword1'] = $item2['name'];
                $msg['keyword2'] = date('Y-m-d H:i:s', time());
                $msg['keyword3'] = $curriculum['subject'];
                $msg['keyword4'] = $curriculum['teacher_name'];//内容
                $msg['remark'] = "如有疑问请联系{$curriculum['teacher_name']}老师，点击查看详情。";//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/ClassRecord/{$curriculum_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item2['wecha_id']}";

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }

        } elseif ($type == '2') {
            //请假成功
            //生成动态信息
            $feed = array(
                "student_id" => $student_id,
                "time" => time(),
                "type" => "15",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '请假反馈通知',
                "msg" => "{$this->session->user['name']}老师已经同意您的请假申请。",
                "url" => "#/Apps/ClassRecord/{$curriculum_id}"
            );
            $this->db->insert("wxt_feed", $feed);

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM202286373"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();
            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            //获取学生信息
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
            $this->db->where("wxt_student_weixin.uid = {$student_id}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
            $student = $this->db->get("wxt_student_weixin")->result_array();

            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "{$item['name']}同学您好，{$this->session->user['name']}老师已经同意您的请假申请。";
                $msg['keyword1'] = $this->session->user['name'];
                $msg['keyword2'] = $curriculum['subject'];
                $msg['keyword3'] = $curriculum['teacher_name'];
                $msg['keyword4'] = date('Y-m-d H:i:s', time());//内容
                $msg['remark'] = "如有疑问请联系{$curriculum['teacher_name']}老师，点击查看详情。";//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/ClassRecord/{$curriculum_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }
        }

        $this->output_json('ok', '考勤记录已更新');

    }

    /**
     * 课堂点评
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function evaluation($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');


        $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.face,wxt_class_attence.status as attence_status,wxt_class_evaluation.info as ev_info,wxt_class_evaluation.jl as ev_jl,wxt_class_evaluation.td as ev_td,wxt_class_evaluation.zs as ev_zs,wxt_class_evaluation.time,wxt_user.name as teacher_name");
        $this->db->where("wxt_class_student.class_id = {$class_id} and wxt_class_attence.status = '1'");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");

        $this->db->join("wxt_class_attence", "wxt_class_attence.student_id = wxt_class_student.student_id and wxt_class_attence.curriculum_id={$curriculum_id}", 'left');
        $this->db->join("wxt_class_evaluation", "wxt_class_evaluation.curriculum_id = {$curriculum_id} and wxt_class_evaluation.student_id = wxt_class_student.student_id", 'left');
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_evaluation.teacher_id",'left');
        $this->db->order_by('wxt_class_evaluation.id', 'asc');

        //获取班级学生
        $data['student'] = $this->db->get("wxt_class_student")->result_array();

        $this->output_json('ok', '课堂点评',$data);

    }

    /**
     * 点评学生
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function evaluation_add($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');

        $this->form_validation->set_rules('sid', '学生id', 'trim|required');
        $this->form_validation->set_rules('ev_jl', '课堂纪律', 'trim|required|less_than_equal_to[5]');
        $this->form_validation->set_rules('ev_td', '学习态度', 'trim|required|less_than_equal_to[5]');
        $this->form_validation->set_rules('ev_zs', '知识掌握', 'trim|required|less_than_equal_to[5]');
        $this->form_validation->set_rules('ev_info', '点评详情', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        } else {

            $this->db->where("curriculum_id = {$curriculum_id} and student_id = {$this->input->post('sid')}");
            if ($this->db->count_all_results("wxt_class_evaluation") > 0) {
                $this->output_json('error', '当前学生已点评请勿重复点评!');
            }


            $ev['curriculum_id'] = $curriculum_id;
            $ev['student_id'] = $this->input->post('sid');
            $ev['jl'] = $this->input->post('ev_jl');
            $ev['td'] = $this->input->post('ev_td');
            $ev['zs'] = $this->input->post('ev_zs');
            $ev['info'] = $this->input->post('ev_info');
            $ev['attached'] = $this->input->post('attached');
            $ev['time'] = time();
            $ev['teacher_id'] = $this->session->user['id'];

            if ($this->db->insert("wxt_class_evaluation", $ev)) {
                $evaluation_id = $this->db->insert_id();

                //点评成功 发送模版消息

                //获取课程信息

                $this->db->select('wxt_class_curriculum.*,wxt_subject.subject,wxt_user.name as teacher_name,wxt_class.class_name');
                $this->db->where("wxt_class_curriculum.id = {$curriculum_id}");

                $this->db->join('wxt_subject', 'wxt_subject.id = wxt_class_curriculum.subject_id', 'left');
                $this->db->join('wxt_user', 'wxt_user.id = wxt_class_curriculum.teacher_id', 'left');
                $this->db->join('wxt_class', 'wxt_class.id = wxt_class_curriculum.class_id', 'left');

                $curriculum = $this->db->get('wxt_class_curriculum')->row_array();

                //生成动态信息
                $feed = array(
                    "student_id" => $ev['student_id'],
                    "time" => time(),
                    "type" => "9",
                    "source" => $this->session->user['name'] . '老师',
                    "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                    "title" => '课后点评提醒',
                    "msg" => "您所上的{$curriculum['class_name']}-{$curriculum['subject']}老师给了您新的课堂评价，请及时查看!",
                    "url" => "#/Apps/Evaluation/view/{$evaluation_id}"
                );
                $this->db->insert("wxt_feed", $feed);


                //获取模版id
                $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM206165018"));
                $temp = $this->db->get('wxt_weixin_temp')->row_array();

                //api 地址
                $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

                //获取学生信息
                $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
                $this->db->where("wxt_student_weixin.uid = {$ev['student_id']}");
                $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
                $student = $this->db->get("wxt_student_weixin")->result_array();

                //发送模版消息
                foreach ($student as $item) {
                    $msg['wecha_id'] = $item['wecha_id'];
                    $msg['template_id'] = $temp['temp_id'];//
                    $msg['first'] = "{$item['name']}同学您好，您所上的{$curriculum['class_name']}老师给了您新的课堂评价，请及时查看!";//(学生姓名)同学您好，您所上的(班级名称)老师给了您新的课堂评价，请及时查看!
                    $msg['keyword1'] = $item['name'];
                    $msg['keyword2'] = $curriculum['subject'];
                    $msg['keyword3'] = $curriculum['teacher_name'] . "\n上课时间：" . date('m-d H:s', $curriculum['start_time']);
                    $msg['remark'] = "点击查看详情。";//内容
                    $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Evaluation/view/{$evaluation_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

                    $this->load->model('queue');
                    $this->queue->post($this->session->school['pigcms_token'],$msg);
                    //erm_post($url, $msg);
                }


                $this->output_json('ok', '点评信息提交成功');
            } else {
                $this->output_json('error', '提交失败');
            }

        }

    }

    /**
     * 学生回评
     * @param int $class_id
     * @param int $curriculum_id
     */
    public function evaluation_student($class_id = 0 ,$curriculum_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($curriculum_id,'class_curriculum');

        $this->db->where("id = {$curriculum_id}");
        $evaluation['curriculum'] = $this->db->get("wxt_class_curriculum")->row_array();

        $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.face,wxt_class_attence.status as attence_status,wxt_class_evaluation.teacher_score,wxt_class_evaluation.reply_info,wxt_class_evaluation.reply_time");
        $this->db->where("wxt_class_student.class_id = {$evaluation['curriculum']['class_id']} and wxt_class_attence.status = '1' and wxt_class_evaluation.reply_time > 0");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
        $this->db->join("wxt_class_attence", "wxt_class_attence.student_id = wxt_class_student.student_id and wxt_class_attence.curriculum_id={$curriculum_id}", 'left');
        $this->db->join("wxt_class_evaluation", "wxt_class_evaluation.curriculum_id = {$curriculum_id} and wxt_class_evaluation.student_id = wxt_student.id");
        $this->db->order_by('wxt_class_evaluation.id', 'desc');

        //获取班级学生
        $data['student'] = $this->db->get("wxt_class_student")->result_array();
        $this->output_json('ok', '学生回评',$data);

    }




}