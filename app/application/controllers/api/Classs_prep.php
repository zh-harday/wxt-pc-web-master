<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/30
 * Time: 下午11:32
 */
class Classs_prep extends MY_Controller
{
    /**
     * 预习提醒
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
        $data['count'] = $this->db->count_all_results('wxt_class_prep');


        $this->db->select("wxt_class_prep.*,wxt_user.name as teacher,wxt_subject.subject");
        $this->db->where(array('wxt_class_prep.class_id' => $class_id));
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_prep.teacher_id");
        $this->db->join("wxt_subject", "wxt_subject.id = wxt_class_prep.subject_id");
        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_prep.id', 'desc');
        $data['prep'] = $this->db->get("wxt_class_prep")->result_array();

        $this->output_json("ok","预习提醒",$data);

    }

    /**
     * 预习详情
     * @param int $class_id
     * @param int $prep_id
     */
    public function view($class_id= 0,$prep_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($prep_id,'class_prep');

        $this->db->where("id = {$prep_id}");
        $data['prep'] = $this->db->get("wxt_class_prep")->row_array();
        $this->output_json("ok","预习提醒",$data);
    }

    /**
     * 添加通知
     * @param int $class_id
     */
    public function add($class_id= 0)
    {
        $this->is_my_school($class_id,'class');

        $this->form_validation->set_rules('title', '标题', 'trim|required');
        $this->form_validation->set_rules('body', '内容', 'trim|required');
        $this->form_validation->set_rules('subject_id', '课程', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        } else {


            //添加通知到数据库
            $prep = array(
                "title" => $this->input->post("title"),
                "info" => empty($this->input->post("info")) ? $this->input->post("title") : $this->input->post("info"),
                "time" => time(),
                "class_id" => $class_id,
                "school_id" => $this->session->school['id'],
                "teacher_id" => $this->session->user['id'],
                "subject_id" => $this->input->post("subject_id"),
                "body" => $this->input->post("body")
            );

            $this->db->where("subject_id = {$prep['subject_id']} and class_id = $class_id");
            if(empty($this->db->count_all_results("wxt_class_subject"))){
                $this->output_json('error', '所选课程不属于本班级！');
            }

            //事务开始
            $this->db->trans_start();
            $this->db->insert("wxt_class_prep", $prep);
            //获取班级通知id
            $prep_id = $this->db->insert_id();



            //生成动态信息
            $feed = array(
                "class_id" => $class_id,
                "time" => time(),
                "type" => "4",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '预习通知',
                "msg" => $prep['title'],
                "url" => "#/Apps/Classwork/PrepareDetail/{$prep_id}"
            );
            $this->db->insert("wxt_feed", $feed);
            //事务结束
            $this->db->trans_complete();

            //获取班级信息
            $this->db->where('id',$class_id);
            $classs = $this->db->get('wxt_class')->row_array();

            $prep_subject = $this->db->select('subject')->where("id = {$prep['subject_id']}")->get("wxt_subject")->row_array();

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM406301063"));
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
                $msg['first'] = "{$item['name']}同学，您所在的{$classs['class_name']}发布了新的课程预习内容，请留意查看。 ";//标题
                $msg['keyword1'] = $prep_subject['subject'];//班级
                $msg['keyword2'] = $this->session->user['name'];//老师
                $msg['keyword3'] = date('Y-m-d H:i:s', time());//时间
                $msg['remark'] = '提前预习有助于您更好的掌握课堂知识，祝您学习愉快!点击查看详细预习内容。';//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Classwork/PrepareDetail/{$prep_id}?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
                //erm_post($url, $msg);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->output_json('error', '发送失败！');

            } else {
                $this->output_json('ok', '预习通知发送成功！');
            }


        }
    }

    /**
     * 删除预习通知
     * @param int $class_id
     * @param int $prep_id
     */
    public function del($class_id= 0,$prep_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($prep_id,'class_prep');

        //删除
        $this->db->where("id = {$prep_id}");
        $this->db->delete("wxt_class_prep");

        $this->db->where("prep_id = {$prep_id}");
        $this->db->delete("wxt_class_prep_browse");

        $this->output_json('ok', '通知信息已被删除！');

    }

}