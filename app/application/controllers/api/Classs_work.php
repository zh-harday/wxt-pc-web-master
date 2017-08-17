<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/30
 * Time: 下午10:47
 */
class Classs_work extends MY_Controller
{
    /**
     * 班级作业
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
        $data['count'] = $this->db->count_all_results('wxt_class_work');

        $this->db->select("wxt_class_work.*,wxt_user.name as teacher_name,wxt_subject.subject as subject_name");
        $this->db->where(array('wxt_class_work.class_id' => $class_id));
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_work.teacher_id");
        $this->db->join("wxt_subject", "wxt_subject.id = wxt_class_work.subject_id");

        $this->db->limit($limit,$page);
        $this->db->order_by('wxt_class_work.id', 'desc');
        $data['work'] = $this->db->get('wxt_class_work')->result_array();
        $this->output_json("ok","班级作业",$data);
    }

    /**
     * 作业详情
     * @param int $class_id
     * @param int $work_id
     */
    public function view($class_id = 0,$work_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($work_id,'class_work');

        $this->db->where("id = {$work_id}");
        $work = $this->db->get("wxt_class_work")->row_array();
        $work['subject'] = $this->db->select('subject as subject_name')->where("id = {$work['subject_id']}")->get("wxt_subject")->row_array();
        $this->output_json("ok","班级作业",$work);

    }

    /**
     * 点评情况
     * @param int $class_id
     * @param int $work_id
     */
    public function dp($class_id = 0,$work_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($work_id,'class_work');

        $this->db->where("id = {$work_id}");
        $work = $this->db->get("wxt_class_work")->row_array();


        $this->db->select("wxt_student.id as student_id,wxt_student.name as student_name ,wxt_student.face,wxt_class_work_log.is_comments,wxt_class_work_log.score,wxt_class_work_log.comments,wxt_class_work_log.status,wxt_class_work_log.student_work,wxt_class_work_log.student_work_file,wxt_class_work_log.comments_file");
        $this->db->where("wxt_class_student.class_id = {$work['class_id']}");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
        $this->db->join("wxt_class_work_log", "wxt_class_work_log.class_work_id = $work_id and wxt_class_work_log.student_id = wxt_class_student.student_id", 'left');

        $this->db->order_by('wxt_class_work_log.id', 'asc');
        $work['dp'] = $this->db->get("wxt_class_student")->result_array();

        foreach ($work['dp'] as &$item){
            $item['student_work_file'] = json_decode($item['student_work_file'],true);
            $item['comments_file'] = json_decode($item['comments_file'],true);
        }

        $this->output_json("ok","点评情况",$work);

    }

    /**
     * 老师点评
     * @param int $class_id
     * @param int $work_id
     */
    public function comments($class_id = 0,$work_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($work_id,'class_work');




        $this->form_validation->set_rules('uid', '学生', 'trim|required|numeric');
        $this->form_validation->set_rules('score', '分数', 'trim|required|less_than_equal_to[5]');
        $this->form_validation->set_rules('comments', '点评', 'trim|required');



        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        }

        $uid = $this->input->post('uid');
        $this->db->where("class_work_id = {$work_id} and student_id = {$uid}");
        $comments = $this->db->get("wxt_class_work_log")->row_array();



        $dp['score'] = $this->input->post('score');
        $dp['comments'] = $this->input->post('comments');
        $dp['comments_file'] = $this->input->post('comments_file');


        if (empty($comments)) {

            $dp['class_work_id'] = $work_id;
            $dp['student_id'] = $uid;
            $dp['is_comments'] = '1';
            $dp['comments_teacher'] = $this->session->user['id'];
            $dp['comments_time'] = time();

            $this->db->insert("wxt_class_work_log", $dp);
            $dp_id = $this->db->insert_id();


        } else if ($comments['is_comments'] == '0') {
            $dp['is_comments'] = '1';
            $dp['comments_teacher'] = $this->session->user['id'];
            $dp['comments_time'] = time();

            $this->db->where("class_work_id = {$work_id} and student_id = {$uid}");
            $this->db->update("wxt_class_work_log", $dp);
            $dp_id = $comments['id'];
        } else {
            $this->output_json('error', '不能重复点评！');
        }

        //获取学生信息
        $this->db->select("name");
        $this->db->where("id = {$uid}");
        $student = $this->db->get("wxt_student")->row_array();

        //生成动态信息
        $feed = array(
            "student_id" => $uid,
            "time" => time(),
            "type" => "10",
            "source" => $this->session->user['name'] . '老师',
            "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
            "title" => '作业点评提醒',
            "msg" => "{$student['name']}同学您好，您的作业老师已经批阅,请留意查看。",
            "url" => "#/Apps/Classwork/homeWorkDP/Detail/{$work_id}"
        );
        $this->db->insert("wxt_feed", $feed);

        //获取模版id
        $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM206241590"));
        $temp = $this->db->get('wxt_weixin_temp')->row_array();

        //api 地址
        $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

        //获取班级信息
        $this->db->select("wxt_class.*");
        $this->db->where("wxt_class_work.id = {$work_id}");
        $this->db->join("wxt_class", "wxt_class.id = wxt_class_work.class_id");
        $classs = $this->db->get("wxt_class_work")->row_array();

