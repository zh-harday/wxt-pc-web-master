<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//用户
$route['api/user']['POST'] = 'api/user/edit';

//消息
$route['api/message/(:num)']['GET'] = 'api/message/view/$1';
$route['api/message']['POST'] = 'api/message/add';

//校区
$route['api/campus']['POST'] = 'api/campus/add';
$route['api/campus/(:num)']['POST'] = 'api/campus/edit/$1';
$route['api/campus/(:num)']['GET'] = 'api/campus/info/$1';

//教室
$route['api/room']['POST'] = 'api/room/add';
$route['api/room/(:num)']['POST'] = 'api/room/edit/$1';
$route['api/room/(:num)']['GET'] = 'api/room/info/$1';
$route['api/room/(:num)/del']['POST'] = 'api/room/del/$1';

//课程类别
$route['api/subject/type']['GET'] = 'api/subject/type';
$route['api/subject/type/(:num)']['GET'] = 'api/subject/type_info/$1';
$route['api/subject/type']['POST'] = 'api/subject/type_add';
$route['api/subject/type/(:num)']['POST'] = 'api/subject/type_edit/$1';
$route['api/subject/type/(:num)/del']['POST'] = 'api/subject/type_del/$1';

//课程详情
$route['api/subject/(:num)']['GET'] = 'api/subject/view/$1';
$route['api/subject']['POST'] = 'api/subject/add';
$route['api/subject/(:num)']['POST'] = 'api/subject/edit/$1';
$route['api/subject/(:num)/del']['POST'] = 'api/subject/del/$1';
$route['api/subject/(:num)/class']['get'] = 'api/subject/classs/$1';
//财务账户
$route['api/finance/account']['GET'] = 'api/finance/account';
$route['api/finance/account/(:num)']['GET'] = 'api/finance/account_info/$1';
$route['api/finance/account']['POST'] = 'api/finance/account_add';
$route['api/finance/account/(:num)']['POST'] = 'api/finance/account_edit/$1';
$route['api/finance/account/(:num)/del']['POST'] = 'api/finance/account_del/$1';

//杂费项目
$route['api/finance/fees_type/(:num)/del']['POST'] = 'api/finance/fees_type_del/$1';
$route['api/finance/fees/(:num)/del']['POST'] = 'api/finance/fees_del/$1';

//员工管理
$route['api/staff/(:num)']['GET'] = 'api/staff/view/$1';
$route['api/staff/(:num)']['POST'] = 'api/staff/edit/$1';
$route['api/staff']['POST'] = 'api/staff/add';
$route['api/staff/(:num)/password']['POST'] = 'api/staff/password/$1';
$route['api/staff/(:num)/staus'] = 'api/staff/staus/$1';

$route['api/staff/(:num)/bind_qrcode']['GET'] = 'api/staff/bind_qrcode/$1';
$route['api/staff/(:num)/unbind']['get'] = 'api/staff/unbind/$1';


//学员管理
$route['api/student/(:num)']['GET'] = 'api/student/view/$1';//学员详情
$route['api/student/(:num)/contact']['GET'] = 'api/student/contact/$1';//获取联系人
$route['api/student/(:num)/contact']['POST'] = 'api/student/contact_add/$1';//添加联系人
$route['api/student/(:num)/del_contact/(:num)']['POST'] = 'api/student/contact_del/$1/$2';//删除指定联系人
$route['api/student/(:num)/class']['GET'] = 'api/student/classs/$1';//学员报名班级
$route['api/student/(:num)/del']['POST'] = 'api/student/del/$1'; //删除学员
$route['api/student']['POST'] = 'api/student/add';//添加学员
$route['api/student/(:num)']['POST'] = 'api/student/edit/$1'; //编辑学员
$route['api/student/(:num)/attence']['GET'] = 'api/student/attence/$1'; //考勤记录
$route['api/student/(:num)/recordss']['GET'] = 'api/student/recordss/$1'; //缴费记录
$route['api/student/(:num)/evaluation']['GET'] = 'api/student/evaluation/$1'; //课堂点评
$route['api/student/(:num)/stats_radar']['GET'] = 'api/student/stats_radar/$1'; //综合统计
$route['api/student/(:num)/work']['GET'] = 'api/student/work/$1'; //作业点评

