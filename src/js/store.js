var vue = require("vue");
var vuex = require('vuex');
var base64 = require("./base64.js");

vue.use(vuex);

var store = new vuex.Store({
    state: {
        headShow: false,
        headID: 1, //导航id
        yx_leftMeauId: 0, //云校left导航id
        setting_meau_id: 1, //云校设置ID
        subject_meau_id: -1, //yx 课程 left id
        subject_detail_meau_id: 1, //yx 课程详情 left id
        yx_yuangong_meau_id: -1, //yx 员工left id
        yx_xy_xq_meau_id: 1, //yx 学员详情菜单id
        yx_cw_leftMeau_id: 1, //财务
        helpid: "",

        updatestudent: 0,

        schoolMessage: {},
        campus: [],
        myMessage: {
            face: ""
        },
        YGMessage: {},

        chengji: "1",
        paikeCount: 0,

        //预报学员详情
        YBstudentDetail: {
            gw: {},
            zy: {}
        },

        wxObj: {
            url: null
        },
        wxurl: [],

        quanxian: [],
        //员工精简版
        ygSimple: [],
        //权限配置
        qxpeizhi: [],
        //班级精简版
        classSimple: [],

        //职位部门
        department: [],

        //新消息提示
        newMsgCount: {},

        //xinxiaoxi list
        newMsgList: [],

        //招生意向学员分页
        zsindex: 1,
        //学员分页
        xyindex: 1,
        //班级分页
        classindex: 1,
        //课程分页
        subindex: 1,
        //员工分页
        ygindex: 1,
        //财务分页
        cwindex: 1,

        //班级类型
        class_method: [{
            label: "次",
            method: "frequency",
            name: "按次班级"
        }, {
            label: "期",
            method: "cycle",
            name: "按期班级"
        }, {
            label: "月",
            method: "month",
            name: "托管班级"
        }],

        wx_xuanchuan: false,
        wx_hudong: false,
        wx_huodong: false,
        wx_shangcheng: false,
        wx_shezhi: false

    },
    mutations: {
        setcwindex: function(state, id) {
            state.cwindex = id;
        },
        setygindex: function(state, id) {
            state.ygindex = id;
        },
        setclassindex: function(state, id) {
            state.classindex = id;
        },
        setsubindex: function(state, id) {
            state.subindex = id;
        },
        setzsindex: function(state, id) {
            state.zsindex = id;
        },
        setxyindex: function(state, id) {
            state.xyindex = id;
        },
        // 更新学员
        updatestudentFun: function(state) {
            state.updatestudent = new Date().getTime();
        },
        //获取校区
        getcampus: function(state, self) {
            self.$http.get("/api/campus")
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.campus = data.data.data;
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
        //查看新消息
        getNewMsgCount: function(state, self) {
            var self = self;
            self.$http.get("/api/message/count")
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.newMsgCount = data.data.data;
                        self.$http.get("/api/message?limit=5")
                            .then(function(data) {
                                if (data.data.status == 'ok') {
                                    state.newMsgList = data.data.data;
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
        //获取部门职位列表
        getZQWBM: function(state, obj) {
            if (!obj.self) {
                alert("请填写self选项");
                return;
            }
            var self = obj.self;
            self.$http.get("/api/department/all")
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.department = data.data.data;
                        obj.successFun ? obj.successFun(data.data) : '';
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
        //班级精简版
        getClassList: function(state, obj) {
            if (!obj.self) {
                alert("请填写self选项");
                return;
            }
            var campus_id = obj.campus_id ? obj.campus_id : "";
            var staus = obj.staus ? obj.staus : "";
            var fee_method = obj.fee_method ? obj.fee_method : "";
            var self = obj.self;
            self.$http.get("/api/classs/simple?campus_id=" + campus_id)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.classSimple = data.data.data.class;
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
        //权限配置
        getPeizhi: function(state) {
            var self = vue;
            self.http.get("/api/config/authority_list")
                .then(function(data) {
                    state.qxpeizhi = data.data;
                }, function(err) {

                })
        },
        //员工精简版列表
        getYGList: function(state, obj) {
            if (!obj.self) {
                alert("请设置self参数")
                return;
            }
            var self = obj.self;
            var campus_id = obj.campus_id ? obj.campus_id : "";
            var department_id = obj.department_id ? obj.department_id : "";
            var group_id = obj.group_id ? obj.group_id : "";
            var is_sc = obj.is_sc ? obj.is_sc : "";
            var is_gw = obj.is_gw ? obj.is_gw : "";
            var is_bzr = obj.is_bzr ? obj.is_bzr : "";
            var is_dk = obj.is_dk ? obj.is_dk : "";
            var is_stk = obj.is_stk ? obj.is_stk : "";
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&department_id=" + department_id + "&group_id=" + group_id + "&is_sc=" + is_sc + "&is_gw=" + is_gw + "&is_bzr=" + is_bzr + "&is_dk=" + is_dk + "&is_stk=" + is_stk)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.ygSimple = data.data.data.staff;
                    } else {
                        store.commit("checkError", {
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
        //设置权限参数
        setQuanxian: function(state, obj) {
            state.quanxian = obj;
        },
        //设置帮助ID
        sethelpid: function(state, id) {
            state.helpid = id;
        },
        //驱动排课
        qudongpauke: function(state, id) {
            state.paikeCount = id;
        },
        //成功提示
        success: function(state, obj) {
            obj.self.$message({
                showClose: true,
                message: obj.msg,
                type: 'success'
            });
        },
        //成功提示1
        success1: function(state, obj) {
            obj.self.$confirm(obj.msg, '提示', {
                confirmButtonText: '确定',
                showCancelButton: false,
                type: 'success'
            }).then(function() {
                obj.fun ? obj.fun() : "";
            })
        },
        //设置错误提示 或者登陆超时提示
        checkError: function(state, obj) {
            obj.self.$message({
                showClose: true,
                message: obj.data.msg,
                type: 'warning'
            });
            if (obj.data.code === 1000) {
                window.location.href = "/v2/";
            }
        },
        //设置错误提示1 或者登陆超时提示1
        checkError1: function(state, obj) {
            obj.self.$confirm(obj.msg, '提示', {
                confirmButtonText: '确定',
                showCancelButton: false,
                type: 'warning'
            }).then(function() {
                if (obj.data) {
                    if (obj.data.code === 1000) {
                        window.location.href = "/v2/";
                    }
                }
            })

        },
        //获取我的信息
        getMyself: function(state) {
            vue.http.get("/api/user")
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        state.myMessage = data.data.data;
                    }
                }, function() {

                })
        },
        setHeadShow: function(state, a) {
            state.headShow = a;
        },
        setHeadID: function(state, id) {
            state.headID = id;
        },
        setYXLeftMeauId: function(state, id) {
            state.yx_leftMeauId = id;
        },
        setSettingLeftMeau: function(state, id) {
            state.setting_meau_id = id;
        },
        setsubject_meau_id: function(state, id) {
            state.subject_meau_id = id;
        },
        setyx_yuangong_meau_id: function(state, id) {
            state.yx_yuangong_meau_id = id;
        },
        setyx_cw_leftMeau_id: function(state, id) {
            state.yx_cw_leftMeau_id = id;
        },
        setLogin: function(state, a) {

            if (a) {} else {
                state.schoolMessage = {};
                state.myMessage = {};
                state.campus = [];
                document.title = "同城学微校通";
            }

        },
        setsubject_detail_meau_id: function(state, id) {
            state.subject_detail_meau_id = id;
        },
        setyx_xy_xq_meau_id: function(state, id) {
            state.yx_xy_xq_meau_id = id;
        },
        //更新成绩列表
        changechengji: function(state, a) {
            state.chengji = a;
        },
        // 获取预报学员信息
        getYBsutudent: function(state, id) {
            var self = vue;
            self.http.get("/api/intention/" + id)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        store.commit("getLuruName", data.data.data);
                    } else {
                        store.checkError({
                            self: self,
                            data: data.data
                        });
                    }
                }, function(err) {

                })
        },
        //获取录入这姓名
        getLuruName: function(state, obj) {
            var self = vue;
            self.http.get("/api/staff/" + obj.reg_user)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        // data.data.data.status_id = data.data.data.status_id == "0" ? "" : data.data.data.status_id;
                        // data.data.data.level_id = data.data.data.level_id == "0" ? "" : data.data.data.level_id;
                        obj.reg_name = data.data.data.staff[0].name;
                        obj.gw = obj.gw == null ? {} : obj.gw;
                        obj.zy = obj.zy == null ? {} : obj.zy;
                        state.YBstudentDetail = obj;
                    } else {
                        state.checkError({
                            self: self,
                            data: data.data
                        });
                    }
                }, function(err) {

                })
        },
        //微信登陆
        wxloginIn: function(state, obj) {
            state.wxObj = obj;
        },
        //获取员工信息
        getYGMessage: function(state, self) {
            self.$http.get("/api/staff/" + self.$route.params.id)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        data.data.data.staff[0].face = "http://wx.eduwxt.com/api/face/teacher/" + data.data.data.staff[0].id;
                        state.YGMessage = data.data.data.staff[0];
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
        //考勤统计
        getkaoqinTJ: function(state, option) {
            var self = option.self;
            self.$http.get("/api/student/count/" + option.sid + "/" + option.type)
                .then(function(data) {
                    if (data.data.status == 'ok') {
                        option.fun(data.data.data);
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
    }
})

module.exports = store;