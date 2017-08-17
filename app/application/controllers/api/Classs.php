<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/21
 * Time: 下午12:06
 */
class Classs extends MY_Controller
{
    public function index()
    {
        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;


        if($this->input->get('type') == 'my'){
            $this->db->select('distinct(wxt_class.id)');
            $this->db->where("(wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']})");
            $this->db->join("wxt_class_curriculum","wxt_class_curriculum.class_id = wxt_class.id",'left');
            $my_class_id = $this->db->get("wxt_class")->result_array();
            $my_class_id2 = array();
            $my_class_id2[] = 0;
            foreach ($my_class_id as $item){
                $my_class_id2[] = $item['id'];
            }

            $this->db->where_in("id",$my_class_id2);



        }

        //统计总数
        $this->db->where("school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }

        if($this->input->get('staus') <> ''){
            if($this->input->get('staus') == '0'){
                $this->db->where("wxt_class.staus IN('0','1')");
            }else{
                $this->db->where("wxt_class.staus",$this->input->get('staus'));
            }
        }

        if(!empty($this->input->get('fee_method'))){
            $this->db->where("fee_method",$this->input->get('fee_method'));
        }

        if(!empty($this->input->get('teacher_id'))){
            $this->db->where("teacher_id",$this->input->get('teacher_id'));
        }

        if(!empty($this->input->get('start_time'))){
            $this->db->where("start_time > {$this->input->get('start_time')} and start_time < {$this->input->get('end_time')}");
        }



        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('class_name', $search);
            $this->db->or_like('remark', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_class");

        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }
        $this->db->select("wxt_class.id,wxt_class.campus_id,wxt_class.class_name,wxt_class.staus,wxt_class.class_type,wxt_class.fee_method,wxt_class.price,wxt_class.start_time,wxt_user.name as teacher");
        //查询数据
        $this->db->where("wxt_class.school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("wxt_class.campus_id",$this->input->get('campus_id'));
        }

        if($this->input->get('staus') <> ''){
            if($this->input->get('staus') == '0'){
                $this->db->where("wxt_class.staus IN('0','1')");
            }else{
                $this->db->where("wxt_class.staus",$this->input->get('staus'));
            }
        }

        if(!empty($this->input->get('fee_method'))){
            $this->db->where("wxt_class.fee_method",$this->input->get('fee_method'));
        }
        if(!empty($this->input->get('teacher_id'))){
            $this->db->where("wxt_class.teacher_id",$this->input->get('teacher_id'));
        }
        if(!empty($this->input->get('start_time'))){
            $this->db->where("wxt_class.start_time > {$this->input->get('start_time')} and wxt_class.start_time < {$this->input->get('end_time')}");
        }



        if($this->input->get('type') == 'my'){
            $this->db->where_in("wxt_class.id",$my_class_id2);
        }



        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_class.class_name', $search);
            $this->db->or_like('wxt_class.remark', $search);
            $this->db->group_end();
        }
        $this->db->join("wxt_user","wxt_user.id = wxt_class.teacher_id");

        $this->db->limit($limit,$page);
        $this->db->order_by("wxt_class.reg_time", 'DESC');
        $data['class'] = $this->db->get("wxt_class")->result_array();

        foreach ($data['class'] as &$item){


            //已上课程
            $this->db->where(array('class_id'=>$item['id'],'status'=>'2'));
            $item['curriculum']['end'] = $this->db->count_all_results('wxt_class_curriculum');

            //全部课程
            $this->db->where(array('class_id'=>$item['id']));
            $item['curriculum']['count'] = $this->db->count_all_results('wxt_class_curriculum');

            //学员统计
            $this->db->where(array('class_id'=>$item['id']));
            $item['student']['count'] = $this->db->count_all_results('wxt_class_student');
        }
        //echo $this->db->last_query();
        $this->output_json("ok","班级列表",$data);



    }

    /**
     *精简班级列表
     */
    public function simple()
    {
        //查询数据
        $this->db->select("id,campus_id,class_name,staus,class_type,fee_method,price");
        $this->db->where("school_id",$this->session->user['school_id']);

        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }

        if($this->input->get('staus') <> ''){
            $this->db->where("staus",$this->input->get('staus'));
        }

        if(!empty($this->input->get('class_type'))){
            $this->db->where("class_type",$this->input->get('class_type'));
        }

