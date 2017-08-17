<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/10
 * Time: 下午4:04
 */

/**
 * Class Subject 课程
 */
class Subject extends MY_Controller
{
    /**
     * 课程列表
     */
    public function index()
    {
        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;


        //统计总数
        $this->db->where("school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }
        if(!empty($this->input->get("type_id"))){
            $this->db->where("type_id",$this->input->get("type_id"));
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('subject', $search);
            $this->db->or_like('remark', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_subject");
        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }

        //查询数据
        $this->db->select("id,subject,campus_id,type_id,remark,reg_time as time");
        $this->db->where("school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }

        if(!empty($this->input->get("type_id"))){
            $this->db->where("type_id",$this->input->get("type_id"));
        }
        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('subject', $search);
            $this->db->or_like('remark', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit,$page);

        $this->db->order_by("id", 'DESC');
        $data['subject'] = $this->db->get("wxt_subject")->result_array();

        $this->output_json("ok","课程列表",$data);

    }

    public function simple()
    {
        //参数设置


        $search = $this->input->get('search');

        //统计总数
        $this->db->where("school_id",$this->session->user['school_id']);

        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }
        if(!empty($this->input->get("type_id"))){
            $this->db->where("type_id",$this->input->get("type_id"));
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('subject', $search);
            $this->db->group_end();
        }
        $data['count'] = $this->db->count_all_results("wxt_subject");
        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }

        //查询数据
        $this->db->select("id,subject,campus_id,type_id");
        $this->db->where("school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id = {$this->input->get('campus_id')} or (campus_id = '1' and school_id = {$this->session->user['school_id']})");
        }

        if(!empty($this->input->get("type_id"))){
            $this->db->where("type_id",$this->input->get("type_id"));
        }
        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('subject', $search);
            $this->db->group_end();
        }

        $this->db->order_by("id", 'DESC');
        $data['subject'] = $this->db->get("wxt_subject")->result_array();

