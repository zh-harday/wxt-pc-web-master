<template>
    <div>
        <h1 class="right_title">
            我的学员
            <br>
            <el-button type="success" size="small" @click="OpenaddNewStudent">添加意向学员</el-button>
            <el-button type="success" size="small" @click="daoruyixiangFun">导入意向学员</el-button>
            <el-input placeholder="请输入姓名或手机号" icon="search" v-model="option.search" @keyup.enter.native="serachFun" :on-icon-click="serachFun" class="btn"></el-input>
        </h1>
        <div class="xy_serach_box">
            <div class="saixuan_box" v-if="myMessage.campus_id == '1' ">
                <span>校区：</span>
                <ul>
                    <li :class="{active:(option.campus_id == '')}" @click="canpus_click('')">全部</li>
                    <li :class="{active:(option.campus_id == item.id )}" v-for="item in campus" @click="canpus_click(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>跟进：</span>
                <ul>
                    <li :class="{active:option.intention_id == '' }" @click="intentionClick('')">全部</li>
                    <li :class="{active:option.intention_id == item.id }" v-for="item in intention" @click="intentionClick(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>意向：</span>
                <ul>
                    <li :class="{active: option.level_id == ''}" @click="levelClick('')">全部</li>
                    <li :class="{active: option.level_id == item.id }" v-for="item in levelList" @click="levelClick(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>招生来源：</span>
                <ul>
                    <li :class="{active: option.source_id == ''}" @click="sourceClick('')">全部</li>
                    <li :class="{active: option.source_id == item.id }" v-for="item in sourceList" @click="sourceClick(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box last" v-if="$route.name == 'recruit_list' ">
                <span>其他：</span>
                <el-select @change="changezygw" v-model="option.zy_id" filterable placeholder="请选择市场专员" size="small">
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="list.name" :value="list.id" v-for="list in ygSimple"></el-option>
                </el-select>
    
                <el-select @change="changezygw" v-model="option.gw_id" filterable placeholder="请选择课程顾问" size="small">
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="list.name" :value="list.id" v-for="list in teachList"></el-option>
                </el-select>
    
                <!--<el-select v-model="option.teach_id" placeholder="请选择试听课老师" size="small">
                                            <el-option label="全部" value="0"></el-option>
                                            <el-option :label="list.name" :value="list.id" v-for="list in teachList"></el-option>
                                        </el-select>-->
    
            </div>
        </div>
        <!--我的学员表单操作中  没有分配-->
        <!--筛选-->
        <el-table :data="studentList.intention" @selection-change="handleSelectionChange" style="width: 100%" @sort-change="lastTimeFun" v-loading="loading" element-loading-text="加载中">
            <el-table-column type="selection" width="62">
            </el-table-column>
            <el-table-column prop="name" label="学员姓名">
            </el-table-column>
            <el-table-column prop="age" label="年龄">
            </el-table-column>
            <el-table-column prop="phone" label="联系电话" min-width="115">
            </el-table-column>
            <el-table-column prop="campus_name" label="所在校区">
            </el-table-column>
            <el-table-column prop="source_name" label="招生渠道">
            </el-table-column>
            <el-table-column prop="status_name" label="跟进状态">
            </el-table-column>
            <el-table-column prop="level_name" label="意向级别">
            </el-table-column>
            <el-table-column prop="last_time" label="上次跟进时间" min-width="120" sortable="custom">
                <template scope="scope">
                    {{ scope.row.last_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column prop="next_time" label="计划跟进时间" min-width="120" sortable="custom">
                <template scope="scope">
                    {{ scope.row.next_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column prop="log_count" label="跟进次数">
            </el-table-column>
            <el-table-column label="操作" width="300">
                <template scope="scope">
                    <el-button type="warning" size="mini" @click="$router.push({name:'student_server',params:{id:scope.row.id}})">跟进</el-button>
                    <el-button type="success" size="mini" @click="$router.push({name:'student_info',params:{id:scope.row.id}})">查看详情</el-button>
                    <el-button type="info" size="mini" @click="openfenpei(scope.row)" v-if="$route.name == 'recruit_list' ">分配顾问</el-button>
                    <el-button type="danger" size="mini" @click="delStudent(scope.row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye">
            <el-button type="success" size="small" @click="openPLfenpei" v-if="$route.name == 'recruit_list' ">批量分配</el-button>
            <el-pagination v-if="studentList.count > studentList.intention.length" @current-change="handleCurrentChange" :current-page="zsindex" :page-size="option.limit" layout="total, prev, pager, next" :total="studentList.count">
            </el-pagination>
        </div>
        <!--添加意向学员-->
        <addyxxy :changeshow="changeshow" v-on:change="getYXstudentList"></addyxxy>
        <!--分配-->
        <fenpei :nm="student_list" :changeid="change_id" v-on:change="updateList"></fenpei>
        <!--导入意向-->
        <daoru :type="2" :changetime="changetime" v-on:change="getYXstudentList"></daoru>
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
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        zsindex: function () {
            return this.$store.state.zsindex;
        }
    },
    data: function () {
        return {
            changetime: 0,
            changeshow: 0,
            loading: false,
            change_id: "",
            student_list: [],
            //筛选学员参数
            option: {
                limit: 12,

                field: "last_time",
                sort: "DESC",

                search: "",
                campus_id: "",
                intention_id: "",
                level_id: "",
                source_id: "",

                zy_id: "",
                gw_id: ""

            },
            //员工列表
            teachList: [],
            //学员列表
            studentList: {
                intention: []
            },

            //跟进状态
            intention: window.sessionStorage.getItem("intention") ? JSON.parse(window.sessionStorage.getItem("intention")) : [],
            //意向级别
            levelList: window.sessionStorage.getItem("levelList") ? JSON.parse(window.sessionStorage.getItem("levelList")) : [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],

        }
    },
    watch: {
        $route: function (val) {
            var self = this;
            self.$store.commit("setzsindex", 1);
            if (self.$route.name == 'recruit_list') {
                //权限设置
                var yx_zszz_syxygl = this.pdqx(["yx_zszz", "yx_zszz_syxygl"]);
                if (!yx_zszz_syxygl) {
                    this.$router.push({ name: 'worktody' });
                    return;
                }
                self.getStudentList(self.option);
            } else {
                //权限设置
                var yx_zszz_glxygl = this.pdqx(["yx_zszz", "yx_zszz_glxygl"]);
                if (!yx_zszz_glxygl) {
                    this.$router.push({ name: 'worktody' });
                    return;
                }
                self.getMyStudentList(self.option);
            }
        }
    },
    methods: {
        //上次跟进时间排序
        lastTimeFun: function (val) {
            if (val.prop == null) return;
            var self = this;
            self.option.field = val.prop;
            self.option.sort = val.order == 'descending' ? 'DESC' : 'ASC';
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        openPLfenpei: function () {
            this.change_id = new Date().getTime();
        },
        handleSelectionChange: function (val) {
            console.log(val)
            this.student_list = val;
        },
        updateList: function () {
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        //获取意向学员
        getYXstudentList: function () {
            this.$store.commit("setzsindex", 1);
            this.option.search = "";
            this.intention_id = "";
            this.level_id = "";
            this.source_id = "";
            this.zy_id = "";
            this.gw_id = "";

            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        //导入意向学员
        daoruyixiangFun: function () {
            this.changetime = new Date().getTime();
        },
        //分配
        openfenpei: function (item) {
            this.change_id = new Date().getTime();
            this.student_list = [item];
        },

        //选择其他条件
        changezygw: function () {
            this.$store.commit("setzsindex", 1);
            this.option.search = "";

            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },

        //打开新增意向学员
        OpenaddNewStudent: function () {
            this.changeshow = new Date().getTime();
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
                        self.getLevel();
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
        //跟进状态点击
        intentionClick: function (id) {
            this.option.intention_id = id;
            this.option.search = "";
            this.$store.commit("setzsindex", 1);
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
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
                        self.getSource();
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
        //意向级别点击
        levelClick: function (id) {
            this.option.level_id = id;
            this.option.search = "";
            this.$store.commit("setzsindex", 1);
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        //获取招生渠道
        getSource: function () {
            var self = this;
            if (self.sourceList.length > 0) {
                if (self.$route.name == 'recruit_list') {
                    self.getStudentList(self.option);
                } else {
                    self.getMyStudentList(self.option);
                }
                return;
            }
            self.$http.get("/api/intention/source")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.sourceList = data.data.data;
                        window.sessionStorage.setItem("sourceList", JSON.stringify(self.sourceList));
                        if (self.$route.name == 'recruit_list') {
                            self.getStudentList(self.option);
                        } else {
                            self.getMyStudentList(self.option);
                        }
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
        //点击招生渠道
        sourceClick: function (id) {
            this.option.source_id = id;
            this.option.search = "";
            this.$store.commit("setzsindex", 1);
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        //获取招生追踪学员列表
        getStudentList: function (obj) {
            var self = this;
            obj.zy_id == "0" ? "" : obj.zy_id;
            obj.gw_id == "0" ? "" : obj.gw_id;
            self.loading = true;
            obj.campus_id = self.myMessage.campus_id == '1' ? obj.campus_id : self.myMessage.campus_id;
            var lecture = "";
            if (self.$route.params.type == "NotArranged") {
                lecture = 0;
            } else if (self.$route.params.type == "AlreadyArranged") {
                lecture = 1;
            } else {
                lecture = "";
            }
            var url = "/api/intention?limit=" + obj.limit + "&page=" + self.zsindex + "&field=" + self.option.field + "&sort=" + self.option.sort + "&search=" + obj.search + "&campus_id=" + obj.campus_id + "&status_id=" + obj.intention_id + "&source_id=" + obj.source_id + "&level_id=" + obj.level_id + "&zy_id=" + obj.zy_id + "&gw_id=" + obj.gw_id + "&lecture=" + lecture;
            self.$http.get(url)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.intention.length; i++) {
                            //校区
                            for (var g = 0; g < self.campus.length; g++) {
                                if (data.data.data.intention[i].campus_id == self.campus[g].id) {
                                    data.data.data.intention[i].campus_name = self.campus[g].name;
                                    break;
                                }
                            }

                            //跟进状态
                            for (var j = 0; j < self.intention.length; j++) {
                                if (data.data.data.intention[i].status_id == self.intention[j].id) {
                                    data.data.data.intention[i].status_name = self.intention[j].name;
                                    break;
                                }
                            }
                            //意向级别
                            for (var k = 0; k < self.levelList.length; k++) {
                                if (data.data.data.intention[i].level_id == self.levelList[k].id) {
                                    data.data.data.intention[i].level_name = self.levelList[k].name;
                                    break;
                                }
                            }
                            //招生渠道
                            for (var l = 0; l < self.sourceList.length; l++) {
                                if (data.data.data.intention[i].source_id == self.sourceList[l].id) {
                                    data.data.data.intention[i].source_name = self.sourceList[l].name;
                                    break;
                                }
                            }
                        }
                        self.studentList = data.data.data;
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
        getMyStudentList: function (obj) {
            var self = this;
            obj.zy_id == "0" ? "" : obj.zy_id;
            obj.gw_id == "0" ? "" : obj.gw_id;
            self.loading = true;
            obj.campus_id = self.myMessage.campus_id == '1' ? obj.campus_id : self.myMessage.campus_id;
            var type = self.$route.params.type;
            var url = "/api/intention/my?limit=" + obj.limit + "&page=" + self.zsindex + "&field=" + self.option.field + "&sort=" + self.option.sort + "&search=" + obj.search + "&campus_id=" + obj.campus_id + "&status_id=" + obj.intention_id + "&source_id=" + obj.source_id + "&level_id=" + obj.level_id + "&type=" + type;
            self.$http.get(url)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.intention.length; i++) {
                            //校区
                            for (var g = 0; g < self.campus.length; g++) {
                                if (data.data.data.intention[i].campus_id == self.campus[g].id) {
                                    data.data.data.intention[i].campus_name = self.campus[g].name;
                                    break;
                                }
                            }

                            //跟进状态
                            for (var j = 0; j < self.intention.length; j++) {
                                if (data.data.data.intention[i].status_id == self.intention[j].id) {
                                    data.data.data.intention[i].status_name = self.intention[j].name;
                                    break;
                                }
                            }
                            //意向级别
                            for (var k = 0; k < self.levelList.length; k++) {
                                if (data.data.data.intention[i].level_id == self.levelList[k].id) {
                                    data.data.data.intention[i].level_name = self.levelList[k].name;
                                    break;
                                }
                            }
                            //招生渠道
                            for (var l = 0; l < self.sourceList.length; l++) {
                                if (data.data.data.intention[i].source_id == self.sourceList[l].id) {
                                    data.data.data.intention[i].source_name = self.sourceList[l].name;
                                    break;
                                }
                            }
                        }
                        self.studentList = data.data.data;
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
        //删除学员
        delStudent: function (obj) {
            var self = this;
            self.$confirm('是否确认删除' + obj.name + '？', '删除学员', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/intention/" + obj.id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            //筛选学员参数
                            self.option = {
                                limit: 12,
                                search: "",
                                campus_id: "",
                                intention_id: "",
                                level_id: "",
                                source_id: "",

                                field: "last_time",
                                sort: "DESC",

                                zy_id: "",
                                gw_id: ""

                            };
                            if (self.$route.name == 'recruit_list') {
                                self.getStudentList(self.option);
                            } else {
                                self.getMyStudentList(self.option);
                            }
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
                self.$message({
                    type: 'info',
                    message: '已取消删除'
                });
            });
        },

        //点击校区
        canpus_click: function (id) {
            this.option.campus_id = id;
            this.option.search = "";
            this.$store.commit("setzsindex", 1);
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },

        //点击页码
        handleCurrentChange: function (val) {
            this.$store.commit("setzsindex", val);
            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }
        },
        //搜索
        serachFun: function () {
            var self = this;

            self.$store.commit("setzsindex", 1);
            self.option.campus_id = '';
            self.option.intention_id = '';
            self.option.level_id = '';
            self.option.source_id = '';
            self.option.zy_id = '';
            self.option.gw_id = '';

            if (this.$route.name == 'recruit_list') {
                this.getStudentList(this.option);
            } else {
                this.getMyStudentList(this.option);
            }

        },
        //获取员工
        getGWTeachList: function () {
            var self = this;
            var campus_id = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.teachList = data.data.data.staff;
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
        var self = this;
        self.getIntention();
        self.getGWTeachList();
        var campus_id = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;
        self.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id,
            is_sc: 1
        });

        //权限设置
        if (self.$route.name == 'recruit_list') {

            var yx_zszz_syxygl = this.pdqx(["yx_zszz", "yx_zszz_syxygl", "yx"]);
            if (!yx_zszz_syxygl) {
                this.$router.push({ name: 'worktody' });
                return;
            }
        } else {
            var yx_zszz_glxygl = this.pdqx(["yx_zszz", "yx_zszz_glxygl", "yx"]);
            if (!yx_zszz_glxygl) {
                this.$router.push({ name: 'worktody' });
                return;
            }
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";

.fenpei_title {
    font-size: 16px;
    color: @c2;
    margin-bottom: 5px;
    span {
        padding: 0 10px;
        color: @c1;
    }
}

.el-select {
    margin-right: 10px;
    width: 150px;
}

.table_id {
    color: @color;
    text-decoration: underline;
    cursor: pointer;
}
.fenye{
    height: 50px;
}
.btn {
    width: 200px;
}
</style>