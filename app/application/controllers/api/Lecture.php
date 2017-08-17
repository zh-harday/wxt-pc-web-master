<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/18
 * Time: 上午12:58
 */
class Lecture extends MY_Controller
{
    /**
     * 试听课
     */
    public function index()
    {


        $time = time();
        $this->db->where("start_time < {$time}");
        $this->db->update('wxt_intention_lecture',array('status'=>'1'));
        //参数设置

        $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
        $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
        $page = ($page - 1) * $limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;
        $order_field = empty($this->input->get('field')) ? 'id' : $this->input->get('field');
        $sort = empty($this->input->get('sort')) ? 'DESC' : $this->input->get('sort');


        //统计总数
        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if ($this->input->get('status') <> '') {
            $this->db->where("status", $this->input->get('status'));
        }

        if (!empty($this->input->get('subject_id'))) {
            $this->db->where("subject_id", $this->input->get('subject_id'));
        }

        if (!empty($this->input->get('teacher_id'))) {
            $this->db->where("teacher_id", $this->input->get('teacher_id'));
        }

        if(!empty($this->input->get('start_time'))){
            $this->db->where("start_time > {$this->input->get('start_time')} and start_time < {$this->input->get('end_time')}");
        }


        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_intention_lecture");
        if (!empty($this->input->get('count'))) {
            $this->output_json("ok", "统计数据", $data);
        }


        //查询数据
        $this->db->select("wxt_intention_lecture.*,wxt_user.name as teacher_name,wxt_campus.name as campus_name,wxt_intention_subject.name as subject_name,wxt_classroom.room_name");

        $this->db->where("wxt_intention_lecture.school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("wxt_intention_lecture.campus_id", $this->input->get('campus_id'));
        }

        if ($this->input->get('status') <> '') {
            $this->db->where("wxt_intention_lecture.status", $this->input->get('status'));
        }

        if (!empty($this->input->get('subject_id'))) {
            $this->db->where("wxt_intention_lecture.subject_id", $this->input->get('subject_id'));
        }

        if (!empty($this->input->get('teacher_id'))) {
            $this->db->where("wxt_intention_lecture.teacher_id", $this->input->get('teacher_id'));
        }

        if(!empty($this->input->get('start_time'))){
            $this->db->where("wxt_intention_lecture.start_time > {$this->input->get('start_time')} and wxt_intention_lecture.start_time < {$this->input->get('end_time')}");
        }


        $this->db->join("wxt_user","wxt_user.id = wxt_intention_lecture.teacher_id",'left');
        $this->db->join("wxt_campus","wxt_campus.id = wxt_intention_lecture.campus_id",'left');
        $this->db->join("wxt_intention_subject","wxt_intention_subject.id = wxt_intention_lecture.subject_id",'left');
        $this->db->join("wxt_classroom","wxt_classroom.id = wxt_intention_lecture.room_id",'left');


        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('wxt_intention_lecture.title', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit, $page);
        $this->db->order_by('wxt_intention_lecture.'.$order_field, $sort);
        $data['lecture'] = $this->db->get("wxt_intention_lecture")->result_array();

        foreach ($data['lecture'] as &$item){
            $this->db->where("lecture_id",$item['id']);
            $item['student_count'] = $this->db->count_all_results("wxt_intention_lecture_booking");

            $this->db->where("lecture_id",$item['id']);
            $this->db->where("status",'1');
            $item['attendance_count'] = $this->db->count_all_results("wxt_intention_lecture_booking");
        }

        $this->output_json("ok", "试听课", $data);
        
    }


