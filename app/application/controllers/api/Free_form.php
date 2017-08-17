<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/18
 * Time: 下午6:21
 */
class Free_form extends MY_Controller
{

    /**
     * 自定义报名表单
     */
    public function index()
    {
        //参数设置

        $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
        $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
        $page = ($page - 1) * $limit;
        $search = $this->input->get('search');
        $data['limit'] = $limit;

        //统计总数
        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if ($this->input->get('status') <> '') {
            $this->db->where("status", $this->input->get('status'));
        }


        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->group_end();
        }

        $data['count'] = $this->db->count_all_results("wxt_free_form");
        if (!empty($this->input->get('count'))) {
            $this->output_json("ok", "统计数据", $data);
        }


        //查询数据
        $this->db->select("id,title,pic,status,view_count");

        $this->db->where("school_id", $this->session->user['school_id']);

        if (!empty($this->input->get('campus_id'))) {
            $this->db->where("campus_id", $this->input->get('campus_id'));
        }

        if ($this->input->get('status') <> '') {
            $this->db->where("status", $this->input->get('status'));
        }



        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->group_end();
        }

        $this->db->limit($limit, $page);
        $this->db->order_by('id','DESC');
        $data['form'] = $this->db->get("wxt_free_form")->result_array();

        foreach ($data['form'] as &$item){
            $this->db->where("form_id",$item['id']);
            $item['student_count'] = $this->db->count_all_results("wxt_free_form_field");
            $item['url'] = "http://wx.eduwxt.com/form/#/?formid=".$item['id'];
            $item['qrcode'] = "/api/free_form/qrcode/".$item['id'];
        }

        $this->output_json("ok", "自定义报名表单", $data);

    }

    /**
     * 生成二维码
     * @param int $form_id
     */
    public function qrcode($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');
        $url = "http://wx.eduwxt.com/form/#/?formid=".$form_id;
        $this->load->helper('phpqrcode');
        QRcode::png($url);
    }

    /**
     * 表单详情
     * @param int $form_id
     */
    public function view($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');
        //查询数据
        $this->db->select("wxt_free_form.*,wxt_user.name as fzr,wxt_intention_source.name as source");
        $this->db->where("wxt_free_form.id", $form_id);
        $this->db->join('wxt_user',"wxt_user.id = wxt_free_form.user_id",'left');
        $this->db->join('wxt_intention_source',"wxt_intention_source.id = wxt_free_form.source_id",'left');

        $data['form'] = $this->db->get("wxt_free_form")->row_array();

        $data['form']['field'] = json_decode($data['form']['field'],true);
        $data['form']['point'] = json_decode($data['form']['point'],true);

        $this->db->where("form_id",$form_id);
        $data['form']['student_count'] = $this->db->count_all_results("wxt_free_form_field");
        $this->output_json("ok", "表单详情", $data);

    }



    /**
     * 添加表单
     */
    public function add()
    {
        $this->form_validation->set_rules('title', '表单名称', 'trim|required');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('source_id', '所属来源', 'trim|required|numeric');
        $this->form_validation->set_rules('user_id', '责任人', 'trim|required|numeric');
        $this->form_validation->set_rules('phone', '咨询电话', 'trim|required|numeric');
        $this->form_validation->set_rules('address', '学校地址', 'trim|required');
        $this->form_validation->set_rules('pic', '封面图', 'trim|required');
        $this->form_validation->set_rules('body', '表单内容', 'trim|required');
        $this->form_validation->set_rules('field', '表单字段', 'trim|required');
        $this->form_validation->set_rules('point', '地图坐标', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error','表单信息不正确',$this->form_validation->error_array());
        }

        $form = $this->input->post(array('title', 'campus_id', 'source_id', 'user_id','phone', 'address','pic','body','field','point'));

        $form['school_id'] = $this->session->school['id'];
        $form['add_time'] = time();


        if ($this->db->insert("wxt_free_form", $form)) {
            $this->output_json('ok', '添加成功', $this->db->insert_id());
        } else {
            $this->output_json('error', '添加失败', $form);
        }

    }

    /**
     * 编辑表单
     * @param int $form_id
     */
    public function edit($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');

        $this->form_validation->set_rules('title', '表单名称', 'trim|required');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('source_id', '所属来源', 'trim|required|numeric');
        $this->form_validation->set_rules('user_id', '责任人', 'trim|required|numeric');
        $this->form_validation->set_rules('phone', '咨询电话', 'trim|required|numeric');
        $this->form_validation->set_rules('address', '学校地址', 'trim|required');
        $this->form_validation->set_rules('pic', '封面图', 'trim|required');
        $this->form_validation->set_rules('body', '表单内容', 'trim|required');
        $this->form_validation->set_rules('field', '表单内容', 'trim|required');
        $this->form_validation->set_rules('point', '地图坐标', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error','表单信息不正确',$this->form_validation->error_array());
        }

        $form = $this->input->post(array('title', 'campus_id', 'source_id', 'user_id','phone', 'address','pic','body','field','point'));


        $this->db->where('id',$form_id);
        if ($this->db->update("wxt_free_form", $form)) {
            $this->output_json('ok', '修改成功', $form_id);
        } else {
            $this->output_json('error', '修改失败', $form);
        }


    }

    /**
     * 删除表单
     * @param int $form_id
     */
    public function del($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');

        $this->db->where('id',$form_id);
        $this->db->delete('wxt_free_form');

        $this->db->where('form_id',$form_id);
        $this->db->delete('wxt_free_form_field');

        $this->output_json('ok', '表单已删除');

    }

    /**
     * 修改状态
     * @param int $form_id
     */
    public function status($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');

        $status = $this->input->post('status');
        $this->db->where('id',$form_id);
        if ($this->db->update("wxt_free_form", array('status'=>$status))) {
            $this->output_json('ok', '修改成功', $form_id);
        } else {
            $this->output_json('error', '修改失败', $status);
        }
    }

    /**
     * 学生列表
     * @param int $form_id
     */
    public function student($form_id = 0)
    {
        $this->is_my_school($form_id, 'free_form');
        //参数设置

        $limit = empty($this->input->get('limit')) ? 20 : $this->input->get('limit');
        $page = empty($this->input->get('page')) ? 1 : $this->input->get('page');
        $page = ($page - 1) * $limit;
        $data['limit'] = $limit;

        //统计总数
        $this->db->where("form_id", $form_id);
        $data['count'] = $this->db->count_all_results("wxt_free_form_field");

        if (!empty($this->input->get('count'))) {
            $this->output_json("ok", "统计数据", $data);
        }


        //查询数据

        $this->db->where("form_id", $form_id);
        $this->db->limit($limit, $page);
        $this->db->order_by('id','DESC');
        $data['student'] = $this->db->get("wxt_free_form_field")->result_array();

        $this->output_json("ok", "报名学生", $data);

    }

    /**
     * 删除学生
     * @param int $student_id
     */
    public function student_del($student_id = 0)
    {
        $this->is_my_school($student_id, 'free_form_field');
        $this->db->where('id',$student_id);
        $this->db->delete('wxt_free_form_field');

        $this->output_json('ok', '已删除');
        
    }


}