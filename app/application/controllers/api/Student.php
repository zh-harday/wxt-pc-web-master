<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/20
 * Time: 下午2:58
 */
class Student extends MY_Controller
{
    public function index()
    {
        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;



        //统计总数

        //生日学员
        if($this->input->get('type') == 'birthday'){
            $this->db->select("id,birthday");
            $this->db->where("school_id = {$this->session->user['school_id']} and birthday > 0");
            $student = $this->db->get("wxt_student")->result_array();

            $birthday_student = array();
            $y = date("Y");
            foreach ($student as $item){

                $birthday = strtotime(date($y."-m-d",$item['birthday']));


                if($birthday > (time() - (86400*7)) and $birthday < (time() + (86400*30))){
                    $birthday_student[] = $item['id'];
                }
            }

            if(empty($birthday_student)){
                $birthday_student =array("0");
            }
            $in_id = $birthday_student;

            //未入班学员
        }else if($this->input->get('type') == 'no_class'){
            $this->db->select("wxt_student.id,wxt_class_student.class_id");
            $this->db->where("wxt_student.school_id = {$this->session->user['school_id']}");
            $this->db->join("wxt_class_student","wxt_student.id = wxt_class_student.student_id","left");
            $student = $this->db->get("wxt_student")->result_array();

            $no_class_student = array();
            foreach ($student as $item){
                if(empty($item['class_id'])){
                    $no_class_student[] = $item['id'];
                }

            }

            if(empty($no_class_student)){
                $no_class_student =array("0");
            }

            $in_id = $no_class_student;

            //停课学员
        }else if($this->input->get('type') == 'stop_class'){
            $this->db->select("distinct(`wxt_student`.`id`)");
            $this->db->where("wxt_student.school_id = {$this->session->user['school_id']}");
            $this->db->join("wxt_student","wxt_student.id = wxt_class_student.student_id and wxt_class_student.status = '0'","left");
            $student = $this->db->get("wxt_class_student")->result_array();

            $stop_class_student = array();
            foreach ($student as $item){
                if(empty($item['class_id'])){
                    $stop_class_student[] = $item['id'];
                }

            }

            if(empty($stop_class_student)){
                $stop_class_student =array("0");
            }

            $in_id = $stop_class_student;

            //待续费学员
        }else if($this->input->get('type') == 'lacking'){

            $this->db->select("wxt_class_student.class_id,wxt_class_student.student_id,wxt_class_student.buy_quantity");
            $this->db->where("wxt_class.school_id = {$this->session->user['school_id']} and wxt_class.fee_method = 'frequency'");
            $this->db->join("wxt_class_student","wxt_class_student on wxt_class.id = wxt_class_student.class_id");
            $student = $this->db->get("wxt_class")->result_array();

            $lacking_student = array();
            foreach ($student as $item){

                $this->db->where("class_id = {$item['class_id']} and student_id = {$item['student_id']} and is_xk = '1'");
                $kx = $this->db->count_all_results("wxt_class_attence");

                if(($item['buy_quantity'] - $kx) <= 3){
                    $lacking_student[] = $item['student_id'];
                }


            }


            if(empty($lacking_student)){
                $lacking_student =array("0");
            }

            $in_id = $lacking_student;


        }



        //我的学员
        if($this->input->get('my') == '1'){

            $this->db->select('distinct(wxt_class.id)');
            $this->db->where("wxt_class.school_id",$this->session->user['school_id']);
            $this->db->where("wxt_class.teacher_id = {$this->session->user['id']} or wxt_class_curriculum.teacher_id = {$this->session->user['id']}");
            $this->db->join("wxt_class_curriculum","wxt_class_curriculum.class_id = wxt_class.id");
            $my_class_id = $this->db->get("wxt_class")->result_array();
            $my_class_id2 = array();
            foreach ($my_class_id as $item){
                $my_class_id2[] = $item['id'];
            }

            $this->db->select('distinct(student_id)');
            if(!empty($my_class_id2)){
                $this->db->where_in("class_id",$my_class_id2);
            }

            $class_student = $this->db->get('wxt_class_student')->result_array();

            $class_student2 = array();
            foreach ($class_student as $item){
                $class_student2[] = $item['student_id'];
            }

            $this->db->select('id');
            $this->db->where("zy_id",$this->session->user['id']);
            $this->db->or_where("gw_id",$this->session->user['id']);
            if(!empty($class_student2)){
                $this->db->or_where_in("id",$class_student2);
            }

            $my_student = $this->db->get('wxt_student')->result_array();

            $my_student2 =array();
            foreach ($my_student as $item){
                $my_student2[] = $item['id'];
            }
//           $this->db->where_in('id',$my_student2);
//            print_r($my_student2);
//
//            echo $this->db->last_query();
//            exit;

        }

        if(!empty($this->input->get('type')) and !empty($in_id)){
            $this->db->where_in('id',$in_id);
        }

        if(!empty($this->input->get('my')) and !empty($my_student2)){
            $this->db->where_in('id',$my_student2);
        }



        $this->db->where("school_id",$this->session->user['school_id']);

        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }

