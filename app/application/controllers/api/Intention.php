<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/16
 * Time: 下午10:46
 */
class Intention extends MY_Controller
{
    /**
     * 意向客户列表
     */
    public function index()
    {
        //参数设置

        $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
        $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
        $page = ($page - 1) * $limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;
        $order_field = empty($this->input->get('field')) ? 'id' : $this->input->get('field');
        $sort = empty($this->input->get('sort')) ? 'DESC' : $this->input->get('sort');


        //统计总数
        if($this->input->get('lecture') <> ''){
            $this->db->select("distinct(intention_id) as id");
            $this->db->where("school_id", $this->session->user['school_id']);
            $booking_intention = $this->db->get("wxt_intention_lecture_booking")->result_array();

            $intention = array();
            foreach ($booking_intention as $item){
                $intention[] = $item['id'];
            }

            if(empty($intention)){
                $intention[] = 0;
            }
            if($this->input->get('lecture') == 1){
                $this->db->where_in('id',$intention);
            }elseif($this->input->get('lecture') == 0){
                $this->db->where_not_in('id',$intention);
            }




        }

        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if (!empty($this->input->get('status_id'))) {
            $this->db->where("status_id", $this->input->get('status_id'));
        }

        if (!empty($this->input->get('source_id'))) {
            $this->db->where("source_id", $this->input->get('source_id'));
        }

        if (!empty($this->input->get('level_id'))) {
            $this->db->where("level_id", $this->input->get('level_id'));
        }

        if (!empty($this->input->get('zy_id'))) {
            $this->db->where("zy_id", $this->input->get('zy_id'));
        }

        if (!empty($this->input->get('gw_id'))) {
            $this->db->where("gw_id", $this->input->get('gw_id'));
        }




        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_intention");
        if (!empty($this->input->get('count'))) {
            $this->output_json("ok", "统计数据", $data);
        }


        //查询数据
        $this->db->select("id,campus_id,name,age,phone,subject_id,level_id,status_id,source_id,reg_user,reg_time,last_time,last_user,next_time,next_user");
        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if (!empty($this->input->get('status_id'))) {
            $this->db->where("status_id", $this->input->get('status_id'));
        }

        if (!empty($this->input->get('source_id'))) {
            $this->db->where("source_id", $this->input->get('source_id'));
        }

        if (!empty($this->input->get('level_id'))) {
            $this->db->where("level_id", $this->input->get('level_id'));
        }

        if (!empty($this->input->get('zy_id'))) {
            $this->db->where("zy_id", $this->input->get('zy_id'));
        }

        if (!empty($this->input->get('gw_id'))) {
            $this->db->where("gw_id", $this->input->get('gw_id'));
        }

        if($this->input->get('lecture') <> ''){

            if($this->input->get('lecture') == 1){
                $this->db->where_in('id',$intention);
            }elseif($this->input->get('lecture') == 0){
                $this->db->where_not_in('id',$intention);
            }


        }


        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit, $page);
        $this->db->order_by($order_field, $sort);
        $data['intention'] = $this->db->get("wxt_intention")->result_array();


        foreach ($data['intention'] as &$item){

            $this->db->where("intention_id",$item['id']);
            $item['log_count'] = $this->db->count_all_results('wxt_intention_log');

        }

