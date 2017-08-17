<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/30
 * Time: 下午3:06
 */
class Classs_score extends MY_Controller
{

    /**
     * 成绩列表
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
        $data['count'] = $this->db->count_all_results('wxt_class_test');


        $this->db->select("wxt_class_test.*,wxt_user.name as teacher");
        $this->db->where(array('wxt_class_test.class_id' => $class_id));
        $this->db->join("wxt_user", "wxt_user.id = wxt_class_test.teacher_id");
        $this->db->limit($limit,$page);
        $this->db->order_by('id', 'desc');
        $data['score'] = $this->db->get("wxt_class_test")->result_array();

        foreach ($data['score'] as &$item){
            $item['subject'] = json_decode($item['subject']);
        }


        $this->output_json("ok","班级成绩",$data);
    }


    /**
     * 成绩详情
     * @param int $class_id
     * @param int $test_id
     */
    public function view($class_id = 0,$test_id = 0)
    {
        $this->is_my_school($class_id,'class');

        $this->db->where(array('class_id'=>$class_id,'id'=>$test_id));
        $score = $this->db->get("wxt_class_test")->row_array();

        $this->db->select("wxt_student.number,wxt_student.name,wxt_class_test_score.score");
        $this->db->where("wxt_class_test_score.ts_id = {$test_id}");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_test_score.sid");
        $score['student'] = $this->db->get("wxt_class_test_score")->result_array();
        $score['subject'] = json_decode($score['subject']);

        $this->output_json("ok","班级成绩",$score);

    }

    /**
     * 删除成绩
     * @param int $class_id
     * @param int $test_id
     */
    public function del($class_id = 0,$test_id = 0)
    {
        $this->is_my_school($class_id,'class');

        //删除
        $this->db->where(array('class_id'=>$class_id,'id'=>$test_id));
        $this->db->delete("wxt_class_test");

        $this->db->where("ts_id = {$test_id}");
        $this->db->delete("wxt_class_test_score");

        $this->output_json('ok', '成绩信息已被删除！');
    }

    public function score_out_xls($class_id=0)
    {

        $this->is_my_school($class_id,'class');

        if(empty($this->input->post('title'))){
            $this->output_json('error', '标题不能为空！');
        }

        if(empty($this->input->post('subject'))){
            $this->output_json('error', '考试科目不能为空！');
        }

        $subject = $this->input->post('subject');
        $subject = str_getcsv($subject);


        $class = $this->db->where(array('id'=>$class_id))->get('wxt_class')->row_array();

        $this->db->where(array('wxt_class_student.class_id'=>$class_id,'wxt_class_student.status'=>'1'));
        $this->db->join('wxt_student','wxt_student.id = wxt_class_student.student_id');
        $student = $this->db->get('wxt_class_student')->result_array();


        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $objPHPExcel = new PHPExcel();

        // 设置属性
        $objPHPExcel
            ->getProperties()  //获得文件属性对象，给下文提供设置资源
            ->setCreator( "同城学微校通")                 //设置文件的创建者
            ->setLastModifiedBy( "同城学微校通")          //设置最后修改者
            ->setTitle( "考试成绩表" )    //设置标题
            ->setSubject( "考试成绩表" )  //设置主题
            ->setDescription( "考试成绩表") //设置备注
            ->setKeywords( "考试成绩表")        //设置标记
            ->setCategory( "考试成绩表");                //设置类别
        // 添加数据
        $objPHPExcel->getActiveSheet()->mergeCells( 'A1:K1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'A1', $this->input->post('title') );//公式
        //$objPHPExcel->getActiveSheet()->protectCells( 'A1','eduwxt');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'a2', 'id' )->setCellValue( 'B2', '学号' )->setCellValue( 'c2', '姓名' );//公式
        //$objPHPExcel->getActiveSheet()->protectCells( 'A2:c3','eduwxt');
        // 学生
        $i = 3;
        foreach ($student as $item){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'a'.$i, $item['id'] )->setCellValue( 'b'.$i, $item['number'] )->setCellValue( 'c'.$i, $item['name'] );
            //$objPHPExcel->getActiveSheet()->protectCells( 'A'.$i.':c'.$i, 'eduwxt' ); //
            $i++;
        }

        // 科目
        $s='d';
        foreach ($subject as $item){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $s.'2', $item );//公式
            //$objPHPExcel->getActiveSheet()->protectCells( $s.'2','eduwxt');
            $s++;
        }

