<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/10
 * Time: 下午3:58
 */

/**
 * Class Room 教室
 */
class Room extends MY_Controller
{

    public function index()
    {
        $this->db->select("id,campus_id,room_name,status,remark");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $classroom = $this->db->get("wxt_classroom")->result_array();
        $this->output_json("ok","ok",$classroom);

    }

    /**
     * 获取教室
     * @param int $rid
     */
    public function info($rid = 0)
    {
        $this->db->select("id,campus_id,room_name,status,remark");
        $this->db->where("id= '{$rid}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $classroom = $this->db->get("wxt_classroom")->row_array();
        $this->output_json("ok","ok",$classroom);
    }

    /**
     * 添加教室
     */
    public function add()
    {

        if($this->input->method() == 'post'){


            $this->form_validation->set_rules('campus_id', '校区', 'trim|required|integer');
            $this->form_validation->set_rules('room_name', '教室名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                $room = $this->input->post();

                $room['remark']     = $this->input->post('remark');
                $room['re_user']    = $this->session->user['id'];
                $room['school_id']  = $this->session->school['id'];
                $room['re_time']    = time();

                $this->db->insert("wxt_classroom",$room);
                $this->output_json("ok","成功",$room);
            }
        }

    }

    /**
     * 编辑教室
     * @param int $rid 教室id
     */
    public function edit($rid = 0)
    {
        if(!is_numeric($rid)){
            $this->output_json("error","参数错误！",'','-1');
        }
        if($this->input->method() == 'post'){



            $this->form_validation->set_rules('room_name', '教室名称', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{

                $room['room_name']     = $this->input->post('room_name');
                $room['remark']     = $this->input->post('remark');

                $this->db->where("id= '{$rid}'");
                $this->db->where("school_id= '{$this->session->user['school_id']}'");
                $this->db->update("wxt_classroom",$room);

                $this->output_json("ok","成功",$room);
            }
        }

    }

    /**
     * 删除教室
     * @param int $rid
     */
    public function del($rid = 0)
    {

        $this->db->where("id= '{$rid}'");
        $this->db->where("school_id= '{$this->session->user['school_id']}'");
        $classroom = $this->db->get("wxt_classroom")->row_array();



        //检查是否本学校校区
        if($classroom['school_id'] == $this->session->user['school_id']){
            $this->db->where('room_id',$rid);
            $curriculum_room = $this->db->count_all_results('wxt_class_curriculum');

            $this->db->where('room_id',$rid);
            $lecture_room = $this->db->count_all_results('wxt_intention_lecture');

            if(($curriculum_room + $lecture_room) > 0){

                $this->output_json("error","您要删除的教室已有相关课程安排，不允许删除！",'','-1');
            }else{
                //删除教室
                $this->db->where("id",$rid);
                $this->db->delete("wxt_classroom");
                $this->output_json("ok","成功");
            }

        }else{
            $this->output_json('error', '无此权限');
        }
    }

}