        $this->output_json("ok", "意向列表", $data);


    }

    /**
     * 我的关联学员
     */
    public function my()
    {
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

        if (!empty($this->input->get('status_id'))) {
            $this->db->where("status_id", $this->input->get('status_id'));
        }

        if (!empty($this->input->get('source_id'))) {
            $this->db->where("source_id", $this->input->get('source_id'));
        }

        if (!empty($this->input->get('level_id'))) {
            $this->db->where("level_id", $this->input->get('level_id'));
        }


        if($this->input->get('type') == 'all'){
            $this->db->group_start();
            $this->db->where("reg_user = {$this->session->user['id']}");
            $this->db->or_where("zy_id = {$this->session->user['id']}");
            $this->db->or_where("gw_id = {$this->session->user['id']}");
            $this->db->or_where("id in(select intention_id from wxt_intention_log where user_id = {$this->session->user['id']} )");
            $this->db->group_end();
        }

        if($this->input->get('type') == 'reg'){
            $this->db->where("reg_user = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'zy'){
            $this->db->where("zy_id = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'gw'){
            $this->db->where("gw_id = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'gz'){
            $this->db->where("id in(select intention_id from wxt_intention_log where user_id = {$this->session->user['id']} )");
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_intention");
       // echo $this->db->last_query();exit;
        if (!empty($this->input->get('count'))) {
            $this->output_json("ok", "统计数据", $data);
        }


        //查询数据
        $this->db->select("id,campus_id,name,age,phone,subject_id,level_id,status_id,source_id,reg_user,reg_time,last_time,last_user,next_time,next_user");
        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if (!empty($this->input->get('status_id'))) {
            $this->db->where("status_id", $this->input->get('status_id'));
        }

        if (!empty($this->input->get('source_id'))) {
            $this->db->where("source_id", $this->input->get('source_id'));
        }

        if (!empty($this->input->get('level_id'))) {
            $this->db->where("level_id", $this->input->get('level_id'));
        }


        if($this->input->get('type') == 'all'){
            $this->db->group_start();
            $this->db->where("reg_user = {$this->session->user['id']}");
            $this->db->or_where("zy_id = {$this->session->user['id']}");
            $this->db->or_where("gw_id = {$this->session->user['id']}");
            $this->db->or_where("id in(select intention_id from wxt_intention_log where user_id = {$this->session->user['id']} )");
            $this->db->group_end();
        }

        if($this->input->get('type') == 'reg'){
            $this->db->where("reg_user = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'zy'){
            $this->db->where("zy_id = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'gw'){
            $this->db->where("gw_id = {$this->session->user['id']}");
        }

        if($this->input->get('type') == 'gz'){
            $this->db->where("id in(select intention_id from wxt_intention_log where user_id = {$this->session->user['id']} )");
        }




        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit, $page);
        $this->db->order_by($order_field, $sort);
        $data['intention'] = $this->db->get("wxt_intention")->result_array();

        foreach ($data['intention'] as &$item){

            $this->db->where("intention_id",$item['id']);
            $item['log_count'] = $this->db->count_all_results('wxt_intention_log');

        }

        $this->output_json("ok", "意向列表", $data);


    }

    /**
     * 添加意向学生
     */
    public function add()
    {
        $this->form_validation->set_rules('name', '学生姓名', 'trim|required');
        $this->form_validation->set_rules('age', '学生年龄', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required|numeric');
        $this->form_validation->set_rules('campus_id', '意向校区', 'trim|required|numeric');
        $this->form_validation->set_rules('subject_id', '预报科目', 'trim|required|numeric');
        $this->form_validation->set_rules('source_id', '招生渠道', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $intention = $this->input->post(
            array('name', 'age', 'phone', 'sex','birthday', 'school', 'address', 'grade', 'weixin', 'qq', 'email',
                'subject_id', 'source_id', 'level_id', 'campus_id', 'status_id',
                'zy_id', 'gw_id', 'remark'));

        $intention['school_id'] = $this->session->school['id'];
        $intention['reg_user'] = $this->session->user['id'];
        $intention['reg_time'] = time();
        $intention['last_user'] = $this->session->user['id'];
        $intention['last_time'] = time();


        if ($this->db->insert("wxt_intention", $intention)) {
            $data['intention_id'] = $this->db->insert_id();
            $data['content'] = (empty($intention['remark']))?'创建了新的客户资料':$intention['remark'];
            $data['time'] = time();
            $data['user_id'] = $this->session->user['id'];
            $data['school_id'] = $this->session->school['id'];
            $this->db->insert('wxt_intention_log',$data);
            $this->output_json('ok', '添加成功', $this->db->insert_id());
        } else {
            $this->output_json('error', '添加失败', $intention);
        }
    }


    /**
     * 意向详情
     * @param int $id
     */
    public function view($id = 0)
    {
        $this->is_my_school($id, 'intention');
        
        $this->db->where("id",$id);
        $data = $this->db->get("wxt_intention")->row_array();

        $this->db->select('id,name');
        $this->db->where("id",$data['zy_id']);
        $data['zy'] = $this->db->get("wxt_user")->row_array();

        $this->db->select('id,name');
        $this->db->where("id",$data['gw_id']);
        $data['gw'] = $this->db->get("wxt_user")->row_array();
        $this->db->delete("wxt_gtd_new_intention",array('user_id'=>$this->session->user['id'],'intention_id'=>$id));
        $this->output_json("ok", "意向详情", $data);
    }


    /**
     * 修改资料
     * @param int $id
     */
    public function edit($id = 0)
    {
        $this->is_my_school($id, 'intention');

        $this->form_validation->set_rules('name', '学生姓名', 'trim|required');
        $this->form_validation->set_rules('age', '学生年龄', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required|numeric');
        $this->form_validation->set_rules('campus_id', '意向校区', 'trim|required|numeric');
        $this->form_validation->set_rules('subject_id', '预报科目', 'trim|required|numeric');
        $this->form_validation->set_rules('source_id', '招生渠道', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $intention = $this->input->post(
            array('name', 'age', 'phone', 'sex','birthday', 'school', 'address', 'grade', 'weixin', 'qq', 'email',
                'subject_id', 'source_id', 'level_id', 'campus_id', 'status_id',
                'zy_id', 'gw_id', 'remark'));

        $intention['last_user'] = $this->session->user['id'];
        $intention['last_time'] = time();

        $this->db->where('id',$id);

        if ($this->db->update("wxt_intention", $intention)) {
//            if(!empty($intention['zy_id'])){
//                $this->db->insert("wxt_gtd_new_intention",array('school_id'=>$this->session->school['id'],'user_id'=>$intention['zy_id'],'intention_id'=>$id,'sender'=>$this->session->user['id']));
//            }
            $this->output_json('ok', '修改成功', $intention);
        } else {
            $this->output_json('error', '修改失败', $intention);
        }
    }

    /**
     * 删除
     * @param int $id
     */
    public function del($id = 0)
    {
        $this->is_my_school($id, 'intention');



        $this->db->where('intention_id',$id);
        $this->db->delete('wxt_intention_log');

        $this->db->where('intention_id',$id);
        $this->db->delete('wxt_intention_lecture_booking');

        $this->db->where('id',$id);
        $this->db->delete('wxt_intention');

        $this->output_json('ok', '已删除');
    }

    /**
     * 分配顾问
     * @param int $id
     */
    public function gw($id = 0)
    {
        $this->is_my_school($id, 'intention');

        $this->form_validation->set_rules('gw_id', '课程顾问', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', '表单错误',$this->form_validation->error_array());
        }

        $gw_id = $this->input->post('gw_id');



        $this->db->where('id',$id);

        if ($this->db->update("wxt_intention", array('gw_id'=>$gw_id,'status_id'=>'2'))) {

            $this->db->delete("wxt_gtd_new_intention",array('user_id'=>$gw_id,'intention_id'=>$id));

            $this->db->insert("wxt_gtd_new_intention",array('school_id'=>$this->session->school['id'],'user_id'=>$gw_id,'intention_id'=>$id,'sender'=>$this->session->user['id']));


            $intention = $this->db->where('id',$id)->get('wxt_intention')->row_array();
            $gw = $this->db->where('id',$gw_id)->get('wxt_user')->row_array();


            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM200806215"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();

            $msg['wecha_id'] = $gw['wecha_id'];
            $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
            $msg['first'] = "{$this->session->user['name']}老师为您分配了一个新的意向学员，请及时跟进。";
            $msg['keyword1'] = '意向学员-'.$intention['name'];
            $msg['keyword2'] = '暂无';
            $msg['remark'] = '点击查看详情。';//内容
            $msg['url'] = "http://wx.eduwxt.com/school/#/Enrollment/Detail/{$id}/Record/?token={$this->session->school['pigcms_token']}&wecha_id={$gw['wecha_id']}";

            $this->load->model('queue');
            $this->queue->post($this->session->school['pigcms_token'],$msg);



            $this->output_json('ok', '修改成功');
        } else {
            $this->output_json('error', '修改失败');
        }

    }

    /**
     * 追踪记录
     * @param int $id
     * @param string $action
     */
    public function track($id = 0,$action = '',$log_id = 0)
    {
        $this->is_my_school($id, 'intention');

        if($this->input->method() == 'get'){
            //参数设置

            $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
            $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
            $page = ($page - 1) * $limit;
            $data['limit'] = $limit;


            //统计总数
            $this->db->where("wxt_intention_log.intention_id", $id);
            $data['count'] = $this->db->count_all_results("wxt_intention_log");



            //查询数据
            $this->db->select("wxt_intention_log.*,wxt_user.name as staff_name,wxt_user_info.face,wxt_user_info.job_description");

            $this->db->where("wxt_intention_log.intention_id", $id);
            $this->db->join("wxt_user","wxt_user.id = wxt_intention_log.user_id");
            $this->db->join("wxt_user_info","wxt_user_info.uid = wxt_intention_log.user_id");

            $this->db->limit($limit, $page);
            $this->db->order_by('wxt_intention_log.time', 'DESC');
            $data['track'] = $this->db->get("wxt_intention_log")->result_array();

            $this->db->delete("wxt_gtd_intention_log",array('user_id'=>$this->session->user['id'],'intention_id'=>$id));

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
                    'intention_id'=>$id,
                    'user_id'=>$this->session->user['id'],
                    'type_id'=>$this->input->post('type'),
                    'time'=>time()
                );


                //检查下次跟踪时间
                $intention = array('last_time'=>time(),'last_user'=>$this->session->user['id']);
                if(!empty($this->input->post('next_time'))){
                    $intention['next_time'] = $this->input->post('next_time');
                    $intention['next_user'] = empty($this->input->post('next_user'))?$this->session->user['id']:$this->input->post('next_user');
                }
                $this->db->where("id",$id);
                $this->db->update('wxt_intention',$intention);

                //是否生成任务
                if(!empty($this->input->post('task'))){

                    $this->db->where("id",$id);
                    $intention = $this->db->get("wxt_intention")->row_array();

                    $task = array(
                        'school_id'=>$this->session->school['id'],
                        'user_id'=>$this->session->user['id'],
                        'start_time'=>time(),
                        'task_time'=>$intention['next_time'],
                        'content'=>$intention['name'].'跟进计划，'.$log['content']
                        );

                    $this->db->insert('wxt_task',$task);
                    $log['task_id'] = $this->db->insert_id();//获取任务id

                }

                //添加任务
                if($this->db->insert('wxt_intention_log',$log)){
                    $log_id = $this->db->insert_id();

                    $this->db->where('id',$id);
                    $intention = $this->db->get('wxt_intention')->row_array();


                    if(!empty($intention['gw_id'])){
                        $this->db->insert('wxt_gtd_intention_log',array('user_id'=>$intention['gw_id'],'intention_id'=>$id,'log_id'=>$log_id));
                    }
                    if(!empty($intention['zy_id'])){
                        $this->db->insert('wxt_gtd_intention_log',array('user_id'=>$intention['zy_id'],'intention_id'=>$id,'log_id'=>$log_id));
                    }
                    $this->output_json('ok', '添加成功');
                } else {
                    $this->output_json('error', '添加失败', $log);
                }


            }elseif($action == 'del'){
                $this->is_my_school($log_id, 'intention_log');

                $this->db->delete('wxt_intention_log',array('id'=>$log_id));
                $this->output_json("ok", "记录已删除");


            }


        }


        
    }



    /**
     * 预报科目管理
     * @param string $action
     * @param int $id
     */
    public function subject($action = '', $id = 0)
    {
        if (!empty($id)) {
            $this->is_my_school($id, 'intention_subject');
        }


        //获取科目
        if ($this->input->method() == 'get') {
            $this->db->where("school_id", $this->session->school['id']);
            $subject = $this->db->get("wxt_intention_subject")->result_array();
            $this->output_json('ok', '预报名科目', $subject);
        } else {
            //添加科目
            if ($action == 'add') {
                $this->form_validation->set_rules('name', '课程名称', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $name = $this->input->post("name");
                $this->db->insert("wxt_intention_subject", array("school_id" => $this->session->school['id'], "name" => $name));

                $this->output_json('ok', '预报名科目添加成功', $this->db->insert_id());
                //编辑科目
            } elseif ($action == 'edit') {
                $this->form_validation->set_rules('name', '课程名称', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $name = $this->input->post("name");
                $this->db->where("id", $id);
                $this->db->update("wxt_intention_subject", array("name" => $name));

                $this->output_json('ok', '预报名科目修改成功');
                //删除科目
            } elseif ($action == 'del') {

                $this->db->where("subject_id",$id);
                $lecture_count = $this->db->count_all_results('wxt_intention_lecture');
                if($lecture_count > 0){
                    $this->output_json('error', '此科目已有课程安排不能删除！');
                }

                $this->db->delete("wxt_intention_subject", array("id" => $id));

                $this->output_json('ok', '预报名科目已被删除');

            }
        }
    }

    /**
     * 跟进状态
     * @param string $action
     * @param int $id
     */
    public function status($action = '', $id = 0)
    {
        if (!empty($id)) {
            $this->is_my_school($id, 'intention_status');
        }


        //获取状态
        if ($this->input->method() == 'get') {
            $this->db->where("school_id = {$this->session->school['id']} or school_id = '0'");
            $subject = $this->db->get("wxt_intention_status")->result_array();
            $this->output_json('ok', '跟进状态', $subject);
        } else {
            //添加状态
            if ($action == 'add') {
                $this->form_validation->set_rules('name', '状态名称', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $name = $this->input->post("name");
                $this->db->insert("wxt_intention_status", array("school_id" => $this->session->school['id'], "name" => $name));

                $this->output_json('ok', '跟进状态添加成功', $this->db->insert_id());
                //编辑状态
            } elseif ($action == 'edit') {
                $this->form_validation->set_rules('name', '状态名称', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $name = $this->input->post("name");
                $this->db->where("id", $id);
                $this->db->update("wxt_intention_status", array("name" => $name));

                $this->output_json('ok', '跟进状态修改成功');
                //删除状态
            } elseif ($action == 'del') {

                $this->db->delete("wxt_intention_status", array("id" => $id));

                $this->output_json('ok', '跟进状态已被删除');

            }
        }
    }

    /**
     * 招生渠道
     * @param string $action
     * @param int $id
     */
    public function source($action = '', $id = 0)
    {
        if (!empty($id)) {
            $this->is_my_school($id, 'intention_source');
        }

        //获取招生渠道
        if ($this->input->method() == 'get') {

            $this->db->where("school_id = {$this->session->school['id']} or school_id = '0'");
            $subject = $this->db->get("wxt_intention_source")->result_array();
            $this->output_json('ok', '招生渠道', $subject);
        } else {
            //添加招生渠道
            if ($action == 'add') {
                $this->form_validation->set_rules('name', '渠道名称', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $name = $this->input->post("name");
                $this->db->insert("wxt_intention_source", array("school_id" => $this->session->school['id'], "name" => $name));

                $this->output_json('ok', '招生渠道添加成功', $this->db->insert_id());
                //编辑招生渠道
            } elseif ($action == 'edit') {
                $this->form_validation->set_rules('name', '渠道名称', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $name = $this->input->post("name");
                $this->db->where("id", $id);
                $this->db->update("wxt_intention_source", array("name" => $name));

                $this->output_json('ok', '招生渠道修改成功');
                //删除招生渠道
            } elseif ($action == 'del') {

                $this->db->delete("wxt_intention_source", array("id" => $id));

                $this->output_json('ok', '招生渠道已被删除');

            }
        }

    }

    /**
     * 意向级别
     * @param string $action
     * @param int $id
     */
    public function level($action = '', $id = 0)
    {
        if (!empty($id)) {
            $this->is_my_school($id, 'intention_level');
        }

        //获取意向级别
        if ($this->input->method() == 'get') {
            $this->db->where("school_id = {$this->session->school['id']} or school_id = '0'");
            $subject = $this->db->get("wxt_intention_level")->result_array();
            $this->output_json('ok', '意向级别', $subject);
        } else {
            //添加意向级别
            if ($action == 'add') {
                $this->form_validation->set_rules('name', '意向级别名称', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $name = $this->input->post("name");
                $this->db->insert("wxt_intention_level", array("school_id" => $this->session->school['id'], "name" => $name));

                $this->output_json('ok', '意向级别添加成功', $this->db->insert_id());
                //编辑意向级别
            } elseif ($action == 'edit') {
                $this->form_validation->set_rules('name', '意向级别名称', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $name = $this->input->post("name");
                $this->db->where("id", $id);
                $this->db->update("wxt_intention_level", array("name" => $name));

                $this->output_json('ok', '意向级别修改成功');
                //删除意向级别
            } elseif ($action == 'del') {

                $this->db->delete("wxt_intention_level", array("id" => $id));

                $this->output_json('ok', '意向级别已被删除');

            }
        }

    }

    /**
     * 反馈标记分类
     * @param string $action
     * @param int $id
     */
    public function track_type($action = '', $id = 0)
    {
        if (!empty($id)) {
            $this->is_my_school($id, 'school_log_type');
        }

        //获取分类
        if ($this->input->method() == 'get') {
            $this->db->where("school_id = {$this->session->school['id']} ");
            if (empty($this->db->count_all_results("wxt_school_log_type"))){

                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '邀约试听', "color" => '#76C7FF'));
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '活动邀请', "color" => '#71F1E0'));
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '电话口语', "color" => '#FECB8E'));
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '投诉处理', "color" => '#AFA5F3'));
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '续费咨询', "color" => '#F3A5CF'));
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => '客户关怀', "color" => '#F06060'));
            }

            $this->db->where("school_id = {$this->session->school['id']} ");
            $subject = $this->db->get("wxt_school_log_type")->result_array();
            $this->output_json('ok', '标记分类', $subject);
        } else {
            //添加分类
            if ($action == 'add') {
                $this->form_validation->set_rules('name', '分类名称', 'trim|required');
                $this->form_validation->set_rules('color', '分类颜色', 'trim|required');

                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }
                $name = $this->input->post("name");
                $color = $this->input->post("color");
                $this->db->insert("wxt_school_log_type", array("school_id" => $this->session->school['id'], "name" => $name, "color" => $color));

                $this->output_json('ok', '标记分类加成功', $this->db->insert_id());
                //编辑分类
            } elseif ($action == 'edit') {
                $this->form_validation->set_rules('name', '分类名称', 'trim|required');
                $this->form_validation->set_rules('color', '分类颜色', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->output_json('error', validation_errors(' ', ' '));
                }

                $name = $this->input->post("name");
                $color = $this->input->post("color");
                $this->db->where("id", $id);
                $this->db->update("wxt_school_log_type", array("name" => $name, "color" => $color));

                $this->output_json('ok', '标记分类修改成功');
                //删除分类
            } elseif ($action == 'del') {

                $this->db->delete("wxt_school_log_type", array("id" => $id));

                $this->output_json('ok', '标记分类已被删除');

            }
        }

    }


}