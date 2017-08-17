<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/23
 * Time: 下午4:46
 */
class Config extends MY_Controller
{
    /**
     * 年级设置
     */
    public function grade()
    {

        $this->db->where("school_id = '0' or school_id = '{$this->session->school['id']}'");
        $data = $this->db->get("wxt_intention_grade")->result_array();
        $this->output_json("ok","年级列表",$data);
    }

    /**
     * authority_list
     */
    public function authority_list()
    {
        $this->load->view('authority_list.json');
    }

    public function bmxz()
    {
        if($this->input->method() == 'get'){
            $this->db->select('bmxz');
            $this->db->where("school_id = '{$this->session->school['id']}'");
            $data = $this->db->get("wxt_school_config")->row_array();
            if(empty($data)){
                $this->db->insert('wxt_school_config',array('school_id'=>$this->session->school['id']));
                $this->output_json("ok","报名须知",'');
            }else{
                $this->output_json("ok","报名须知",$data['bmxz']);
            }

        }

        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('bmxz', '报名须知', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->output_json('error', validation_errors(' ', ' '));
            }
            $this->db->where('school_id',$this->session->school['id']);
            $this->db->update('wxt_school_config',array('bmxz'=>$this->input->post('bmxz')));
            $this->output_json("ok","修改成功",$this->input->post('bmxz'));

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
            $this->is_my_school($id, 'intention_log_type');
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