        if(!empty($this->input->get('class_id'))){
            $this->db->where("id in (select student_id as id from `wxt_class_student` where class_id = '".$this->input->get('class_id')."' )");
        }

        if(!empty($this->input->get('start_time')) and  !empty($this->input->get('end_time'))){
            $this->db->where("reg_time >= ".$this->input->get('start_time')." and reg_time <= ".$this->input->get('end_time'));
        }



        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('number', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_student");
        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }


        //查询数据
        $this->db->select("id,campus_id,status,number,name,sex,birthday,phone,reg_time,money,intention_id,sales_id");
        if(!empty($this->input->get('type')) and !empty($in_id)){
            $this->db->where_in('id',$in_id);
        }

        if(!empty($this->input->get('my')) and !empty($my_student2)){
            $this->db->where_in('id',$my_student2);
        }

        $this->db->where("school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("campus_id",$this->input->get('campus_id'));
        }

        if(!empty($this->input->get('class_id'))){
            $this->db->where("id in (select student_id as id from `wxt_class_student` where class_id = '".$this->input->get('class_id')."' )");
        }

        if(!empty($this->input->get('start_time')) and  !empty($this->input->get('end_time'))){
            $this->db->where("reg_time >= ".$this->input->get('start_time')." and reg_time <= ".$this->input->get('end_time'));
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('number', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit,$page);

        $field = empty($this->input->get('field'))?'reg_time':$this->input->get('field');
        $direction = empty($this->input->get('direction'))?'DESC':$this->input->get('direction');

        $this->db->order_by($field, $direction);
        $data['student'] = $this->db->get("wxt_student")->result_array();

        foreach ($data['student'] as &$item){
            $this->db->where('uid',$item['id']);
            $item['weixin_bind'] = $this->db->count_all_results("wxt_student_weixin");

        }

        $this->output_json("ok","学员列表",$data);

    }


    /**
     * 学员详情
     * @param int $id
     */
    public function view($id = 0)
    {

        $this->is_my_school($id,'student');

        $this->db->where("id",$id);

        $data['student'] = $this->db->get("wxt_student")->result_array();

        $this->output_json("ok","学员详情",$data);

    }

    /**
     * 学员联系人
     * @param int $id
     */
    public function contact($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->db->select("id,contact_name,phone,relation,job");
        $this->db->where("student_id",$id);
        $contact =  $this->db->get('wxt_student_contact')->result_array();
        $this->output_json("ok","联系人",$contact);
    }


    /**
     * 添加联系人
     * @param int $id
     */
    public function contact_add($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->form_validation->set_rules('contact_name', '姓名', 'trim|required');
        $this->form_validation->set_rules('phone', '电话', 'trim|required');
        $this->form_validation->set_rules('relation', '关系', 'trim|required');

        if ($this->form_validation->run() == FALSE){
            $this->output_json('error', validation_errors(' ', ' '));
        }else{
            $contact = $this->input->post(array('contact_name','phone','relation',"job"));

            $contact['school_id'] = $this->session->school['id'];
            $contact['student_id'] = $id;
            $this->db->insert("wxt_student_contact",$contact);
            $this->output_json('ok', "添加成功！");

        }

    }

    /**
     * 删除联系人
     * @param $sid
     * @param $id
     */
    public function contact_del($sid,$id)
    {
        if(!is_numeric($id) or !is_numeric($sid)){
            $this->output_json('error', "参数错误！");
        }
        $this->is_my_school($sid,'student');

        $this->db->delete("wxt_student_contact",array("id"=>$id));
        $this->output_json('ok', "删除成功！");
    }

