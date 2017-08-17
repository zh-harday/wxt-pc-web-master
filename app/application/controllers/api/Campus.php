<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/10
 * Time: 下午3:34
 */

/**
 * Class Campus 校区
 */
class Campus extends MY_Controller
{
    public function index()
    {
        $this->db->select("id,name,contacts,phone,qtdh,address,remark");
        $this->db->where("school_id = '{$this->session->user['school_id']}'");
        $campus = $this->db->get("wxt_campus")->result_array();

        $this->output_json("ok","成功",$campus);

    }

    /**
     * 校区信息
     * @param int $cid 校区id
     */
    public function info($cid = 0)
    {
        if(!is_numeric($cid)){
            $this->output_json("error","参数错误！",'','-1');
        }
        //获取
        $this->db->select("id,name,contacts,phone,qtdh,address,remark");
        $this->db->where("school_id = '{$this->session->user['school_id']}'");
        $this->db->where("id = '{$cid}'");
        $campus = $this->db->get("wxt_campus")->result_array();

        $this->output_json("ok","成功",$campus);

    }

    public function add()
    {

        $this->db->where("school_id",$this->session->user['school_id']);
        $campus_count = $this->db->count_all_results("wxt_campus");

        if($campus_count >= $this->session->school['campus_quota']){
            $this->output_json('error', '校区数量已达上限，如要增加新校区请联系业务人员！');
        }

        $this->form_validation->set_rules('name', '校区名称', 'trim|required');
        $this->form_validation->set_rules('contacts', '联系人', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required');
        $this->form_validation->set_rules('address', '校区地址', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));
        }else{
            //添加新校区
            $campus['name'] = $this->input->post('name');
            $campus['school_id'] = $this->session->user['school_id'];
            $campus['contacts'] = $this->input->post('contacts');
            $campus['phone'] = $this->input->post('phone');
            $campus['address'] = $this->input->post('address');
            $campus['remark'] = $this->input->post('remark');
            $campus['re_time'] = time();

            $campus['prov'] = $this->input->post('prov');
            $campus['city'] = $this->input->post('city');
            $campus['dist'] = $this->input->post('dist');

            $campus['qtdh'] = $this->input->post('qtdh');

            $this->db->insert("wxt_campus",$campus);

            $this->output_json("ok","成功",$campus);
        }

    }

    public function edit($cid = 0)
    {
        if(!is_numeric($cid)){
            $this->output_json("error","参数错误！",'','-1');
        }
        $this->form_validation->set_rules('name', '校区名称', 'trim|required');
        $this->form_validation->set_rules('contacts', '联系人', 'trim|required');
        $this->form_validation->set_rules('phone', '联系电话', 'trim|required');
        $this->form_validation->set_rules('qtdh', '前台电话', 'trim|required');
        $this->form_validation->set_rules('address', '地址', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));
        }else {
            $campus = $this->input->post();


            $this->db->where("id = '{$cid}'");
            $this->db->where("school_id",$this->session->user['school_id']);
            $this->db->update("wxt_campus",$campus);
            $this->output_json("ok","成功",$campus);

        }

    }

}