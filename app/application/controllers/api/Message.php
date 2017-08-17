<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/16
 * Time: 下午3:20
 */

/**
 * 消息管理
 * Class Message
 */
class Message extends MY_Controller
{
    /**
     * 消息列表
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
        //筛选条件
        if($this->input->get('type') == '0'){
            $this->db->where("wxt_msg.type = '0'");

        }elseif ($this->input->get('type') == '1'){

            if($this->session->user['campus_id'] == '1'){
                $this->db->where("wxt_msg.school_id = {$this->session->user['school_id']} ");
            }else{
                $this->db->where("(wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
            }

        }else{
            if($this->session->user['campus_id'] == '1'){
                $this->db->where("wxt_msg.type = '0' or wxt_msg.school_id = {$this->session->user['school_id']} ");
            }else{
                $this->db->where("wxt_msg.type = '0' or (wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
            }
        }


        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_msg.title', $search);
            $this->db->or_like('wxt_msg.message', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_msg");


        //查询数据

        $this->db->select("wxt_msg.*,wxt_campus.name as campus_name,wxt_user.name as seed_name,wxt_msg_statue.statue");
        //筛选条件
        if($this->input->get('type') == '0'){
            $this->db->where("wxt_msg.type = '0'");

        }elseif ($this->input->get('type') == '1'){
            if($this->session->user['campus_id'] == '1'){
                $this->db->where("wxt_msg.school_id = {$this->session->user['school_id']} ");
            }else{
                $this->db->where("(wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
            }

        }else{
            if($this->session->user['campus_id'] == '1'){
                $this->db->where("wxt_msg.type = '0' or wxt_msg.school_id = {$this->session->user['school_id']} ");
            }else{
                $this->db->where("wxt_msg.type = '0' or (wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
            }
        }

        $this->db->join("wxt_campus","wxt_campus.id = wxt_msg.campus_id",'LEFT');
        $this->db->join("wxt_user","wxt_user.id = wxt_msg.seed_id",'LEFT');
        $this->db->join("wxt_msg_statue","wxt_msg_statue.msg_id = wxt_msg.id and wxt_msg_statue.user_id = {$this->session->user['id']}",'LEFT');

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_msg.title', $search);
            $this->db->or_like('wxt_msg.message', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit,$page);

        $this->db->order_by("wxt_msg.id", 'DESC');
        $data['message'] =$this->db->get("wxt_msg")->result_array();
//        echo $this->db->last_query();
//        exit;



        $this->output_json("ok","消息",$data);

    }

    /**
     * 统计
     */
    public function count()
    {


        //统计总数

        if($this->session->user['campus_id'] == '1'){
            $this->db->where("wxt_msg.school_id = 0 or wxt_msg.school_id = {$this->session->user['school_id']} ");
        }else{
            $this->db->where("wxt_msg.school_id = 0 or (wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
        }
        $data['count'] = $this->db->count_all_results("wxt_msg");


        $this->db->where("wxt_msg_statue.statue = '1'");
        if($this->session->user['campus_id'] == '1'){
            $this->db->where("wxt_msg.school_id = 0 or wxt_msg.school_id = {$this->session->user['school_id']} ");
        }else{
            $this->db->where("wxt_msg.school_id = 0 or (wxt_msg.type = '1' and wxt_msg.school_id = {$this->session->user['school_id']}) or (wxt_msg.type = '2' and wxt_msg.campus_id = {$this->session->user['campus_id']})");
        }
        $this->db->join("wxt_msg_statue","wxt_msg_statue.msg_id = wxt_msg.id and wxt_msg_statue.user_id = {$this->session->user['id']}");
        $data['unread'] = $data['count'] - $this->db->count_all_results("wxt_msg");

        $this->output_json("ok","新消息",$data);
        
    }

    /**
     * 消息详情
     * @param int $id
     */
    public function view($id = 0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', "参数错误！");
        }


        $this->db->select("wxt_msg.*,wxt_campus.name as campus_name,wxt_user.name as seed_name,wxt_msg_statue.statue");
        $this->db->where("wxt_msg.id",$id);

        $this->db->join("wxt_campus","wxt_campus.id = wxt_msg.campus_id","LEFT");
        $this->db->join("wxt_user","wxt_user.id = wxt_msg.seed_id","LEFT");
        $this->db->join("wxt_msg_statue","wxt_msg_statue.msg_id = wxt_msg.id and wxt_msg_statue.user_id = {$this->session->user['id']}","LEFT");

        $data =$this->db->get("wxt_msg")->row_array();
        if(empty($data)){
            $this->output_json('error', "参数错误！");
        }

        if($data['type'] == '0' or $data['school_id'] == $this->session->user['school_id']){
            //检查阅读状态
            if(empty($data['statue'])){
                $this->db->insert("wxt_msg_statue",array("msg_id"=>$id,"user_id"=>$this->session->user['id'],"statue"=>'1',"read_time"=>time()));
            }
            $data['statue'] = '1';

            $this->output_json("ok","消息",$data);
        }else{
            $this->output_json('error', "参数错误！");

        }





    }

    /**
     * 发送消息
     */
    public function add()
    {
        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('title', '消息标题', 'trim|required');
            $this->form_validation->set_rules('message', '消息内容', 'trim|required');
            $this->form_validation->set_rules('campus_id', '校区', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {

                $message['type'] = ($this->input->post('campus_id') > 1)?'2':'1';
                $message['title'] = $this->input->post('title');
                $message['message'] = $this->input->post('message');
                $message['school_id'] = $this->session->user['school_id'];
                $message['campus_id'] = $this->input->post('campus_id');
                $message['seed_id'] = $this->session->user['id'];
                $message['seed_time'] = time();

                $this->db->insert("wxt_msg",$message);
                $msg_id= $this->db->insert_id();

                //获取模版id
                $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM207351906"));
                $temp = $this->db->get('wxt_weixin_temp')->row_array();

                if($message['type']== 1){
                    $where_sql = "school_id = {$message['school_id']}";

                }else{
                    $where_sql = "school_id = {$message['school_id']} and (campus_id = {$message['campus_id']} or campus_id = 0)";
                }

                $this->db->where($where_sql);
                $user = $this->db->get('wxt_user')->result_array();


                //发送模版消息
                //(学生姓名)同学您好，您的作业老师已经发布了最新批阅信息，请及时查看! 点击查看作业评价详情。
                foreach ($user as $item) {
                    $msg['wecha_id'] = $item['wecha_id'];
                    $msg['template_id'] = $temp['temp_id'];
                    $msg['first'] = "{$item['name']}老师您好，您有一个重要通知，内容如下:";
                    $msg['keyword1'] = '校区通知';
                    $msg['keyword2'] = date('Y-m-d H:i:s', time());
                    $msg['keyword3'] = $message['title'];
                    $msg['remark'] = '点击查看详情。';//内容
                    $msg['url'] = "http://wx.eduwxt.com/school/#/Apps/School/Notice/Detail/{$msg_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

                    $this->load->model('queue');
                    $this->queue->post($this->session->school['pigcms_token'],$msg);
                }





                $this->output_json("ok","发送成功！",$message);

            }
        }

    }

}