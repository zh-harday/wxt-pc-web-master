<template>
    <div class="yx_class">
        <div class="left">
            <h1>招生追踪
                <article>管理意向学员并反馈跟进情况</article>
            </h1>
            <router-link :to="{ name:'student_server'}" :class="{active:$route.name=='student_server'}">服务跟踪</router-link>
            <router-link :to="{ name:'student_shiting'}" :class="{active:$route.name=='student_shiting'}">试听记录</router-link>
            <router-link :to="{ name:'student_info'}" :class="{active:$route.name=='student_info'}">学员信息</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span class="yixiangxueyuanName">
                    {{ YBstudentDetail.name | getFirstWord }}
                </span>
                <div>
                    <h1>
                        <span>{{ YBstudentDetail.name }}</span>
                        <el-select v-model="YBstudentDetail.status_id" placeholder="跟进状态" size="mini" @change="changeGZstuas">
                            <el-option :label="item.name" :value="item.id" v-for="item in intention"></el-option>
                        </el-select>
                        <el-select v-model="YBstudentDetail.level_id" placeholder="意向级别" size="mini" @change="changeGZstuas">
                            <el-option :label="item.name" :value="item.id" v-for="item in levelList"></el-option>
                        </el-select>
                    </h1>
                    <div class="xiangqing">
                        <p>
                            <span v-for="item in campus" v-if="item.id == YBstudentDetail.campus_id">{{ item.name }}</span>
                            <span v-for="item in YBsubjectList" v-if="item.id == YBstudentDetail.subject_id">预报科目：{{ item.name }}</span>
                            <span v-for="item in sourceList" v-if="item.id == YBstudentDetail.source_id">招生渠道：{{ item.name }}</span>
                            <span>信息录入：{{ YBstudentDetail.reg_name }} 录入</span>
                        </p>
                        <p>
                            <span>市场专员：{{ YBstudentDetail.zy.name }}</span>
                            <span>课程顾问：{{ YBstudentDetail.gw.name }}</span>
                        </p>
                    </div>
                    <span @click="$router.go(-1)">
                        <i class="el-icon-arrow-left"></i>返回上一级</span>
                    <div class="btn_box">
                        <el-button type="primary" size="mini" @click="anpaishiting">安排试听</el-button>
                        <el-button type="primary" size="mini" @click="zhuanruxueyuan">转为正式学员</el-button>
                    </div>
                </div>
            </div>
            <router-view></router-view>
        </div>
        <!--安排试听-->
        <el-dialog title="安排试听" v-model="dialogTableVisible">
            <p class="fengexian"></p>
            <el-table :data="shitingkeList.lecture" v-loading="loading" element-loading-text="加载中">
                <el-table-column property="start_time" label="日期" width="200">
                    <template scope="scope">
                        {{ scope.row.start_time | yyyy_mm_dd_M_S_week }}
                    </template>
                </el-table-column>
                <el-table-column property="subject_name" label="预报科目"></el-table-column>
                <el-table-column property="title" label="试听课名称"></el-table-column>
                <el-table-column property="room_name" label="上课教室"></el-table-column>
                <el-table-column property="teacher_name" label="授课老师"></el-table-column>
                <el-table-column property="" label="已预约">
                    <template scope="scope">
                        {{ scope.row.student_count }}人
                    </template>
                </el-table-column>
                <el-table-column label="操作">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="yuyueshiting(scope.row.id)">安排试听</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            value: "1",
            dialogTableVisible: false,
            loading: false,
            studentData: {},
            //试听课列表
            shitingkeList: {
                lecture: []
            },
            //跟进状态
            intention: window.sessionStorage.getItem("intention") ? JSON.parse(window.sessionStorage.getItem("intention")) : [],
            //意向级别
            levelList: window.sessionStorage.getItem("levelList") ? JSON.parse(window.sessionStorage.getItem("levelList")) : [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
            //预报科目
            YBsubjectList: window.sessionStorage.getItem("YBsubjectList") ? JSON.parse(window.sessionStorage.getItem("YBsubjectList")) : [],

            //新增学员信息
            selfMessage: {
                campus_id: "",
                name: "",
                number: "",
                sex: "男",
                birthday: "",
                school: "",
                grade_id: "",
                phone: "",
                email: "",
                weixin: "",
                qq: "",
                address: "",
                remark: "",
                reg_time: "",
                sales_id: "",//销售人员id
                intention_id: "",//招生追踪关联id

                zy_id: "",
                gw_id: ""
            }
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        YBstudentDetail: function () {
            return this.$store.state.YBstudentDetail;
        }
    },
    filters: {
        getFirstWord: function (val) {
            if (val && val != "") {
                return val.substr(0, 1)
            } else {
                return "";
            }
        }
    },
    methods: {
        //转为正式学员
        zhuanruxueyuan: function () {
            var self = this;
            self.$confirm('是否将此意向学员转为正式学员?', '提示', {
                confirmButtonText: '转入',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.getYBsutudent();
            }).catch(function () {

            });
        },
        // 获取预报学员信息
        getYBsutudent: function () {
            var self = this;
            self.$http.get("/api/intention/" + self.$route.params.id)
                .then(function (datas) {
                    if (datas.data.status == 'ok') {
                        self.$prompt('请输入学员学号', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                        }).then(function (value) {
                            if ((value.value + "").trim() == "") {
                                self.$message({
                                    showClose: true,
                                    message: "请输入学号",
                                    type: "warning"
                                })
                                return;
                            }
                            var data = datas.data.data;
                            self.selfMessage.name = data.name;
                            self.selfMessage.campus_id = data.campus_id;
                            self.selfMessage.number = value.value;
                            self.selfMessage.sex = data.sex == "" ? "未知" : data.sex;
                            self.selfMessage.age = data.age;
                            self.selfMessage.birthday = data.birthday;
                            self.selfMessage.school = data.school;
                            self.selfMessage.grade_id = data.grade_id;
                            self.selfMessage.phone = data.phone;
                            self.selfMessage.email = data.email;
                            self.selfMessage.weixin = data.weixin;
                            self.selfMessage.qq = data.qq;
                            self.selfMessage.address = data.address;
                            self.selfMessage.remark = data.remark;
                            self.selfMessage.reg_time = new Date().getTime() / 1000;
                            self.selfMessage.sales_id = "";
                            self.selfMessage.intention_id = "";
                            self.selfMessage.zy_id = data.zy;
                            self.selfMessage.gw_id = data.gw;
                            self.tianjiaxueyuanFun(data.id);
                        }).catch(function () {

                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: datas.data
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
        //添加正式学员函数
        tianjiaxueyuanFun: function (id) {
            var self = this;
            var formData = new FormData();
            formData.append("campus_id", self.selfMessage.campus_id);
            formData.append("name", self.selfMessage.name);
            formData.append("number", self.selfMessage.number);
            formData.append("sex", self.selfMessage.sex);
            formData.append("birthday", self.selfMessage.birthday);
            formData.append("school", self.selfMessage.school);
            formData.append("grade_id", self.selfMessage.grade_id);
            formData.append("phone", self.selfMessage.phone);
            formData.append("email", self.selfMessage.email);
            formData.append("weixin", self.selfMessage.weixin);
            formData.append("qq", self.selfMessage.qq);
            formData.append("address", self.selfMessage.address);
            formData.append("remark", self.selfMessage.remark);
            formData.append("reg_time", self.selfMessage.reg_time);
            formData.append("sales_id", self.selfMessage.sales_id);
            formData.append("intention_id", self.selfMessage.intention_id);

            formData.append("zy_id", self.selfMessage.zy_id);
            formData.append("gw_id", self.selfMessage.gw_id);

            self.$http.post("/api/student", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$confirm('已将该意向学员转为正式学员，是否要补全学员信息?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'success'
                        }).then(function () {
                            //跳转学员data.data.data.id
                            self.$router.push({ name: 'student_dtl_m', params: { id: data.data.data.id } })
                        }).catch(function () {

                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.addxueyuanModel = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //打开安排试听
        anpaishiting: function () {
            this.dialogTableVisible = true;
            this.getShitingList();
        },
        //安排试听课
        yuyueshiting: function (id) {
            var self = this;
            var formData = new FormData();
            formData.append("intention_id", self.$route.params.id);
            formData.append("relate_user", self.myMessage.id);
            self.$http.post("/api/lecture/" + id + "/booking", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
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
        //获取未上课的试听课列表
        getShitingList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/lecture?status=0")
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.shitingkeList = data.data.data;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //选择意向状态
        changeGZstuas: function () {
            var self = this;
            if (!self.YBstudentDetail.name) {
                return;
            }
            var formData = new FormData();
            for (var k in self.YBstudentDetail) {
                formData.append(k, self.YBstudentDetail[k]);
            }
            self.$http.post("/api/intention/" + self.YBstudentDetail.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("getYBsutudent", self.$route.params.id);
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
        }
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 2);
        this.$store.commit("getYBsutudent", this.$route.params.id);
        this.getYBsubject();
        this.getIntention();
        this.getLevel();
        this.getSource();

        //权限设置
        var yx_zszz_syxygl = this.pdqx(["yx_zszz", "yx_zszz_syxygl", "yx"]);
        var yx_zszz_glxygl = this.pdqx(["yx_zszz", "yx_zszz_glxygl", "yx"]);
        if (!yx_zszz_syxygl && !yx_zszz_glxygl) {
            this.$router.push({ name: 'worktody' });
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.btn_box {
    position: absolute;
    right: 0;
    bottom: 2px;
}

.xiangqing {
    width: 100%;
    >p {
        padding-bottom: 5px;
        &:last-child {
            padding-bottom: 0;
        }
        >span {
            margin-right: 15px;
        }
    }
}

.subject_right_top {
    >span {
        &.yixiangxueyuanName {
            line-height: 68px;
            text-align: center;
            font-size: 40px;
            background-color: @color;
            color: #fff;
        }
    }
    >div {
        >h1 {
            .el-select {
                width: 100px;
            }
        }
    }
}
</style>