        //得到当前活动的表,注意下文教程中会经常用到$objActSheet
        $objActSheet = $objPHPExcel->getActiveSheet();
        // 活动的表设置名称
        $objActSheet->setTitle($this->input->post('title'));


        // 生成2003excel格式的xls文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$class['class_name'].'-'.$this->input->post('title').'.xls"');
        header('Cache-Control: max-age=0');

        //输出文件
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;

    }

    /**
     * 导出表格
     * @param int $class_id
     */
    public function out_xls($class_id=0)
    {

        $this->is_my_school($class_id,'class');

        if(empty($this->input->post('title'))){
            $this->output_json('error', '标题不能为空！');
        }

        if(empty($this->input->post('subject'))){
            $this->output_json('error', '考试科目不能为空！');
        }

        $subject = $this->input->post('subject');
        $subject = str_getcsv($subject);


        $class = $this->db->where(array('id'=>$class_id))->get('wxt_class')->row_array();

        $this->db->where(array('wxt_class_student.class_id'=>$class_id,'wxt_class_student.status'=>'1'));
        $this->db->join('wxt_student','wxt_student.id = wxt_class_student.student_id');
        $student = $this->db->get('wxt_class_student')->result_array();


        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

        $objPHPExcel = new PHPExcel();

        // 设置属性
        $objPHPExcel
            ->getProperties()  //获得文件属性对象，给下文提供设置资源
            ->setCreator( "同城学微校通")                 //设置文件的创建者
            ->setLastModifiedBy( "同城学微校通")          //设置最后修改者
            ->setTitle( "考试成绩表" )    //设置标题
            ->setSubject( "考试成绩表" )  //设置主题
            ->setDescription( "考试成绩表") //设置备注
            ->setKeywords( "考试成绩表")        //设置标记
            ->setCategory( "考试成绩表");                //设置类别
        // 添加数据
        $objPHPExcel->getActiveSheet()->mergeCells( 'A1:K1');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'A1', $this->input->post('title') );//公式
        //$objPHPExcel->getActiveSheet()->protectCells( 'A1','eduwxt');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'a2', 'id' )->setCellValue( 'B2', '学号' )->setCellValue( 'c2', '姓名' );//公式
        //$objPHPExcel->getActiveSheet()->protectCells( 'A2:c3','eduwxt');
        // 学生
        $i = 3;
        foreach ($student as $item){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( 'a'.$i, $item['id'] )->setCellValue( 'b'.$i, $item['number'] )->setCellValue( 'c'.$i, $item['name'] );
            //$objPHPExcel->getActiveSheet()->protectCells( 'A'.$i.':c'.$i, 'eduwxt' ); //
            $i++;
        }

        // 科目
        $s='d';
        foreach ($subject as $item){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue( $s.'2', $item );//公式
            //$objPHPExcel->getActiveSheet()->protectCells( $s.'2','eduwxt');
            $s++;
        }

        //得到当前活动的表,注意下文教程中会经常用到$objActSheet
        $objActSheet = $objPHPExcel->getActiveSheet();
        // 活动的表设置名称
        $objActSheet->setTitle($this->input->post('title'));


        // 生成2003excel格式的xls文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$class['class_name'].'-'.$this->input->post('title').'.xls"');
        header('Cache-Control: max-age=0');

        //输出文件
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;

    }

    /**
     * 导入成绩
     * @param int $class_id
     */
    public function add($class_id = 0)
    {
        $this->is_my_school($class_id,'class');

        $config = array();
        $config['upload_path']      = './upload/temp/';
        $config['allowed_types']    = 'xls|xlsx';
        $config['encrypt_name']     = TRUE;

        $this->load->library('upload', $config);


        if ( ! $this->upload->do_upload('file'))
        {
            $this->output_json('error',$this->upload->display_errors(' ',' '));
        }else {
            $excel = $this->upload->data();

            $this->load->library('PHPExcel');
            $this->load->library('PHPExcel/IOFactory');
            $IOFactory = IOFactory::load($excel['full_path']);

            $dataArray = $IOFactory->getActiveSheet()->toArray();

            //事务开始
            $this->db->trans_start();


            if(empty($dataArray['0']['0'])){
                $this->output_json('error', '表格文件格式错误！');
            }else{
                $subject = array();

                foreach ($dataArray['1'] as $k=>$v) {
                    if($k > 2){
                        if(!empty($v)){
                            $subject[] = $v;
                        }
                    }

                }
                if (empty($subject)){
                    $this->output_json('error', '表格文件格式错误！');
                }
                $subject_len = count($subject);

                $subject = json_encode($subject);

                $this->db->insert('wxt_class_test',array('class_id'=>$class_id,'title'=>$dataArray['0']['0'],'time'=>time(),'teacher_id'=>$this->session->user['id'],'subject'=>$subject));
                $test_id = $this->db->insert_id();
            }


            foreach ($dataArray as $k=>$v) {
                if($k > 1){

                    if(!empty($v['0'])){
                        $score =  array();
                        foreach ($v as $kk=>$vv) {
                            if($kk > 2 and $kk <= ($subject_len + 2)){
                                $score[]= $vv;
                            }
                        }
                        $score = json_encode($score);

                        $this->db->insert('wxt_class_test_score',array('ts_id'=>$test_id,'sid'=>$v['0'],'score'=>$score));
                    }

                }

            }

            $this->db->trans_complete();

            unlink($excel['full_path']);//删除临时文件

            if ($this->db->trans_status() === FALSE)
            {
                $this->output_json('error', '导入失败！');
            }else{
                $this->output_json('ok', '导入成功',array('test_id'=>$test_id));
            }


        }

    }

    /**
     * 成绩通知
     * @param int $tid
     */
    public function notice($tid = 0)
    {
        //检查参数
        if (!is_numeric($tid)) {
            $this->output_json('error', '您提交的数据有误！');
        }
        $this->db->where("id = {$tid}");
        $score = $this->db->get("wxt_class_test")->row_array();

        $this->is_my_school($score['class_id'],'class');

        $this->db->select("wxt_student.number,wxt_student.name,wxt_class_test_score.score");
        $this->db->where("wxt_class_test_score.ts_id = {$tid}");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_test_score.sid");
        $score['student'] = $this->db->get("wxt_class_test_score")->result_array();
        $score['subject'] = json_decode($score['subject']);


        //生成动态信息
        $feed = array(
            "class_id" => $score['class_id'],
            "time" => time(),
            "type" => "12",
            "source" => $this->session->user['name'] . '老师',
            "source_logo" => '//wx.eduwxt.com/api/face/teacher/' . $this->session->user['id'],
            "title" => '班级通知提醒',
            "msg" => $score['title'].'成绩',
            "url" => "#/Apps/QueryScore/"
        );
        $this->db->insert("wxt_feed", $feed);


        //获取班级信息
        $this->db->where('id',$score['class_id']);
        $classs = $this->db->get('wxt_class')->row_array();

        //获取模版id
        $this->db->where(array("school_id" => $this->session->school['id'], "short" => "OPENTM408404050"));
        $temp = $this->db->get('wxt_weixin_temp')->row_array();
        //api 地址
        $url = 'http://wx.eduwxt.com/index.php?g=Home&m=Index&a=sms&token=' . $this->session->school['pigcms_token'];

        //获取班级学生
        $this->db->select("wxt_student_weixin.wecha_id,wxt_student.name");
        $this->db->where("wxt_class_student.class_id = {$score['class_id']} and wxt_class_student.status = '1'");
        $this->db->join("wxt_student_weixin", "wxt_student_weixin.uid = wxt_class_student.student_id");
        $this->db->join("wxt_student", "wxt_student.id = wxt_class_student.student_id");
        $student = $this->db->get("wxt_class_student")->result_array();

        //发送模版消息
        foreach ($student as $item) {
            $msg['wecha_id'] = $item['wecha_id'];
            $msg['template_id'] = $temp['temp_id'];//'s8t-kNd_BOQJowzL3mPXf9tLmj6PhECBcNySOK_huzA';//班级通知模版
            $msg['first'] = "{$item['name']}同学，{$score['title']}成绩已发出，请注意查看。";//标题
            $msg['keyword1'] = $score['title'].'成绩';//内容
            $msg['keyword2'] = $item['name'];
            $msg['remark'] = '具体内容请点击查看详情。';//内容
            $msg['url'] = "http://wx.eduwxt.com/student/#/Apps/QueryScore/?token=" . $this->session->school['pigcms_token'] . "&wecha_id=" . $item['wecha_id'];

            $this->load->model('queue');
            $this->queue->post($this->session->school['pigcms_token'],$msg);
        }

        //事务结束
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->output_json('error', '发送失败！');

        } else {
            $this->db->where("id",$tid);
            $this->db->update('wxt_class_test',array("status"=>'1'));
            $this->output_json('ok', '成绩通知发送成功！');
        }

    }

}