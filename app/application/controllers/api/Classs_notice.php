<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/30
 * Time: 下午4:01
 */
class Classs_notice extends MY_Controller
{
    /**
     * 通知列表
     * @param int $class_id
     */
    public function index($class_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $limit = empty($this->input->get('limit'))?20:$this->input->get('limit');
        $page = empty($this->input->get('page'))?1:$this->input->get('page');
        $page = ($page-1)*$limit;
        $data['limit'] = $limit;

        $this->db->where(array('class_id'=>$class_id));
        $data['count'] = $this->db->count_all_results('wxt_notice');


        $this->db->select("wxt_notice.id,wxt_notice.time,wxt_notice.title,wxt_notice.info,wxt_notice.is_receipt,wxt_user.name as from_name");
        $this->db->where(array('wxt_notice.class_id' => $class_id));
        $this->db->join("wxt_user", "wxt_user.id = wxt_notice.from");
        $this->db->limit($limit,$page);
        $this->db->order_by('id', 'desc');
        $data['score'] = $this->db->get("wxt_notice")->result_array();

        $this->output_json("ok","班级通知",$data);
    }

    /**
     * 通知详情
     * @param int $class_id
     * @param int $notice_id
     */
    public function view($class_id= 0,$notice_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($notice_id,'notice');

        $this->db->where("id = {$notice_id}");
        $notice = $this->db->get("wxt_notice")->row_array();

        if ($notice['is_receipt'] == '1') {
            $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.phone,wxt_notice_receipt.re_time");
            $this->db->where("wxt_class_student.class_id = {$notice['class_id']}");
            $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
            $this->db->join("wxt_notice_receipt", "wxt_notice_receipt.student_id = wxt_student.id and wxt_notice_receipt.notice_id = {$notice_id}", 'left');
            $notice['receipt'] = $this->db->get("wxt_class_student")->result_array();
        }
        $this->output_json("ok","班级通知",$notice);
    }

    /**
     * 添加通知
     * @param int $class_id
     */
    public function add($class_id = 0)
    {
        $this->is_my_school($class_id,'class');


        $this->form_validation->set_rules('title', '标题', 'trim|required');
        $this->form_validation->set_rules('body', '内容', 'trim|required');


        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        } else {
            //事务开始
            $this->db->trans_start();

            //添加通知到数据库
            $notice = array(
                "title" => $this->input->post("title"),
                "info" => empty($this->input->post("info")) ? $this->input->post("title") : $this->input->post("info"),
                "time" => time(),
                "class_id" => $class_id,
                "type" => "class",
                "school_id" => $this->session->school['id'],
                "from" => $this->session->user['id'],
                "to" => "student",
                "body" => $this->input->post("body"),
                "is_receipt" => $this->input->post("is_receipt")
            );
            $this->db->insert("wxt_notice", $notice);
            //获取班级通知id
            $notice_id = $this->db->insert_id();

            //生成动态信息
            $feed = array(
                "class_id" => $class_id,
                "time" => time(),
                "type" => "12",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '班级通知提醒',
                "msg" => $notice['title'],
                "url" => "#/Apps/Notice/{$notice_id}"
            );
            $this->db->insert("wxt_feed", $feed);
            //事务结束
            $this->db->trans_complete();

            //获取班级信息
            $this->db->where("id",$class_id);
            $classs = $this->db->get("wxt_class")->row_array();

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM402277404"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();

            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
            $this->db->where("wxt_class_student.class_id = {$class_id} and wxt_class_student.status = '1'");
            $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_class_student.student_id");
            $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");

            //获取班级学生
            $student = $this->db->get("wxt_class_student")->result_array();

            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "{$item['name']}同学，您所在的班级老师发布了新的通知，请留意查看! ";//标题
                $msg['keyword1'] = $classs['class_name'];//班级
                $msg['keyword2'] = $this->session->user['name'];//老师
                $msg['keyword3'] = date('Y-m-d H:i:s', time());//时间
                $msg['keyword4'] = $notice['title'];//内容
                $msg['remark'] = '具体内容请点击查看通知详情。';//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Notice/{$notice_id}?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }


            if ($this->db->trans_status() === FALSE) {
                $this->output_json('error', '发送失败！');

            } else {
                $this->output_json('ok', '班级通知发送成功！');
            }


        }

    }

    /**
     * 删除通知
     * @param int $class_id
     * @param int $notice_id
     */
    public function del($class_id = 0,$notice_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($notice_id,'notice');

        //删除
        $this->db->where("id = {$notice_id}");
        $this->db->delete("wxt_notice");

        $this->db->where("notice_id = {$notice_id}");
        $this->db->delete("wxt_notice_browse");

        $this->db->where("notice_id = {$notice_id}");
        $this->db->delete("wxt_notice_receipt");
        $this->output_json('ok', '通知信息已被删除！');
    }

    /**
     * @param int $class_id
     * @param int $notice_id
     */
    public function receipt($class_id = 0,$notice_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($notice_id,'notice');

        $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name,wxt_student.phone,wxt_notice_receipt.re_time");
        $this->db->where("wxt_class_student.class_id = {$class_id} ");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id", 'left');
        $this->db->join("wxt_notice_receipt", "wxt_notice_receipt.student_id = wxt_student.id and wxt_notice_receipt.notice_id = {$notice_id}", 'left');
        $data['receipt'] = $this->db->get("wxt_class_student")->result_array();

        $this->output_json('ok', '确认回执！',$data);
    }

}