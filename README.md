#wxt-pc-web

http://127.0.0.1:801/v2/#/Yunxiao

http://img.eduwxt.com

微信表单图片预览
http://wxt-1251355418.file.myqcloud.com/file/s12/201704/96c0f6c9777d65aaa066caf02db4e87a.png
http://wxt-1251355418.file.myqcloud.com/file/s12/201704/7e09c03476fab4bd744231ef4e036c5a.png
http://wxt-1251355418.file.myqcloud.com/file/s12/201704/d998736eb81b5783c0917c0b0215d925.png

http://127.0.0.1:801/v2/#/Yunxiao/Trusteeship/11587/Today



//招生追踪 -- 学员信息 -- 

意向学员新增明细：http://echarts.baidu.com/demo.html#bar1
意向学员总数增长趋势：http://echarts.baidu.com/demo.html#confidence-band
年度新增报班汇总：http://echarts.baidu.com/demo.html#bar-tick-align
 
学员分布总览：http://echarts.baidu.com/demo.html#pie-nest
学员总数增长趋势：http://echarts.baidu.com/demo.html#confidence-band
学员班级分布：http://echarts.baidu.com/demo.html#bar-gradient
学员课程分布：http://echarts.baidu.com/demo.html#bar-gradient
 
财务数据情况：http://echarts.baidu.com/demo.html#bar-brush

//http://127.0.0.1:801/v2/#/Yunxiao/Trusteeship/10900


//头像
学校logo地址为 http://wx.eduwxt.com/api/face/school/:id
老师头像地址统一为 http://wx.eduwxt.com/api/face/teacher/:id

http://wx.eduwxt.com

最新问题
1 . 缴费完成后，跳转到财务详情页（需要在缴费完成后返回财务流水id ）--已修改
3 . 排课冲突检测 不准确（经测试，我只是一门课的授课老师，我在任何班级都没有代课，排课显示授课老师冲突）--已修改
4 . 点评作业接口报错--已修改
5 . 没有课程结束通知模板消息--已修改
6 . 班级通知无法查看回执--已修改
7 . 发送成绩通知功能未做 --已修改
8 . 学员 考勤记录统计 
9 . 学员 作业记录统计(-已修改)
10 . 微信表单导入学员 需设置市场专员为表单负责人--已修改
11 . 导入意向学员- 指定市场专员 课程顾问 渠道来源 --已修改
12 . GDT新意向学员 显示不准确（经测试，微信表单获取的学员  在最新学员里总数加上了，而列表中获取不到数据，查看该学员总数会减掉）。--已修改
13 . 分配意向学员的操作日志（暂时不做）

14 . 意向学员-显示跟踪次数 （在意向学员列表中返回）
15 . 报表-招生统计-意向学员新增明细统计有误（微信表单提交提交一个意向学员信息，在统计的时候，手动录入也统计上了）。
16 . 续费不能选择收费模式（0正常收费 1赠送 2转结，）/ 提交信息的时候，缺少收费模式字段，缴费中有（fee_mode）！

6月23日统计问题：
1 . PC招生： 意向学员导入问题，年龄字段只能为数字的限制，修改意向学员资料的时候，必须把年龄改成数字才能保存！（年龄字段在转成正式学员的时候用不上） ok
2 . 修改每页显示条数（彭林）ok
3 . 意向学员列表按跟进时间，计划时间排序排序（彭林，接口已支持） ok
4 . 请假次数显示：
     pc: 班级-班级学员列表中显示 ok
     老师端：显示在首页 学生请假申请的卡片中展示，接口需在 3.1.8.3 班级管理-排课管理-课程签到状态中返回学员请假次数 ok
     学生端：我的-我的班级列表中展示（我的-我的班级列表接口返回） ok
5 . 作业点评：
    PC：班级-班级作业-点评接口（需要增加附件字段/注：需要上传图片） ok
        班级-班级作业-点评情况接口（返回附件字段） ok
    老师端：班级管理-作业-点评（需要增加附件字段/注：需要语音和图片进行点评）ok
           班级管理-作业-点评情况接口（返回附件字段,返回学生提交的作业）ok
    学生端: 课堂作业-作业详情-返回我提交的作业 ok


7月5号
    1 . 意向学员模板消息
    2 . 

 