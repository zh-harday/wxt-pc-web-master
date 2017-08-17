<template>
    <div>
        <el-table :data="RegisterClass.class" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="class_name" label="班级名称"></el-table-column>
            <el-table-column prop="staus" label="班级类型">
                <template scope="scope">
                    <span v-if="scope.row.class_type == 1" style="color:#20a0ff">托管班级</span>
                    <span v-if="scope.row.class_type == 0">普通班级</span>
                </template>
            </el-table-column>
            <el-table-column prop="teacher_name" label="班主任">
            </el-table-column>
            <el-table-column prop="reg_time" label="报名日期">
                <template scope="scope">
                    <span>{{ scope.row.reg_time | yyyy_mm_dd }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="报名期次">
                <template scope="scope">
                    <span>{{ scope.row.buy_quantity }}{{ scope.row.fee_method | fee_method }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="kx" label="已消课时">
                <template scope="scope">
                    <span v-if="scope.row.class_type == 0">{{ scope.row.kx }}</span>
                    <span v-if="scope.row.class_type == 1">--</span>
                </template>
            </el-table-column>

            <el-table-column prop="staus" label="班级状态">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.staus == 0">正常班级</el-tag>
                    <el-tag type="warning" v-if="scope.row.staus == 2">预报名</el-tag>
                    <el-tag type="danger" v-if="scope.row.staus == -1">已停课</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="staus" label="学员状态">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.student_status == 1">正常</el-tag>
                    <el-tag type="danger" v-if="scope.row.student_status == 0">已停课</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="" label="操作" width="250">
                <template scope="scope">
                    <el-button v-if="yx_cwgl_jf" type="primary" size="mini" @click="$router.push({name:'renewRegistration',params:{id:$route.params.id}})">续费</el-button>
                    <el-button type="info" size="mini" @click="zhuanbanFun(scope.row)" v-if="yx_bjgl_my_qtxx">转班</el-button>
                    <el-button type="danger" size="mini" @click="dingke(scope.row.id,0)" v-if=" yx_bjgl_my_qtxx && scope.row.student_status == 1 && scope.row.class_type == 0">停课</el-button>
                    <el-button type="success" size="mini" @click="dingke(scope.row.id,1)" v-if=" yx_bjgl_my_qtxx && scope.row.student_status == 0 && scope.row.class_type == 0">复课</el-button>
                    <el-button type="warning" size="mini" @click="openLog(scope.row.id)">操作日志</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-show="RegisterClass.count > RegisterClass.class.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="page" :page-sizes="[10, 20, 30, 40]" :page-size="limit" layout="total, prev, pager, next" :total="RegisterClass.count">
            </el-pagination>
        </div>
        <!--转班-->
        <zhuanban :classmsg="classmsg" :changetime="changetime" @change="zhuanbanchange"></zhuanban>
        <!-- 学员操作日志 -->
        <el-dialog title="操作日志" v-model="logshow">
            <p class="fengexian"></p>
            <el-table :data="logList.log" style="width: 100%" v-loading="loading1" element-loading-text="加载中">
                <el-table-column prop="class_name" label="班级名称">
                </el-table-column>
                <el-table-column prop="teacher_name" label="操作人">
                </el-table-column>
                <el-table-column prop="time" label="操作时间">
                    <template scope="scope">
                        <span>{{ scope.row.time | yyyy_mm_dd_M_S }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="action" label="操作">
                </el-table-column>
                <el-table-column prop="detail" label="操作详情">
                </el-table-column>
            </el-table>
            <div class="fenye" v-show="logList.count > logList.log.length">
                <el-pagination @current-change="handlechange" :current-page="logpage" :page-size="loglimit" layout="total, prev, pager, next" :total="logList.count">
                </el-pagination>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        }
    },
    data: function () {
        return {
            RegisterClass: {
                class: []
            },
            loading: false,
            loading1: false,

            classDetail: {},

            limit: 10,
            page: 1,

            //转班相关
            changetime: 0,
            classmsg: {
                class_id: "",
                student_id: "",
                class_name: "",
                class_fee_method: "",
                count: "",//剩余课次
                campus_id: "",
                start_time:"",
                end_time:"",
                class_type:"",
                student_name:""
            },

            logshow: false,
            logList: {
                log: [],
                count:0
            },
            loglimit: 10,
            logpage: 1,
            classid: null,

            yx_cwgl_jf: false,
            yx_bjgl_my_qtxx:false
        }
    },
    methods: {
        //日志分页
        handlechange: function (val) {
            this.logpage = val;
            this.getLogList();
        },
        //打开日志
        openLog: function (cid) {
            this.logshow = true;
            this.logList.log = [];
            this.logList.count = 0;
            this.logpage = 1;
            this.classid = cid;
            this.getLogList();
        },
        //获取已报班级列表
        getLogList: function (cid) {
            var self = this;
            self.loading1 = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/class_log/" + self.classid + "?limit=" + self.loglimit + "&page=" + self.logpage)
                .then(function (data) {
                    self.loading1 = false;
                    if (data.data.status == 'ok') {
                        self.logList = data.data.data;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading1 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //分页
        handleCurrentChange: function (val) {
            this.page = val;
            this.getRegisterClass();
        },

        //转班弹出
        zhuanbanFun: function (obj) {
            console.log(obj);
            this.classmsg.campus_id = obj.campus_id;
            this.classmsg.class_id = obj.id;
            this.classmsg.student_id = this.$route.params.id;
            this.classmsg.class_name = obj.class_name;
            this.classmsg.class_fee_method = obj.fee_method;
            this.classmsg.count = obj.buy_quantity - obj.kx;//剩余课次
            this.classmsg.student_name = obj.name;
            this.classmsg.start_time = obj.student_start_time;
            this.classmsg.end_time = obj.student_end_time;
            this.classmsg.class_type = obj.class_type;

            this.changetime = new Date().getTime();
        },
        //转班成功回掉
        zhuanbanchange: function () {
            this.getRegisterClass();
        },
        //日期转换
        transformTime: function (val) {//xxxx-xx-xx
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
        },
        //停课
        dingke: function (id, status) {
            var self = this;
            var title = status == 1 ? "复课" : "停课"
            self.$confirm('是否要对学员进行' + title + '操作？', title, {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                var formData = new FormData();
                formData.append("status", status);
                self.$http.post("/api/classs/" + id + "/student/" + self.$route.params.id + "/status", formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            if (status == 0) {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: data.data.msg,
                                });
                            }

                            if (status == 1) {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: data.data.msg,
                                });
                            }
                            self.getRegisterClass()
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
        //获取已报班级列表
        getRegisterClass: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/class?limit=" + self.limit + "&page=" + self.page)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.RegisterClass = data.data.data;
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
        }
    },
    created: function () {
        var self = this;
        self.$store.commit('setyx_xy_xq_meau_id', 4);
        self.getRegisterClass()

        //权限设置
        this.yx_cwgl_jf = this.pdqx(["yx_cwgl_jf","yx_cwgl", "yx"]);
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>