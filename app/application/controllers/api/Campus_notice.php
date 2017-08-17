<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/6/9
 * Time: 上午11:02
 */
class Campus_notice extends MY_Controller
{

    public function index()
    {
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where("wxt_notice.school_id = {$this->session->school['id']} and wxt_notice.type = 'campus'");
        $data['count'] = $this->db->count_all_results('wxt_notice');

        //校园通知
        $this->db->select("wxt_notice.id,wxt_notice.time,wxt_notice.title,wxt_user.name as teacher_name,wxt_notice.info,wxt_campus.name as campus_name");
        $this->db->where("wxt_notice.school_id = {$this->session->school['id']} and wxt_notice.type = 'campus'");
        $this->db->join("wxt_campus","wxt_campus.id = wxt_notice.campus_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_notice.from");
        $this->db->order_by('wxt_notice.id', 'desc');
        $this->db->limit($limit,$page);
        $data['notice'] = $this->db->get("wxt_notice")->result_array();
        $this->output_json("ok", "校区通知", $data);

    }

    /**
     * 通知详情
     * @param int $id
     */
    public function view($id = 0)
    {
        $this->is_my_school($id,'notice');

        $this->db->select("wxt_notice.id,wxt_notice.time,wxt_notice.title,wxt_user.name as teacher_name,wxt_notice.info,wxt_notice.body,wxt_campus.name as campus_name");
        $this->db->where("wxt_notice.id = {$id}");
        $this->db->join("wxt_campus","wxt_campus.id = wxt_notice.campus_id");
        $this->db->join("wxt_user","wxt_user.id = wxt_notice.from");

        $data = $this->db->get("wxt_notice")->row_array();
        $this->output_json("ok", "通知详情", $data);

    }

    /**
     * 添加通知
     */
    public function add()
    {
        $this->form_validation->set_rules('title', '通知标题', 'trim|required');
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('body', '通知详情', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }else{

            $notice['title'] = $this->input->post('title');
            $notice['school_id'] = $this->session->school['id'];
            $notice['from'] = $this->session->user['id'];
            $notice['type'] = 'campus';
            $notice['campus_id'] = $this->input->post('campus_id');
            $notice['info'] = $this->input->post('info');
            $notice['body'] = $this->input->post('body');
            $notice['time'] = time();

            $this->db->trans_start();
            $this->db->insert("wxt_notice",$notice);

            //获取班级通知id
            $notice_id = $this->db->insert_id();

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM207868292"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();


            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
            if($notice['campus_id'] == '1'){
                $this->db->where("wxt_student.school_id = '{$this->session->school['id']}'");
            }else{
                $this->db->where("wxt_student.campus_id = '{$notice['campus_id']}' and wxt_student.school_id = '{$this->session->school['id']}'");
            }

            $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_student.id");
            //获取学生
            $student = $this->db->get("wxt_student")->result_array();

            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "{$item['name']}同学，您所在的学校发布了新的通知，请留意查看! ";//标题
                $msg['keyword1'] = $notice['title'];
                $msg['keyword2'] = date('Y-m-d H:i',time());
                $msg['remark'] = $notice['info']."\n具体内容请点击查看通知详情";//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Notice/{$notice_id}?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
            }

            //生成动态信息
            $feed = array(
                "campus_id" => $notice['campus_id'],
                "time" => time(),
                "type" => "13",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '校园通知提醒',
                "msg" => $notice['title'],
                "url" => "#/Apps/Notice/{$notice_id}"
            );
            $this->db->insert("wxt_feed", $feed);
            //事务结束
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->output_json('error', '发送失败！');

            } else {
                $this->output_json('ok', '校园通知发送成功！');
            }
        }

    }

    /**
     * 修改
     * @param int $id
     */
    public function edit($id = 0)
    {
        $this->form_validation->set_rules('title', '通知标题', 'trim|required');
        $this->form_validation->set_rules('body', '通知详情', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }else{

            $notice['title'] = $this->input->post('title');
            $notice['info'] = $this->input->post('info');
            $notice['body'] = $this->input->post('body');

            $this->db->trans_start();
            $this->db->where('id',$id);
            $this->db->update("wxt_notice",$notice);
            $this->output_json('ok', '修改成功！');


        }

    }

    /**
     * 删除
     * @param int $id
     */
    public function del($id = 0)
    {
        $this->is_my_school($id,'notice');
        $this->db->where("id = {$id}");
        $this->db->delete('wxt_notice');
        $this->output_json('ok', '删除成功！');

    }

}