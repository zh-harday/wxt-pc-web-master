<template>
    <div>
        <div class="content_two">
            <div class="c_left">
                <h1>排课日历</h1>
                <div class="timeboxs">
                    <span>当前时间</span>
                    <time>{{ nowtime | M_S }}</time>
                    <p>
                        <span>{{ nowtime | mm_dd }}</span>
                        <s>{{ nowtime | week }}</s>
                    </p>
                </div>
                <span>今日签到</span>
                <p>{{ allcount }}人次</p>
            </div>
            <div class="c_right">
                <h1 class="c_right_h1">
                    签到管理
                    <el-dropdown trigger="click" @command="CommandFunc">
                        <el-button type="success" size="small">
                            批量考勤<i class="el-icon-caret-bottom el-icon--right"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="1">签到</el-dropdown-item>
                            <el-dropdown-item command="2/1">签退-送达学校</el-dropdown-item>
                            <el-dropdown-item command="2/0">签退-离校</el-dropdown-item>
                            <el-dropdown-item command="3">接送通知</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </h1>
                <el-table :data="studentList.student" style="width: 100%" v-loading="loading" :element-loading-text="loadingtext" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="62">
                    </el-table-column>
                    <el-table-column prop="student_name" label="姓名">
                    </el-table-column>
                    <el-table-column prop="phone" label="联系电话" min-width="130"></el-table-column>
                    <el-table-column prop="count.qiandao" label="今日签到">
                    </el-table-column>
                    <el-table-column prop="count.qiantui" label="今日签退">
                    </el-table-column>
                    <el-table-column prop="count.qingjia" label="今日请假">
                    </el-table-column>
                    <el-table-column prop="count.qingjia" label="签到状态" width="100">
                        <template scope="scope">
                            <el-tag type="gray" v-if="scope.row.now.status == 2">已签退</el-tag>
                            <el-tag type="success" v-if="scope.row.now.status == 1">已签到</el-tag>
                            <el-tag type="warning" v-if="scope.row.now.status == 3">已请假</el-tag>
                            <el-tag type="danger" v-if="scope.row.now.status == 0">未到</el-tag>
                            <span v-if="!scope.row.now.status">未操作</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="now.remark" label="备注" min-width="200">
                        <template scope="scope">
                            <span style="color:#acacac">{{ (!scope.row.now.remark || scope.row.now.remark == '')?'无':scope.row.now.remark }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="操作" width="200">
                        <template scope="scope">
                            <div v-if="scope.row.now.status">
                                <el-button type="primary" size="mini" @click="qiandaoFun(scope.row)" v-if="scope.row.now.status == 2 || scope.row.now.status == 3 || scope.row.now.status == 0">签到</el-button>
                                <el-dropdown trigger="click" @command="handleCommand" v-if="scope.row.now.status == 1">
                                    <el-button type="info" size="mini">
                                        签退
                                        <i class="el-icon-caret-bottom el-icon--right"></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item :command="{type:1,data:scope.row}">送达学校</el-dropdown-item>
                                        <el-dropdown-item :command="{type:0,data:scope.row}">离校</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                                <el-button type="warning" size="mini" @click="qingjiaFun(scope.row)" v-if="scope.row.now.status == 2 || scope.row.now.status == 0">请假</el-button>
                                <el-button type="success" size="mini" @click="sendMessage(scope.row,true)">接送通知</el-button>
                            </div>
                            <div v-if="!scope.row.now.status">
                                <el-button type="primary" size="mini" @click="qiandaoFun(scope.row)">签到</el-button>
                                <el-button type="warning" size="mini" @click="qingjiaFun(scope.row)">请假</el-button>
                                <el-button type="success" size="mini" @click="sendMessage(scope.row,true)">接送通知</el-button>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="fenye">
                    <el-pagination v-show="studentList.count > studentList.student.length" @current-change="handleCurrentChange" :current-page="page" :page-size="limit" layout="total, prev, pager, next" :total="studentList.count">
                    </el-pagination>
                </div>
            </div>
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
        }
    },
    data: function () {
        return {
            loading: false,
            loadingtext: "加载中",
            page: 1,
            limit: 15,
            allcount: 0,
            nowtime: new Date().getTime() / 1000,
            studentList: {
                count: 0,
                student: []
            },
            timeout: null,
            pl_arr: [],
            index: 0
        }
    },
    methods: {
        handleSelectionChange: function (arr) {
            this.pl_arr = arr;
        },
        handleCurrentChange: function () { },
        //批量
        CommandFunc: function (val) {
            var self = this;
            if (self.pl_arr.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请勾选学生",
                    type: "warning"
                })
                return;
            }

            if (val == 3) {

                self.index = 0;
                self.loadingtext = "正在发送通知(" + self.index + "/" + self.pl_arr.length + ")";
                self.loading = true;
                self.sendMessage(self.pl_arr[self.index]);
            }

            if (val == 1) {
                self.index = 0;
                self.loadingtext = "正在签到(" + self.index + "/" + self.pl_arr.length + ")";
                self.loading = true;
                self.kaoqin({
                    cid: self.pl_arr[self.index].student_id,
                    status: 1,
                    status_info: "",
                    remark: "",
                    student_name: self.pl_arr[self.index].student_name
                }, { num: 1, type: '' })
            }

            if (val == '2/1' || val == '2/0') {
                var num = val.split("/")[0];
                var type = val.split("/")[1];
                self.index = 0;
                self.loadingtext = "正在签退(" + self.index + "/" + self.pl_arr.length + ")";
                self.loading = true;
                self.kaoqin({
                    cid: self.pl_arr[self.index].student_id,
                    status: num,
                    status_info: type,
                    remark: "",
                    student_name: self.pl_arr[self.index].student_name
                }, { num: num, type: type })
            }

        },
        //send message
        sendMessage: function (obj, once) {
            var self = this;
            var formData = new FormData();
            formData.append("type", "jstz");
            self.$http.post("/api/classs/" + self.$route.params.id + "/" + obj.student_id + "/tuoguan/notice", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (once) {
                            self.$store.commit("success", {
                                self: self,
                                msg: "已通知'" + obj.student_name + "'的家长",
                            });
                        }
                        self.loadingtext = "正在发送通知(" + self.index + "/" + self.pl_arr.length + ")";
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }

                    if (once) return;

                    //不管发送成功还是失败 都会进入下一个
                    if (self.index < self.pl_arr.length) {
                        self.sendMessage(self.pl_arr[self.index])
                        self.index++;
                    } else {
                        self.loading = false;
                        self.index = 0;
                        self.$store.commit("success", {
                            self: self,
                            msg: "通知发送完成",
                        });
                        return;
                    }

                }, function () {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //get now time fun
        getNowTime: function () {
            var self = this;
            this.nowtime = new Date().getTime() / 1000;
            self.timeout = setTimeout(self.getNowTime, 1000);
        },
        //获取今日考勤列表
        getStudentList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/tuoguan/today")
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        data.data.data.student.forEach(function (item) {
                            item.now = item.now ? item.now : {};
                            self.allcount += Number(item.count.qiandao);
                            return item;
                        })
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
        //kaoqin
        kaoqin: function (obj, num, once) {
            /*
                {
                    cid:1,
                    status:1,
                    status_info:"",
                    remark:"",
                    student_name:''
                }
            */
            var self = this;
            var formData = new FormData();
            formData.append("status", obj.status);
            formData.append("status_info", obj.status_info);
            formData.append("remark", obj.remark);

            self.$http.post("/api/classs/" + self.$route.params.id + "/" + obj.cid + "/tuoguan/attence", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (once) {
                            self.$store.commit("success", {
                                self: self,
                                msg: "'" + obj.student_name + "'考勤状态已更改",
                            });
                            self.getStudentList();
                        }
                        if (num.num == 1) {
                            self.loadingtext = "正在签到(" + (self.index + 1) + "/" + self.pl_arr.length + ")";
                        }
                        if (num.num == 2) {
                            self.loadingtext = "正在签退(" + (self.index + 1) + "/" + self.pl_arr.length + ")";
                        }
                    } else {
                        if (once) {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }

                    if (once) return;
                    self.index++;
                    //不管发送成功还是失败 都会进入下一个
                    if (self.index < self.pl_arr.length) {
                        self.kaoqin({
                            cid: self.pl_arr[self.index].student_id,
                            status: num.num,
                            status_info: num.type,
                            remark: "",
                            student_name: self.pl_arr[self.index].student_name
                        }, num)

                    } else {
                        self.loading = false;
                        self.index = 0;
                        self.$store.commit("success", {
                            self: self,
                            msg: "考勤状态已更改",
                        });
                        self.getStudentList();
                        return;
                    }

                }, function () {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //签退下拉菜单
        handleCommand: function (obj) {
            this.kaoqin({
                cid: obj.data.student_id,
                status: 2,
                status_info: obj.type,
                remark: obj.data.remark,
                student_name: obj.data.student_name
            }, { num: 2, type: '' }, true);
        },
        //签到
        qiandaoFun: function (obj) {
            this.kaoqin({
                cid: obj.student_id,
                status: 1,
                status_info: "",
                remark: "",
                student_name: obj.student_name
            }, { num: 1, type: '' }, true);
        },

        //请假
        qingjiaFun: function (obj) {
            var self = this;
            self.$prompt('请输入请假理由', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(function(value){
                if(!value.value || value.value.trim() == ''){
                    self.$message({
                        showClose: true,
                        message: "请输入请假理由",
                        type: "warning"
                    })
                    return;
                }
                self.kaoqin({
                    cid: obj.student_id,
                    status: 3,
                    status_info: "",
                    remark: value.value,
                    student_name: obj.student_name
                }, { num: 3, type: '' }, true);
            }).catch(function(){
                
            });
            
        }
    },
    created: function () {
        this.getNowTime();
        this.getStudentList();
    },
    destroyed: function () {
        clearTimeout(this.timeout);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less"; //我的课表 班级课表公共样式 月
.el-dropdown-menu {
    width: 110px;
}

.timeboxs {
    border-bottom: 1px dashed #ddd;
    padding: 24px 0 30px 0;
    >span {
        display: block;
        text-align: center;
        font-size: 14px;
        color: @c3;
        margin-bottom: 20px;
    }
    >time {
        display: block;
        text-align: center;
        font-size: 36px;
        color: @color;
        margin-bottom: 20px;
    }
    >p {
        overflow: hidden;
        font-size: 20px;
        color: @c2;
        span {
            display: block;
            width: 46%;
            float: left;
            text-align: right;
        }
        s {
            display: block;
            width: 46%;
            float: right;
            text-align: left;
            text-decoration: none;
        }
    }
}

.c_left {
    >span {
        display: block;
        text-align: center;
        font-size: 14px;
        color: @c3;
        margin-bottom: 10px;
        padding-top: 28px;
    }
    >p {
        text-align: center;
        font-size: 20px;
        color: @c2;
        padding-bottom: 30px;
    }
}

.c_right_h1 {
    font-size: @h3;
    color: @c1;
    padding: 10px 0;
    font-weight: normal;
    margin-bottom: 10px;
    .el-dropdown {
        float: right;
    }
}
</style>