        $this->output_json("ok","课程列表",$data);

    }

    /**
     * 课程统计
     */
    public function count()
    {

        //统计总数
        $this->db->where("school_id",$this->session->user['school_id']);

        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }
        if(!empty($this->input->get("type_id"))){
            $this->db->where("type_id",$this->input->get("type_id"));
        }

        $data['count'] = $this->db->count_all_results("wxt_subject");
        $this->output_json("ok","课程统计",$data);

    }

    /**
     * 课程详情
     * @param int $id
     */
    public function view($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        $this->db->select("id,subject,campus_id,type_id,remark,reg_time as time");
        $this->db->where("id",$id);
        $this->db->where("school_id",$this->session->user['school_id']);
        $data = $this->db->get("wxt_subject")->row_array();


        $this->db->select("wxt_subject_teacher.teacher_id,wxt_user.name as teacher");
        $this->db->where("wxt_subject_teacher.subject_id",$id);
        $this->db->join("wxt_user","wxt_user.id = wxt_subject_teacher.teacher_id");
        $data['teacher'] = $this->db->get("wxt_subject_teacher")->result_array();

        $this->db->select("wxt_class.id,wxt_class.class_name,wxt_class.campus_id");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_subject.class_id and wxt_class.staus <> '-1'");
        $this->db->where("wxt_class_subject.subject_id",$id);

        $data['class'] = $this->db->get("wxt_class_subject")->result_array();

        $this->output_json("ok","课程详情",$data);
    }

    /**
     * 开课班级
     * @param int $id
     */
    public function classs($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        $this->db->select("wxt_class.id,wxt_class.class_name,wxt_class.campus_id");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_subject.class_id and wxt_class.staus <> '-1'");
        $this->db->where("wxt_class_subject.subject_id",$id);

        $data = $this->db->get("wxt_class_subject")->result_array();

        $this->output_json("ok","开课班级",$data);

    }

    /**
     * 添加课程
     */
    public function add()
    {
        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('subject', '课程名称', 'trim|required');
            $this->form_validation->set_rules('campus_id', '校区', 'trim|required');
            $this->form_validation->set_rules('type_id', '课程类别', 'trim|required');
            $this->form_validation->set_rules('teacher', '授课老师', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {
                $subject['subject'] = $this->input->post('subject');
                $subject['campus_id'] = $this->input->post('campus_id');
                $subject['type_id'] = $this->input->post('type_id');
                $subject['remark'] = $this->input->post('remark');
                $subject['school_id'] = $this->session->user['school_id'];
                $subject['reg_time'] = time();


                $subject_teacher = str_getcsv($this->input->post('teacher'));

                if(!is_array($subject_teacher) or empty($subject_teacher)){
                    $this->output_json('error', '请至少选择一个授课老师');
                }

                $this->db->insert("wxt_subject",$subject);
                $sid = $this->db->insert_id();

                foreach ($subject_teacher as $teacher){
                    $this->db->insert("wxt_subject_teacher",array('subject_id'=>$sid,'teacher_id'=>$teacher));
                }

                $this->output_json("ok","ok",$this->input->post());
            }


        }

    }

    /**
     * 修改课程
     * @param int $id
     */
    public function edit($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('subject', '课程名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }
            $subject['subject'] = $this->input->post('subject');
            $subject['remark'] = $this->input->post('remark');
            $subject_teacher = str_getcsv($this->input->post('teacher'));

            if(!is_array($subject_teacher) or empty($subject_teacher)){
                $this->output_json('error', '请至少选择一个授课老师');
            }

            $this->db->where(array("id"=>$id,"school_id"=>$this->session->user['school_id']));
            $this->db->update("wxt_subject",$subject);

            if(!empty($subject_teacher)){
                $this->db->delete("wxt_subject_teacher",array("subject_id"=>$id));
                foreach ($subject_teacher as $teacher){
                    $this->db->insert("wxt_subject_teacher",array('subject_id'=>$id,'teacher_id'=>$teacher));
                }
            }

            $this->output_json("ok","课程内容修改成功！",$this->input->post());


        }

    }

    /**
     * 删除课程
     * @param int $id
     */
    public function del($id = 0)
    {

        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }
        $this->db->where("id",$id);
        $subject = $this->db->get("wxt_subject")->row_array();
        if($subject['school_id'] <> $this->session->user['school_id']){
            $this->output_json('error', "无此权限！");
        }


        $this->db->where("subject_id",$id);
        if($this->db->count_all_results("wxt_class_subject") > 0){
            $this->output_json('error', "已有班级选择了本课程，不允许删除！");
        }else{
            $this->db->delete("wxt_subject",array("id"=>$id));
            $this->output_json("ok","此课程已被删除！",$id);
        }



    }



    /**
     * 课程分类
     */
    public function type()
    {
        $this->db->select("id,type_name,remark");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $type = $this->db->get("wxt_subject_type")->result_array();
        $this->output_json("ok","ok",$type);

    }

    /**
     * 课程类别信息
     * @param int $sid
     */
    public function type_info($sid = 0)
    {
        $this->db->select("id,type_name,remark");
        $this->db->where("id= '{$sid}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $type = $this->db->get("wxt_subject_type")->row_array();
        $this->output_json("ok","ok",$type);
    }

    /**
     * 添加课程类别
     */
    public function type_add()
    {
        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('type_name', '课程类别', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {
                //添加新教室
                $subject_type['school_id']  = $this->session->school['id'];
                $subject_type['type_name']  = $this->input->post('type_name');
                $subject_type['remark']     = $this->input->post('remark');
                $subject_type['re_time']    = time();
                $subject_type['re_user']    = $this->session->user['school_id'];

                $this->db->insert("wxt_subject_type",$subject_type);
                $this->output_json("ok","ok",$subject_type);
            }


        }
    }

    /**
     * 编辑课程类别
     * @param int $sid
     */
    public function type_edit($sid = 0)
    {
        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('type_name', '课程类别', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {

                $subject_type['type_name']  = $this->input->post('type_name');
                $subject_type['remark']     = $this->input->post('remark');

                $this->db->where("id= '{$sid}'");
                $this->db->where("school_id= '{$this->session->user['school_id']}'");
                $this->db->update("wxt_subject_type",$subject_type);
                $this->output_json("ok","ok",$subject_type);
            }


        }

    }

    /**
     * 删除课程类别
     * @param int $sid
     */
    public function type_del($sid =0)
    {

        $this->db->where("id= '{$sid}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $type = $this->db->get("wxt_subject_type")->row_array();



        //检查是否本学校校区
        if($type['school_id'] == $this->session->user['school_id']){


            $this->db->where('type_id',$sid);
            if($this->db->count_all_results('wxt_subject') > 0){

                $this->output_json("error","您要删除的类别下已有课程，不允许删除！",'','-1');
            }else{
                //删除教室
                $this->db->where("id",$sid);
                $this->db->delete("wxt_subject_type");
                $this->output_json("ok","成功");
            }

        }else{
            $this->output_json('error', '无此权限');
        }

    }





}