    /**
     * 学员所在班级
     * @param int $id
     */
    public function classs($id = 0)
    {
        $this->is_my_school($id,'student');

        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;

        $data['limit'] = $limit;

        $this->db->where(array('student_id'=>$id));
        $data['count'] = $this->db->count_all_results('wxt_class_student');


        $this->db->select('wxt_class.*,wxt_class_student.buy_quantity,wxt_class_student.buy_recordss_id,wxt_class_student.reg_time as add_class_time,wxt_user.name as teacher_name,wxt_class_student.status as student_status,wxt_class_student.start_time as student_start_time,wxt_class_student.end_time as student_end_time');

        $this->db->where(array('wxt_class_student.student_id' =>$id));

        $this->db->join('wxt_class', 'wxt_class.id = wxt_class_student.class_id','left');
        $this->db->join('wxt_user', 'wxt_user.id = wxt_class.teacher_id','left');
        $this->db->order_by('wxt_class_student.id', 'DESC');
        $this->db->limit($limit,$page);
        $data['class'] = $this->db->get('wxt_class_student')->result_array();

        foreach ($data['class'] as &$item){
            $this->db->where("class_id = {$item['id']} and student_id = {$id} and is_xk = '1'");
            $item['kx'] = $this->db->count_all_results("wxt_class_attence");
        }
        $this->output_json("ok","报名班级",$data);

    }


    /**
     * 考勤记录
     * @param int $id
     */
    public function attence($id = 0)
    {
        $this->is_my_school($id,'student');


        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;

        $data['limit'] = $limit;

        //统计总数
        $this->db->where("wxt_class_attence.student_id = {$id}");

        $data['count'] = $this->db->count_all_results("wxt_class_attence");

        $this->db->select("wxt_class_attence.id,wxt_class.class_name,wxt_subject.subject,wxt_user.name as subject_teacher,wxt_classroom.room_name,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time,wxt_class_curriculum.status,wxt_class_attence.status as attence_status,wxt_class_attence.time as attence_time,wxt_class_attence.is_xk");
        $this->db->join("wxt_class","wxt_class_attence.class_id = wxt_class.id");
        $this->db->join("wxt_class_curriculum","wxt_class_curriculum.id =wxt_class_attence.curriculum_id");
        $this->db->join("wxt_subject","wxt_class_curriculum.subject_id = wxt_subject.id");
        $this->db->join("wxt_user","wxt_class_curriculum.teacher_id = wxt_user.id");
        $this->db->join("wxt_classroom","wxt_class_curriculum.room_id = wxt_classroom.id");

        $this->db->where("wxt_class_attence.student_id = {$id}");

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_attence.id','DESC' );
        $data['attence'] = $this->db->get("wxt_class_attence")->result_array();

        $this->output_json("ok","考勤记录",$data);
    }


    /**
     * 缴费记录
     * @param int $id
     */
    public function recordss($id = 0)
    {
        $this->is_my_school($id,'student');


        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        //统计总数
        $this->db->where("wxt_finance_orders.student_id = {$id}");
        $this->db->join('wxt_finance_orders','wxt_finance_orders.id = wxt_finance_recordss.orders_id');
        $data['count'] = $this->db->count_all_results("wxt_finance_recordss");

        $this->db->select('wxt_finance_recordss.*,wxt_finance_orders.id as orders_id,wxt_finance_orders.school_id,wxt_finance_orders.campus_id,wxt_finance_orders.payee,wxt_finance_orders.payee_account,wxt_finance_orders.payee_time,wxt_finance_orders.reg_time,wxt_finance_orders.remark');
        $this->db->join('wxt_finance_orders','wxt_finance_orders.id = wxt_finance_recordss.orders_id');

        $this->db->where("wxt_finance_orders.student_id = {$id}");

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_finance_recordss.id','DESC' );
        $data['recordss'] = $this->db->get("wxt_finance_recordss")->result_array();

        $this->output_json("ok","缴费记录",$data);
    }

    /**
     * 课堂点评
     * @param int $id
     */
    public function evaluation($id = 0)
    {
        $this->is_my_school($id,'student');

        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        //统计总数
        $this->db->where("student_id = {$id}");
        $data['count'] = $this->db->count_all_results("wxt_class_evaluation");

        $this->db->select("wxt_class_evaluation.id as evaluation_id,wxt_class_evaluation.status,wxt_class_evaluation.time,wxt_class.class_name,wxt_subject.subject as subject_name,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time,wxt_class_evaluation.jl,wxt_class_evaluation.td,wxt_class_evaluation.zs,wxt_class_evaluation.info,wxt_user.name as teacher");

        $this->db->join("wxt_class_curriculum","wxt_class_curriculum.id = wxt_class_evaluation.curriculum_id");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_curriculum.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_curriculum.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_evaluation.teacher_id");

        $this->db->where(array('wxt_class_evaluation.student_id'=>$id));

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_evaluation.time','DESC' );
        $data['evaluation'] = $this->db->get("wxt_class_evaluation")->result_array();

        $this->output_json("ok","课堂点评",$data);

    }

