//主文件引入
var vue = require("vue");
var store = require("./store.js");



/*****************个人信息**************************/
var myMessage = require("../vue/my/index.vue");
/**********************通知*************************/
var notice = require("../vue/notice/index.vue");
var notice_msgbox = require("../vue/notice/msgbox.vue");
var fabu_notice = require("../vue/notice/fabu.vue");
var notice_detail = require("../vue/notice/notice_detal.vue");
/******************************登陆***************************/
var login = require("../vue/other/login.vue");
/*************************今日工作**************************/
var worktody = require("../vue/worktoday/index.vue");
/*----------------------萬象云校-------------------------*/
var yunxiao = require("../vue/yunxiao/yunxiao.vue");
//报表
var Report = require("../vue/yunxiao/baobiao/index.vue");
var Report_zs = require("../vue/yunxiao/baobiao/zhaoshengshuju.vue");
var Report_xueyuan = require("../vue/yunxiao/baobiao/xueyuantongji.vue");
var Report_caiwu = require("../vue/yunxiao/baobiao/caiwufenxi.vue");
//招生
var recruit = require("../vue/yunxiao/zhaosheng/index.vue");
var recruit_list = require("../vue/yunxiao/zhaosheng/list.vue");
var wxtable = require("../vue/yunxiao/zhaosheng/wxtable.vue");
var wxtablexq = require("../vue/yunxiao/zhaosheng/wxtable_xq.vue");
var wxtableMsg = require("../vue/yunxiao/zhaosheng/wxtable_xq_msg.vue");
var EntreredDeatil = require("../vue/yunxiao/zhaosheng/wxtable_xq_bm.vue");
var studentXQ = require("../vue/yunxiao/zhaosheng/student_xq.vue");
var studentXQmsg = require("../vue/yunxiao/zhaosheng/student_xq_msg.vue");
var studentXQServer = require("../vue/yunxiao/zhaosheng/student_xq_fuwugz.vue");
var studentXQshiting = require("../vue/yunxiao/zhaosheng/student_xq_shiting.vue");
var addnewwxtable = require("../vue/yunxiao/zhaosheng/wxtable_add.vue");
var Audition = require("../vue/yunxiao/zhaosheng/yuyueshiting.vue");
var EnrollmentTracking = require("../vue/yunxiao/zhaosheng/zhaoshengzuizong.vue");

//学员
var student = require("../vue/yunxiao/xueyuan/index.vue");
var student_list = require("../vue/yunxiao/xueyuan/xueyuan_list.vue");
var student_dtl = require("../vue/yunxiao/xueyuan/xq.vue");
var student_dtl_m = require("../vue/yunxiao/xueyuan/xq_message.vue");
var ServiceTracking = require("../vue/yunxiao/xueyuan/ServiceTracking.vue");
var RegisterClass = require("../vue/yunxiao/xueyuan/RegisterClass.vue");
var student_dtl_Comment = require("../vue/yunxiao/xueyuan/comment.vue");
var AttendanceRecord = require("../vue/yunxiao/xueyuan/AttendanceRecord.vue");
var HomeworkReview = require("../vue/yunxiao/xueyuan/HomeworkReview.vue");
var Registration = require("../vue/yunxiao/xueyuan/baomingjiaofei.vue");
var newRegistration = require("../vue/yunxiao/xueyuan/newBaoming.vue");
var renewRegistration = require("../vue/yunxiao/xueyuan/xufeibaoming.vue");
//班级
var ClassPage = require("../vue/yunxiao/class/index.vue");
var classDetail = require("../vue/yunxiao/class/detail.vue");
var class_keqian = require("../vue/yunxiao/class/keqian.vue");
var class_kehou = require("../vue/yunxiao/class/kehou.vue");
var class_yuxitixing = require("../vue/yunxiao/class/yuxitixing.vue");
var class_banjizuoye = require("../vue/yunxiao/class/banjizuoye.vue");
var class_banjitongzhi = require("../vue/yunxiao/class/banjitongzhi.vue");
var class_chengjiguanli = require("../vue/yunxiao/class/chengjiguanli.vue");
var class_xueyuanliebiao = require("../vue/yunxiao/class/xueyuanliebiao.vue");
var class_log = require("../vue/yunxiao/class/log.vue");
var class_fabu = require("../vue/yunxiao/class/fabu.vue");
//课程
var curriculum = require("../vue/yunxiao/kecheng/index.vue");
var curriculum_list = require("../vue/yunxiao/kecheng/kecheng.vue");
var curriculum_detail = require("../vue/yunxiao/kecheng/detail.vue");
var curriculum_detail_class = require("../vue/yunxiao/kecheng/kaikebanji.vue");
var curriculum_detail_teach = require("../vue/yunxiao/kecheng/shoukelaoshi.vue");
var curriculum_detail_info = require("../vue/yunxiao/kecheng/jibenxinxi.vue");
//员工
var staff = require("../vue/yunxiao/yuangong/index.vue");
var staff_list = require("../vue/yunxiao/yuangong/yglist.vue");
var staff_detail = require("../vue/yunxiao/yuangong/xq.vue");
var staff_detail_xg = require("../vue/yunxiao/yuangong/xq_xg.vue");
var Jurisdiction = require("../vue/yunxiao/yuangong/xq_qx.vue");
var CurriculumList = require("../vue/yunxiao/yuangong/paike.vue");
//财务
var Finance = require("../vue/yunxiao/caiwu/index.vue");
var Finance_mx = require("../vue/yunxiao/caiwu/mingxi.vue");
var Finance_detail = require("../vue/yunxiao/caiwu/cwxiangqing.vue");
var Print = require("../vue/yunxiao/caiwu/dayin.vue");
//设置
var setting = require("../vue/yunxiao/shezhi/index.vue");
var campus = require("../vue/yunxiao/shezhi/xiaoqu.vue");
var moban = require("../vue/yunxiao/shezhi/moban.vue");
var classroom = require("../vue/yunxiao/shezhi/classroom.vue");
var curriculums = require("../vue/yunxiao/shezhi/kecheng.vue");
var Finance_set = require("../vue/yunxiao/shezhi/caiwu.vue");
var fees = require("../vue/yunxiao/shezhi/xuezafei.vue");
var organization = require("../vue/yunxiao/shezhi/zuzhi.vue");
var track = require("../vue/yunxiao/shezhi/track.vue");
var clean = require("../vue/yunxiao/shezhi/clean.vue");