        if(!empty($this->input->get('fee_method'))){
            $this->db->where("fee_method",$this->input->get('fee_method'));
        }

        $this->db->order_by("reg_time", 'DESC');
        $data['class'] = $this->db->get("wxt_class")->result_array();

        $this->output_json("ok","班级列表",$data);
    }

    /**
     * 班级详情
     * @param int $id
     */
    public function view($id = 0)
    {
        $this->is_my_school($id,'class');

        $this->db->where("id = '{$id}'");
        $data['info'] = $this->db->get("wxt_class")->row_array();

        $this->db->select('name as campus_name');
        $this->db->where("id = '{$data['info']['campus_id']}'");
        $campus = $this->db->get("wxt_campus")->row_array();
        $data['info']['campus_name'] = $campus['campus_name'];

        $this->db->select('name as teacher_name');
        $this->db->where("id = '{$data['info']['teacher_id']}'");
        $teacher = $this->db->get("wxt_user")->row_array();
        $data['info']['teacher_name'] = $teacher['teacher_name'];

        $this->db->select("wxt_subject.id,wxt_subject.subject as subject_name");
        $this->db->where("wxt_class_subject.class_id = '{$id}'");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_subject.subject_id");
        $data['info']['subject'] =$this->db->get("wxt_class_subject")->result_array();

        $this->output_json("ok","班级详情",$data['info']);
    }


    /**
     * 添加班级
     */
    public function add()
    {
        $this->form_validation->set_rules('class_name', '班级名称', 'trim|required');
        $this->form_validation->set_rules('class_type', '班级类型', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('teacher_id', '班主任', 'trim|required|numeric');
        $this->form_validation->set_rules('price', '学费', 'trim|required|numeric');
        $this->form_validation->set_rules('staus', '班级状态', 'trim|required|numeric');
        $this->form_validation->set_rules('subject', '班级课程', 'trim|required');
        $this->form_validation->set_rules('allow_leave', '是否允许请假', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $class = $this->input->post(array(
                                            'campus_id',
                                            'class_name',
                                            'class_type',
                                            'teacher_id',
                                            'fee_method',
                                            'price',
                                            'max_student',
                                            'start_time',
                                            'remark',
                                            'staus',
                                            'allow_leave'
                                        ), TRUE);

        $class['school_id'] = $this->session->school['id'];
        $class['reg_time'] = time();
        $class['reg_user_id'] = $this->session->user['id'];

        $this->is_my_school($class['campus_id'],'campus');
        $this->is_my_school($class['teacher_id'],'user');


        $subject = str_getcsv($this->input->post('subject'));

        if(!is_array($subject) or empty($subject)){
            $this->output_json('error', '请至少选择一个课程');
        }
        $this->db->insert("wxt_class",$class);
        $class_id = $this->db->insert_id();
        if ($class_id) {
            foreach ($subject as $item) {
                $this->db->insert("wxt_class_subject",array('class_id' => $class_id, "school_id" => $this->session->school['id'], 'subject_id' => $item));
            }

            $this->output_json('ok', '班级信息添加成功');
        } else {
            $this->output_json('error', '班级信息添加失败');
        }

    }

    public function edit($class_id = 0)
    {
        $this->is_my_school($class_id,'class');

        $this->db->where("id = '{$class_id}'");
        $class = $this->db->get("wxt_class")->row_array();

        if ($class['staus'] == '0') {
            $this->form_validation->set_rules('class_name', '班级名称', 'trim|required');
            $this->form_validation->set_rules('teacher_id', '班主任', 'trim|required|numeric');
            $this->form_validation->set_rules('staus', '开课状态', 'trim|required');
            $this->form_validation->set_rules('max_student', '最大人数', 'trim|required');
            $this->form_validation->set_rules('price', '学费', 'trim|required|numeric');
            $this->form_validation->set_rules('allow_leave', '是否允许请假', 'trim|required|in_list[0,1]');
            //$this->form_validation->set_rules('remark', '班级介绍', 'trim|required');


            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            } else {

                $data = $this->input->post(array(
                    'class_name',
                    'teacher_id',
                    'staus',
                    'max_student',
                    'price',
                    'allow_leave',
                    'remark'
                ));

                if ($data['staus'] <> '0') {
                    $this->db->where("class_id = {$class_id} and start_time > " . time());
                    if ($this->db->count_all_results("wxt_class_curriculum") > 0) {
                        $this->output_json('error', '当前班级还有课程没有结束，不能修改开班状态！');
                    }

                }

                $this->db->where("id = $class_id");

                if ($this->db->update("wxt_class", $data)) {
                    $this->db->where("class_id = {$class_id}");
                    $this->db->delete("wxt_class_subject");

                    $subject = str_getcsv($this->input->post('subject'));

                    if(!is_array($subject) or empty($subject)){
                        $this->output_json('error', '请至少选择一个课程');
                    }
                    foreach ($subject as $item) {
                        $this->db->insert("wxt_class_subject", array('class_id' => $class_id, "school_id" => $this->session->school['id'], 'subject_id' => $item));
                    }
                    $this->output_json('ok', '班级信息已修改！');
                } else {
                    $this->output_json('error', '修改失败');
                }

            }
        } else {

            $this->form_validation->set_rules('class_name', '班级名称', 'trim|required');
            $this->form_validation->set_rules('start_time', '开课日期', 'trim|required');
            $this->form_validation->set_rules('teacher_id', '班主任', 'trim|required|numeric');
            $this->form_validation->set_rules('staus', '开课状态', 'trim|required');
            $this->form_validation->set_rules('max_student', '最大人数', 'trim|required');
            $this->form_validation->set_rules('price', '学费', 'trim|required|numeric');
            //$this->form_validation->set_rules('remark', '班级介绍', 'trim|required');


            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            } else {

                $data = $this->input->post(array(
                    'class_name',
                    'teacher_id',
                    'staus',
                    'max_student',
                    'price',
                    'remark',
                    'campus_id',
                    'start_time',
                    'fee_method',
                    'allow_leave'
                ));
                //$data['start_time'] = strtotime($data['start_time']);


                $this->db->where("id = $class_id");

                if ($this->db->update("wxt_class", $data)) {
                    $this->db->where("class_id = {$class_id}");
                    $this->db->delete("wxt_class_subject");

                    $subject = str_getcsv($this->input->post('subject'));

                    if(!is_array($subject) or empty($subject)){
                        $this->output_json('error', '请至少选择一个课程');
                    }
                    foreach ($subject as $item) {
                        $this->db->insert("wxt_class_subject", array('class_id' => $class_id, "school_id" => $this->session->school['id'], 'subject_id' => $item));
                    }


                    $this->output_json('ok', '班级信息已修改！');
                } else {
                    $this->output_json('error', '修改失败');
                }

            }


        }
    }

    /**
     * 删除班级
     * @param int $class_id
     */
    public function del($class_id = 0)
    {

        $this->is_my_school($class_id,'class');

        $this->db->where('class_id', $class_id);
        $curriculum = $this->db->count_all_results('wxt_class_curriculum');
        if($curriculum > 0 ){
            $this->output_json('error', '当前班级已有排课信息，不允许删除！');
        }

        $this->db->where('class_id', $class_id);
        $student = $this->db->count_all_results('wxt_class_student');
        if($student > 0 ){
            $this->output_json('error', '当前班级已有学员信息，不允许删除！');
        }

        if (($curriculum + $student) > 0) {
            $this->output_json('error', '修改失败');
            $json['msg'] = '当前班级已有学员或排课信息，不允许删除！';
            $json['status'] = 'error';
        } else {
            //删除


            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_attence');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_prep');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_reservation');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_subject');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_test');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_work');

            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_curriculum');
            $this->db->where(array('class_id'=>$class_id))->delete('wxt_class_student');

            $this->db->where(array('id'=>$class_id))->delete("wxt_class");


            $this->output_json('ok', '班级信息已删除');
        }

    }

    /**
     * 班级日志
     * @param int $class_id
     * @param int $sid
     */
    public function class_log($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;


        $this->db->where(array('class_id'=>$class_id));
        $data['count'] = $this->db->count_all_results('wxt_class_student_log');

        $this->db->select("wxt_class_student_log.*,wxt_user.name as teacher_name,wxt_student.name as student_name");
        $this->db->where(array('wxt_class_student_log.class_id'=>$class_id));
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_student_log.user_id");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student_log.student_id");

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_student_log.id', 'desc');
        $data['log'] = $this->db->get('wxt_class_student_log')->result_array();
        $this->output_json("ok","班级日志",$data);


    }



}