    /**
     * 综合统计
     * @param int $id
     */
    public function stats_radar($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->db->select("avg(wxt_class_evaluation.jl) as jl,avg(wxt_class_evaluation.td) as td,avg(wxt_class_evaluation.zs) as zs");
        $this->db->where("wxt_class_evaluation.student_id = {$id}");
        $this->db->join("wxt_class_curriculum","wxt_class_curriculum.id = wxt_class_evaluation.curriculum_id");
        $data['evaluation'] = $this->db->get('wxt_class_evaluation')->row_array();


        $this->db->select("avg(wxt_class_work_log.score) as zy");
        $this->db->where("wxt_class_work_log.student_id = {$id}");
        $this->db->join("wxt_class_work","wxt_class_work.id = wxt_class_work_log.class_work_id");
        $data['class_work'] = $this->db->get('wxt_class_work_log')->row_array();

        $this->db->select("status");
        $this->db->where(array('student_id'=>$id));
        $data['attence'] = $this->db->get('wxt_class_attence')->result_array();
        $attence = array();
        foreach ($data['attence'] as $item){
            if($item['status'] == '1'){
                $attence[] = 5;
            }else{
                $attence[] = 0;
            }
        }
        if(empty($attence)){
            $data['attence'] = 5;
        }else{
            $data['attence'] = array_sum($attence) / count($attence);
        }



        $data = array(
            'qd' => intval($data['attence']),
            'jl' => intval($data['evaluation']['jl']),
            'td' => intval($data['evaluation']['td']),
            'zy' => intval($data['class_work']['zy']),
            'zs' => intval($data['evaluation']['zs'])
        );

        $this->output_json("ok","综合统计",$data);

    }

    /**
     * 作业记录
     * @param int $id
     */
    public function work($id = 0)
    {
        $this->is_my_school($id,'student');

        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        //统计总数
        $this->db->where("student_id = {$id}");
        $data['count'] = $this->db->count_all_results("wxt_class_work_log");

        $this->db->select("wxt_class.class_name,wxt_user.name as teacher,wxt_subject.subject,wxt_class_work.id,wxt_class_work.title,wxt_class_work.time,wxt_class_work_log.score,wxt_class_work_log.comments,wxt_class_work_log.comments_time");

        $this->db->join("wxt_class_work","wxt_class_work.id = wxt_class_work_log.class_work_id");
        $this->db->join("wxt_class","wxt_class.id = wxt_class_work.class_id");
        $this->db->join("wxt_subject","wxt_subject.id = wxt_class_work.subject_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_class_work.teacher_id");

        $this->db->where(array('wxt_class_work_log.student_id'=>$id,'is_comments'=>1));

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_work_log.time','DESC' );
        $data['work'] = $this->db->get("wxt_class_work_log")->result_array();

        $this->output_json("ok","作业记录",$data);

    }

