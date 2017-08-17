<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/2
 * Time: 下午5:21
 */

/**
 * Class User 用户
 */
use qcloudcos\Cosapi;
use Intervention\Image\ImageManager;
class User extends MY_Controller
{

    /**
     * 用户信息
     */
    public function index()
    {

        $this->db->select("wxt_user.id,wxt_user.name,wxt_user.email,wxt_user.phone,wxt_user.weixin_id,wxt_user.wecha_id,wxt_user.staus,wxt_user.group_id,wxt_user.school_id,wxt_user.campus_id,wxt_user_info.staff_id,wxt_user_info.face,wxt_user_info.sex,wxt_user_info.entry_time,wxt_user_info.is_full_time,wxt_user_info.birthday,wxt_user_info.qq,wxt_user_info.job_description,wxt_user_info.motto,wxt_user_info.sfz,wxt_user.authority,wxt_user.is_sc,wxt_user.is_gw,wxt_user.is_bzr,wxt_user.is_dk,wxt_user.is_stk");
        $this->db->where("wxt_user.id = '{$this->session->user['id']}' and wxt_user.school_id = '{$this->session->user['school_id']}'");
        $this->db->join("wxt_user_info","wxt_user_info.uid = wxt_user.id");
        $user = $this->db->get("wxt_user")->row_array();


        if(empty($user)){
            $this->output_json("error","用户信息不存在！",'','-1');
        }else{

            if(empty($user['authority'])){
                $this->db->select("authority");
                $this->db->where("id = '{$user['group_id']}'");
                $user['authority'] = $this->db->get("wxt_user_group")->row_array()['authority'];
            }
            $user['authority'] = json_decode($user['authority'],true);

            $user['face'] =  "//wx.eduwxt.com/api/face/teacher/".$user['id'];
            if(empty($user['authority'])){
                $user['authority'] = array();
            }
            $this->output_json("ok","成功",$user);
        }
    }

    /**
     * 用户权限
     */
    public function authority()
    {

        if ($this->input->method() == 'post'){
            $this->form_validation->set_rules('authority', '权限数据', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                $this->db->where("id",$this->session->user['id']);
                $this->db->update("wxt_user",array('authority'=>$this->input->post('authority')));
                $this->output_json("ok","成功",$this->input->post());
            }


        }else{
            $this->db->select("id,role,group_id,authority");
            $this->db->where("wxt_user.id = '{$this->session->user['id']}'");
            $user = $this->db->get("wxt_user")->row_array();

            if(empty($user)){
                $this->output_json("error","用户信息不存在！",'','-1');
            }else{
                if(empty($user['authority'])){
                    $this->db->select("authority");
                    $this->db->where("id = '{$user['group_id']}'");
                    $user['authority'] = $this->db->get("wxt_user_group")->row_array()['authority'];
                }
                $user['authority'] = json_decode($user['authority'],true);

                if(empty($user['authority'])){
                    $user['authority'] = array();
                }
                $this->output_json("ok","成功",$user);
            }
        }

    }

    /**
     * 编辑用户资料
     */
    public function edit()
    {
        if($this->input->method() == 'post'){



            $this->form_validation->set_rules('phone', '手机号码', 'trim|required');
            $this->form_validation->set_rules('birthday', '出生日期', 'trim|required');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                if(!isMobile($this->input->post('phone'))){
                    $this->output_json('error', '手机号码格式不正确');
                }

                $where = array('school_id'=>$this->session->user['school_id'],'phone'=>$this->input->post('phone'),'id <>'=>$this->session->user['id']);
                $this->db->where($where);

                if($this->db->count_all_results("wxt_user") > 0){
                    $this->output_json('error', '此手机号已被其他用户绑定');
                }

                $user['phone'] = $this->input->post('phone');
                $user['email'] = $this->input->post('email');
                $user['weixin_id'] = $this->input->post('weixin_id');

                $user_info['birthday'] = strtotime($this->input->post('birthday'));
                $user_info['sfz'] = $this->input->post('sfz');
                $user_info['sex'] = $this->input->post('sex');
                $user_info['qq'] = $this->input->post('qq');
                $user_info['motto'] = $this->input->post('motto');

                $this->db->where("id",$this->session->user['id']);
                $this->db->update("wxt_user",$user);

                $this->db->where("uid",$this->session->user['id']);
                $this->db->update("wxt_user_info",$user_info);
                $this->output_json("ok","成功",$this->input->post());
            }
        }

    }

    /**
     * 修改头像
     */
    public function face()
    {
        if ($this->input->method() == 'post'){

            $uid = $this->session->user['id'];

            $this->load->library('upload');
            $this->load->helper('qcos');
            $school = $this->session->school;

            $path = '/file/s'.$school['id'].'/'.date('Ym').'/';
            $bucket = 'wxt';
            $config['upload_path'] = '.'.$path;
            $config['allowed_types'] = 'gif|jpg|png|jpeg|zip|rar|doc|xls';
            $config['encrypt_name'] = true;
            $config['file_ext_tolower'] = true;
            $config['max_size'] = 1024*4;

            if(!file_exists($config['upload_path'])){
                mkdir($config['upload_path'],0700,true);
            }

            $this->upload->initialize($config);


            if($this->upload->do_upload('newface')){

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

                    $this->db->where("uid = '{$uid}'");
                    $this->db->update("wxt_user_info",array('face'=>$file2['file_path']));

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
     * 修改密码
     */
    public function password()
    {

        if($this->input->method() == 'post'){


            $this->form_validation->set_rules('password', '原密码', 'trim|required');
            $this->form_validation->set_rules('password2', '新密码', 'trim|required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {

                $this->output_json('error', validation_errors(' ', ' '));
            }else{
                if(md5($this->input->post('password')) <> $this->session->user['password']){
                    $this->output_json("error","原密码不正确");
                }

                $user['password'] = md5($this->input->post('password2'));

                $this->db->where("id",$this->session->user['id']);
                $this->db->update("wxt_user",$user);
                $this->output_json("ok","修改成功");
            }


        }

    }

    /**
     * 微信绑定二维码
     */
    public function bind_qrcode()
    {
        $url = "http://wx.eduwxt.com/index.php?g=Wap&m=Erm&a=binding&token={$this->session->school['pigcms_token']}&uid={$this->session->user['id']}";
        $this->load->helper('phpqrcode');
        QRcode::png($url);
    }

    /**
     * 解除微信绑定
     */
    public function unbind()
    {
        $this->db->where("id",$this->session->user['id']);
        $this->db->update("wxt_user",array('wecha_id'=>''));
        $this->output_json("ok","您的账号已与微校通系统解除绑定！");

    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        $this->output_json("ok","成功退出！");
    }

}