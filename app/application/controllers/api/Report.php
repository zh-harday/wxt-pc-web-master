<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/4/20
 * Time: 下午3:41
 */
class Report extends MY_Controller
{
    /**
     * 实时招生数据
     */
    public function recruit_hot()
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
                );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }

        //php获取今日开始时间戳和结束时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        //php获取昨日起始时间戳和结束时间戳
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;

        //php获取本周起始时间戳和结束时间戳
        $beginWeek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
        $endWeek=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));

        //php获取本月起始时间戳和结束时间戳
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));


        //本月数据
        //意向学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginThismonth,'reg_time <'=>$endThismonth));
        $data['month']['intetion'] = $this->db->count_all_results('wxt_intention');
        //新增学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginThismonth,'reg_time <'=>$endThismonth));
        $data['month']['student'] = $this->db->count_all_results('wxt_student');
        //收入统计
        $this->db->select("sum(wxt_finance_recordss.actual) as total");
        if(!empty($this->input->get('campus'))){
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.campus_id = {$this->input->get('campus')} and wxt_finance_orders.payee_time > {$beginThismonth} and wxt_finance_orders.payee_time < {$endThismonth}  and wxt_finance_recordss.type = '1'");
        }else{
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.payee_time > {$beginThismonth} and wxt_finance_orders.payee_time < {$endThismonth}  and wxt_finance_recordss.type = '1'");
        }
        $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
        $data['month']['orders'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
        $data['month']['orders'] = empty($data['month']['orders'])?0:$data['month']['orders'];

        //本周数据
        //意向学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginWeek,'reg_time <'=>$endWeek));
        $data['week']['intetion'] = $this->db->count_all_results('wxt_intention');
        //新增学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginWeek,'reg_time <'=>$endWeek));
        $data['week']['student'] = $this->db->count_all_results('wxt_student');
        //收入统计
        $this->db->select("sum(wxt_finance_recordss.actual) as total");
        if(!empty($this->input->get('campus'))){
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.campus_id = {$this->input->get('campus')} and wxt_finance_orders.payee_time > {$beginWeek} and wxt_finance_orders.payee_time < {$endWeek}  and wxt_finance_recordss.type = '1'");
        }else{
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.payee_time > {$beginWeek} and wxt_finance_orders.payee_time < {$endWeek}  and wxt_finance_recordss.type = '1'");
        }
        $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
        $data['week']['orders'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
        $data['week']['orders'] = empty($data['week']['orders'])?0:$data['week']['orders'];

        //昨日数据
        //意向学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginYesterday,'reg_time <'=>$endYesterday));
        $data['yesterday']['intetion'] = $this->db->count_all_results('wxt_intention');
        //新增学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginYesterday,'reg_time <'=>$endYesterday));
        $data['yesterday']['student'] = $this->db->count_all_results('wxt_student');
        //收入统计
        $this->db->select("sum(wxt_finance_recordss.actual) as total");
        if(!empty($this->input->get('campus'))){
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.campus_id = {$this->input->get('campus')} and wxt_finance_orders.payee_time > {$beginYesterday} and wxt_finance_orders.payee_time < {$endYesterday}  and wxt_finance_recordss.type = '1'");
        }else{
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.payee_time > {$beginYesterday} and wxt_finance_orders.payee_time < {$endYesterday}  and wxt_finance_recordss.type = '1'");
        }
        $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
        $data['yesterday']['orders'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
        $data['yesterday']['orders'] = empty($data['yesterday']['orders'])?0:$data['yesterday']['orders'];


        //今日数据
        //意向学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginToday,'reg_time <'=>$endToday));
        $data['today']['intetion'] = $this->db->count_all_results('wxt_intention');
        //新增学员
        $this->db->where($where);
        $this->db->where(array('reg_time >'=>$beginToday,'reg_time <'=>$endToday));
        $data['today']['student'] = $this->db->count_all_results('wxt_student');
        //收入统计
        $this->db->select("sum(wxt_finance_recordss.actual) as total");
        if(!empty($this->input->get('campus'))){
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.campus_id = {$this->input->get('campus')} and wxt_finance_orders.payee_time > {$beginToday} and wxt_finance_orders.payee_time < {$endToday}  and wxt_finance_recordss.type = '1'");
        }else{
            $this->db->where("wxt_finance_orders.school_id = {$this->session->school['id']} and wxt_finance_orders.payee_time > {$beginToday} and wxt_finance_orders.payee_time < {$endToday}  and wxt_finance_recordss.type = '1'");
        }
        $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
        $data['today']['orders'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
        $data['today']['orders'] = empty($data['today']['orders'])?0:$data['today']['orders'];


        $this->output_json('ok', '实时招生数据', $data);
    }


    /**
     * 意向学员新增明细
     * @param int $week 周
     */
    public function intetion_week($week = 0)
    {
        if(!is_numeric($week)){
            $this->output_json('ok', '参数错误');
        }

        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }
        $beginWeek=mktime(0,0,0,date('m'),date('d')-date('w')+1+($week*7),date('Y'));
        $data['week'] = date("Y年第W周",$beginWeek);
        $w = array('一','二','三','四','五','六','天');

        for($i=1;$i<=7;$i++){
            //意向学员
            $this->db->where($where);
            $this->db->where(array('reg_time >'=>$beginWeek,'reg_time <'=>$beginWeek+(60*60*24)-1,'is_form'=>'0'));
            $ww = $w[$i-1];
            $data['intetion']['星期'.$ww] = $this->db->count_all_results('wxt_intention');
            //$data['intetion'][] = $this->db->count_all_results('wxt_intention');

            //意向学员来自微信
            $this->db->where($where);
            $this->db->where(array('reg_time >'=>$beginWeek,'reg_time <'=>$beginWeek+(60*60*24)-1,'is_form'=>'1'));
            $data['intetion_wx']['星期'.$ww] = $this->db->count_all_results('wxt_intention');
            //$data['intetion_wx'][] = $this->db->count_all_results('wxt_intention');
            $beginWeek = $beginWeek+(60*60*24);

        }

        $this->output_json('ok', '意向学员新增明细', $data);
        

    }

    /**
     * 意向学员总数增长趋势
     * @param int $year 年
     */
    public function intetion_year($year = 2017)
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }

        //$data['year'] = $year.'年';
        for ($i = 1;$i <= 12;$i++ ){
            $start = strtotime($year."-".$i."-01");
            $end = mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
            //意向学员
            $this->db->where($where);
            $this->db->where(array('reg_time >'=>$start,'reg_time <'=>$end));
            $data[$i.'月'] = $this->db->count_all_results('wxt_intention');
        }

        $this->output_json('ok', '意向学员总数增长趋势', $data);
    }

    /**
     * 年度新增报班汇总
     * @param int $year
     */
    public function baoban_year($year = 2017)
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'wxt_class.school_id'=>$this->session->school['id'],
                'wxt_class.campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'wxt_class.school_id'=>$this->session->school['id']
            );
        }

        //$data['year'] = $year.'年';
        for ($i = 1;$i <= 12;$i++ ){
            $start = strtotime($year."-".$i."-01");
            $end = mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
            //意向学员
            $this->db->where($where);
            $this->db->where(array('wxt_class_student.reg_time >'=>$start,'wxt_class_student.reg_time <'=>$end));
            $this->db->join('wxt_class',"wxt_class.id = wxt_class_student.class_id");
            $data[$i.'月'] = $this->db->count_all_results('wxt_class_student');
        }

        $this->output_json('ok', '年度新增报班汇总', $data);
        
    }

    /**
     * 年度学员增长趋势
     * @param int $year
     */
    public function student_year($year = 2017)
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }

        //$data['year'] = $year.'年';
        for ($i = 1;$i <= 12;$i++ ){
            $start = strtotime($year."-".$i."-01");
            $end = mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));
            //意向学员
            $this->db->where($where);
            $this->db->where(array('reg_time >'=>$start,'reg_time <'=>$end));
            $data[$i.'月'] = $this->db->count_all_results('wxt_student');
        }

        $this->output_json('ok', '年度学员增长趋势', $data);

    }

    /**
     * 学员分布总揽
     */
    public function student_sort()
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'wxt_student.school_id'=>$this->session->school['id'],
                'wxt_student.campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'wxt_student.school_id'=>$this->session->school['id']
            );
        }

        //学生统计

        $data['总数'] = $this->db->where($where)->count_all_results("wxt_student");
        $data['男'] = $this->db->where($where)->where("sex = '男'")->count_all_results("wxt_student");
        $data['女'] = $this->db->where($where)->where("sex = '女'")->count_all_results("wxt_student");
        $data['未知'] = $this->db->where($where)->where("sex = '未知'")->count_all_results("wxt_student");

        //未入班级学员
        $this->db->select('count(DISTINCT wxt_student.id) count');
        $this->db->where($where);
        $this->db->join("wxt_class_student","wxt_student.id = wxt_class_student.student_id");
        $data['正常上课学员'] = $this->db->get("wxt_student")->row_array()['count'];
        $data['未入班学员'] = $data['总数']-$data['正常上课学员'];

        //停课学员
        $this->db->select('count(DISTINCT wxt_student.id) count');
        $this->db->where($where);
        $this->db->join("wxt_student","wxt_student.id = wxt_class_student.student_id and wxt_class_student.status = '0'");
        $data['停课学员'] = $this->db->get("wxt_class_student")->row_array()['count'];


        //待续费学员
        $this->db->select("wxt_class_student.class_id,wxt_class_student.student_id,wxt_class_student.buy_quantity");
        $this->db->where($where);
        $this->db->where("wxt_class.fee_method = 'frequency'");
        $this->db->join("wxt_class_student","wxt_class_student on wxt_class.id = wxt_class_student.class_id");
        $this->db->join("wxt_student","wxt_student.id = wxt_class_student.student_id");
        $student = $this->db->get("wxt_class")->result_array();
        $i=0;
        foreach ($student as $item){
            $this->db->where("class_id = {$item['class_id']} and student_id = {$item['student_id']} and is_xk = '1'");
            $kx = $this->db->count_all_results("wxt_class_attence");

            if(($item['buy_quantity'] - $kx) <= 3){
                $i++;
            }
        }

        $data['待续费学员'] = $i;



        $this->output_json('ok', '学员分布总揽', $data);

    }

    /**
     * 班级人数统计
     */
    public function student_class()
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }

        //班级人数统计
        $this->db->select("id,class_name");
        $this->db->where($where);
        $this->db->where("staus = '0'");
        $class = $this->db->get("wxt_class")->result_array();
        $class_count = array();
        foreach ($class as $item){
            $class_count[] = array(
                'name' =>$item['class_name'],
                'value' => $this->db->where("class_id = {$item['id']}")->count_all_results("wxt_class_student")
            );
        }

        $this->output_json('ok', '班级人数统计', $class_count);
    }

    /**
     * 学员课程分布
     */
    public function student_subject()
    {

        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'school_id'=>$this->session->school['id'],
                'campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'school_id'=>$this->session->school['id']
            );
        }


        $this->db->select("id,subject");
        $this->db->where($where);
        $subject = $this->db->get("wxt_subject")->result_array();
        $subject_count = array();
        foreach ($subject as $item){
            $subject_count[] = array(
                'name' =>$item['subject'],
                'class_count' => $this->db->where("subject_id = {$item['id']}")->count_all_results("wxt_class_subject"),
                'student_count'=>$this->db->where("class_id in(select class_id from wxt_class_subject where subject_id = {$item['id']})")->count_all_results("wxt_class_student")
            );

        }

        $this->output_json('ok', '学员课程分布', $subject_count);




    }


    /**
     * 年度财务数据
     * @param int $year
     */
    public function inancial_year($year = 2017)
    {
        if(!empty($this->input->get('campus'))){
            $this->is_my_school($this->input->get('campus'), 'campus');
            $where = array(
                'wxt_finance_orders.school_id'=>$this->session->school['id'],
                'wxt_finance_orders.campus_id'=>$this->input->get('campus')
            );
        }else{
            $where = array(
                'wxt_finance_orders.school_id'=>$this->session->school['id']
            );
        }
//
        //$data['year'] = $year.'年';
        for ($i = 1;$i <= 12;$i++ ){
            $start = strtotime($year."-".$i."-01");
            $end = mktime(23,59,59,date('m',$start),date('t',$start),date('Y',$start));


            $this->db->select("sum(wxt_finance_recordss.actual) as total");
            $this->db->where($where);
            $this->db->where("wxt_finance_orders.payee_time > {$start} and wxt_finance_orders.payee_time < {$end} and wxt_finance_recordss.type = '1' ");
            $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
            $data['收入'][$i.'月'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
            $data['收入'][$i.'月'] = empty($data['收入'][$i.'月'])?0:$data['收入'][$i.'月'];

            $this->db->select("sum(wxt_finance_recordss.actual) as total");
            $this->db->where($where);
            $this->db->where("wxt_finance_orders.payee_time > {$start} and wxt_finance_orders.payee_time < {$end} and wxt_finance_recordss.type = '0' ");
            $this->db->join("wxt_finance_recordss", "wxt_finance_recordss.orders_id = wxt_finance_orders.id");
            $data['支出'][$i.'月'] = $this->db->get("wxt_finance_orders")->row_array()['total'];
            $data['支出'][$i.'月'] = empty($data['支出'][$i.'月'])?0:$data['支出'][$i.'月'];

        }

        $this->output_json('ok', '年度财务数据', $data);
        
    }

}