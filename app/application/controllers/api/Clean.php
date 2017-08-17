<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/6/13
 * Time: 上午10:46
 */
class Clean extends MY_Controller
{

    public function init()
    {

        
    }

    public function module($module = '')
    {

        if($this->$module()){
            $this->output_json('ok',$module.'模块清理完毕');
        }

    }

    private function classs()
    {
        $this->db->trans_start();

        $sql_class = "class_id in (select id from wxt_class where school_id = {$this->session->user['school_id']})";
        $sql_curriculum = "curriculum_id in (select id from wxt_class_curriculum where school_id = {$this->session->user['school_id']})";

        $this->db->delete('wxt_class_attence',$sql_curriculum);//清理考勤信息
        $this->db->delete('wxt_class_evaluation',$sql_curriculum);//清理点评信息
        $this->db->delete('wxt_class_leave',$sql_curriculum);//清理请假信息
        $this->db->delete('wxt_class_curriculum',$sql_class);//清理课程信息

        $this->db->where("prep_id in (select id from wxt_class_prep where school_id = {$this->session->user['school_id']})");
        $this->db->delete('wxt_class_prep_browse');//清理预习提醒
        $this->db->delete('wxt_class_prep',$sql_class);//清理预习提醒


        $this->db->delete('wxt_class_reservation',$sql_class);//清理
        $this->db->delete('wxt_class_student',$sql_class);//清理班级学生
        $this->db->delete('wxt_class_subject',$sql_class);//清理班级课程
        $this->db->delete('wxt_class_test',$sql_class);//清理成绩信息

        $this->db->where("class_work_id in (select id from wxt_class_work where school_id = {$this->session->user['school_id']})");
        $this->db->delete('wxt_class_work_log');//清理作业记录
        $this->db->delete('wxt_class_work',$sql_class);//清理作业记录

        $this->db->delete('wxt_notice',"class_id in (select id from wxt_class where school_id = {$this->session->user['school_id']})");//清理班级通知

        $this->db->delete('wxt_feed',"class_id in (select id from wxt_class where school_id = {$this->session->user['school_id']})");//清理feed

        $this->db->delete('wxt_class',"school_id = {$this->session->user['school_id']}");//删除班级

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    private function student()
    {
        $this->db->trans_start();

        $sql_student = "student_id in (select id from wxt_student where school_id = {$this->session->user['school_id']})";

        $this->db->delete('wxt_im_msg',$sql_student);//删除聊天信息
        $this->db->delete('wxt_student_contact',$sql_student);//删除学生联系人
        $this->db->delete('wxt_feed',$sql_student);//清理feed

        $this->db->delete('wxt_student_service_log',"school_id",$this->session->user['school_id']);//删除服务日志
        $this->db->delete('wxt_student_weixin',"school_id",$this->session->user['school_id']);//删除微信绑定信息

        $this->db->delete('wxt_student',"school_id = {$this->session->user['school_id']}");//删除学生信息

        $this->db->delete('wxt_suggest',"school_id = {$this->session->user['school_id']}");//删除投诉建议信息

        $this->db->trans_complete();

        return $this->db->trans_status();

    }

    private function intention()
    {
        $this->db->trans_start();
        $sql = "school_id = {$this->session->user['school_id']}";


        //$this->db->delete('wxt_intention_grade',$sql);//
        $this->db->delete('wxt_intention_lecture',$sql);//清除试听课
        $this->db->delete('wxt_intention_lecture_booking',$sql);//清除试听课报名信息
        $this->db->delete('wxt_intention_level',$sql);//清除意向等级设置
        $this->db->delete('wxt_intention_log',$sql);//清除意向学员跟踪记录
        $this->db->delete('wxt_intention_source',$sql);//清除意向学员来源设置
        $this->db->delete('wxt_intention_status',$sql);//清除意向学员转改设置
        $this->db->delete('wxt_intention_subject',$sql);//清除意向学员课程设置

        $this->db->delete('wxt_free_form',$sql);//清除自定义表单
        $this->db->delete('wxt_free_form_field',$sql);//清除自定义表单

        $this->db->delete('wxt_gtd_intention_log',"intention_id in (select id from wxt_intention where school_id = {$this->session->user['school_id']})");//
        $this->db->delete('wxt_gtd_new_intention',$sql);//

        $this->db->delete('wxt_intention',$sql);//清除意向学员


        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    private function finance()
    {
        $this->db->trans_start();
        $sql = "school_id = {$this->session->user['school_id']}";

        $this->db->delete('wxt_finance_orders',$sql);//清除流水订单
        $this->db->delete('wxt_finance_recordss',$sql);//清除流水
        $this->db->trans_complete();

        return $this->db->trans_status();
        
    }

}