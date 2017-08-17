<template>
    <div>
        <div class="content_two">
            <datetmp title="排课日历" :month-date="monthDate" v-on:changetime="changetime" v-on:readyfun="readyFun"></datetmp>
            <div class="c_right">
                <h1 class="c_right_h1">排课列表</h1>
                <el-table :data="paikeList.curriculum" style="width: 100%" v-loading="loading" :element-loading-text="loadingtext" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="62">
                    </el-table-column>
                    <el-table-column prop="subject" min-width="120px" label="课程名称">
                    </el-table-column>
                    <el-table-column prop="start_time" label="上课时间" min-width="200px">
                        <template scope="scope">
                            {{ scope.row.start_time | yyyy_mm_dd_M_S_week }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="teacher_name" min-width="120px" label="授课老师">
                    </el-table-column>
                    <el-table-column prop="room_name" label="授课教室" min-width="95px">
                    </el-table-column>
                    <el-table-column prop="status" label="课程状态" min-width="95px">
                        <template scope="scope">
                            <el-tag type="danger" v-if="scope.row.status == 0 && scope.row.isYanwu == 0">已延误</el-tag>
                            <el-tag type="primary" v-if="scope.row.status == 0 && scope.row.isYanwu == 1">即将开课</el-tag>
                            <el-tag type="warning" v-if="scope.row.status == 0 && scope.row.isYanwu == 2">待上课</el-tag>
                            <el-tag type="success" v-if="scope.row.status == 1">上课中</el-tag>
                            <el-tag type="gray" v-if="scope.row.status == 2">已下课</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="操作" width="200">
                        <template scope="scope">
                            <el-button type="primary" size="mini" @click="shangkeFun(scope.row.id,'start')" v-if="yx_bjgl_my_kczt && scope.row.status == 0 && scope.row.isToday == 1">上课</el-button>
                            <el-button type="primary" size="mini" @click="shangkeFun(scope.row.id,'end')" v-if="yx_bjgl_my_kczt && scope.row.status == 1">下课</el-button>
                            <el-button type="primary" size="mini" @click="openKaoqinFun(scope.row.id)" v-if="yx_bjgl_my_xskq && ( (scope.row.status == 0 && scope.row.isToday == 1) || scope.row.status == 1)">考勤管理</el-button>
                            <el-button type="primary" size="mini" @click="tiaokeFun(scope.row)" v-if="yx_bjgl_my_kczt && scope.row.status != 1">调课</el-button>
                            <el-button type="danger" size="mini" @click="delSubject(scope.row.id)" v-if="yx_bjgl_my_qtxx && scope.row.isToday != 1">删除</el-button>
                            <!--<el-button type="warning" size="mini" @click="kechengFun(scope.row.id)">调课记录</el-button>-->
                        </template>
                    </el-table-column>
                </el-table>
                <div class="fenye">
                    <el-button type="danger" size="small" @click="pldeleteFun" v-show="!loading">批量删除</el-button>
                    <el-button type="danger" size="small" v-show="loading" disabled>批量删除</el-button>
                    <el-pagination v-show="paikeList.count > paikeList.curriculum.length" @current-change="handleCurrentChange" :current-page="paikeData.page" :page-size="paikeData.limit" layout="total, prev, pager, next" :total="paikeList.count">
                    </el-pagination>
                </div>
            </div>
        </div>
        <!--调课-->
        <el-dialog title="调课" v-model="tiaokeshow">
            <p class="fengexian"></p>
            <el-form :model="tiaokeform" label-width="80px">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="课程">
                            <el-select v-model="tiaokeform.subject_id" placeholder="请选择课程" @change="getSubject">
                                <el-option v-for="item in classDetail.subject" :label="item.subject_name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="授课老师">
                            <el-select v-model="tiaokeform.teacher_id" placeholder="请选择授课老师" filterable>
                                <el-option v-for="item in subjectDetail.teacher" :label="item.teacher" :value="item.teacher_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="上课教室">
                            <el-select v-model="tiaokeform.room_id" placeholder="请选择教室">
                                <el-option v-for="item in classroomList" :label="item.room_name" :value="item.id" v-if="classDetail.campus_id == item.campus_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="上课日期">
                            <el-date-picker v-model="tiaokeform.sdate" :picker-options="pickerOptions0" type="date" placeholder="选择日期" @change="changeRiqi"></el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="上课时间">
                            <el-time-picker v-model="tiaokeform.time" placeholder="选择上课时间" format="HH:mm"></el-time-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="时长(分钟)">
                            <el-input v-model="tiaokeform.sc" placeholder="请输入时长" type="number"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="调课原因">
                            <el-input v-model="tiaokeform.change_info" placeholder="请输入调课原因" type="textarea"></el-input>
                            <p class="tk_tishi">请确定以上内容正确后点击确认修改！</p>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="tiaokeshow = false">取消</el-button>
                <el-button type="primary" @click="tiaokefunction">确认修改</el-button>
            </div>
        </el-dialog>
        <!--调课记录-->
        <el-dialog title="课程调整记录" v-model="kechengshow">
            <div class="tk_box">
                <h1>阅读课</h1>
                <p>
                    <span>现上课时间：
                        <s>2017-03-02 09:11</s> 45分钟</span>
                    <span style="text-align:right">原上课时间：2017-02-02 09:00 45分钟</span>
                </p>
                <p>调课原因：今天雾霾太大了，我们早点上课，早点回家</p>
            </div>
            <div class="tk_box1">
                <h2>暂未查看</h2>
                <ul>
                    <li v-for="n in 4">
                        <div class="tk_left">
                            <img src="../../../img/photo.jpg" alt="">
                        </div>
                        <div class="tk_right">
                            <h1>
                                露露
                                <el-button type="text">标记为已读</el-button>
                            </h1>
                            <p>电话：1380945 2165</p>
                        </div>
                    </li>
                </ul>
                <h2>已查看</h2>
                <div>
                    <el-tooltip class="item" effect="dark" content="15319333285" placement="bottom" v-for="n in 11">
                        <el-tag type="primary">阿九</el-tag>
                    </el-tooltip>
                </div>
            </div>
        </el-dialog>
        <kaoqin :id="kaoqinid" :changeid="kaoqinid_change"></kaoqin>
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
        paikeCount: function () {
            return this.$store.state.paikeCount;
        }
    },
    watch: {
        paikeCount: function () {
            this.paikeData.page = 1;
            this.getPaike();
        }
    },
    data: function () {
        return {

            loadingtext: "加载中",

            kaoqinid: "",
            kaoqinid_change: "",
            classDetail: window.sessionStorage.getItem("classDetail" + this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("classDetail" + this.$route.params.id)) : {
                subject: []
            },
            subjectDetail: {},
            classroomList: [],
            loading: false,
            //调课
            tiaokeshow: false,
            paikeData: {
                limit: 13,
                page: 1,
                type: "wait"
            },
            //调课form
            tiaokeform: {
                id: "",
                subject_id: "",
                teacher_id: "",
                room_id: "",
                sdate: "",
                time: "",
                sc: "",
                change_info: ""
            },
            //课程调整弹框
            kechengshow: false,
            monthDate: [],

            paikeList: {
                curriculum: []
            },

            pickerOptions0: {
                disabledDate: function (time) {
                    return time.getTime() < +new Date() - (1000 * 60 * 60 * 24);
                }
            },

            //piliang shanchu
            index: 0,
            daishanchuList: [],

            changeDate: "",
            yx_bjgl_my_view: false,
            yx_bjgl_my_kczt: false,
            yx_bjgl_my_xskq: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        changetime:function(data){
            this.paikeData.page = 1;
            this.changeDate = data;
            this.getPaike();
        },
        readyFun:function(arr,datetime){
            console.log(datetime)
            this.checkPaikeFun(arr,datetime);
            this.getPaike();
        },
        //批量删除
        handleSelectionChange: function (item) {
            this.daishanchuList = item;
        },
        //检查排课
        checkPaikeFun: function (arr,datetime) {
            var self = this;
            var date = datetime.year + "-" + (datetime.month + 1);
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum/month/" + date + "?type=wait")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < arr.length; i++) {
                            for (var k in data.data.data) {
                                if (parseInt(k) == arr[i].day) {
                                    arr[i].empt = false;
                                    break;
                                }
                            }
                        }
                        self.monthDate = arr;
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
        //打开考勤
        openKaoqinFun: function (id) {
            this.kaoqinid = id;
            this.kaoqinid_change = new Date().getTime();
        },
        //调课-格式化日期
        changeRiqi: function (val) {
            this.tiaokeform.sdate = val;
        },
        //上课
        shangkeFun: function (id, staus) {
            var self = this;
            var title = staus == 'start' ? '是否进行要上课操作?' : '是否进行要下课操作?'
            var st = staus == 'start' ? '该课程已上课' : '该课程已下课,请到课后管理查看';

            self.$confirm(title, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                var formData = new FormData();
                formData.append("status", staus);
                self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/" + id + "/status", formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: st,
                            });
                            self.getPaike();
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
        //获取教室列表
        getClassroom: function () {
            var self = this;
            self.$http.get("/api/room")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classroomList = data.data.data;
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
        //班级详情
        getClassDetail: function (obj) {
            var self = this;
            if (self.classDetail.subject.length == 0) {
                self.$http.get("/api/classs/" + self.$route.params.id)
                    .then(function (data) {
                        self.loading = false;
                        if (data.data.status == 'ok') {
                            self.classDetail = data.data.data;
                            window.sessionStorage.setItem("classDetail" + self.$route.params.id, JSON.stringify(self.classDetail));
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
        //课程详情
        getSubject: function (id) {
            var self = this;
            self.$http.get("/api/subject/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.subjectDetail = data.data.data;
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
        //排课详情
        getSubjectDetail: function (obj) {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum/" + obj.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.tiaokeform.subject_id = data.data.data.curriculum.subject_id;
                        self.tiaokeform.teacher_id = data.data.data.curriculum.teacher_id;
                        self.tiaokeform.room_id = data.data.data.curriculum.room_id;
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
        //点击页码
        handleCurrentChange: function (val) {
            this.paikeData.page = val;
            this.getPaike();
        },
        //获取课程列表
        getPaike: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum?&day=" + self.changeDate + "&type=" + self.paikeData.type + "&page=" + self.paikeData.page + "&limit=" + self.paikeData.limit)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.curriculum.length; i++) {
                            var ltime = new Date(data.data.data.curriculum[i].start_time * 1000);
                            var lyear = ltime.getFullYear();
                            var lmonth = ltime.getMonth();
                            var lday = ltime.getDate();
                            if (ltime < (new Date().getTime() - 1000 * 60 * 60 * new Date().getHours())) {
                                data.data.data.curriculum[i].isToday = 0;
                            } else if (lyear == new Date().getFullYear() && lmonth == new Date().getMonth() && lday == new Date().getDate()) {
                                data.data.data.curriculum[i].isToday = 1;
                            } else {
                                data.data.data.curriculum[i].isToday = 2;
                            }

                            if (ltime < (new Date().getTime() - 1000 * 60 * 30)) {
                                data.data.data.curriculum[i].isYanwu = 0;
                            } else if (lyear == new Date().getFullYear() && lmonth == new Date().getMonth() && lday == new Date().getDate()) {
                                data.data.data.curriculum[i].isYanwu = 1;
                            } else {
                                data.data.data.curriculum[i].isYanwu = 2;
                            }

                        }
                        self.paikeList = data.data.data;
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
        //批量删除排课函数
        pldeleteFun: function () {
            var self = this;
            if(self.daishanchuList.length == 0){
                self.$message({
                    showClose: true,
                    message: "请选择要删除的课程",
                    type: "error"
                })
                return;
            }
            self.loading = true;
            self.loadingtext = "删除中,请稍后...";
            self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/" + self.daishanchuList[self.index].id + "/del")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.index++;
                        if (self.index >= self.daishanchuList.length) {
                            self.loading = false;
                            self.loadingtext = "加载中";
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg,
                            });
                            self.getPaike();
                            self.index = 0;
                            return;
                        }
                        self.pldeleteFun();
                    } else {
                        self.loading = false;
                        self.loadingtext = "加载中";
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.loadingtext = "加载中";
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除课程
        delSubject: function (id) {
            var self = this;
            self.$confirm('此操作将永久删除该课程, 是否继续?', '删除课程', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg,
                            });
                            self.getPaike();
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
        //初始化时间 09:30:00
        getTimes: function (val) {
            var n = new Date(val * 1000);
            var year = n.getFullYear();
            var month = n.getMonth();
            var day = n.getDate();
            var hours = n.getHours();
            var min = n.getMinutes();
            return new Date(year, month, day, hours, min);
        },
        //格式化时间
        getDatea: function (val) {//2017-01-11
            if (!val) return "";
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
        },
        //调课打开
        tiaokeFun: function (obj) {
            this.tiaokeshow = true;
            this.getClassDetail(obj);
            this.tiaokeform.id = obj.id;
            this.tiaokeform.sdate = this.getDatea(obj.start_time);
            this.tiaokeform.time = this.getTimes(obj.start_time);
            this.tiaokeform.sc = parseInt((obj.end_time - obj.start_time) / 60);
            this.getSubjectDetail(obj)
        },
        // 调课
        tiaokefunction: function (id) {
            var self = this;
            var h = self.tiaokeform.time.getHours();
            var m = self.tiaokeform.time.getMinutes();
            var val = h + ":" + m;
            if (!self.tiaokeform.sdate) {
                self.$message({
                    showClose: true,
                    message: "请选择上课日期",
                    type: "warning"
                })
                return;
            }
            if (self.tiaokeform.change_info.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入调课原因",
                    type: "warning"
                })
                return;
            }
            var formData = new FormData();
            formData.append("subject_id", self.tiaokeform.subject_id);
            formData.append("teacher_id", self.tiaokeform.teacher_id);
            formData.append("room_id", self.tiaokeform.room_id);
            formData.append("sdate", self.tiaokeform.sdate);
            formData.append("time", val);
            formData.append("sc", self.tiaokeform.sc);
            formData.append("change_info", self.tiaokeform.change_info);

            self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/" + self.tiaokeform.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.tiaokeshow = false;
                        self.tiaokeform.change_info = "";
                        self.getPaike();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.tiaokeshow = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //打开调课记录
        kechengFun: function (id) {
            this.kechengshow = true;
        }
    },
    created: function () {
        this.getClassroom();

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //上下课，调课
        this.yx_bjgl_my_kczt = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_kczt", "yx_bjgl_all", "yx"]);
        //考勤
        this.yx_bjgl_my_xskq = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_xskq", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less"; //我的课表 班级课表公共样式 月


.tk_tishi {
    font-size: 14px;
    color: @error;
}

.c_right_h1 {
    font-size: @h3;
    color: @c1;
    padding: 10px 0;
    font-weight: normal;
    margin-bottom: 10px;
    span {
        float: right;
        font-size: 14px;
        color: @c2;
        font-weight: normal;
        padding-top: 5px;
    }
}
</style>