        //获取学生信息
        $this->db->select("wxt_student_weixin.wecha_id,wxt_student.*");
        $this->db->where("wxt_student_weixin.uid = {$uid}");
        $this->db->join("wxt_student", "wxt_student.id = wxt_student_weixin.uid");
        $student = $this->db->get("wxt_student_weixin")->result_array();

        //发送模版消息
        //(学生姓名)同学您好，您的作业老师已经发布了最新批阅信息，请及时查看! 点击查看作业评价详情。
        foreach ($student as $item) {
            $msg['wecha_id'] = $item['wecha_id'];
            $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
            $msg['first'] = "{$item['name']}同学您好，您的作业老师已经批阅,请留意查看。";
            $msg['keyword1'] = $item['name'];
            $msg['keyword2'] = $classs['class_name'];
            $msg['keyword3'] = date('Y-m-d H:i:s', time());
            $msg['keyword4'] = '';//内容
            $msg['remark'] = '点击查看作业评价详情。';//内容
            $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Classwork/homeWorkDP/Detail/{$work_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

            $this->load->model('queue');
            $this->queue->post($this->session->school['pigcms_token'],$msg);
        }

        $this->output_json('ok', '点评成功！');

    }

    /**
     * 删除作业
     * @param int $class_id
     * @param int $work_id
     */
    public function del($class_id = 0,$work_id = 0)
    {
        $this->is_my_school($class_id,'class');
        $this->is_my_school($work_id,'class_work');

        //删除
        $this->db->where("id = {$work_id}");
        $this->db->delete("wxt_class_work");

        $this->db->where("class_work_id = {$work_id}");
        $this->db->delete("wxt_class_work_log");
        $this->output_json('ok', '作业信息已被删除！');

    }


    /**
     * 添加作业
     * @param int $class_id
     */
    public function add($class_id = 0)
    {

        $this->is_my_school($class_id,'class');

        $this->form_validation->set_rules('title', '标题', 'trim|required');
        $this->form_validation->set_rules('body', '内容', 'trim|required');
        $this->form_validation->set_rules('subject_id', '课程', 'trim|required');


        if ($this->form_validation->run() == FALSE) {

            $this->output_json('error', validation_errors(' ', ' '));

        } else {
            //事务开始


            //添加作业到数据库
            $work = array(
                "title" => $this->input->post("title"),
                "info" => empty($this->input->post("info")) ? $this->input->post("title") : $this->input->post("info"),
                "time" => time(),
                "class_id" => $class_id,
                "school_id" => $this->session->school['id'],
                "teacher_id" => $this->session->user['id'],
                "body" => $this->input->post("body"),
                "subject_id" => $this->input->post("subject_id")
            );

            $this->db->where("subject_id = {$work['subject_id']} and class_id = $class_id");
            if(empty($this->db->count_all_results("wxt_class_subject"))){
                $this->output_json('error', '所选课程不属于本班级！');
            }
            $this->db->trans_start();
            $this->db->insert("wxt_class_work", $work);
            //获取id
            $work_id = $this->db->insert_id();
            //$work_subject = $this->db->select('subject')->where("id = {$work['subject_id']}")->get("wxt_subject")->row_array();


            //生成动态信息
            $feed = array(
                "class_id" => $class_id,
                "time" => time(),
                "type" => "8",
                "source" => $this->session->user['name'] . '老师',
                "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
                "title" => '作业提醒',
                "msg" => $work['title'],
                "url" => "#/Apps/Classwork/Detail/{$work_id}"
            );
            $this->db->insert("wxt_feed", $feed);
            //事务结束
            $this->db->trans_complete();

            //获取班级信息
            $this->db->where('id',$class_id);
            $classs = $this->db->get('wxt_class')->row_array();

            //获取模版id
            $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM200799669"));
            $temp = $this->db->get('wxt_weixin_temp')->row_array();
            //api 地址
            $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

            //获取班级学生
            $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
            $this->db->where("wxt_class_student.class_id = {$class_id} and wxt_class_student.status = '1'");
            $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_class_student.student_id");
            $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
            $student = $this->db->get("wxt_class_student")->result_array();

            $work_subject = $this->db->select('subject')->where("id = {$work['subject_id']}")->get("wxt_subject")->row_array();

            //发送模版消息
            foreach ($student as $item) {
                $msg['wecha_id'] = $item['wecha_id'];
                $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
                $msg['first'] = "{$item['name']}同学您好，您报名的{$classs['class_name']}有新的作业发布了，请留意查看! ";//标题
                $msg['keyword1'] = date('Y-m-d H:i:s', time());//时间
                $msg['keyword2'] = $work_subject['subject'];//课程
                $msg['keyword3'] = $work['title'];//标题
                $msg['keyword4'] = $work['info'];//内容
                $msg['remark'] = '温故而知新，可以为师矣!请您及时完成作业，点击查看作业详情。';//内容
                $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/Classwork/Detail/{$work_id}?token={$this->session->school['pigcms_token']}&wecha_id={$item['wecha_id']}";

                $this->load->model('queue');
                $this->queue->post($this->session->school['pigcms_token'],$msg);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->output_json('error', '发送失败！');

            } else {
                $this->output_json('ok', '发送成功！');
            }


        }

    }



}