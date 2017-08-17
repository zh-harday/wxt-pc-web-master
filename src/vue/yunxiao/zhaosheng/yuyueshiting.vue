<template>
    <div>
        <h1 class="right_title">
            预约试听管理
            <br>
            <el-button type="success" size="small" @click="openAddfun">添加试听课</el-button>
            <el-input placeholder="请输入试听课名称" icon="search" v-model="option.search" @keyup.enter.native="serachFun" :on-icon-click="serachFun" class="btn"></el-input>
        </h1>
        <div class="xy_serach_box">
            <div class="saixuan_box" v-if="myMessage.campus_id == 1">
                <span>校区：</span>
                <ul>
                    <li :class="{active:(option.campus_id == '')}" @click="canpus_click('')">全部</li>
                    <li :class="{active:(option.campus_id == item.id )}" v-for="item in campus" @click="canpus_click(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>课程状态：</span>
                <ul>
                    <li :class="{active: option.status == ''}" @click="clickStaus('')">全部</li>
                    <li :class="{active: option.status == '0'}" @click="clickStaus('0')">未上课</li>
                    <li :class="{active: option.status == '1'}" @click="clickStaus('1')">已上课</li>
                </ul>
            </div>
            <div class="saixuan_box last">
                <span>其他：</span>
                <el-select v-model="option.subject_id" placeholder="请选择预报科目" size="small" @change="changeSubject" filterable>
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="list.name" :value="list.id" v-for="list in YBsubjectList"></el-option>
                </el-select>
                <el-select v-model="option.teacher_id" placeholder="请选择代课老师" size="small" @change="changeSubject" filterable>
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="list.name" :value="list.id" v-for="list in ygSimple"></el-option>
                </el-select>
                <el-date-picker @change="changetime" size="small" v-model="option.time" type="daterange" :picker-options="pickerOptions2" placeholder="选择试听课时间" align="left">
                </el-date-picker>
            </div>
        </div>
    
        <el-table :data="shitingkeList.lecture" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="title" label="试听课名称">
            </el-table-column>
            <el-table-column prop="campus_name" label="所属校区">
            </el-table-column>
            <el-table-column prop="subject_name" label="试听科目">
            </el-table-column>
            <el-table-column prop="teacher_name" label="代课老师">
            </el-table-column>
            <el-table-column prop="room_name" label="上课教室">
            </el-table-column>
            <el-table-column prop="start_time" label="上课日期" min-width="200">
                <template scope="scope">
                    {{ scope.row.start_time | yyyy_mm_dd_M_S_week }}
                </template>
            </el-table-column>
            <el-table-column prop="status" label="课程状态">
                <template scope="scope">
                    <span v-if="scope.row.status == 0">未上课</span>
                    <span v-if="scope.row.status == 1">已上课</span>
                </template>
            </el-table-column>
            <el-table-column prop="student_count" label="预约人数">
            </el-table-column>
            <el-table-column prop="attendance_count" label="到会人数">
            </el-table-column>
            <el-table-column label="操作" width="220">
                <template scope="scope">
                    <el-button type="success" size="mini" @click="openSTXQ(scope.row.id)">查看详情</el-button>
                    <el-button type="warning" size="mini" @click="bianjiFun(scope.row)">编辑</el-button>
                    <el-button type="danger" size="mini" @click="delSTK(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-if="shitingkeList.count > shitingkeList.lecture.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="option.page" :page-size="option.limit" layout="total, prev, pager, next" :total="shitingkeList.count">
            </el-pagination>
        </div>
    
        <!--详情-->
        <el-dialog title="试听详情" v-model="shitingxq">
            <p class="fengexian"></p>
            <el-table :data="shitingkeDetailList.booking" style="width: 100%">
                <el-table-column prop="name" label="姓名">
                </el-table-column>
                <el-table-column prop="intention_status" label="跟进状态">
    
                </el-table-column>
                <el-table-column prop="status" label="到会状态">
                    <template scope="scope">
                        <el-tag type="success" v-if="scope.row.status == 1">已到</el-tag>
                        <el-tag type="warning" v-if="scope.row.status == 0">未到</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="staff_name" label="邀约人">
                </el-table-column>
                <el-table-column label="操作" width="220">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="qiandaoyuyue(scope.row.id)" v-if="scope.row.status == 0">标记到会</el-button>
                        <el-button type="danger" size="mini" @click="yichushitingke(scope.row.id)">移除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="shitingkeDetailList.count > shitingkeDetailList.booking.length">
                <el-pagination @current-change="stXQpageClick" :current-page="stoption.page" :page-size="stoption.limit" layout="total, prev, pager, next" :total="shitingkeDetailList.count">
                </el-pagination>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="shitingxq = false">取 消</el-button>
                <el-button type="primary" @click="shitingxq = false">确 定</el-button>
            </span>
        </el-dialog>
    
        <!--添加试听课 编辑-->
        <el-dialog :title="stTitle" v-model="addSubject" class="addSubject">
            <p class="fengexian"></p>
            <el-form :model="stform" label-width="90px">
                <el-row :gutter="50">
                    <el-col :span="12">
                        <el-form-item label="选择校区">
                            <el-select v-model="stform.campus_id" placeholder="请选择校区" @change="changeCampus" filterable v-if="myMessage.campus_id == 1 && stTitle != '修改试听课'">
                                <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                            </el-select>
                            <el-select v-model="stform.campus_id" placeholder="请选择校区" @change="changeCampus" v-if="myMessage.campus_id != 1 || stTitle == '修改试听课'" disabled>
                                <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="上课教室">
                            <el-select v-model="stform.room_id" placeholder="请选择上课教室" filterable>
                                <el-option :label="item.room_name" :value="item.id" v-for="item in classroom"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <el-row :gutter="50">
                    <el-col :span="12">
                        <el-form-item label="代课老师">
                            <el-select v-model="stform.teacher_id" placeholder="请选择代课老师" filterable>
                                <el-option :label="item.name" :value="item.id" v-for="item in ygSimple"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="上课时间">
                            <el-date-picker v-model="stform.start_time" type="datetime" placeholder="选择日期时间">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="50">
                    <el-col :span="12">
                        <el-form-item label="预报科目">
                            <el-select v-model="stform.subject_id" placeholder="请选择预报科目">
                                <el-option :label="item.name" :value="item.id" v-for="item in YBsubjectList"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="试听课名称">
                            <el-input v-model="stform.title" placeholder="请输入试听课名称"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="addSubject = false">取消</el-button>
                <el-button type="primary" @click="addSubjectFUn" v-if="stTitle == '添加试听课' ">添加</el-button>
                <el-button type="primary" @click="addSubjectFUn" v-else>保存</el-button>
            </span>
        </el-dialog>
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
        }
    },
    data: function () {
        return {
            formLabelWidth: "75px",
            shitingxq: false,
            addSubject: false,
            loading: false,
            stTitle: "添加试听课",
            //教室列表
            classroom: [],

            //预报科目
            YBsubjectList: window.sessionStorage.getItem("YBsubjectList") ? JSON.parse(window.sessionStorage.getItem("YBsubjectList")) : [],
            //编辑添加form
            stform: {
                id: "",
                campus_id: "",
                room_id: "",
                teacher_id: "",
                start_time: "",
                subject_id: "",
                title: ""
            },
            //试听课列表
            shitingkeList: {
                lecture: []
            },
            //试听课详情 列表
            shitingkeDetailList: {
                booking: []
            },
            //试听课蚕食
            stoption: {
                page: 1,
                limit: 10,
                num: "",
            },
            //筛选试听参数
            option: {
                page: 1,
                limit: 10,
                search: "",
                campus_id: "",
                status: "",
                subject_id: "",
                teacher_id: "",
                start_time: "",
                end_time: "",
                time: ""
            },

            ch_campus_id: "",

            pickerOptions2: {
                shortcuts: [{
                    text: '最近一周',
                    onClick: function (picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近一个月',
                    onClick: function (picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近三个月',
                    onClick: function (picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            }

        }
    },
    methods: {
        //时间
        changetime: function (val) {
            if (val == "") {
                this.option.start_time = "";
                this.option.end_time = "";
                this.getShitingList();
                return;
            }
            var starttime = val.split(" - ")[0].split("-");
            var endtime = val.split(" - ")[1].split("-");
            this.option.start_time = new Date(starttime[0], starttime[1], starttime[2]).getTime() / 1000;
            this.option.end_time = new Date(endtime[0], endtime[1], endtime[2]).getTime() / 1000;
            this.option.search = "";
            this.option.page = 1;
            this.getShitingList();
        },
        //标记到会
        qiandaoyuyue: function (id) {
            var self = this;
            self.$http.post("/api/lecture/" + self.stoption.num + "/booking/" + id + "/check_in")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.stoption.page = 1;
                        self.getShitingDetail();
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
        //移除试听课
        yichushitingke: function (id) {
            var self = this;
            self.$http.post("/api/lecture/" + self.stoption.num + "/booking/" + id + "/del")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.stoption.page = 1;
                        self.getShitingDetail();
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
        //试听详情分页
        stXQpageClick: function (val) {
            this.stoption.page = val;
            this.getShitingDetail();
        },
        //打开试听详情
        openSTXQ: function (id) {
            var self = this;
            self.shitingxq = true;
            self.stoption.num = id;
            self.getShitingDetail();
        },
        //试听课详情
        getShitingDetail: function () {
            var self = this;
            self.$http.get("/api/lecture/" + self.stoption.num + "/booking?limit=" + self.stoption.limit + "&page=" + self.stoption.page)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.shitingkeDetailList = data.data.data;
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
        //获取试听课列表
        getShitingList: function () {
            var self = this;
            self.loading = true;
            console.log(self.option.page)
            self.option.campus_id = self.myMessage.campus_id == "1" ? self.option.campus_id : self.myMessage.campus_id;
            var url = "/api/lecture?limit=" + self.option.limit + "&page=" + self.option.page + "&search=" + self.option.search + "&campus_id=" + self.option.campus_id + "&status=" + self.option.status + "&subject_id=" + self.option.subject_id + "&teacher_id=" + self.option.teacher_id + "&start_time=" + self.option.start_time + "&end_time=" + self.option.end_time;
            self.$http.get(url)
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
        //添加 修改试听课
        addSubjectFUn: function () {
            var self = this;
            var url = self.stTitle == "修改试听课" ? ("/api/lecture/" + self.stform.id) : "/api/lecture";

            var times;
            if (typeof self.stform.start_time == 'object') {
                times = self.stform.start_time.getTime() / 1000;
            } else {
                if (self.stform.start_time == '') {
                    times = 0;
                } else {
                    times = new Date(self.stform.start_time.split("/")[0], self.stform.start_time.split("/")[1], self.stform.start_time.split("/")[2]).getTime() / 1000;
                }
            }

            var formData = new FormData();

            for (var k in self.stform) {
                if (k == 'start_time') {
                    formData.append(k, times);
                } else {
                    formData.append(k, self.stform[k]);
                }
            }
            self.$http.post(url, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.addSubject = false;
                        if (self.stTitle == "修改试听课") {
                            self.getShitingList();
                        } else {
                            self.option = {
                                page: 1,
                                limit: 10,
                                search: "",
                                campus_id: "",
                                status: "",
                                subject_id: "",
                                teacher_id: "",
                                start_time: "",
                                end_time: "",
                                time: ""
                            };
                            self.getShitingList();
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

        //选择校区  改变教室
        changeCampus: function (val) {
            var self = this;
            self.getClassroom(val)
            self.$store.commit("getYGList", {
                self: self,
                campus_id: val,
                is_stk: 1
            })
        },

        //获取教室列表
        getClassroom: function (id) {
            var self = this;
            self.$http.get("/api/room")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        var arr = [];
                        if (id == "1") {
                            self.classroom = data.data.data;
                        } else {
                            for (var i = 0; i < data.data.data.length; i++) {
                                if (data.data.data[i].campus_id == id) {
                                    arr.push(data.data.data[i]);
                                }
                            }
                            self.classroom = arr;
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
        //开打编辑
        bianjiFun: function (obj) {
            this.stTitle = "修改试听课";
            this.addSubject = true;


            for (var k in this.stform) {
                this.stform[k] = obj[k];
            }
            var t = this.stform.start_time * 1000;
            this.stform.start_time = new Date(t);

            this.getYBsubject();
            this.getClassroom(obj.campus_id);

            var self = this;
            self.$store.commit("getYGList", {
                self: self,
                campus_id: obj.campus_id,
                is_stk: 1
            })

        },
        //打开添加试听课
        openAddfun: function () {
            var self = this;
            this.stTitle = "添加试听课";
            this.addSubject = true;
            this.getYBsubject();

            for (var k in this.stform) {
                this.stform[k] = "";
            }

            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.stform.campus_id = campus_id;
        },
        //删除试听课
        delSTK: function (id) {
            var self = this;
            self.$confirm('是否确认删除这条试听课记录?', '删除试听课记录', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/lecture/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.option.page = 1;
                            self.option.search = "";
                            self.getShitingList();
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
        //点击课程状态
        clickStaus: function (id) {
            this.option.status = id;
            this.option.search = "";
            this.option.page = 1;
            this.getShitingList();
        },
        //点击校区
        canpus_click: function (id) {
            var self = this;
            self.option.campus_id = id;
            self.option.search = "";
            self.option.page = 1;
            self.getShitingList();
            self.$store.commit("getYGList", {
                self: self,
                campus_id: id,
                is_stk: 1
            })
        },
        //改变 预报科目 和代课老师
        changeSubject: function () {
            this.option.search = "";
            this.option.page = 1;
            this.getShitingList();
        },
        //点击页码
        handleCurrentChange: function (val) {
            this.option.page = val;
            this.getShitingList();
        },
        //搜索
        serachFun: function () {
            this.option.page = 1;
            this.option.campus_id = "";
            this.option.status = "";
            this.option.subject_id = "";
            this.option.teacher_id = "";
            this.getShitingList();
        }
    },
    created: function () {
        var self = this;
        this.getShitingList();
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.stform.campus_id = campus_id;

        self.getClassroom(self.myMessage.campus_id);

        self.$store.commit("getYGList", {
            self: self,
            campus_id: campus_id,
            is_stk: 1
        })

        setTimeout(function(){
            //判断是否从工作看板进入
            if(self.$route.query.add){
                self.openAddfun();
            }
        },1000)

        //权限设置
        var yx_zszz_yyst = this.pdqx(["yx_zszz", "yx_zszz_yyst", "yx"]);
        if (!yx_zszz_yyst) {
            this.$router.push({ name: 'worktody' });
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.addSubject {
    .el-select {
        margin-right: 0;
        width: 100%;
    }
    .el-date-editor {
        width: 100%;
    }
}

.el-select {
    margin-right: 10px;
    width: 140px;
}

.btn {
    width: 200px;
}

.el-date-editor {
    width: 140px;
}
</style>