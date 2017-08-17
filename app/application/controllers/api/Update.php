<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/7/24
 * Time: 下午6:22
 */
class Update extends MY_Controller
{
    public function index()
    {

        //2017年7月24日 更新 班级课程数据库
        $this->db->select('wxt_class_subject.id,wxt_class.id as class_id');
        $this->db->join("wxt_class","wxt_class.id = wxt_class_subject.class_id","left");
        $class_subject = $this->db->get("wxt_class_subject")->result_array();


        foreach ($class_subject as $item){
            if(empty($item['class_id'])){
                $this->db->where("id",$item['id']);
                $this->db->delete("wxt_class_subject");
            }

        }
        
    }

}