$route['api/student/(:num)/class_log/(:num)']['GET'] = 'api/student/class_log/$1/$2';//班级日志

$route['api/student/(:num)/payment']['POST'] = 'api/student/payment/$1';//缴费
$route['api/student/(:num)/renewal/(:num)']['POST'] = 'api/student/renewal/$1/$2';//续费
$route['api/student/(:num)/refund/(:num)']['POST'] = 'api/student/refund/$1/$2';//退费

//班级管理
$route['api/classs/(:num)']['GET'] = 'api/classs/view/$1';//班级详情
$route['api/classs']['POST'] = 'api/classs/add';//添加班级
$route['api/classs/(:num)']['POST'] = 'api/classs/edit/$1';//修改班级
$route['api/classs/(:num)/del']['POST'] = 'api/classs/del/$1';//删除班级

$route['api/classs/(:num)/student']['GET'] = 'api/classs_student/index/$1';//班级学员
$route['api/classs/(:num)/student/count']['GET'] = 'api/classs_student/count/$1';//班级学员
$route['api/classs/(:num)/student/(:num)/del']['POST'] = 'api/classs_student/del/$1/$2';//删除学员
$route['api/classs/(:num)/student/(:num)/status']['POST'] = 'api/classs_student/status/$1/$2';//变更学员状态
$route['api/classs/(:num)/student/(:num)/change']['POST'] = 'api/classs_student/change/$1/$2';//转班
$route['api/classs/(:num)/student/(:num)/edit']['POST'] = 'api/classs_student/edit/$1/$2';//调整课次
$route['api/classs/(:num)/student/(:num)']['get'] = 'api/classs_student/view/$1/$2';//学员信息

$route['api/classs/(:num)/score']['GET'] = 'api/classs_score/index/$1';//班级成绩
$route['api/classs/(:num)/score/(:num)']['GET'] = 'api/classs_score/view/$1/$2';//班级成绩详情
$route['api/classs/(:num)/score/out_xls']['POST'] = 'api/classs_score/out_xls/$1';//班级成绩 导出表格
$route['api/classs/(:num)/score']['POST'] = 'api/classs_score/add/$1';//班级成绩 导入
$route['api/classs/(:num)/score/notice/(:num)']['POST'] = 'api/classs_score/notice/$2';//班级成绩 通知
$route['api/classs/(:num)/score/(:num)/del']['POST'] = 'api/classs_score/del/$1/$2';//班级成绩 删除

$route['api/classs/(:num)/notice']['GET'] = 'api/classs_notice/index/$1';//班级通知
$route['api/classs/(:num)/notice/(:num)']['GET'] = 'api/classs_notice/view/$1/$2';//班级通知详情
$route['api/classs/(:num)/notice/(:num)/receipt']['GET'] = 'api/classs_notice/receipt/$1/$2';//班级通知回执
$route['api/classs/(:num)/notice']['POST'] = 'api/classs_notice/add/$1';//班级通知 发布
$route['api/classs/(:num)/notice/(:num)/del']['POST'] = 'api/classs_notice/del/$1/$2';//班级通知 删除


$route['api/classs/(:num)/work']['GET'] = 'api/classs_work/index/$1';//班级作业
$route['api/classs/(:num)/work/(:num)']['GET'] = 'api/classs_work/view/$1/$2';//班级作业详情
$route['api/classs/(:num)/work/(:num)/dp']['GET'] = 'api/classs_work/dp/$1/$2';//班级作业点评情况
$route['api/classs/(:num)/work']['POST'] = 'api/classs_work/add/$1';//班级作业 发布
$route['api/classs/(:num)/work/(:num)/del']['POST'] = 'api/classs_work/del/$1/$2';//班级作业 删除
$route['api/classs/(:num)/work/(:num)/comments']['POST'] = 'api/classs_work/comments/$1/$2';//班级作业 点评