    public function service($id = 0,$action='',$log_id = 0)
    {

        $this->is_my_school($id, 'student');

        if($this->input->method() == 'get'){
            //参数设置

            $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
            $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
            $page = ($page - 1) * $limit;
            $data['limit'] = $limit;


            //统计总数
            $this->db->where("student_id", $id);
            $data['count'] = $this->db->count_all_results("wxt_student_service_log");



            //查询数据
            $this->db->select("wxt_student_service_log.*,wxt_user.name as staff_name,wxt_user_info.face,wxt_user_info.job_description");

            $this->db->where("wxt_student_service_log.student_id", $id);
            $this->db->join("wxt_user","wxt_user.id = wxt_student_service_log.user_id");
            $this->db->join("wxt_user_info","wxt_user_info.uid = wxt_student_service_log.user_id");

            $this->db->limit($limit, $page);
            $this->db->order_by('wxt_student_service_log.time', 'DESC');
            $data['track'] = $this->db->get("wxt_student_service_log")->result_array();

            $this->output_json("ok", "追踪记录", $data);
        }else{

            //添加记录
            if($action == 'add'){

                $this->form_validation->set_rules('content', '内容', 'trim|required');
                $this->form_validation->set_rules('type', '信息类型', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $log = array(
                    'content'=>$this->input->post('content'),
                    'school_id'=>$this->session->school['id'],
                    'student_id'=>$id,
                    'user_id'=>$this->session->user['id'],
                    'type_id'=>$this->input->post('type'),
                    'time'=>time()
                );



                //添加任务
                if($this->db->insert('wxt_student_service_log',$log)){
                    $this->output_json('ok', '添加成功', $this->db->insert_id());
                } else {
                    $this->output_json('error', '添加失败', $log);
                }
            }elseif($action == 'del'){
                $this->is_my_school($log_id, 'student_service_log');

                $this->db->delete('wxt_student_service_log',array('id'=>$log_id));
                $this->output_json("ok", "记录已删除");

            }


        }
    }

    /**
     * 添加学员
     */
    public function add()
    {

        $this->form_validation->set_rules('campus_id', '校区', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('number', '学号', 'trim|required');
        $this->form_validation->set_rules('phone', '电话', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $this->output_json('error', validation_errors(' ', ' '));
        }else{
            $student = $this->input->post(array(
                'campus_id',
                'name',
                'number',
                'sex',
                'birthday',
                'school',
                'grade_id',
                'phone',
                'email',
                'qq',
                'weixin',
                'address',
                'remark',
                'reg_time',
                'sales_id',
                'intention_id',
                'zy_id',
                'gw_id'
            ),TRUE);

            $student['school_id'] = $this->session->school['id'];
            $student['birthday'] = strtotime($student['birthday']);
            $student['reg_time'] = strtotime($student['reg_time']);
            $student['reg_time'] = empty($student['reg_time'])?time():$student['reg_time'];


            $this->db->where(array("school_id"=>$this->session->school['id'],"number"=>$student['number']));
            if($this->db->count_all_results("wxt_student") > 0){
                $this->output_json('error','当前学号已被占用');
            }

            $this->db->where(array("school_id"=>$this->session->school['id'],"name"=>$student['name'],"phone"=>$student['phone']));
            if($this->db->count_all_results("wxt_student") > 0){
                $this->output_json('error','当前学生信息已存在');
            }

            $this->db->insert("wxt_student",$student);
            $student['id'] = $this->db->insert_id();
            if (empty($student['id'])){
                $this->output_json('error','添加失败');
            }else{
                $this->output_json('ok','添加成功',$student);
            }
        }
    }

    /**
     * 修改学员
     * @param int $id
     */
    public function edit($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('number', '学号', 'trim|required');
        $this->form_validation->set_rules('phone', '电话', 'trim|required|numeric');


        if ($this->form_validation->run() == FALSE){

            $this->output_json('error',validation_errors(' ',' '));
        }else{
            $student = $this->input->post(array(
                'name',
                'number',
                'reg_time',
                'sex',
                'birthday',
                'school',
                'grade_id',
                'phone',
                'email',
                'qq',
                'weixin',
                'address',
                'remark',
                'reg_time',
                'zy_id',
                'gw_id'
            ),TRUE);

            $student['birthday'] = strtotime($student['birthday']);
            $student['reg_time'] = strtotime($student['reg_time']);


            $this->db->where("school_id = {$this->session->school['id']} and number = {$student['number']} and id <>  $id");
            if($this->db->count_all_results("wxt_student") > 0){
                $this->output_json('error','当前学号已被占用');
            }

            $this->db->where(array("school_id"=>$this->session->school['id'],"name"=>$student['name'],"phone"=>$student['phone'],'id <>'=>$id));
            if($this->db->count_all_results("wxt_student") > 0){
                $this->output_json('error','当前学生信息已存在');
            }

            $this->db->where("id = $id");

            if($this->db->update("wxt_student",$student)){
                $this->output_json('ok','学生资料已修改！');
            }else{
                $this->output_json('error','修改失败');
            }


        }

    }


    /**
     * 删除学员
     * @param $id
     */
    public function del($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->db->where(array("student_id"=>$id));
        $count = $this->db->count_all_results("wxt_class_student");

        $this->db->where(array("student_id"=>$id));
        $count2 = $this->db->count_all_results("wxt_finance_orders");


        if($count > 0 or $count2 > 0){
            $this->output_json('error', "已有报名或缴费信息不允许删除！");
        }else{
            $this->db->where(array("id"=>$id))->delete("wxt_student");
            $this->db->where(array("student_id"=>$id))->delete("wxt_student_contact");
            $this->db->where(array("uid"=>$id))->delete("wxt_student_weixin");
            $this->output_json("ok","学员信息已删除");
        }

    }

    /**
     * 缴费
     * @param int $id
     */
    public function payment($id = 0)
    {
        $this->is_my_school($id,'student');

        $this->db->where("id",$id);
        $student = $this->db->get("wxt_student")->row_array();

        $this->form_validation->set_rules('payee_time', '收费时间', 'trim|required');
        $this->form_validation->set_rules('account', '财务账户', 'trim|required');
        $this->form_validation->set_rules('paylist', '收费项目', 'trim|required');

        if ($this->form_validation->run() == FALSE){

            $this->output_json('error',validation_errors(' ',' '));
        }
        //获取缓存的订单数据
        $paylist = $this->input->post('paylist');
        $paylist = json_decode($paylist,true);




        //获取历史订单号
        $this->db->select("ordersid");
        $this->db->where("id",$student['school_id']);
        $sc = $this->db->get("wxt_school")->row_array();
        //生成新订单号
        $ordersid = $sc['ordersid']+1;
        //更新历史订单号
        $this->db->where("id",$student['school_id']);
        $this->db->update("wxt_school",array('ordersid'=>$ordersid));
        $ordersid = date("Ymd").$this->session->school['id'].$ordersid;

        $orders['id'] = $ordersid;
        //学校ID
        $orders['school_id'] = $this->session->school['id'];
        //校区ID
        $orders['campus_id'] = $student['campus_id'];
        //学生ID
        $orders['student_id'] = $id;
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
        $orders['zy_id'] = $this->input->post('zy_id');
        $orders['gw_id'] = $this->input->post('gw_id');

        $this->db->where("id",$orders['payee_account_id']);
        $school_account = $this->db->get("wxt_school_account")->row_array();
        $orders['payee_account'] =  $school_account['account_name'];
        //开始事务
        $this->db->trans_start();


        //生成订单
        $this->db->insert("wxt_finance_orders",$orders);
        //$orders_id = $this->db->insert_id();
        $orders_id = $ordersid;


        foreach ($paylist as $item){
            if($item['fee_type'] == '0'){

                $recordss = array(
                    'orders_id'=>$orders_id,
                    'school_id'=>$this->session->school['id'],
                    'type'=>'1',
                    'fee_name'=>$item['fee_name'],
                    'fee_type'=>'0',
                    'fee_type_id'=>$item['fee_id'],
                    'fee_mode'=>$item['fee_mode'],
                    'quantity'=>$item['quantity'],
                    'unit'=>$item['unit'],
                    'price'=>$item['price'],
                    'discount'=>$item['discount'],
                    'preferential'=>$item['preferential'],
                    'actual'=>$item['actual']);



                $this->db->where("class_id = {$item['fee_id']} and student_id = {$student['id']}");
                if($this->db->count_all_results("wxt_class_student") > 0){
                    $this->output_json('error', '不能重复报班！');
                    exit;
                }



                //加入流水
                $this->db->insert('wxt_finance_recordss',$recordss);
                $recordss_id = $this->db->insert_id();

                $this->db->where("id",$item['fee_id']);
                $class = $this->db->get("wxt_class")->row_array();
                if($class['class_type'] == '1'){
                    //加入班级
                    $this->db->insert('wxt_class_student',array(
                        'class_id'=>$item['fee_id'],
                        'school_id'=>$this->session->school['id'],
                        'student_id'=>$id,
                        'reg_time'=>time(),
                        'buy_quantity'=>$item['quantity'],
                        'buy_recordss_id'=>$recordss_id,
                        'start_time'=> empty($item['start_time'])?0:$item['start_time'],
                        'end_time'=> empty($item['end_time'])?0:$item['end_time']
                    ));

                }else{
                    //加入班级
                    $this->db->insert('wxt_class_student',array(
                        'class_id'=>$item['fee_id'],
                        'school_id'=>$this->session->school['id'],
                        'student_id'=>$id,
                        'reg_time'=>time(),
                        'buy_quantity'=>$item['quantity'],
                        'buy_recordss_id'=>$recordss_id
                    ));

                }




                $log['class_id'] = $item['fee_id'];
                $log['student_id'] = $id;
                $log['action'] = '加入班级';
                $log['time'] = time();
                $log['detail'] = "报名加入班级";
                $log['user_id'] = $this->session->user['id'];

                $this->db->insert('wxt_class_student_log',$log);
            }else{

                $recordss = array(
                    'orders_id'=>$orders_id,
                    'school_id'=>$this->session->school['id'],
                    'type'=>'1',
                    'fee_name'=>$item['fee_name'],
                    'fee_type'=>'1',
                    'fee_type_id'=>$item['fee_id'],
                    'fee_mode'=>$item['fee_mode'],
                    'quantity'=>$item['quantity'],
                    'unit'=>$item['unit'],
                    'price'=>$item['price'],
                    'discount'=>$item['discount'],
                    'preferential'=>$item['preferential'],
                    'actual'=>$item['actual']);

                //加入流水
                $this->db->insert('wxt_finance_recordss',$recordss);

            }
        }


        //结束事务
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE)
        {
            $this->output_json('error', '录入数据时发生了意外的错误！');
        }else{
            //生成动态信息
            $feed = array(
                "student_id" => $id,
                "time" => time(),
                "type" => "2",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '缴费成功通知',
                "msg" => "{$student['name']}同学您好，您已经缴费成功。",
                "url" => "#/My/Orders/{$orders_id}"
            );
            $this->db->insert("wxt_feed", $feed);

            $orders['all_actual'] = 0.00;
            foreach ($paylist as $item) {
                $orders['all_actual'] += $item['actual'];
            }

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM400962287"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();

            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            //获取学生信息
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
            $this->db->where("wxt_student_weixin.uid = {$student['id']}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
            $student = $this->db->get("wxt_student_weixin")->result_array();
            foreach ($student as $item)
            {
                $msg['template_id'] =  $temp['temp_id'];//
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['first']    = "{$item['name']}同学您好，您已缴费成功。";//标题
                $msg['keyword1'] = $item['name'];//
                $msg['keyword2'] = $item['phone'];//老师
                $msg['keyword3'] = '#'.$orders_id;//时间
                $msg['keyword4'] = $orders['payee_account'];//内容
                $msg['keyword5'] = $orders['all_actual'].'元';//内容
                $msg['remark'] = '点击查看详细信息。';//内容
                $msg['url'] = 'http://wx.eduwxt.com/student/#/My/Orders/'.$orders_id."?token=".$this->session->school['pigcms_token']."&wecha_id=".$item['wecha_id'];//内容

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
            }

            //模版消息
            $this->output_json('ok', '收费数据已经成功录入！',array('orders_id'=>$orders_id));
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
        $this->form_validation->set_rules('quantity', '续报次数', 'trim|required|numeric');
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
        $recordss['unit'] = '次';
        $recordss['price'] = $class['price'];
        $recordss['preferential'] = ($quantity * $class['price']) - $actual;
        $recordss['actual'] = $actual;
        $this->db->insert('wxt_finance_recordss', $recordss);

        $this->db->where("id = {$student['id']}");
        $this->db->update('wxt_class_student', array('buy_quantity' => $buy_quantity));

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
            $log['detail'] = "增加课次：".$quantity;
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);


            $this->output_json('ok', '操作成功！',array('orders_id'=>$orders_id));
        }

    }

    /**
     * 退费
     * @param int $sid 学生id
     * @param int $rs_id
     */
    public function refund($sid = 0,$rs_id = 0)
    {
        $this->is_my_school($sid,'student');

        $this->form_validation->set_rules('actual', '退款金额', 'trim|required|numeric');
        $this->form_validation->set_rules('account', '退款账户', 'trim|required|numeric');
        $this->form_validation->set_rules('payee_time', '退款时间', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        } else {
            //开始事务
            $this->db->trans_start();

            $student = $this->db->where('id',$sid)->get("wxt_student")->row_array();
            $rs = $this->db->where('id',$rs_id)->get("wxt_finance_recordss")->row_array();

            if($rs['actual'] < $this->input->post('actual')){
                $this->output_json('error', "退款金额不能大于收款金额！");
            }


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

            $orders['school_id'] = $this->session->school['id'];
            $orders['campus_id'] = $student['campus_id'];
            $orders['student_id'] = $sid;
            $orders['type'] = '0';
            $orders['payee_id'] = $this->session->user['id'];
            $orders['payee'] = $this->session->user['name'];
            //退款账户
            $orders['payee_account_id'] = $this->input->post('account');
            $this->db->where("id",$orders['payee_account_id']);
            $school_account = $this->db->get("wxt_school_account")->row_array();
            $orders['payee_account'] =  $school_account['account_name'];

            $orders['payee_time'] = $this->input->post('payee_time');
            $orders['reg_time'] = time();
            $orders['remark'] = $this->input->post('remark');

            $this->db->insert('wxt_finance_orders',$orders);
            $orders_id = $this->db->insert_id();


            $recordss['actual'] = $this->input->post('actual');
            $recordss['school_id'] = $this->session->school['id'];
            $recordss['orders_id'] = $orders_id;
            $recordss['type'] = '0';
            $recordss['fee_name'] = '退费-' . $rs['fee_name'];
            $recordss['fee_type'] = $rs['fee_type'];
            $recordss['fee_type_id'] = $rs_id;
            $recordss['quantity'] = '1';

            $this->db->insert('wxt_finance_recordss',$recordss);


            //结束事务
            $this->db->trans_complete();


            if ($this->db->trans_status() === FALSE)
            {
                $this->output_json('error', '失败！');
            }else{

                $this->output_json('ok', '退费信息录入成功！',array('orders_id'=>$orders_id));
            }


        }
        
    }


    /**
     * 学生统计
     * @param int $sid
     * @param string $type
     */
    public function count($sid = 0,$type = '')
    {
        switch ($type){
            case 'work':
                $this->db->where("student_id = {$sid} and score = '1'");
                $data['score']['1'] = $this->db->count_all_results('wxt_class_work_log');
                $this->db->where("student_id = {$sid} and score = '2'");
                $data['score']['2'] = $this->db->count_all_results('wxt_class_work_log');
                $this->db->where("student_id = {$sid} and score = '3'");
                $data['score']['3'] = $this->db->count_all_results('wxt_class_work_log');
                $this->db->where("student_id = {$sid} and score = '4'");
                $data['score']['4'] = $this->db->count_all_results('wxt_class_work_log');
                $this->db->where("student_id = {$sid} and score = '5'");
                $data['score']['5'] = $this->db->count_all_results('wxt_class_work_log');

                break;
            case 'attence':
                //签到状态 0未 1签 2 请假 3旷课 4预请假
                $this->db->where("student_id = {$sid} and status = '1'");
                $data['attence']['1'] = $this->db->count_all_results('wxt_class_attence');
                $this->db->where("student_id = {$sid} and status = '2'");
                $data['attence']['2'] = $this->db->count_all_results('wxt_class_attence');
                $this->db->where("student_id = {$sid} and status = '3'");
                $data['attence']['3'] = $this->db->count_all_results('wxt_class_attence');
                break;
            case 'evaluation':
                $this->db->select("avg(wxt_class_evaluation.jl) as jl,avg(wxt_class_evaluation.td) as td,avg(wxt_class_evaluation.zs) as zs");
                $this->db->where("wxt_class_evaluation.student_id = {$sid}");
                $data['evaluation'] = $this->db->get('wxt_class_evaluation')->row_array();


                $this->db->select("avg(wxt_class_work_log.score) as zy");
                $this->db->where("wxt_class_work_log.student_id = {$sid}");
                $data['class_work'] = $this->db->get('wxt_class_work_log')->row_array();

                $this->db->select("status");
                $this->db->where(array('student_id'=>$sid));
                $data['attence'] = $this->db->get('wxt_class_attence')->result_array();
                $attence = array();
                foreach ($data['attence'] as $item){
                    if($item['status'] == '1'){
                        $attence[] = 5;
                    }else{
                        $attence[] = 0;
                    }
                }
                if(empty($attence)){
                    $data['attence'] = 5;
                }else{
                    $data['attence'] = array_sum($attence) / count($attence);
                }



                $data['radar'] = array(
                    'qd' => intval($data['attence']),
                    'jl' => intval($data['evaluation']['jl']),
                    'td' => intval($data['evaluation']['td']),
                    'zy' => intval($data['class_work']['zy']),
                    'zs' => intval($data['evaluation']['zs'])
                );
                unset($data['attence']);
                unset($data['class_work']);
                unset($data['evaluation']);

                break;
            default:
                $this->output_json('error', '参数不正确！');

        }


        $this->output_json('ok', '统计信息！',$data);
    }

    /**
     * 班级日志
     * @param int $class_id
     * @param int $sid
     */
    public function class_log($sid = 0 , $class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;


        $this->db->where(array('class_id'=>$class_id,'student_id'=>$sid));
        $data['count'] = $this->db->count_all_results('wxt_class_student_log');

        $this->db->select("wxt_class_student_log.*,wxt_user.name as teacher_name,wxt_class.class_name");
        $this->db->where(array('wxt_class_student_log.class_id'=>$class_id,'wxt_class_student_log.student_id'=>$sid));
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_student_log.user_id");
        $this->db->join("wxt_class", "wxt_class.id = wxt_class_student_log.class_id");

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_student_log.id', 'desc');
        $data['log'] = $this->db->get('wxt_class_student_log')->result_array();
        $this->output_json("ok","班级日志",$data);


    }



}