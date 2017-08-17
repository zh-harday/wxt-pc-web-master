<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/10
 * Time: 下午3:48
 */

/**
 * Class Weixin 微信
 */
class Weixin extends MY_Controller
{
    /**
     * 模版消息
     */
    public function template()
    {

        $this->db->select("wxt_config_weixin_temp.*,wxt_weixin_temp.temp_id");
        $this->db->join("wxt_weixin_temp","wxt_weixin_temp.short = wxt_config_weixin_temp.short and wxt_weixin_temp.school_id = {$this->session->user['school_id']}","left");
        $this->db->order_by("sort",'ASC');
        $template = $this->db->get("wxt_config_weixin_temp")->result_array();

        $this->output_json("ok","ok",$template);

    }

    /**
     * 初始化模版消息
     */
    public function template_init()
    {
        if($this->input->method() == 'post'){
            //获取授权
            $access_token = $this->get_token();
            //检查行业
            if(!$this->is_industry($access_token)){
                $this->output_json("error","您的微信公众号行业设置或授权不正确！");
            }

            //清空模版库
            $template = $this->get_all_template($access_token);

            foreach ($template['template_list'] as $item){
                $this->del_template($access_token,$item['template_id']);
            }
            $this->db->where('school_id', $this->session->school['id']);
            $this->db->delete('wxt_weixin_temp');



            //重新获取模版
            $temp = $this->db->get("wxt_config_weixin_temp")->result_array();
            foreach ($temp as $item){
                $data = $this->add_template($access_token,$item['short']);
                if($data['errcode'] == 0){
                    $this->db->insert('wxt_weixin_temp', array("school_id"=>$this->session->school['id'],"short"=>$item['short'],"temp_id"=>$data['template_id']));
                }
            }

            $this->db->select("wxt_config_weixin_temp.*,wxt_weixin_temp.temp_id");
            $this->db->join("wxt_weixin_temp","wxt_weixin_temp.short = wxt_config_weixin_temp.short and wxt_weixin_temp.school_id = {$this->session->user['school_id']}","left");
            $this->db->order_by("sort",'ASC');
            $template = $this->db->get("wxt_config_weixin_temp")->result_array();

            $this->output_json("ok","ok",$template);
        }


    }

    /**
     * 获取模版授权码
     */
    public function template_get()
    {
        if($this->input->method() == 'post'){

            $access_token = $this->get_token();
            if(!$this->is_industry($access_token)){
                $this->output_json("error","您的微信公众号行业设置或授权不正确！");
            }
            if(empty($this->input->post("short"))){
                $this->output_json("error","参数错误！");
            }

            $short = $this->input->post("short");



            $this->db->where(array("school_id"=>$this->session->school['id'],"short"=>$short));
            $temp = $this->db->get("wxt_weixin_temp")->row_array();

            if(empty($temp)){
                $this->db->where("short",$short);
                if($this->db->count_all_results("wxt_config_weixin_temp") < 1){
                    $this->output_json("error","此模版不存在！！");
                }


                $data = $this->add_template($access_token,$short);
                if($data['errcode'] == 0){
                    $this->db->insert('wxt_weixin_temp', array("school_id"=>$this->session->school['id'],"short"=>$short,"temp_id"=>$data['template_id']));
                    $this->output_json("ok","ok",$data);
                }else{
                    $this->output_json("error","授权错误请重新尝试！");
                }

            }else{
                $this->del_template($access_token,$temp['temp_id']);

                $data = $this->add_template($access_token,$short);
                if($data['errcode'] == 0){
                    $this->db->where("id",$temp['id']);
                    $this->db->update('wxt_weixin_temp', array("temp_id"=>$data['template_id']));
                    $this->output_json("ok","ok",$data);
                }else{
                    $this->output_json("error","授权错误请重新尝试！");
                }

            }
        }


    }

    /**
     * 获取token
     * @return mixed
     */
    private function get_token()
    {
        if(empty($this->session->school['pigcms_token'])){
            $this->output_json("error","请绑定公众号后再设置！");
        }

        $data = erm_post("http://wx.eduwxt.com/index.php?g=Home&m=Index&a=getToken&token={$this->session->school['pigcms_token']}");
        return $data;
    }

    /**
     * 获取行业设置
     * @param string $access_token
     * @return mixed
     */
    private function get_industry($access_token='')
    {
        if(empty($access_token)){
            $this->output_json("error","获取不到 access_token！");
        }
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token={$access_token}";
        return json_decode(erm_post($url),TRUE);
    }

    /**
     * 检查行业设置
     * @param string $access_token
     * @return bool
     */
    private function is_industry($access_token='')
    {
        $industry = $this->get_industry($access_token);


        if($industry['primary_industry']['second_class'] == '互联网|电子商务' and $industry['secondary_industry']['second_class'] == '培训'){
            return true;
        }else{
            $this->set_industry($access_token);

            $industry = $this->get_industry($access_token);
            if($industry['primary_industry']['second_class'] == '互联网|电子商务' and $industry['secondary_industry']['second_class'] == '培训'){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * 设置行业
     * @param string $access_token
     * @return mixed
     */
    private function set_industry($access_token='')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token={$access_token}";
        return json_decode(erm_post($url,json_encode(array("industry_id1"=>"1","industry_id2"=>"16"))),TRUE);
    }


    /**
     * 获取所有模版
     * @param string $access_token
     * @return mixed
     */
    private function get_all_template($access_token = '')
    {
        $url ="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";
        return json_decode(erm_post($url),TRUE);
    }

    /**
     * 删除指定模版
     * @param string $access_token
     * @param string $template_id
     * @return mixed
     */
    private function del_template($access_token = '',$template_id='')
    {
        $url ="https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token={$access_token}";
        return json_decode(erm_post($url,json_encode(array("template_id"=>$template_id))),TRUE);
    }

    /**
     * 添加指定模版
     * @param string $access_token
     * @param string $short
     * @return mixed
     */
    private function add_template($access_token = '',$short='')
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$access_token}";
        return json_decode(erm_post($url,json_encode(array("template_id_short"=>$short))),TRUE);
    }







}