$route['api/classs/(:num)/prep']['GET'] = 'api/classs_prep/index/$1';//班级通知
$route['api/classs/(:num)/prep/(:num)']['GET'] = 'api/classs_prep/view/$1/$2';//班级通知详情
$route['api/classs/(:num)/prep']['POST'] = 'api/classs_prep/add/$1';//班级通知 发布
$route['api/classs/(:num)/prep/(:num)/del']['POST'] = 'api/classs_prep/del/$1/$2';//班级通知 删除

$route['api/classs/(:num)/curriculum']['GET'] = 'api/classs_curriculum/index/$1';//班级排课
$route['api/classs/(:num)/curriculum/month/(:any)']['GET'] = 'api/classs_curriculum/month/$1/$2';//指定月份 那天有课
$route['api/classs/(:num)/curriculum/count']['GET'] = 'api/classs_curriculum/count/$1';//班级排课统计
$route['api/classs/(:num)/curriculum/(:num)']['GET'] = 'api/classs_curriculum/view/$1/$2';//班级排课详情
$route['api/classs/(:num)/curriculum/is_conflict']['POST'] = 'api/classs_curriculum/is_conflict/$1';//检测冲突
$route['api/classs/(:num)/curriculum']['POST'] = 'api/classs_curriculum/add/$1';//添加排课
$route['api/classs/(:num)/curriculum/(:num)']['POST'] = 'api/classs_curriculum/edit/$1/$2';//调课
$route['api/classs/(:num)/curriculum/(:num)/del']['POST'] = 'api/classs_curriculum/del/$1/$2';//班级排课删除
$route['api/classs/(:num)/curriculum/(:num)/status']['POST'] = 'api/classs_curriculum/status/$1/$2';//班级排课 修改状态
$route['api/classs/(:num)/curriculum/(:num)/attence']['GET'] = 'api/classs_curriculum/attence/$1/$2';//班级排课 考勤状态
$route['api/classs/(:num)/curriculum/(:num)/attence']['POST'] = 'api/classs_curriculum/student_attence/$1/$2';//班级排课 考勤

$route['api/classs/(:num)/curriculum/(:num)/evaluation']['GET'] = 'api/classs_curriculum/evaluation/$1/$2';//班级排课 课堂点评状态
$route['api/classs/(:num)/curriculum/(:num)/evaluation']['POST'] = 'api/classs_curriculum/evaluation_add/$1/$2';//班级排课 点评学生
$route['api/classs/(:num)/curriculum/(:num)/evaluation_student']['GET'] = 'api/classs_curriculum/evaluation_student/$1/$2';//班级排课 学生回评

$route['api/classs/(:num)/class_log']['GET'] = 'api/classs/class_log/$1';//班级日志

//托管班级
$route['api/classs/(:num)/tuoguan/today']['GET'] = 'api/classs_tuoguan/today/$1';//今日托管
$route['api/classs/(:num)/tuoguan/log']['GET'] = 'api/classs_tuoguan/log/$1';//托管日志
$route['api/classs/(:num)/(:num)/tuoguan/attence']['POST'] = 'api/classs_tuoguan/attence/$1/$2';//托管 考勤
$route['api/classs/(:num)/(:num)/tuoguan/notice']['POST'] = 'api/classs_tuoguan/notice/$1/$2';//托管 通知

$route['api/classs/(:num)/tuoguan/month/(:any)']['GET'] = 'api/classs_tuoguan/month/$1/$2';//托管 日历

$route['api/classs/(:num)/(:num)/tuoguan/extime'] = 'api/classs_tuoguan/extime/$1/$2';//托管 调课
$route['api/classs/(:num)/(:num)/tuoguan/change']['POST'] = 'api/classs_tuoguan/change/$1/$2';//托管 转班
$route['api/classs/(:num)/(:num)/tuoguan/renewal']['POST'] = 'api/classs_tuoguan/renewal/$2/$1';//托管 需费



