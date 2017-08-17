<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/30
 * Time: 下午12:08
 */
class Classs_student extends MY_Controller
{
    /**
     * 班级学员
     * @param int $class_id
     */
    public function index($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;


        $this->db->where(array('class_id'=>$class_id));
        $data['count'] = $this->db->count_all_results('wxt_class_student');


        $this->db->select('wxt_student.id,wxt_student.name,wxt_student.number,wxt_student.sex,wxt_student.phone,wxt_class_student.buy_quantity,wxt_class_student.reg_time as add_class_time,wxt_class_student.status as student_status,wxt_class_student.start_time,wxt_class_student.end_time');

        $this->db->where(array('wxt_class_student.class_id' =>$class_id));

        $this->db->join('wxt_student', 'wxt_student.id = wxt_class_student.student_id','left');
        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_student.reg_time', 'asc');
        $data['student'] = $this->db->get('wxt_class_student')->result_array();

        $this->db->where("id",$class_id);
        $class = $this->db->get("wxt_class")->row_array();

        if($class['class_type'] == '1'){
            foreach ($data['student'] as &$item) {

                $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['id'],'status'=>'1'));
                $item['count']['qiandao'] = $this->db->count_all_results('wxt_class_tuoguan_log');

                $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['id'],'status'=>'2'));
                $item['count']['qiantui'] = $this->db->count_all_results('wxt_class_tuoguan_log');

                $this->db->where(array('class_id'=>$class_id,'student_id'=>$item['id'],'status'=>'3'));
                $item['count']['qingjia'] = $this->db->count_all_results('wxt_class_tuoguan_log');

            }
        }else{
            foreach ($data['student'] as &$item) {

                $this->db->where("class_id = {$class_id} and student_id = {$item['id']} and is_xk = '1'");
                $item['kx'] = $this->db->count_all_results("wxt_class_attence");

                $this->db->where("class_id = {$class_id} and student_id = {$item['id']} and status = '2'");
                $item['leave_count'] = $this->db->count_all_results("wxt_class_attence");

            }
        }




        $this->output_json("ok","班级学员",$data);

    }

    /**
     * 班级学员人数统计
     * @param int $class_id
     */
    public function count($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->db->where(array('class_id'=>$class_id));
        $data['count'] = $this->db->count_all_results('wxt_class_student');
        $this->output_json("ok","班级学员人数统计",$data);
    }

    /**
     * 删除班级学员
     * @param int $class_id
     * @param int $sid
     */
    public function del($class_id = 0,$sid = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');

        //删除班级学员
        $this->db->where("student_id = {$sid} and class_id = {$class_id}");
        $this->db->delete("wxt_class_student");
        //删除班级签到信息
        $this->db->where("student_id = {$sid} and class_id = {$class_id}");
        $this->db->delete("wxt_class_attence");



        $this->db->select("name");
        $this->db->where("id = {$sid}");
        $student = $this->db->get("wxt_student")->row_array();
        $this->db->where("id = {$class_id}");
        $class = $this->db->get("wxt_class")->row_array();
        //记录日志
        //$this->autolog("班级学员管理","将{$class['class_name']} 班级的学生 {$student['name']} 从当前班级删除。");

        $log['class_id'] = $class_id;
        $log['student_id'] = $sid;
        $log['action'] = '移除学员';
        $log['time'] = time();
        $log['detail'] = "从班级移除学员";
        $log['user_id'] = $this->session->user['id'];

        $this->db->insert('wxt_class_student_log',$log);


        $this->output_json('ok', '学员信息删除成功');

    }


    /**
     * 学员状态
     * @param int $class_id
     * @param int $sid
     */
    public function status($class_id = 0, $sid = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');

        $this->db->select("name");
        $this->db->where("id = {$sid}");
        $student = $this->db->get("wxt_student")->row_array();
        $this->db->where("id = {$class_id}");
        $class = $this->db->get("wxt_class")->row_array();


        $this->db->where("student_id = {$sid} and class_id = {$class_id}");
        if(empty($this->input->post('status'))){
            $this->db->update("wxt_class_student", array('status' => '0'));

            //记录日志
            //$this->autolog("班级学员管理","将{$class['class_name']} 班级的学生 {$student['name']} 停课。");

            $log['class_id'] = $class_id;
            $log['student_id'] = $sid;
            $log['action'] = '停课';
            $log['time'] = time();
            $log['detail'] = "停止上课";
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);


            $this->output_json('ok', '学员已停课');
        }else{
            $this->db->update("wxt_class_student", array('status' => '1'));

            //记录日志
            //$this->autolog("班级学员管理","将{$class['class_name']} 班级的学生 {$student['name']} 复课。");


            $log['class_id'] = $class_id;
            $log['student_id'] = $sid;
            $log['action'] = '复课';
            $log['time'] = time();
            $log['detail'] = "恢复上课";
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);

            $this->output_json('ok', '学员已复课');
        }

    }


    /**
     * 转班
     * @param int $class_id
     * @param int $sid
     */
    public function change($class_id = 0, $sid = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');

        $this->form_validation->set_rules('class_id', '转入班级', 'trim|required|numeric');
        $this->form_validation->set_rules('quantity', '课次数量', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE){
            $this->output_json('error',validation_errors(' ',' '));
        }
        $this->db->select("name");
        $this->db->where("id = {$sid}");
        $student = $this->db->get("wxt_student")->row_array();

        $this->db->where("id = {$class_id}");
        $class = $this->db->get("wxt_class")->row_array();
//        $this->db->where("class_id = {$class_id} and student_id = {$sid}");
//        $data['class_student'] = $this->db->get("wxt_class_student")->row_array();

        $new_class_id = $this->input->post('class_id');


        $this->db->where("id = {$new_class_id}");
        $new_class = $this->db->get("wxt_class")->row_array();

        if($new_class['campus_id'] <> $class['campus_id']){
            $this->output_json('error', '不允许跨校区转班！');
        }

        //$this->is_my_school($new_class_id,'class');


        if ($class['fee_method'] == 'cycle') {
            $quantity = 1;
        } else {
            $quantity = $this->input->post('quantity');
        }
        $this->db->where("class_id = {$new_class_id} and student_id = {$sid}");
        if (empty($this->db->count_all_results("wxt_class_student"))) {
            $this->db->where("class_id = {$class_id} and student_id = {$sid}");
            $this->db->update("wxt_class_student", array('class_id' => $new_class_id, 'buy_quantity' => $quantity, 'reg_time' => time(), 'status' => '1'));

            $this->autolog("班级学员管理","将学生 {$student['name']} 从 {$class['class_name']} 班级转入 {$new_class['class_name']} ");

            $log['class_id'] = $class_id;
            $log['student_id'] = $sid;
            $log['action'] = '转班';
            $log['time'] = time();
            $log['detail'] = "从 {$class['class_name']} 班级转入 {$new_class['class_name']}";
            $log['user_id'] = $this->session->user['id'];

            $this->db->insert('wxt_class_student_log',$log);


            $this->output_json('ok', '操作成功！');

        } else {
            $this->output_json('error', '此学生在当前班级已有报名信息！');
        }
    }

    /**
     * 调整课程数量
     * @param int $class_id
     * @param int $sid
     */
    public function edit($class_id = 0, $sid = 0)
    {

        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');

        $this->form_validation->set_rules('type', '操作', 'trim|required');
        $this->form_validation->set_rules('quantity', '课次数量', 'trim|required|numeric');
        $this->form_validation->set_rules('remark', '调整原因', 'trim|required');


        if ($this->form_validation->run() == FALSE){
            $this->output_json('error',validation_errors(' ',' '));
        }
        $this->db->where("id = {$class_id}");
        $class = $this->db->get("wxt_class")->row_array();

        if($class['fee_method'] == 'cycle'){
            $this->output_json('error','按期计费不支持调整课次');
        }
        $this->db->select("wxt_student.name,wxt_class_student.*");
        $this->db->where("wxt_class_student.class_id = {$class_id} and wxt_class_student.student_id = {$sid}");
        $this->db->join('wxt_student',"wxt_student.id = wxt_class_student.student_id");
        $student = $this->db->get("wxt_class_student")->row_array();

        $this->db->where("class_id = {$class_id} and student_id = {$sid} and is_xk = '1'");
        $student['kx'] = $this->db->count_all_results("wxt_class_attence");
        $quantity = $this->input->post('quantity');

        if($this->input->post('type') == 'cut'){
           if( ($student['buy_quantity'] -  $student['kx']) >= $quantity){
               $buy_quantity = $student['buy_quantity']-$quantity;

           }else{
               $this->output_json('error','要减少的课次不能大于剩余课次！');
           }

        }elseif ($this->input->post('type') == 'add'){
            $buy_quantity = 0 + $student['buy_quantity'] + $quantity;
        }else{
            $this->output_json('error','参数错误！');
        }


        $this->db->where("id",$student['id']);
        $this->db->update("wxt_class_student",array('buy_quantity'=>$buy_quantity));

        //$this->autolog("班级学员管理","调整学生 {$student['name']} 在 {$class['class_name']} 班级的课次为 {$buy_quantity} ");

        $action = ($this->input->post('type') == 'add')?'增加课次':'减少课次';
        $log['class_id'] = $class_id;
        $log['student_id'] = $sid;
        $log['action'] = '调整课次';
        $log['time'] = time();
        $log['detail'] = $action.$this->input->post('quantity').'，备注：'.$this->input->post('remark');
        $log['user_id'] = $this->session->user['id'];

        $this->db->insert('wxt_class_student_log',$log);

        $this->output_json('ok', '调整成功！',$buy_quantity);

    }

    /**
     * 学生在班级的基本信息
     * @param int $class_id
     * @param int $sid
     */
    public function view($class_id = 0, $sid = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($sid,'student');

        $this->db->select("wxt_student.id,wxt_student.name,wxt_student.number,wxt_class_student.reg_time,wxt_class_student.status,wxt_class_student.buy_quantity");
        $this->db->where("wxt_class_student.student_id = {$sid}");
        $this->db->join('wxt_student',"wxt_student.id = wxt_class_student.student_id");
        $student = $this->db->get("wxt_class_student")->row_array();

        $this->db->where("class_id = {$class_id} and student_id = {$sid} and is_xk = '1'");
        $student['kx'] = $this->db->count_all_results("wxt_class_attence");

        $this->output_json('ok', '学生在班级的基本信息！',$student);

    }




}