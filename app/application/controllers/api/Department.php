<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/21
 * Time: 下午3:38
 */
class Department extends MY_Controller
{

    public function index()
    {
        //查询数据
        $this->db->where("school_id = {$this->session->user['school_id']} or school_id = 0");
        $department = $this->db->get("wxt_user_department")->result_array();
        $this->output_json("ok","部门列表",$department);
    }


    /**
     * 职务
     * @param int $did
     */
    public function group($did = 0)
    {
        if(!is_numeric($did)){
            $this->output_json('error', "参数错误！");
        }
        //查询数据
        $this->db->select("id,name");
        $this->db->where("school_id = {$this->session->user['school_id']} and department_id = {$did}");
        $department = $this->db->get("wxt_user_group")->result_array();
        $this->output_json("ok","职务列表",$department);

    }

    /**
     * 所有部门与职务
     */
    public function all()
    {
        //查询数据
        $this->db->where("school_id = {$this->session->user['school_id']} or school_id = 0");
        $department = $this->db->get("wxt_user_department")->result_array();

        foreach ($department as &$item){
            $this->db->select("id,name");
            $this->db->where("department_id = {$item['id']} and (school_id= '{$this->session->user['school_id']}' or school_id = '0')");
            $item['group'] = $this->db->get("wxt_user_group")->result_array();
        }
        $this->output_json("ok","部门列表",$department);
    }



}