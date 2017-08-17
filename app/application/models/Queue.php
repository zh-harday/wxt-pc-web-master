<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/5/15
 * Time: 下午5:15
 */
class Queue extends CI_Model
{


    public function post($token='',$data=array())
    {
        if(empty($data['keyword4'])){
            $data['keyword4'] = '';
        }
        if(empty($data['keyword5'])){
            $data['keyword5'] = '';
        }
        $this->db->insert('wxt_queue',array('type'=>'post','token'=>$token,'data'=>json_encode($data),'add_time'=>time()));
        return $this->db->insert_id();
    }


}