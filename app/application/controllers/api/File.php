<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/18
 * Time: 下午8:00
 */
use qcloudcos\Cosapi;
use Intervention\Image\ImageManager;
class File extends MY_Controller
{

    public function upload()
    {
        if ($this->input->method() == 'post'){
            $this->load->library('upload');
            $this->load->helper('qcos');
            $school = $this->session->school;
            $path = '/file/s'.$school['id'].'/'.date('Ym').'/';
            $bucket = 'wxt';
            $config['upload_path'] = '.'.$path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|zip|rar|doc|xls';
            $config['encrypt_name'] = true;
            $config['file_ext_tolower'] = true;
            $config['max_size'] = 1024*10;

            if(!file_exists($config['upload_path'])){
                mkdir($config['upload_path'],0700,true);
            }

            $this->upload->initialize($config);


            if($this->upload->do_upload('file')){

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
                    $this->output_json("ok","成功",$file2);
                }else{
                    $this->output_json("error","错误",$ret);
                }
            }else{
                $this->output_json("error","上传失败：".$this->upload->display_errors('',''),'','-1');
            }
        }

    }

    /**
     * 导入学生信息
     */
    public function input_student()
    {
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $config = array();
        $config['upload_path']      = './upload/temp/';
        $config['allowed_types']    = 'xls|xlsx';
        $config['encrypt_name']     = TRUE;

        $this->load->library('upload', $config);


        if ( ! $this->upload->do_upload('file'))
        {
            $this->output_json('error',$this->upload->display_errors(' ',' '));
        }
        else {
            $excel = $this->upload->data();

            $this->load->library('PHPExcel');
            $this->load->library('PHPExcel/IOFactory');
            $IOFactory = IOFactory::load($excel['full_path']);

            $dataArray = $IOFactory->getActiveSheet()->toArray();

            unset($dataArray['0']);
            $s_count = 0;

            foreach ($dataArray as &$item){

                if(empty($item['2']) or empty($item['1']) or empty($item['4'])){
                    continue;
                }

                $item['repeat'] = '1';

                $this->db->where("school_id = {$this->session->school['id']} and number = '{$item['2']}' or (name = '{$item['1']}' and phone = '{$item['4']}')");
                if($this->db->count_all_results("wxt_student") > 0){
                    $item['repeat'] = '1';
                }else{


                    $campus_id = $this->input->post('campus_id');

                    $student = array(
                        'school_id' => $this->session->school['id'],
                        'campus_id' => $campus_id,
                        'status' => '1',
                        'number' => $item['2'],
                        'name' => $item['1'],
                        'sex' => ($item['3'] == '男' or $item['3'] == '女')?$item['3']:'未知',
                        'age' => $item['5'],
                        'school' => $item['6'],
                        'grade_id' => '17',
                        'phone' => $item['4'],
                        'email' => $item['7'],
                        'qq' => $item['8'],
                        'weixin' => $item['9'],
                        'address' => $item['10'],
                        'remark' => $item['11'],
                        'reg_time' => strtotime($item['0'])
                    );

                    $this->db->insert("wxt_student",$student);
                    $sid = $this->db->insert_id();

                    if(!empty($sid)){


                        //导入联系人信息
                        if(!empty($item['12']) and !empty($item['13']) and !empty($item['14'])){
                            $student_contact = array(
                                'school_id' => $this->data['school']['id'],
                                'student_id' => $sid,
                                'contact_name' => $item['12'],
                                'phone'=>$item['13'],
                                'relation'=>$item['12'],
                                'job'=>$item['14']
                            );

                            $this->db->insert("wxt_student_contact",$student_contact);
                        }
                        //成功计数
                        $s_count++;

                        $item['repeat'] = '0';

                    }

                    @unlink($excel['full_path']);

                }

            }

            $data['count'] = $s_count;
            $data['all_count'] = count($dataArray);
            $data['list']=$dataArray;
            if(empty($data['count'])){
                $this->output_json('error','没有有效的学生信息！');
            }else{
                $this->output_json('ok','载入成功',$data);
            }
        }

    }

    /**
     * 导入意向信息
     */
    public function input_customer()
    {
        $this->form_validation->set_rules('campus_id', '校区', 'trim|required|numeric');
        $this->form_validation->set_rules('zy_id', '市场专员', 'trim|required|numeric');
        $this->form_validation->set_rules('gw_id', '课程顾问', 'trim|required|numeric');
        $this->form_validation->set_rules('source_id', '渠道来源', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $this->output_json('error', validation_errors(' ', ' '));
        }

        $config = array();
        $config['upload_path']      = './upload/temp/';
        $config['allowed_types']    = 'xls|xlsx';
        $config['encrypt_name']     = TRUE;

        $this->load->library('upload', $config);


        if ( ! $this->upload->do_upload('file'))
        {
            $this->output_json('error',$this->upload->display_errors(' ',' '));
        }
        else {
            $excel = $this->upload->data();

            $this->load->library('PHPExcel');
            $this->load->library('PHPExcel/IOFactory');
            $IOFactory = IOFactory::load($excel['full_path']);

            $dataArray = $IOFactory->getActiveSheet()->toArray();

            unset($dataArray['0']);
            $s_count = 0;

            foreach ($dataArray as &$item){

                if(empty($item['1']) or empty($item['2'])){
                    continue;
                }


                $this->db->where("school_id = {$this->session->school['id']} and name = '{$item['0']}' and phone = '{$item['2']}'");
                if($this->db->count_all_results("wxt_intention") > 0){
                    $item['repeat'] = '1';
                }else{
                    $campus_id = $this->input->post('campus_id');

                    $student = array(
                        'school_id' => $this->session->school['id'],
                        'campus_id' => $campus_id,
                        'name' => $item['0'],
                        'age' => $item['1'],
                        'phone' => $item['2'],
                        'sex' => ($item['3'] == '男' or $item['3'] == '女')?$item['3']:'未知',
                        'school' => $item['4'],
                        'email' => $item['5'],
                        'qq' => $item['6'],
                        'weixin' => $item['7'],
                        'address' => $item['8'],
                        'remark' => $item['9'],
                        'reg_time' => time(),
                        'reg_user'=>$this->session->user['id'],
                        'zy_id'=>$this->input->post('zy_id'),
                        'gw_id'=>$this->input->post('gw_id'),
                        'source_id'=>$this->input->post('source_id'),
                        'status_id'=>'2'
                    );

                    $this->db->insert("wxt_intention",$student);
                    $s_count++;
                }


            }
            @unlink($excel['full_path']);

            $data['count'] = $s_count;
            $data['all_count'] = count($dataArray);
            $data['list']=$dataArray;
            if(empty($data['count'])){
                $this->output_json('error','没有有效的信息！');
            }else{
                $this->output_json('ok','载入成功',$data);
            }
        }

    }

    public function test()
    {


    }


}