<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/16
 * Time: 上午10:51
 */
use Intervention\Image\ImageManager;
class Staff extends MY_Controller
{
    public function index()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;



        //统计总数
        $this->db->where("wxt_user.school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("wxt_user.campus_id",$this->input->get('campus_id'));
        }
        if(!empty($this->input->get("department_id"))){
            $this->db->where("wxt_user_group.department_id",$this->input->get("department_id"));
        }


        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_user.name', $search);
            $this->db->or_like('wxt_user.account', $search);
            $this->db->or_like('wxt_user.phone', $search);
            $this->db->or_like('wxt_user.email', $search);
            $this->db->group_end();
        }

        $this->db->join("wxt_user_group","wxt_user_group.id = wxt_user.group_id");
        $data['count'] = $this->db->count_all_results("wxt_user");

        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }


        //查询数据
        $this->db->select("wxt_user.id,wxt_user.account,wxt_user.name,wxt_user.email,wxt_user.campus_id,wxt_user.phone,wxt_user.staus,wxt_user.login_time,wxt_user_department.name as department_name,wxt_user_group.name as group_name,wxt_user.wecha_id,wxt_user_info.face,wxt_user_info.staff_id,wxt_user_info.sex");
        $this->db->where("wxt_user.school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("wxt_user.campus_id",$this->input->get('campus_id'));
        }

        if(!empty($this->input->get("department_id"))){
            $this->db->where("wxt_user_group.department_id",$this->input->get("department_id"));
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_user.name', $search);
            $this->db->or_like('wxt_user.account', $search);
            $this->db->or_like('wxt_user.phone', $search);
            $this->db->or_like('wxt_user.email', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit,$page);

        $this->db->order_by("wxt_user.id", 'DESC');
        $this->db->join("wxt_user_info","wxt_user_info.uid = wxt_user.id");
        $this->db->join("wxt_user_group","wxt_user_group.id = wxt_user.group_id");
        $this->db->join("wxt_user_department","wxt_user_department.id = wxt_user_group.department_id");
        $data['staff'] = $this->db->get("wxt_user")->result_array();

        $this->output_json("ok","员工列表",$data);

    }

    public function simple()
    {
        //查询数据
        $this->db->select("wxt_user.id,wxt_user.name,wxt_user.campus_id,wxt_user_group.department_id,wxt_user.group_id");
        $this->db->where("wxt_user.school_id",$this->session->user['school_id']);
        $this->db->where("wxt_user.staus",'1');
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("(campus_id = {$this->input->get('campus_id')} or campus_id = '1')");
        }

        if(!empty($this->input->get("department_id"))){
            $this->db->where("wxt_user_group.department_id",$this->input->get("department_id"));
        }

        if(!empty($this->input->get("group_id"))){
            $this->db->where("wxt_user.group_id",$this->input->get("group_id"));
        }

        if(!empty($this->input->get("is_sc"))){
            $this->db->where("wxt_user.is_sc",$this->input->get("is_sc"));
        }
        if(!empty($this->input->get("is_gw"))){
            $this->db->where("wxt_user.is_gw",$this->input->get("is_gw"));
        }
        if(!empty($this->input->get("is_bzr"))){
            $this->db->where("wxt_user.is_bzr",$this->input->get("is_bzr"));
        }
        if(!empty($this->input->get("is_dk"))){
            $this->db->where("wxt_user.is_dk",$this->input->get("is_dk"));
        }
        if(!empty($this->input->get("is_stk"))){
            $this->db->where("wxt_user.is_stk",$this->input->get("is_stk"));
        }

        $this->db->order_by("wxt_user.id", 'DESC');
        $this->db->join("wxt_user_group","wxt_user_group.id = wxt_user.group_id");
        $data['staff'] = $this->db->get("wxt_user")->result_array();

        $this->output_json("ok","员工列表",$data);
    }

    /**
     * 员工详情
     * @param int $id
     */
    public function view($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        $this->db->select("wxt_user.id,wxt_user.name,wxt_user.account,wxt_user.email,wxt_user.phone,wxt_user.weixin_id,wxt_user.wecha_id,wxt_user.staus,wxt_user_department.id as department_id,wxt_user_department.name as department_name,wxt_user.group_id,wxt_user_group.name as group_name,wxt_user.school_id,wxt_user.campus_id,wxt_user_info.staff_id,wxt_user_info.face,wxt_user_info.sex,wxt_user_info.entry_time,wxt_user_info.is_full_time,wxt_user_info.birthday,wxt_user_info.qq,wxt_user_info.job_description,wxt_user_info.remark,wxt_user_info.motto,wxt_user_info.sfz,wxt_user.is_sc,wxt_user.is_gw,wxt_user.is_bzr,wxt_user.is_dk,wxt_user.is_stk");
        $this->db->where("wxt_user.id",$id);
        $this->db->where("wxt_user.school_id",$this->session->user['school_id']);

        $this->db->join("wxt_user_info","wxt_user_info.uid = wxt_user.id");
        $this->db->join("wxt_user_group","wxt_user_group.id = wxt_user.group_id");
        $this->db->join("wxt_user_department","wxt_user_department.id = wxt_user_group.department_id");
        $data['staff'] = $this->db->get("wxt_user")->result_array();



        $this->output_json("ok","员工详情",$data);

    }

    /**
     * 编辑员工信息
     * @param int $id
     */
    public function edit($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('staff_id', '工号', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required');
        $this->form_validation->set_rules('campus_id', '所在校区', 'trim|required');
        $this->form_validation->set_rules('group_id', '部门职务', 'trim|required');
        $this->form_validation->set_rules('is_full_time', '是否全职', 'trim|required');
        $this->form_validation->set_rules('entry_time', '入职时间', 'trim|required');


        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));
        }else {
            $user['name'] = $this->input->post('name');
            $user['email'] = $this->input->post('email');
            $user['phone'] = $this->input->post('phone');
            $user['campus_id'] = $this->input->post('campus_id');
            $user['group_id'] = $this->input->post('group_id');
            $user['weixin_id'] = $this->input->post('weixin_id');

            $user['is_sc'] = empty($this->input->post('is_sc'))?'0':'1';
            $user['is_gw'] = empty($this->input->post('is_gw'))?'0':'1';
            $user['is_bzr'] = empty($this->input->post('is_bzr'))?'0':'1';
            $user['is_dk'] = empty($this->input->post('is_dk'))?'0':'1';
            $user['is_stk'] = empty($this->input->post('is_stk'))?'0':'1';

            $user_info['staff_id'] = $this->input->post('staff_id');
            $user_info['sex'] = $this->input->post('sex');
            $user_info['entry_time'] = strtotime($this->input->post('entry_time'));
            $user_info['is_full_time'] = $this->input->post('is_full_time');
            $user_info['sfz'] = $this->input->post('sfz');
            $user_info['birthday'] = strtotime($this->input->post('birthday'));
            $user_info['qq'] = $this->input->post('qq');
            $user_info['job_description'] = $this->input->post('job_description');
            $user_info['remark'] = $this->input->post('remark');


            $this->db->where("id",$id);
            $this->db->where("school_id",$this->session->user['school_id']);
            $this->db->update("wxt_user",$user);

            $this->db->where("uid",$id);
            $this->db->where("school_id",$this->session->user['school_id']);
            $this->db->update("wxt_user_info",$user_info);
            $this->output_json("ok","成功",$this->input->post());

        }

    }



    public function add()
    {
        $this->form_validation->set_rules('account', '账号', 'trim|required|alpha_numeric');
        $this->form_validation->set_rules('password', '密码', 'trim|required');
        $this->form_validation->set_rules('name', '姓名', 'trim|required');
        $this->form_validation->set_rules('staff_id', '工号', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required');
        $this->form_validation->set_rules('campus_id', '所在校区', 'trim|required');
        $this->form_validation->set_rules('group_id', '部门职务', 'trim|required');
        $this->form_validation->set_rules('is_full_time', '是否全职', 'trim|required');
        $this->form_validation->set_rules('entry_time', '入职时间', 'trim|required');


        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));
        }else {
            $user['account'] = $this->input->post('account');
            $user['password'] = md5($this->input->post('password'));
            $user['name'] = $this->input->post('name');
            $user['campus_id'] = $this->input->post('campus_id');
            $user['group_id'] = $this->input->post('group_id');
            $user['school_id'] = $this->session->school['id'];
            $user['phone'] = $this->input->post('phone');
            $user['email'] = $this->input->post('email');
            $user['reg_time'] = time();
            $user['weixin_id'] = $this->input->post('weixin_id');

            $user['is_sc'] = empty($this->input->post('is_sc'))?'0':'1';
            $user['is_gw'] = empty($this->input->post('is_gw'))?'0':'1';
            $user['is_bzr'] = empty($this->input->post('is_bzr'))?'0':'1';
            $user['is_dk'] = empty($this->input->post('is_dk'))?'0':'1';
            $user['is_stk'] = empty($this->input->post('is_stk'))?'0':'1';


            $user['role'] = '1';


            $user_info['school_id'] = $this->session->school['id'];
            $user_info['staff_id'] = $this->input->post('staff_id');
            $user_info['face'] = '/assets/images/users/avatar-'.rand(1,12).'.jpg';
            $user_info['entry_time'] = strtotime($this->input->post('entry_time'));
            $user_info['is_full_time'] = $this->input->post('is_full_time');
            $user_info['sex'] = $this->input->post('sex');
            $user_info['birthday'] = strtotime($this->input->post('birthday'));
            $user_info['email'] = $this->input->post('email');
            $user_info['qq'] = $this->input->post('qq');
            $user_info['sfz'] = $this->input->post('sfz');
            $user_info['remark'] = $this->input->post('remark');
            $user_info['job_description'] = $this->input->post('job_description');




            //检查用户名是否存在

            $this->db->where(array('account'=>$user['account']));
            if($this->db->count_all_results("wxt_user") > 0){
                $this->output_json('error', "账号{$user['account']}已存在,不可重复添加！");

            }
            //检查手机账号是否存在
            $this->db->where(array('school_id'=>$this->session->user['school_id'],'phone'=>$user['phone']));
            if($this->db->count_all_results("wxt_user") > 0){
                $this->output_json('error', '已存在手机号为'.$user['phone'].'的用户,不可重复添加!');
            }
            if(!isMobile($user['phone'])){
                $this->output_json('error', '手机号码格式不正确!');
            }

            //检查工号是否存在
            $where = array('school_id'=>$this->session->user['school_id'],'staff_id'=>$user_info['staff_id']);
            $this->db->where($where);
            if($this->db->count_all_results('wxt_user_info') > 0){
                $this->output_json('error', '工号'.$user_info['staff_id'].'已存在,不可重复添加!');
            }

            //事务开始
            $this->db->trans_start();

            $this->db->insert("wxt_user",$user);

            $user_info['uid'] = $this->db->insert_id();
            $this->db->insert("wxt_user_info",$user_info);

            $this->db->trans_complete();
            //事务结束

            if($this->db->trans_status()){
                $this->output_json('ok', '添加成功！');
            }else{
                $this->output_json('error', '添加失败！');
            }
        }

    }

    /**
     * 修改密码
     * @param int $id
     */
    public function password($id = 0)
    {

        if($this->input->method() == 'post'){
            $this->form_validation->set_rules('password', '新密码', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                $user['password'] = md5($this->input->post('password'));
                $this->db->where("id",$id);
                $this->db->where("school_id",$this->session->user['school_id']);
                $this->db->update("wxt_user",$user);
                $this->output_json("ok","修改成功");
            }
        }

    }

    /**
     * 权限
     */
    public function authority($id = 0)
    {

        if ($this->input->method() == 'post'){
            $this->form_validation->set_rules('authority', '权限数据', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                $this->db->where("id",$id);
                $this->db->update("wxt_user",array('authority'=>$this->input->post('authority')));
                $this->output_json("ok","成功",$this->input->post());
            }


        }else{
            $this->db->select("id,group_id,role,authority");
            $this->db->where("wxt_user.id = '{$id}'");
            $user = $this->db->get("wxt_user")->row_array();

            if(empty($user)){
                $this->output_json("error","用户信息不存在！",'','-1');
            }else{
                if(empty($user['authority'])){
                    $this->db->select("authority");
                    $this->db->where("id = '{$user['group_id']}'");
                    $user['authority'] = $this->db->get("wxt_user_group")->row_array()['authority'];
                }
                $user['authority'] = json_decode($user['authority'],true);
                if(empty($user['authority'])){
                    $user['authority'] = array();
                }
                $this->output_json("ok","成功",$user);
            }
        }

    }

    /**
     * 员工状态
     * @param int $id
     */
    public function staus($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        if($this->input->method() == 'get'){
            $this->db->select("staus");
            $this->db->where("id",$id);
            $this->db->where("school_id",$this->session->user['school_id']);
            $data = $this->db->get("wxt_user")->row_array();
            $this->output_json("ok","ok",$data);

        }else{
            $staus = $this->input->post('staus');

            if(empty($staus)){
                $staus = '0';
            }else{
                $staus = '1';
            }
            $this->db->where("id",$id);
            $this->db->where("school_id",$this->session->user['school_id']);
            $this->db->update("wxt_user",array("staus"=>$staus));
            $this->output_json("ok","修改成功");
        }

    }

    /**
     * 微信绑定二维码
     */
    public function bind_qrcode($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }

        $url = "http://wx.eduwxt.com/index.php?g=Wap&m=Erm&a=binding&token={$this->session->school['pigcms_token']}&uid={$id}";
        $this->load->helper('phpqrcode');
        QRcode::png($url);
    }

    /**
     * 解除微信绑定
     */
    public function unbind($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }
        $this->db->where("id",$id);
        $this->db->where("school_id",$this->session->user['school_id']);
        $this->db->update("wxt_user",array('wecha_id'=>''));
        $this->output_json("ok","您的账号已与微校通系统解除绑定！");

    }

    public function del($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }
    }

    public function curriculum($id = 0)
    {
        $this->is_my_school($id,'user');
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

        $this->db->where(array('teacher_id'=>$id));

        $day = $this->input->get('day');
        if(!empty($day)){
            $today_start = strtotime($day);
            $today_end = strtotime($day) + 86400;
            $this->db->where("wxt_class_curriculum.start_time > {$today_start} and wxt_class_curriculum.start_time < {$today_end}");
        }


        $data['count'] = $this->db->count_all_results('wxt_class_curriculum');


        $this->db->select('wxt_class_curriculum.id,wxt_subject.subject,wxt_class.class_name,wxt_classroom.room_name,wxt_class_curriculum.status,wxt_class_curriculum.start_time,wxt_class_curriculum.end_time');

        $this->db->where("wxt_class_curriculum.teacher_id = {$id} ");

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
        $this->db->join('wxt_classroom', 'wxt_classroom.id = wxt_class_curriculum.room_id', 'left');
        $this->db->join('wxt_class', 'wxt_class.id = wxt_class_curriculum.class_id', 'left');

        if($type == 'wait'){
            $this->db->order_by('wxt_class_curriculum.start_time', 'ase');
        }elseif ($type == 'end'){
            $this->db->order_by('wxt_class_curriculum.start_time', 'desc');
        }else{
            $this->db->order_by('wxt_class_curriculum.start_time', 'ase');
        }

        $this->db->limit($limit,$page);
        $data['curriculum'] = $this->db->get("wxt_class_curriculum")->result_array();

        $this->output_json("ok","员工排课",$data);
        
    }

    /**
     * 检查月份课程
     * @param int $class_id 班级id
     * @param int $month 月份时间戳
     */
    public function month_curriculum($id = 0,$month = 0)
    {


        if(empty($month)){
            $time = time();
        }else{
            $time = strtotime($month.'-01');
        }


        $start_time = mktime(0,0,1,date('m',$time),1,date('Y',$time));
        $end_time = mktime(23,59,59,date('m',$time),date('t',$time),date('Y',$time));


        $this->db->select("start_time");
        $this->db->where("teacher_id = $id and start_time > $start_time and end_time < $end_time");

        $curriculum = $this->db->get("wxt_class_curriculum")->result_array();

        $data = array();
        foreach ($curriculum as $item){
            $i = date("d",$item['start_time']);
            $data[$i]= 'true';
        }
        $this->output_json("ok","排课统计",$data);

    }


}