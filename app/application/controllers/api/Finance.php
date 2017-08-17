<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/10
 * Time: 下午4:08
 */

/**
 * Class Finance 财务
 */
class Finance extends MY_Controller
{

    /**
     * 财务账户
     */
    public function account()
    {
        $this->db->select("id,campus_id,account_name,remark");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $account = $this->db->get("wxt_school_account")->result_array();
        $this->output_json("ok","ok",$account);
    }

    /**
     * 财务账户信息
     * @param int $id
     */
    public function account_info($id = 0)
    {
        $this->db->select("id,campus_id,account_name,remark");
        $this->db->where("id= '{$id}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $account = $this->db->get("wxt_school_account")->row_array();
        $this->output_json("ok","ok",$account);
    }

    /**
     * 编辑财务账户
     * @param int $id
     */
    public function account_edit($id = 0)
    {
        if($this->input->method() == 'post'){


            $this->form_validation->set_rules('account_name', '账户名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {

                $account['account_name']    = $this->input->post('account_name');
                $account['remark']          = $this->input->post('remark');

                $this->db->where("id= '{$id}'");
                $this->db->where("school_id= '{$this->session->user['school_id']}'");
                $this->db->update("wxt_school_account",$account);
                $this->output_json("ok","ok",$account);
            }


        }

    }

    /**
     * 添加财务账户
     */
    public function account_add()
    {
        if($this->input->method() == 'post'){

            $this->form_validation->set_rules('campus_id', '校区', 'trim|required');
            $this->form_validation->set_rules('account_name', '账户名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else {

                $account['school_id'] = $this->session->school['id'];
                $account['campus_id'] = $this->input->post('campus_id');
                $account['account_name'] = $this->input->post('account_name');
                $account['remark']    = $this->input->post('remark');
                $account['re_time'] = time();
                $account['re_user'] = $this->session->user['school_id'];

                $this->db->insert("wxt_school_account",$account);
                $this->output_json("ok","ok",$account);
            }


        }
    }

    /**
     * 删除财务账户
     * @param int $id
     */
    public function account_del($id=0)
    {
        if(!is_numeric($id)){
            $this->output_json('error', '参数错误');
        }
        $this->db->where("id= '{$id}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $account = $this->db->get("wxt_school_account")->row_array();


        if($account['school_id'] == $this->session->user['school_id']){

            $this->db->where('payee_account_id',$id);
            if($this->db->count_all_results('wxt_finance_orders') > 0){
                $this->output_json('error', '您要删除的账户下已有收款记录信息，不允许删除！');
            }else{
                //删除收款账户
                $this->db->where("id",$id);
                $this->db->delete("wxt_school_account");
                $this->output_json("ok","ok");
            }

        }else{
            $this->output_json('error', '无此权限');
        }

    }

    /**
     * 杂费分类
     */
    public function fees_type()
    {

        if($this->input->method() == 'get'){
            $this->db->select("id,type_name");
            $this->db->where("school_id= '{$this->session->user['school_id']}'");
            $fees_type = $this->db->get("wxt_fees_type")->result_array();
            $this->output_json("ok","ok",$fees_type);

        }elseif ($this->input->method() == 'post'){

            $this->form_validation->set_rules('type_name', '分类名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }
            $fees_type['type_name'] = $this->input->post('type_name');
            $fees_type['school_id'] = $this->session->user['school_id'];
            $this->db->insert("wxt_fees_type",$fees_type);
            $this->output_json("ok","ok",$fees_type);

        }

    }




//    public function fees_type_info($tid = 0)
//    {
//        $this->db->select("id,type_name");
//        $this->db->where("id",$tid);
//        $this->db->where("school_id= '{$this->session->user['school_id']}'");
//        $fees_type = $this->db->get("wxt_fees_type")->row_array();
//        $this->output_json("ok","ok",$fees_type);
//    }

    /**
     * 删除杂费类别
     * @param int $tid
     */
    public function fees_type_del($tid = 0)
    {
        $this->db->where("id= '$tid'");
        $fees_type = $this->db->get("wxt_fees_type")->row_array();
        if($fees_type['school_id'] == $this->session->user['school_id']){
            $this->db->where('fees_type',$tid);
            if($this->db->count_all_results('wxt_fees_list') > 0){
                $this->output_json('error', '本项目下已有收费记录不允许删除!', '', '1');
            }else{
                //删除
                $this->db->where("id",$tid);
                $this->db->delete('wxt_fees_type');
                $this->output_json("ok","ok");
            }

        }else{
            $this->output_json('error', '无此权限');
        }

    }


    /**
     * 杂费列表
     */
    public function fees()
    {
        if($this->input->method() == 'get'){
            $this->db->select("id,campus_id,fees_type,fees_name,price,unit,remark");
            $this->db->where("school_id= '{$this->session->user['school_id']}'");
            $fees_list = $this->db->get("wxt_fees_list")->result_array();
            $this->output_json("ok","ok",$fees_list);
        }elseif ($this->input->method() == 'post'){

            $this->form_validation->set_rules('campus_id', '校区', 'trim|required');
            $this->form_validation->set_rules('fees_type', '分类', 'trim|required');
            $this->form_validation->set_rules('fees_name', '名称', 'trim|required');
            $this->form_validation->set_rules('price', '价格', 'trim|required');
            $this->form_validation->set_rules('unit', '单位', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }
            $fees['campus_id'] = $this->input->post('campus_id');
            $fees['school_id'] = $this->session->user['school_id'];
            $fees['fees_type'] = $this->input->post('fees_type');
            $fees['fees_name'] = $this->input->post('fees_name');
            $fees['price'] = $this->input->post('price');
            $fees['unit'] = $this->input->post('unit');
            $fees['remark'] = $this->input->post('remark');
            $this->db->insert("wxt_fees_list",$fees);
            $this->output_json("ok","ok",$fees);

        }

    }

    /**
     * 删除杂费项目
     * @param int $fid
     */
    public function fees_del($fid = 0)
    {

        if(!is_numeric($fid)){
            $this->output_json('error', '参数错误');
        }

        $this->db->where("id= '{$fid}'");
        $account = $this->db->get("wxt_fees_list")->row_array();


        if($account['school_id'] == $this->session->user['school_id']){

            $fees_re = $this->db->where("fee_type = '1' and fee_type_id = {$fid}")->count_all_results("wxt_finance_recordss");

            if($fees_re > 0){
                $this->output_json('error', '本项目下已有收费记录不允许删除!', '', '1');
            }else{
                $this->db->where("id",$fid)->delete("wxt_fees_list");
                $this->output_json("ok","ok");
            }

        }else{
            $this->output_json('error', '无此权限');
        }
    }

    /**
     * 财务流水
     */
    public function records()
    {
        //参数设置

        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;



        //统计总数
        $this->db->where("wxt_finance_recordss.school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("wxt_finance_orders.campus_id",$this->input->get('campus_id'));
        }
        if($this->input->get("type") <> ''){
            $this->db->where("wxt_finance_recordss.type",$this->input->get("type"));
        }

        if($this->input->get("fee_type") <> ''){
            $this->db->where("wxt_finance_recordss.fee_type",$this->input->get("fee_type"));
        }

        if(!empty($this->input->get("payee_account_id"))){
            $this->db->where("wxt_finance_orders.payee_account_id",$this->input->get("payee_account_id"));
        }

        if(!empty($this->input->get("payee_id"))){
            $this->db->where("wxt_finance_orders.payee_id",$this->input->get("payee_id"));
        }


        if(!empty($this->input->get('start_time'))){
            $this->db->where("wxt_finance_orders.payee_time > {$this->input->get('start_time')} and wxt_finance_orders.payee_time < {$this->input->get('end_time')}");
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_student.name', $search);
            $this->db->or_like('wxt_finance_orders.id', $search);
            $this->db->or_like('wxt_finance_orders.remark', $search);
            $this->db->group_end();
        }
        $this->db->join('wxt_finance_orders','wxt_finance_orders.id = wxt_finance_recordss.orders_id','left');
        $this->db->join('wxt_student','wxt_student.id = wxt_finance_orders.student_id');
        $data['count'] = $this->db->count_all_results("wxt_finance_recordss");
        if(!empty($this->input->get('count'))){
            $this->output_json("ok","统计数据",$data);
        }

        //查询数据
        $this->db->select('wxt_student.name as student_name,wxt_student.number as student_number,wxt_finance_recordss.*,wxt_finance_orders.id as orders_id,wxt_finance_orders.school_id,wxt_finance_orders.campus_id,wxt_finance_orders.payee,wxt_finance_orders.payee_account,wxt_finance_orders.payee_time,wxt_finance_orders.reg_time,wxt_finance_orders.remark');
        $this->db->where("wxt_finance_recordss.school_id",$this->session->user['school_id']);
        if(!empty($this->input->get('campus_id'))){
            $this->db->where("wxt_finance_orders.campus_id",$this->input->get('campus_id'));
        }
        if($this->input->get("type") <> ''){
            $this->db->where("wxt_finance_recordss.type",$this->input->get("type"));
        }

        if($this->input->get("fee_type") <> ''){
            $this->db->where("wxt_finance_recordss.fee_type",$this->input->get("fee_type"));
        }

        if(!empty($this->input->get("payee_account_id"))){
            $this->db->where("wxt_finance_orders.payee_account_id",$this->input->get("payee_account_id"));
        }

        if(!empty($this->input->get("payee_id"))){
            $this->db->where("wxt_finance_orders.payee_id",$this->input->get("payee_id"));
        }


        if(!empty($this->input->get('start_time'))){
            $this->db->where("wxt_finance_orders.payee_time > {$this->input->get('start_time')} and wxt_finance_orders.payee_time < {$this->input->get('end_time')}");
        }

        if(!empty($search)){
            $this->db->group_start();
            $this->db->like('wxt_student.name', $search);
            $this->db->or_like('wxt_finance_orders.id', $search);
            $this->db->or_like('wxt_finance_orders.remark', $search);
            $this->db->group_end();
        }


        $this->db->limit($limit,$page);

        $this->db->order_by("wxt_finance_recordss.id", 'DESC');
        $this->db->join('wxt_finance_orders','wxt_finance_orders.id = wxt_finance_recordss.orders_id');
        $this->db->join('wxt_student','wxt_student.id = wxt_finance_orders.student_id');
        $data['subject'] = $this->db->get("wxt_finance_recordss")->result_array();

        $this->output_json("ok","缴费记录",$data);
    }

    /**
     * 订单详情
     * @param int $id
     */
    public function records_view($id = 0)
    {
        $this->is_my_school($id,'finance_orders');

        $this->db->where('id',$id);
        $data['info'] = $this->db->get('wxt_finance_orders')->row_array();

        $this->db->where(array('orders_id'=>$id));
        $data['recordss'] = $this->db->get('wxt_finance_recordss')->result_array();

        $this->output_json("ok","订单详情",$data);
    }





}