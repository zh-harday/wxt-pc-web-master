<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/9
 * Time: 上午11:00
 */

/**
 * Class School 学校
 */
use qcloudcos\Cosapi;
use Intervention\Image\ImageManager;
class School extends MY_Controller
{
    public function index()
    {

        $this->db->select("id,name,description,leader,phone,address,logo,pack_id,status,exp_time,campus_quota,student_quota,pigcms_token,pigcms_user");
        $this->db->where("id = '{$this->session->user['school_id']}'");
        $school = $this->db->get("wxt_school")->row_array();


        if(empty($school)){
            $this->output_json("error","信息不存在！",'','-1');
        }else{

            $this->db->db_select('wxt_pigcms');
            $this->db->select('id,uid,wxname,winxintype,token');
            $this->db->where('uid',$school['pigcms_user']);
            $pigcms = $this->db->get('pigcms_wxuser')->row_array();
            $this->db->db_select('wxt_admin');

            if($school['pigcms_token'] <> $pigcms['token']){
                $this->db->where('id',$this->session->user['school_id']);
                $this->db->update('wxt_school',array('pigcms_token'=>$pigcms['token']));
            }
            $this->output_json("ok","成功",$school);
        }
    }

    /**
     * 学校信息 编辑
     */
    public function info()
    {
        if($this->input->method() == 'post'){


            $this->form_validation->set_rules('name', '学校名称', 'trim|required');
            $this->form_validation->set_rules('address', '学校地址', 'trim|required');
            $this->form_validation->set_rules('leader', '负责人', 'trim|required');
            $this->form_validation->set_rules('phone', '联系电话', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                $school['name'] = $this->input->post('name');
                $school['address'] = $this->input->post('address');
                $school['leader'] = $this->input->post('leader');
                $school['phone'] = $this->input->post('phone');
                $school['description'] = $this->input->post('description');

                $this->db->where("id",$this->session->user['school_id']);
                $this->db->update("wxt_school",$school);
                $this->output_json("ok","成功",$school);
            }
        }

    }


    /**
     * 学校logo
     */
    public function logo()
    {
        if($this->input->method() == 'post'){
            $this->load->library('upload');
            $this->load->helper('qcos');
            $school = $this->session->school;
            $path = '/file/s'.$school['id'].'/'.date('Ym').'/';
            $bucket = 'wxt';
            $config['upload_path'] = '.'.$path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['encrypt_name'] = true;
            $config['file_ext_tolower'] = true;
            $config['max_size'] = 1024*4;

            if(!file_exists($config['upload_path'])){
                mkdir($config['upload_path'],0700,true);
            }

            $this->upload->initialize($config);


            if($this->upload->do_upload('nlogo')){

                $file = $this->upload->data();
                $filesize = $file['file_size'];

                //是否压缩
                if($file['is_image'] and $file['image_width'] > 1400){
                    $manager = new ImageManager();
                    $image = $manager->make($file['full_path'])->resize(1400,$file['image_height']/($file['image_width']/1400));
                    //$image = $manager->make($file['full_path'])->fit(300,300);
                    $image->save($file['full_path']);

                    clearstatcache(TRUE,$file['full_path']);
                    $filesize = round($image->filesize()/1024, 2);
                }


                $ret = Cosapi::upload($bucket,$file['full_path'],$path.$file['file_name']);

                if($ret['code'] == '0' or $ret['code'] == '-4018'){
                    $file2 = array(
                        'school_id'=>$school['id'],
                        'file_name'=>$file['file_name'],
                        'user_id'=>$this->session->user['id'],
                        'add_time'=>time(),
                        'file_path'=>$path.$file['file_name'],
                        'file_type'=>$file['file_type'],
                        'file_size'=>$filesize,
                        'is_image'=>$file['is_image']?'1':'0',
                        'cos_bucket'=>$bucket,
                        'cos_path'=>$ret['data']['access_url']
                    );
                    $this->db->insert("wxt_file",$file2);

                    $this->db->where("id",$this->session->user['school_id']);
                    $this->db->update("wxt_school",array('logo'=>$file2['file_path']));

                    $this->output_json("ok","成功",$file2);
                }else{
                    $this->output_json("error","错误",$ret);
                }
            }else{
                $this->output_json("error","上传失败：".$this->upload->display_errors('',''),'','-1');
            }




        }else{
            $this->db->select("logo");
            $this->db->where("id = '{$this->session->user['school_id']}'");
            $school = $this->db->get("wxt_school")->row_array();


            if(empty($school)){
                $this->output_json("error","信息不存在！",'','-1');
            }else{
                $this->output_json("ok","成功",$school);
            }
        }
        
    }


    

}