    /**
     * 添加试听课
     */
    public function add()
    {
        $this->form_validation->set_rules('title', '试听课名称', 'trim|required');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('subject_id', '预报课程', 'trim|required|numeric');
        $this->form_validation->set_rules('teacher_id', '代课老师', 'trim|required|numeric');
        $this->form_validation->set_rules('room_id', '上课教室', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开课时间', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $lecture = $this->input->post(array('title', 'campus_id', 'subject_id', 'teacher_id','room_id', 'start_time'));

        $lecture['school_id'] = $this->session->school['id'];


        if ($this->db->insert("wxt_intention_lecture", $lecture)) {
            $this->output_json('ok', '添加成功', $this->db->insert_id());
        } else {
            $this->output_json('error', '添加失败', $lecture);
        }

    }

    /**
     * 编辑
     * @param int $lecture_id
     */
    public function edit($lecture_id = 0)
    {
        $this->is_my_school($lecture_id, 'intention_lecture');

        $this->form_validation->set_rules('title', '试听课名称', 'trim|required');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('subject_id', '预报课程', 'trim|required|numeric');
        $this->form_validation->set_rules('teacher_id', '代课老师', 'trim|required|numeric');
        $this->form_validation->set_rules('room_id', '上课教室', 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', '开课时间', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $lecture = $this->input->post(array('title', 'campus_id', 'subject_id', 'teacher_id','room_id', 'start_time'));

        $this->db->where("id",$lecture_id);

        if ($this->db->update("wxt_intention_lecture", $lecture)) {
            $this->output_json('ok', '编辑成功', $lecture);
        } else {
            $this->output_json('error', '编辑失败', $lecture);
        }


    }

    /**
     * 删除
     * @param int $lecture_id
     */
    public function del($lecture_id = 0)
    {
        $this->is_my_school($lecture_id, 'intention_lecture');

        $this->db->delete('wxt_intention_lecture',array('id'=>$lecture_id));
        $this->db->delete('wxt_intention_lecture_booking',array('lecture_id'=>$lecture_id));
        $this->output_json("ok", "记录已删除");
    }


    /**
     * 预约学员
     * @param int $lecture_id
     * @param int $booking_id
     */
    public function booking($lecture_id = 0,$action = '',$booking_id = 0)
    {
        $this->is_my_school($lecture_id, 'intention_lecture');

        if ($this->input->method() == 'get') {
            //参数设置

            $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
            $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
            $page = ($page - 1) * $limit;
            $data['limit'] = $limit;


            //统计总数
            $this->db->where("lecture_id", $lecture_id);
            $data['count'] = $this->db->count_all_results("wxt_intention_lecture_booking");


            //查询数据
            $this->db->select("wxt_intention_lecture_booking.id,wxt_intention_lecture_booking.status,wxt_intention.name,wxt_user.name as staff_name,wxt_intention.status_id,wxt_intention_status.name as intention_status");

            $this->db->where("wxt_intention_lecture_booking.lecture_id", $lecture_id);
            $this->db->join("wxt_user", "wxt_user.id = wxt_intention_lecture_booking.relate_user",'left');
            $this->db->join("wxt_intention", "wxt_intention.id = wxt_intention_lecture_booking.intention_id",'left');
            $this->db->join("wxt_intention_status", "wxt_intention_status.id = wxt_intention.status_id",'left');

            $this->db->limit($limit, $page);
            $this->db->order_by('wxt_intention_lecture_booking.id', 'DESC');
            $data['booking'] = $this->db->get("wxt_intention_lecture_booking")->result_array();

            $this->output_json("ok", "预约学员", $data);
        }else{

            //安排试听
            if($action == 'add'){
                $this->form_validation->set_rules('intention_id', '预约学员', 'trim|required');
                $this->form_validation->set_rules('relate_user', '邀约人', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $booking = $this->input->post(array('intention_id','relate_user'));

                $booking['lecture_id'] = $lecture_id;
                $booking['school_id'] = $this->session->school['id'];

                $this->db->where(array('lecture_id'=>$booking['lecture_id'],'intention_id'=>$booking['intention_id']));

                if($this->db->count_all_results('wxt_intention_lecture_booking') > 0){
                    $this->output_json('error', '此试听课已有安排');
                }


                if ($this->db->insert("wxt_intention_lecture_booking", $booking)) {
                    $this->output_json('ok', '添加成功', $this->db->insert_id());
                } else {
                    $this->output_json('error', '添加失败', $booking);
                }
            //取消试听
            }elseif ($action == 'del'){

                $this->is_my_school($booking_id, 'intention_lecture_booking');

                $this->db->delete('wxt_intention_lecture_booking',array('id'=>$booking_id));
                $this->output_json("ok", "记录已删除");

            //签到
            }elseif ($action == 'check_in'){

                $this->db->update("wxt_intention_lecture_booking", array('status'=>'1'),array('id'=>$booking_id));
                $this->output_json('ok', '状态已修改', $booking_id);

            }

        }
    }

    /**
     * 查询学生的试听记录
     * @param int $intention_id
     */
    public function intention($intention_id = 0)
    {
        $this->is_my_school($intention_id, 'intention');

        //参数设置

        $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
        $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
        $page = ($page - 1) * $limit;
        $data['limit'] = $limit;


        //统计总数
        $this->db->where("intention_id", $intention_id);
        $data['count'] = $this->db->count_all_results("wxt_intention_lecture_booking");

        //查询数据
        $this->db->select("wxt_intention_lecture_booking.id,wxt_intention_lecture_booking.status,wxt_intention_lecture.title,wxt_intention_subject.name as subject_name,wxt_user.name as teacher,relate_user.name as staff_name,wxt_intention_lecture.start_time");

        $this->db->where("wxt_intention_lecture_booking.intention_id", $intention_id);
        $this->db->join("wxt_intention_lecture", "wxt_intention_lecture.id = wxt_intention_lecture_booking.lecture_id");
        $this->db->join("wxt_intention_subject", "wxt_intention_subject.id = wxt_intention_lecture.subject_id");
        $this->db->join("wxt_user as relate_user", "relate_user.id = wxt_intention_lecture_booking.relate_user");
        $this->db->join("wxt_user", "wxt_user.id = wxt_intention_lecture.teacher_id");
        $this->db->limit($limit, $page);
        $this->db->order_by('wxt_intention_lecture_booking.id', 'DESC');
        $data['booking'] = $this->db->get("wxt_intention_lecture_booking")->result_array();

        $this->output_json("ok", "学员试听记录", $data);

    }



}