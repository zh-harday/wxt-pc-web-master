<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/2
 * Time: 下午4:52
 */

/**
 * Class Login 登录
 */
class Login extends MY_Controller
{
    public function index()
    {
        if($this->input->method() == 'post'){
            $where['account']    = $this->input->post("account");
            $where['password']   = md5($this->input->post("password"));

            $user = $this->db->where($where)->get("wxt_user")->row_array();
            if(empty($user)){
                $this->output_json('error','账号或密码不正确！');
            }

            if($user['staus'] <> '1'){
                $this->output_json('error','您的账号已被停用！');
            }



            $school = $this->db->where("id",$user['school_id'])
                ->get("wxt_school")
                ->row_array();

            if($school['exp_time'] < time()){
                $this->output_json('error','您的学校账号已到期，如需帮助请联系在线客服！');
            }

            if($school['status'] == '0'){
                $this->output_json('error','您的学校账号已被停用，如需帮助请联系在线客服！');
            }


            $this->session->user = $user;
            $this->session->school = $school;

            //更新最后登录时间与IP地址
            $this->db->where('id',$this->session->user['id']);
            $this->db->update("wxt_user",array('login_time'=>time(),'login_ip'=>ip2long($this->input->ip_address())));
            $this->db->where('id',$this->session->school['id']);
            $this->db->update("wxt_school",array('login_time'=>time(),'login_user'=>$this->session->user['id']));

            $this->autolog('系统登录','登录成功');

            $this->output_json('ok','登录成功');
        }

    }

    public function sso($id,$key)
    {

        $this->db->select('sso_key');
        $this->db->where('id',$id);
        $sso_key = $this->db->get('wxt_school')->row_array()['sso_key'];

        if($sso_key == $key){



            $school = $this->db->where("id",$id)->get("wxt_school")->row_array();
            $user = $this->db->where(array('id'=>$school['admin_id']))->get("wxt_user")->row_array();

            $this->session->user = $user;
            $this->session->school = $school;

            redirect('http://erm.eduwxt.com/v2/#/sso_login');

        }else{
            show_error('请勿越权操作！');
        }


    }

}