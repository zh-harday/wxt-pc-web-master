<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/25
 * Time: 上午11:49
 */
class User_group extends MY_Controller
{

    /**
     * 部门
     */
    public function department()
    {
        $this->db->where("school_id= '{$this->session->user['school_id']}' or school_id = '0'");
        $department = $this->db->get("wxt_user_department")->result_array();
        $this->output_json("ok","ok",$department);

    }

    /**
     * 职务组
     * @param int $department_id
     */
    public function group($department_id = 0)
    {
        $this->db->select("id,department_id,name");
        $this->db->where("department_id = $department_id and (sch ool_id= '{$this->session->user['school_id']}' or school_id = '0')");
        $group = $this->db->get("wxt_user_group")->result_array();
        $this->output_json("ok","ok",$group);
    }

    public function group_info($group_id = 0)
    {
        if($group_id <> 1){
            $this->is_my_school($group_id, 'user_group');
        }
        $this->db->where("id= '{$group_id}'");
        $group = $this->db->get("wxt_user_group")->row_array();
        $group['authority'] = json_decode($group['authority'],true);


        if(empty($group['authority'])){
            $group['authority'] = array();
        }
        $this->output_json("ok","ok",$group);
    }

    /**
     * 添加职务组
     */
    public function group_add()
    {

        if($this->input->method() == 'post'){


            $this->form_validation->set_rules('department_id', '部门', 'trim|required|integer');
            $this->form_validation->set_rules('name', '职务名称', 'trim|required');
            $this->form_validation->set_rules('authority', '权限', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{

                $group['school_id']         = $this->session->school['id'];
                $group['department_id']     = $this->input->post('department_id');
                $group['name']              = $this->input->post('name');
                $group['authority']         = $this->input->post('authority');



                $this->db->insert("wxt_user_group",$group);
                $this->output_json("ok","成功",$group);
            }
        }

    }


    /**
     * 修改职务组
     * @param int $group_id
     */
    public function group_edit($group_id = 0)
    {
        $this->is_my_school($group_id, 'user_group');

        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('department_id', '部门', 'trim|required|integer');
            $this->form_validation->set_rules('name', '职务名称', 'trim|required');
            $this->form_validation->set_rules('authority', '权限', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{

                $group['department_id']     = $this->input->post('department_id');
                $group['name']              = $this->input->post('name');
                $group['authority']         = $this->input->post('authority');

                $this->db->where("id= '{$group_id}'");
                $this->db->update("wxt_user_group",$group);

                $this->output_json("ok","成功",$group);
            }
        }

    }


    /**
     * 删除职务组
     * @param int $group_id
     */
    public function group_del($group_id = 0)
    {
        $this->is_my_school($group_id, 'user_group');
        $this->db->where('group_id',$group_id);
        if($this->db->count_all_results('wxt_user') > 0){
            $this->output_json("error","您要删除的职务组已有员工，不允许删除！",'','-1');
        }else{
            $this->db->where("id",$group_id);
            $this->db->delete("wxt_user_group");
            $this->output_json("ok","成功");
        }
    }


}