//招生追踪
$route['api/intention']['POST'] = 'api/intention/add';//添加学员
$route['api/intention/(:num)']['get'] = 'api/intention/view/$1';//学员详情
$route['api/intention/(:num)']['post'] = 'api/intention/edit/$1';//学员修改
$route['api/intention/(:num)/del']['post'] = 'api/intention/del/$1';//删除
$route['api/intention/(:num)/gw']['post'] = 'api/intention/gw/$1';//分配顾问

$route['api/intention/(:num)/track']['get'] = 'api/intention/track/$1';//跟踪记录
$route['api/intention/(:num)/track']['post'] = 'api/intention/track/$1/add';//添加跟踪记录
$route['api/intention/(:num)/track/(:num)/del']['post'] = 'api/intention/track/$1/del/$2';//删除跟踪记录

//预报科目
$route['api/intention/subject']['post'] = 'api/intention/subject/add';//添加
$route['api/intention/subject/(:num)']['post'] = 'api/intention/subject/edit/$1';//修改
$route['api/intention/subject/(:num)/del']['post'] = 'api/intention/subject/del/$1';//删除

//跟进状态
$route['api/intention/status']['post'] = 'api/intention/status/add';//添加
$route['api/intention/status/(:num)']['post'] = 'api/intention/status/edit/$1';//修改
$route['api/intention/status/(:num)/del']['post'] = 'api/intention/status/del/$1';//删除

//招生渠道
$route['api/intention/source']['post'] = 'api/intention/source/add';//添加
$route['api/intention/source/(:num)']['post'] = 'api/intention/source/edit/$1';//修改
$route['api/intention/source/(:num)/del']['post'] = 'api/intention/source/del/$1';//删除

//意向级别
$route['api/intention/level']['post'] = 'api/intention/level/add';//添加
$route['api/intention/level/(:num)']['post'] = 'api/intention/level/edit/$1';//修改
$route['api/intention/level/(:num)/del']['post'] = 'api/intention/level/del/$1';//删除

//标记分类
$route['api/intention/track_type']['post'] = 'api/intention/track_type/add';//添加
$route['api/intention/track_type/(:num)']['post'] = 'api/intention/track_type/edit/$1';//修改
$route['api/intention/track_type/(:num)/del']['post'] = 'api/intention/track_type/del/$1';//删除

//试听课
$route['api/lecture']['post'] = 'api/lecture/add';//添加
$route['api/lecture/(:num)']['post'] = 'api/lecture/edit/$1';//修改
$route['api/lecture/(:num)/del']['post'] = 'api/lecture/del/$1';//删除

$route['api/lecture/(:num)/booking']['get'] = 'api/lecture/booking/$1';//预约学员

$route['api/lecture/(:num)/booking']['post'] = 'api/lecture/booking/$1/add';//安排预约
$route['api/lecture/(:num)/booking/(:num)/del']['post'] = 'api/lecture/booking/$1/del/$2';//删除预约
$route['api/lecture/(:num)/booking/(:num)/check_in']['post'] = 'api/lecture/booking/$1/check_in/$2';//预约课状态

//微信表单
$route['api/free_form']['post'] = 'api/free_form/add';//添加
$route['api/free_form/(:num)']['post'] = 'api/free_form/edit/$1';//修改
$route['api/free_form/(:num)']['get'] = 'api/free_form/view/$1';//详情
$route['api/free_form/(:num)/del']['post'] = 'api/free_form/del/$1';//删除
$route['api/free_form/(:num)/status']['post'] = 'api/free_form/status/$1';//修改状态
$route['api/free_form/(:num)/student']['get'] = 'api/free_form/student/$1';//表单报名学员

//工作台
//选课报名
$route['api/workbench/reservation/(:num)']['post'] = 'api/workbench/reservation/$1/edit';//修改状态

//校区通知
$route['api/campus_notice/(:num)']['get'] = 'api/campus_notice/view/$1';
$route['api/campus_notice']['post'] = 'api/campus_notice/add';
$route['api/campus_notice/(:num)']['post'] = 'api/campus_notice/edit/$1';
$route['api/campus_notice/(:num)/del']['post'] = 'api/campus_notice/del/$1';









