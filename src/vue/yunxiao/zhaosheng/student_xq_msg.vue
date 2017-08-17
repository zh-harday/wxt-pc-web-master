<template>
    <div class="xueyuan_msg">
        <!--展示学员信息-->
        <div class="student_zanshi" v-if="msgShow">
            <el-form ref="form" :model="YBstudentDetail" label-width="70px">
                <h1 class="ybstudent_title">基础信息</h1>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            {{ YBstudentDetail.name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年龄">
                            {{ YBstudentDetail.age }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            {{ YBstudentDetail.phone }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <h1 class="ybstudent_title">意向信息</h1>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="所属校区">
                            <span v-for="item in campus" v-if="item.id == YBstudentDetail.campus_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="预报科目">
                            <span v-for="item in YBsubjectList" v-if="item.id == YBstudentDetail.subject_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="招生渠道">
                            <span v-for="item in sourceList" v-if="item.id == YBstudentDetail.source_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="跟进状态">
                            <span v-for="item in intention" v-if="item.id == YBstudentDetail.status_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="意向级别">
                            <span v-for="item in levelList" v-if="item.id == YBstudentDetail.level_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="市场专员">
                            <span v-for="item in ygSimple" v-if="item.id == YBstudentDetail.zy_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="课程顾问">
                            <span v-for="item in GWteachList" v-if="item.id == YBstudentDetail.gw_id">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <h1 class="ybstudent_title">补充信息</h1>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="生日">
                            {{ YBstudentDetail.birthday | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学校">
                            {{ YBstudentDetail.school }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年级">
                            <span v-for="item in gradeList" v-if="item.id == YBstudentDetail.grade">{{ item.name }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="性别">
                            {{ YBstudentDetail.sex }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="邮箱">
                            {{ YBstudentDetail.email=== 'null'?'':YBstudentDetail.email }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            {{ YBstudentDetail.qq === 'null'?'':YBstudentDetail.qq }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="微信">
                            {{ YBstudentDetail.weixin === 'null'?'':YBstudentDetail.weixin }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="16">
                        <el-form-item label="家庭地址">
                            {{ YBstudentDetail.address === 'null'?'':YBstudentDetail.address }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="备注">
                    {{ YBstudentDetail.remark === 'null'?'':YBstudentDetail.remark }}
                </el-form-item>
                <div class="student_msg_bottom">
                    <el-button type="danger" size="small" @click="delStudent($route.params.id)">删除学员</el-button>
                    <el-button type="primary" size="small" @click="openXiugaiStudentMsg">修改信息</el-button>
                </div>
            </el-form>
        </div>
        <!--修改学员信息-->
        <div class="student_xiugai" v-if="!msgShow">
            <el-form ref="form" :model="YBstudentDetails" label-width="75px">
                <h1 class="ybstudent_title">基础信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="姓名*">
                            <el-input v-model="YBstudentDetails.name" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年龄*">
                            <el-input v-model="YBstudentDetails.age" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话*">
                            <el-input v-model="YBstudentDetails.phone" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <h1 class="ybstudent_title">意向信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="所属校区*">
                            <el-select v-model="YBstudentDetails.campus_id" placeholder="请选择校区">
                                <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="预报科目*">
                            <el-select v-model="YBstudentDetails.subject_id" placeholder="请选择预报科目">
                                <el-option v-for="item in YBsubjectList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="招生渠道*">
                            <el-select v-model="YBstudentDetails.source_id" placeholder="请选择招生渠道">
                                <el-option v-for="item in sourceList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="跟进状态">
                            <el-select v-model="YBstudentDetails.status_id" placeholder="请选择跟进状态">
                                <el-option v-for="item in intention" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="意向级别">
                            <el-select v-model="YBstudentDetails.level_id" placeholder="请选择意向级别">
                                <el-option v-for="item in levelList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="市场专员">
                            <el-select v-model="YBstudentDetails.zy_id" placeholder="请选择市场专员" filterable v-if="!yx_zszz_syxygl" disabled>
                                <el-option v-for="item in ygSimple" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                            <el-select v-model="YBstudentDetails.zy_id" placeholder="请选择市场专员" filterable v-if="yx_zszz_syxygl">
                                <el-option v-for="item in ygSimple" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="课程顾问">
                            <el-select v-model="YBstudentDetails.gw_id" placeholder="请选择课程顾问" filterable v-if="!yx_zszz_syxygl" disabled>
                                <el-option v-for="item in GWteachList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                            <el-select v-model="YBstudentDetails.gw_id" placeholder="请选择课程顾问" filterable v-if="yx_zszz_syxygl">
                                <el-option v-for="item in GWteachList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <h1 class="ybstudent_title">补充信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="生日">
                            <el-date-picker v-model="YBstudentDetails.birthday" type="date" placeholder="选择日期">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学校">
                            <el-input v-model="YBstudentDetails.school" placeholder="请输入学校"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年级">
                            <el-select v-model="YBstudentDetails.grade" placeholder="请选择年级">
                                <el-option v-for="item in gradeList" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="性别">
                            <el-radio class="radio" v-model="YBstudentDetails.sex" label="男">男</el-radio>
                            <el-radio class="radio" v-model="YBstudentDetails.sex" label="女">女</el-radio>
                            <el-radio class="radio" v-model="YBstudentDetails.sex" label="未知">未知</el-radio>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="邮箱">
                            <el-input v-model="YBstudentDetails.email" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            <el-input v-model="YBstudentDetails.qq" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="微信">
                            <el-input v-model="YBstudentDetails.weixin" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="16">
                        <el-form-item label="家庭地址">
                            <el-input v-model="YBstudentDetails.address" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="备注">
                    <el-input v-model="YBstudentDetails.remark" placeholder="请输入姓名" type="textarea"></el-input>
                </el-form-item>
                <div class="student_msg_bottom">
                    <el-button size="small" @click="msgShow = true">取消</el-button>
                    <el-button type="primary" size="small" @click="xiugaiMessage">保存信息</el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        YBstudentDetail: function () {
            return this.$store.state.YBstudentDetail;
        }
    },
    data: function () {
        return {
            msgShow: true,
            input: "",
            //员工
            GWteachList: [],
            //年纪
            gradeList: window.sessionStorage.getItem("gradeList") ? JSON.parse(window.sessionStorage.getItem("gradeList")) : [],
            //跟进状态
            intention: window.sessionStorage.getItem("intention") ? JSON.parse(window.sessionStorage.getItem("intention")) : [],
            //意向级别
            levelList: window.sessionStorage.getItem("levelList") ? JSON.parse(window.sessionStorage.getItem("levelList")) : [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
            //预报科目
            YBsubjectList: window.sessionStorage.getItem("YBsubjectList") ? JSON.parse(window.sessionStorage.getItem("YBsubjectList")) : [],
            //预报学院信息
            // YBstudentDetail: window.sessionStorage.getItem("YBstudentDetail" + this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("YBstudentDetail" + this.$route.params.id)) : {},
            YBstudentDetails: {
                campus_id: "",
                name: "",
                age: "",
                phone: "",
                status_id: "",
                subject_id: "",
                source_id: "",
                level_id: "",
                zy_id: "",
                gw_id: "",
                birthday: "",
                school: "",
                address: "",
                grade: "",
                weixin: "",
                qq: "",
                email: "",
                remark: "",
                sex: ""
            },

            yx_zszz_syxygl:false
        }
    },
    methods: {
        //打开修改界面
        openXiugaiStudentMsg: function () {
            var self = this;
            self.msgShow = false;
            for (var k in self.YBstudentDetails) {
                self.YBstudentDetails[k] = self.YBstudentDetail[k];
            }
            if (self.YBstudentDetails.birthday == "") {
                self.YBstudentDetails.birthday = "";
            } else if (self.YBstudentDetails.birthday == "0") {
                self.YBstudentDetails.birthday = "";
            } else {
                var v = self.YBstudentDetails.birthday * 1000;
                var date = new Date(v);
                self.YBstudentDetails.birthday = date.toLocaleDateString();
            }
        },
        //修改学员
        xiugaiMessage: function () {
            var self = this;
            if (self.YBstudentDetails.name.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "学员姓名不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.YBstudentDetails.age.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "年龄不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.YBstudentDetails.phone.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "电话号码不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.YBstudentDetails.campus_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择意向校区",
                    type: "warning"
                })
                return;
            }
            if (self.YBstudentDetails.subject_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择预报科目",
                    type: "warning"
                })
                return;
            }
            if (self.YBstudentDetails.source_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择招生渠道",
                    type: "warning"
                })
                return;
            }

            var tims = "";
            if (typeof self.YBstudentDetails.birthday == 'object') {
                tims = self.YBstudentDetails.birthday.getTime() / 1000;
            } else {
                if (self.YBstudentDetails.birthday != '') {
                    var val = self.YBstudentDetails.birthday;
                    var date = new Date(val.split("/")[0], val.split("/")[1], val.split("/")[2]).getTime() / 1000;
                    tims = date;
                }
            }



            var formData = new FormData();
            for (var k in self.YBstudentDetails) {
                if (k == 'birthday') {
                    formData.append(k, tims);
                } else {
                    formData.append(k, self.YBstudentDetails[k]);
                }
            }



            self.$http.post("/api/intention/" + self.YBstudentDetail.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.$store.commit("getYBsutudent", this.$route.params.id);
                        self.msgShow = true;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除学员
        delStudent: function (id) {
            var self = this;
            self.$confirm('是否确认删除该学员？', '删除学员', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/intention/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.$router.push({ name: 'recruit_list' });

                        } else {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }, function (err) {
                        self.$message({
                            showClose: true,
                            message: "网络错误",
                            type: "error"
                        })
                    })
            }).catch(function () {
               
            });
        },
        //预报科目
        getYBsubject: function () {
            var self = this;
            if (self.YBsubjectList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/subject")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.YBsubjectList = data.data.data;
                        window.sessionStorage.setItem("YBsubjectList", JSON.stringify(self.YBsubjectList));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取跟进状态
        getIntention: function () {
            var self = this;
            if (self.intention.length > 0) {
                self.getLevel();
                return;
            }
            self.$http.get("/api/intention/status")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.intention = data.data.data;
                        window.sessionStorage.setItem("intention", JSON.stringify(self.intention));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取意向级别列表
        getLevel: function () {
            var self = this;
            if (self.levelList.length > 0) {
                self.getSource();
                return;
            }
            self.$http.get("/api/intention/level")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.levelList = data.data.data;
                        window.sessionStorage.setItem("levelList", JSON.stringify(self.levelList));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取招生渠道
        getSource: function () {
            var self = this;
            if (self.sourceList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/source")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.sourceList = data.data.data;
                        window.sessionStorage.setItem("sourceList", JSON.stringify(self.sourceList));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        // 获取预报学员信息
        getYBsutudent: function () {
            var self = this;
            self.$http.get("/api/intention/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.getLuruName(data.data.data);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取录入这姓名
        getLuruName: function (obj) {
            var self = this;
            self.$http.get("/api/staff/" + obj.reg_user)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        obj.reg_name = data.data.data.staff[0].name;
                        self.YBstudentDetail = obj;
                        window.sessionStorage.setItem("YBstudentDetail" + self.$route.params.id, JSON.stringify(self.YBstudentDetail));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取年级列表
        getgradeList: function () {
            var self = this;
            if (self.gradeList.length > 0) {
                return;
            }
            self.$http.get("/api/config/grade")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.gradeList = data.data.data;
                        window.sessionStorage.setItem("gradeList", JSON.stringify(self.gradeList));
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取顾问员工
        getTeachList: function () {
            var self = this;
            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.GWteachList = data.data.data.staff;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        this.getYBsubject();
        this.getIntention();
        this.getLevel();
        this.getSource();
        this.getTeachList();
        var self = this;
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        this.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id,
            is_sc: 1
        });

        this.yx_zszz_syxygl = this.pdqx(["yx_zszz", "yx_zszz_syxygl", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.xueyuan_msg {
    background-color: #fff;
    padding: 20px;
    .ybstudent_title {
        font-size: @h3;
        color: @c2;
        font-weight: normal;
        margin-bottom: 10px;
    }
}

.student_zanshi {
    .el-form-item {
        margin-bottom: 0;
    }
}

.student_msg_bottom {
    text-align: right;
    padding: 10px 0;
}

.fengexian2 {
    margin-top: 10px;
}
</style>