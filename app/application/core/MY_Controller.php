<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2016/11/8
 * Time: 下午4:11
 */
class MY_Controller extends CI_Controller
{
    public $user;
    public $school;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url','public'));
        $this->load->library(array('session','form_validation'));

        if($this->uri->segment(2) <> 'login' and empty($this->session->user)){
            $this->output_json("error", "未登录或登录超时！", '', 1000);
        }


    }

    /**
     * @param string $status
     * @param string $msg
     * @param string $array
     * @param string $code
     */
    public function output_json($status = 'ok', $msg = 'Not msg!', $array = '', $code = '0')
    {
        if($status == 'error' and empty($code)){
            $code = '500';
        }
        $data['status'] = $status;
        $data['code'] = $code;
        $data['msg'] = $msg;
        $data['data'] = $array;



        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data))
            ->_display();
        exit();

    }

    /**
     * 检查权限
     * @param int $id
     * @param string $table
     * @return bool
     */
    public function is_my_school($id = 0,$table = '')
    {
        //检查参数
        if(!is_numeric($id) or empty($table)) {
            $this->output_json('error', "参数错误！");
        }

        $this->db->where("id",$id);
        $this->db->where("school_id",$this->session->user['school_id']);
        if(empty($this->db->count_all_results("wxt_".$table))){
            $this->output_json('error', "无权限！");
        }
        return true;

    }

    /**
     * @param string $type 类别
     * @param string $body 操作
     */
    public function autolog($type='',$body = '' )
    {

        $log = array(
            "school_id"=>$this->session->user['school_id'],
            "user_id"=>$this->session->user['id'],
            'url'=>$this->uri->uri_string(),
            "time"=>time(),
            "user_ip"=>empty(ip2long($this->input->ip_address()))?ip2long("127.0.0.1"):ip2long($this->input->ip_address()),
            "type"=>$type,
            "body"=>$body
        );
        $this->db->insert("wxt_school_log",$log);

    }

}