//托管班级
var Trusteeship = require("../vue/yunxiao/class/detail_tg.vue");
var todayClass = require("../vue/yunxiao/class/todayClass.vue");
// var studentlist1 = require("../vue/yunxiao/class/studentList.vue");
var ManagedRecord = require("../vue/yunxiao/class/tuoguanjilu.vue");

//微信营销绑定
var wxbind = require("../vue/weixin/wxbind.vue");

/*----------------------微信营销-------------------------*/
var wxyingxiao = require("../vue/weixin/yingxiao.vue");
//宣传
var Propaganda = require("../vue/weixin/xuanchuan.vue");
//互动
var Interaction = require("../vue/weixin/hudong.vue");
//活动
var activity = require("../vue/weixin/huodong.vue");
//商城
var ShoppingMall = require("../vue/weixin/shangcheng.vue");
//设置
var wx_shezhi = require("../vue/weixin/shezhi.vue");

var routes = [{ //打印
    path: '/Print/:id',
    name: "Print",
    component: Print
}, { //我的
    path: '/My/:type',
    name: "myMessage",
    component: myMessage
}, { //登陆
    path: '/Login',
    name: 'login',
    component: login
}, { //通知
    path: '/Notice',
    name: "notice",
    component: notice,
    children: [{
            path: 'List/AllMessage',
            name: "AllMessage",
            component: notice_msgbox
        },
        {
            path: 'List/SchoolMessage',
            name: "SchoolMessage",
            component: notice_msgbox
        },
        {
            path: 'List/Systemmessage',
            name: "Systemmessage",
            component: notice_msgbox
        }, {
            path: "Release",
            name: "Release",
            component: fabu_notice
        }, {
            path: "Detail/:id",
            name: "notice_detail",
            component: notice_detail
        }
    ]
}, {
    path: '/Yunxiao',
    name: "yunxiao",
    component: yunxiao,
    children: [{ //报表
        path: 'Report',
        name: "Report",
        component: Report,
        children: [{
            path: 'EnrollmentData',
            name: "zhaoshengshuju",
            component: Report_zs
        }, {
            path: 'Student',
            name: "xueyuantongji",
            component: Report_xueyuan
        }, {
            path: 'FinancialAnalysis',
            name: "caiwufenxi",
            component: Report_caiwu
        }]
    }, { //招生
        path: 'Recruit',
        name: "recruit",
        component: recruit,
        children: [{
            path: "All/:type",
            name: "recruit_list",
            component: recruit_list
        }, {
            path: "My/:type",
            name: "recruit_my",
            component: recruit_list
        }, {
            path: "Wxtable",
            name: "wxtable",
            component: wxtable
        }, { //招生 新增微信表单
            path: "AddnewTable",
            name: "AddnewTable",
            component: addnewwxtable
        }, { //预约试听管理
            path: "Audition",
            name: "Audition",
            component: Audition
        }, { //招生追踪设置
            path: "EnrollmentTracking",
            name: "EnrollmentTracking",
            component: EnrollmentTracking
        }]
    }, { //招生微信表单详情
        path: "Wxtable/Detail/:id",
        name: "wxtablexq",
        component: wxtablexq,
        children: [{
            path: "Message",
            name: "wxtableMsg",
            component: wxtableMsg
        }, {
            path: "EntreredDeatil",
            name: "bmXQ",
            component: EntreredDeatil
        }]
    }, { //招生  学员详情
        path: "Recruit/Student/:id",
        name: "studentXQ",
        component: studentXQ,
        children: [{
            path: "Info",
            name: "student_info",
            component: studentXQmsg
        }, {
            path: "Server",
            name: "student_server",
            component: studentXQServer
        }, {
            path: "Audition",
            name: "student_shiting",
            component: studentXQshiting
        }]
    }, { //学员
        path: 'Student',
        name: "student",
        component: student,
        children: [{
            path: "List/:type",
            name: "student_list",
            component: student_list
        }, {
            path: "List/My/:type",
            name: "student_list1",
            component: student_list
        }]
    }, { //学员详情 
        path: 'Student/Detail',
        name: "student_dtl",
        component: student_dtl,
        children: [{
            path: "Message/:id",
            name: "student_dtl_m",
            component: student_dtl_m
        }, {
            path: "ServiceTracking/:id",
            name: "ServiceTracking",
            component: ServiceTracking
        }, {
            path: "RegisterClass/:id",
            name: "RegisterClass",
            component: RegisterClass
        }, {
            path: "Comment/:id",
            name: "student_dtl_Comment",
            component: student_dtl_Comment
        }, {
            path: "AttendanceRecord/:id",
            name: "AttendanceRecord",
            component: AttendanceRecord
        }, {
            path: "HomeworkReview/:id",
            name: "HomeworkReview",
            component: HomeworkReview
        }, {
            path: "Registration/:id",
            name: "Registration",
            component: Registration
        }, {
            path: "newRegistration/:id",
            name: "newRegistration",
            component: newRegistration
        }, {
            path: "renewRegistration/:id",
            name: "renewRegistration",
            component: renewRegistration
        }]
    }, { //班级
        path: 'Class',
        name: "class",
        component: ClassPage
    }, { //班级
        path: 'MyClass',
        name: "class1",
        component: ClassPage
    }, { //班级详情
        path: 'ClassDetail/:id',
        name: "classDetail",
        component: classDetail,
        children: [{
                path: 'Before',
                name: "class_keqian",
                component: class_keqian
            },
            {
                path: 'After',
                name: "class_kehou",
                component: class_kehou
            }, {
                path: 'Preview',
                name: "class_yuxitixing",
                component: class_yuxitixing
            }, {
                path: 'Homework',
                name: "class_banjizuoye",
                component: class_banjizuoye
            }, {
                path: 'Notice',
                name: "class_banjitongzhi",
                component: class_banjitongzhi
            }, {
                path: 'Achievement',
                name: "class_chengjiguanli",
                component: class_chengjiguanli
            }, {
                path: 'StudentList',
                name: "class_xueyuanliebiao",
                component: class_xueyuanliebiao
            }, {
                path: 'ReleasePreview',
                name: 'class_fabu_yuxitixing',
                component: class_fabu
            }, {
                path: 'ReleaseHomework',
                name: 'class_fabu_zuoye',
                component: class_fabu
            }, {
                path: 'ReleaseNotice',
                name: 'class_fabu_tongzhi',
                component: class_fabu
            }, {
                path: 'classLog',
                name: 'class_log',
                component: class_log
            }
        ]
    }, {
        //托管班级详情
        path: 'Trusteeship/:id',
        name: "Trusteeship",
        component: Trusteeship,
        children: [{
            path: 'Today',
            name: "ttpToday",
            component: todayClass
        }, {
            path: 'StudentList',
            name: "studentlist1",
            component: class_xueyuanliebiao
        }, {
            path: 'Notice',
            name: "tg_classNotice",
            component: class_banjitongzhi
        }, {
            path: 'ManagedRecord',
            name: "ManagedRecord",
            component: ManagedRecord
        }, {
            path: 'classLog',
            name: 'tg_class_log',
            component: class_log
        }, {
            path: 'ReleaseNotice',
            name: 'tgclass_fabu_tongzhi',
            component: class_fabu
        }]
    }, { //课程
        path: 'Curriculum',
        name: "curriculum",
        component: curriculum,
        children: [{
            path: 'List/:id',
            name: "curriculum_list",
            component: curriculum_list
        }]
    }, { //课程详情
        path: 'Curriculum/Detail',
        name: "curriculum_detail",
        component: curriculum_detail,
        children: [{
            path: 'Class/:id',
            name: "curriculum_detail_class",
            component: curriculum_detail_class
        }, {
            path: 'Teacher/:id',
            name: "curriculum_detail_teach",
            component: curriculum_detail_teach
        }, {
            path: 'Information/:id',
            name: "curriculum_detail_info",
            component: curriculum_detail_info
        }]
    }, { //员工
        path: 'Staff',
        name: "staff",
        component: staff,
        children: [{
            path: 'List/:id',
            name: 'staffList',
            component: staff_list
        }]
    }, { //员工详情
        path: 'Staff/Detail',
        name: "staff_detail",
        component: staff_detail,
        children: [{
            path: 'Modify/:id',
            name: 'staff_detail_xg',
            component: staff_detail_xg
        }, {
            path: 'CurriculumList/:id',
            name: 'CurriculumList',
            component: CurriculumList
        }, {
            path: 'Jurisdiction/:id',
            name: 'Jurisdiction',
            component: Jurisdiction
        }]
    }, { //财务
        path: 'Finance',
        name: "finance",
        component: Finance,
        children: [{
                path: "List",
                name: "Finance_mx",
                component: Finance_mx
            },
            {
                path: "Detail/:id",
                name: "Finance_detail",
                component: Finance_detail
            }
        ]
    }, { //设置
        path: 'Setting',
        name: "setting",
        component: setting,
        children: [{
                path: 'Campus',
                name: "campus",
                component: campus,
            },
            {
                path: 'TemplateMessage',
                name: "moban",
                component: moban,
            },
            {
                path: 'Classroom',
                name: "classroom",
                component: classroom,
            },
            {
                path: 'Curriculums',
                name: "curriculums",
                component: curriculums,
            },
            {
                path: 'Finance',
                name: "Finance_set",
                component: Finance_set,
            },
            {
                path: 'Fees',
                name: "fees",
                component: fees,
            },
            {
                path: 'Organization',
                name: "organization",
                component: organization,
            },
            {
                path: 'StudentService',
                name: "track",
                component: track,
            },
            {
                path: 'Clean',
                name: "clean",
                component: clean,
                beforeEnter: function(to, from, next) {
                    if (store.state.myMessage.group_id == 1) {
                        next();
                    }
                }
            }
        ]
    }]
}, {
    path: '/Wxyx',
    name: "wxyx",
    component: wxyingxiao,
    //登陆微信
    beforeEnter: function(to, from, next) {
        var self = vue;
        self.http.get("/api/pigcms/login")
            .then(function(data) {
                if (data.data.status == 'ok') {
                    store.commit("wxloginIn", data.data.data);

                    var script = document.createElement("script");
                    script.src = data.data.data.url;
                    script.onload = script.onreadystatechange = function(data) {
                        getWXurl(next);
                    }
                    document.head.appendChild(script);

                } else {
                    self.$store.commit("checkError", {
                        self: self,
                        data: data.data
                    });
                }
            }, function(err) {
                self.$message({
                    showClose: true,
                    message: "网络错误",
                    type: "error"
                })
            })
    },
    children: [
        //宣传
        {
            path: "Propaganda",
            name: "Propaganda",
            component: Propaganda
        },
        //互动
        {
            path: "Interaction",
            name: "Interaction",
            component: Interaction
        },
        //活动
        {
            path: "activity",
            name: "activity",
            component: activity
        },
        //商城
        {
            path: "ShoppingMall",
            name: "ShoppingMall",
            component: ShoppingMall
        },
        //设置
        {
            path: "Setup",
            name: "Setup",
            component: wx_shezhi
        }
    ]
}, {
    path: '/Wxyx/Bind',
    name: "wxbind",
    component: wxbind,
    beforeEnter: function(to, from, next) {
        var self = vue;
        self.http.get("/api/pigcms/login")
            .then(function(data) {
                if (data.data.status == 'ok') {
                    console.log(data)
                    store.commit("wxloginIn", data.data.data);

                    var script = document.createElement("script");
                    script.src = data.data.data.url;
                    script.onload = script.onreadystatechange = function(data) {
                        store.state.myMessage = {};
                        store.state.schoolMessage = {};
                        store.state.campus = {};
                        window.location = "http://wx.eduwxt.com/index.php?g=School&m=Weixin&a=v2pre_auth_code";
                    }
                    document.head.appendChild(script);
                } else {
                    self.$store.commit("checkError", {
                        self: self,
                        data: data.data
                    });
                }
            }, function(err) {
                self.$message({
                    showClose: true,
                    message: "网络错误",
                    type: "error"
                })
            })
    }
}, {
    path: '/Worktody',
    name: "worktody",
    component: worktody
}, {
    path: '/sso_login',
    redirect: "/Worktody",
    component: worktody
}, { path: '*', component: worktody }]

function getWXurl(next) {
    console.log(store.state.wxurl)
    if (store.state.wxurl.length > 0) {
        next();
        return;
    }
    vue.http.get("/api/pigcms/menu")
        .then(function(data) {
            store.state.wxurl = data.data;
            console.log(store.state.wxurl)
            next();
        }, function(err) {

        })
}

module.exports = routes;