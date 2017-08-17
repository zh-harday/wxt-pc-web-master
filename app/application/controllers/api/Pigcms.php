<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/21
 * Time: 下午3:15
 */
class Pigcms extends MY_Controller
{

    public $pigcms;

    public function __construct()
    {
        parent::__construct();

        $this->db->db_select('wxt_pigcms');
        $this->db->select('id,uid,wxname,winxintype,token');
        $this->db->where('uid',$this->session->school['pigcms_user']);
        $this->pigcms = $this->db->get('pigcms_wxuser')->row_array();
        $this->db->db_select('wxt_admin');

    }

    /**
     * 小猪菜单
     */
    public function menu()
    {
        $this->load->view('wx_menu.json',$this->pigcms);
    }

    /**
     * 小猪登录
     */
    public function login()
    {
        $sso_key = md5('eduwxt_'.$this->session->school['pigcms_user'].time());
        $this->user_update($this->session->school['pigcms_user'],array('sso_key' => $sso_key));
        $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Users&a=v2_login&id='.$this->session->school['pigcms_user'].'&sso_key='.$sso_key;
        $this->output_json("ok","ok",array('url'=>$url));
    }

    public function user_update($id,$data)
    {
        $this->db->db_select('wxt_pigcms');
        $this->db->where('id',$id);
        $return = $this->db->update('pigcms_users',$data);
        $this->db->db_select('wxt_admin');
        return $return;
    }

}

