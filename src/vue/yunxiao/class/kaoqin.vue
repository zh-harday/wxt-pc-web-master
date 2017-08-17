<template>
    <div>
        <!--考勤管理-->
        <el-dialog title="考勤管理" v-model="kaoqinshow">
            <el-tabs v-model="activeName" type="card">
                <el-tab-pane label="未处理" name="first">
                    <div class="kaoqinguanli_box">
                        <el-input placeholder="搜索" icon="search" v-model="KQsearch1" @input="kqsearchFun1" class="kqsearchinput">
                        </el-input>
                        <el-table :data="kaoqing_a" border style="width: 100%" @selection-change="handleSelectionChange" v-loading="loading1" element-loading-text="加载中">
                            <el-table-column type="selection" width="60">
                            </el-table-column>
                            <el-table-column prop="name" label="姓名">
                            </el-table-column>
                            <el-table-column prop="phone" label="联系电话">
                            </el-table-column>
                            <el-table-column prop="attence_status" label="签到状态">
                                <template scope="scope">
                                    <el-tag type="success" v-if="scope.row.attence_status == null || scope.row.attence_status == 0  ">待签到</el-tag>
                                    <el-tag type="warning" v-if="scope.row.attence_status == 4">预请假</el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="leave_info" label="备注">
                                <template scope="scope">
                                    <span v-if="scope.row.leave_time == null ">无</span>
                                    <span v-if="scope.row.leave_time != null ">{{ scope.row.leave_info }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column label="操作" width="220">
                                <template scope="scope">
                                    <el-button type="primary" size="mini" @click="xiugaiKQstuas(scope.row,1,1)">签到</el-button>
                                    <el-dropdown trigger="click" @command="handleCommand" v-if="classDetail.fee_method == 'frequency'">
                                        <el-button type="danger" size="mini">
                                            未到
                                            <i class="el-icon-caret-bottom el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_3_1'">扣课时</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_3_0'">不扣课时</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                     <el-button type="danger" size="mini" v-if="classDetail.fee_method == 'cycle'" @click="handleCommand(scope.row.sid+'_'+scope.row.name+'_3_0')">未到</el-button>
                                    <el-dropdown trigger="click" @command="handleCommand" v-if="classDetail.fee_method == 'frequency'">
                                        <el-button type="warning" size="mini">
                                            确认请假
                                            <i class="el-icon-caret-bottom el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_2_1'">扣课时</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_2_0'">不扣课时</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                    <el-button type="warning" size="mini" v-if="classDetail.fee_method == 'cycle'" @click="handleCommand(scope.row.sid+'_'+scope.row.name+'_2_0')">确认请假</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                        <div class="kaoqinbtn">
                            <el-button type="primary" @click="piliangqiandao">批量签到</el-button>
                        </div>
                    </div>
                </el-tab-pane>
                <el-tab-pane label="已处理" name="second">
                    <div class="kaoqinguanli_box">
                        <el-input placeholder="搜索" icon="search" v-model="KQsearch2" @input="kqsearchFun2" class="kqsearchinput">
                        </el-input>
                        <el-table :data="kaoqing_b" border style="width: 100%" v-loading="loading1" element-loading-text="加载中">
                            <el-table-column prop="name" label="姓名">
                            </el-table-column>
                            <el-table-column prop="phone" label="联系电话">
                            </el-table-column>
                            <el-table-column prop="attence_status" label="签到状态">
                                <template scope="scope">
                                    <el-tag type="success" v-if="scope.row.attence_status == 1 ">已签到</el-tag>
                                    <el-tag type="warning" v-if="scope.row.attence_status == 2 ">已请假</el-tag>
                                    <el-tag type="danger" v-if="scope.row.attence_status == 3 ">旷课</el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="is_xk" label="是否扣课时">
                                <template scope="scope">
                                    <el-tag type="danger" v-if="scope.row.is_xk == 1 && classDetail.fee_method == 'frequency' ">扣课时</el-tag>
                                    <el-tag type="warning" v-if="scope.row.is_xk == 0 && classDetail.fee_method == 'frequency' ">不扣课时</el-tag>
                                    <span v-if="classDetail.fee_method == 'cycle'">--</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="leave_info" label="备注">
                                <template scope="scope">
                                    <span v-if="scope.row.leave_time == null ">无</span>
                                    <span v-if="scope.row.leave_time != null ">{{ scope.row.leave_info }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column label="操作" width="100">
                                <template scope="scope">
                                    <el-dropdown trigger="click" @command="handleCommand" v-if="classDetail.fee_method == 'frequency'">
                                        <el-button type="primary" size="mini">
                                            修改考勤
                                            <i class="el-icon-caret-bottom el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_1_1'">已签到</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_2_0'">已请假-不扣课时</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_2_1'">已请假-扣课时</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_3_0'">未到-不扣课时</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_3_1'">未到-扣课时</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                    <el-dropdown trigger="click" @command="handleCommand" v-if="classDetail.fee_method == 'cycle'">
                                        <el-button type="primary" size="mini">
                                            修改考勤
                                            <i class="el-icon-caret-bottom el-icon--right"></i>
                                        </el-button>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_1_1'">已签到</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_2_0'">已请假</el-dropdown-item>
                                            <el-dropdown-item :command="scope.row.sid+'_'+scope.row.name+'_3_0'">未到</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    props: ["id", "cid", "changeid"],
    data: function () {
        return {
            //考勤管理
            loading1: false,
            kaoqinshow: false,
            //选项卡nameee
            activeName: "first",
            //考勤搜索12
            KQsearch1: "",
            KQsearch2: "",
            //已考勤学员
            kaoqing_a: [],
            kaoqing_as: [],
            //未考勤学员
            kaoqing_b: [],
            kaoqing_bs: [],
            //考勤多选
            multipleSelection: [],

            classDetail: {}
        }
    },
    methods: {
        //考勤管理搜索
        kqsearchFun1: function (val) {
            var self = this;
            self.kaoqing_a = [];
            for (var i = 0; i < self.kaoqing_as.length; i++) {
                if (self.kaoqing_as[i].name.indexOf(val.trim()) != -1) {
                    self.kaoqing_a.push(self.kaoqing_as[i])
                }
            }
            if (self.kaoqing_a.length == 0) {
                self.kaoqing_a = self.kaoqing_as;
            }
        },
        kqsearchFun2: function (val) {
            var self = this;
            self.kaoqing_b = [];
            for (var i = 0; i < self.kaoqing_bs.length; i++) {
                if (self.kaoqing_bs[i].name.indexOf(val.trim()) != -1) {
                    self.kaoqing_b.push(self.kaoqing_bs[i])
                }
            }
            if (self.kaoqing_b.length == 0) {
                self.kaoqing_b = self.kaoqing_bs;
            }
        },
        //多选学员
        handleSelectionChange(val) {
            this.multipleSelection = val;
        },
        //修改考勤 下拉
        handleCommand: function (command) {
            console.log(command)
            var sid = command.split("_")[0];
            var sname = command.split("_")[1];
            var a = command.split("_")[2];
            var b = command.split("_")[3];
            this.xiugaiKQstuas({ sid: sid, name: sname }, a, b);
        },
        //批量签到
        piliangqiandao: function () {
            var self = this;
            for (var i = 0; i < self.multipleSelection.length; i++) {
                self.xiugaiKQstuas(self.multipleSelection[i], 1, 1)
            }
        },
        //修改考勤状态
        xiugaiKQstuas: function (obj, a, b) {
            var self = this;
            var formData = new FormData();
            formData.append("student_id", obj.sid);
            formData.append("type", a);
            formData.append("xk", b);
            var cid = self.cid ? self.cid : self.$route.params.id;
            self.$http.post("/api/classs/" + cid + "/curriculum/" + self.subject_id + "/attence", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: obj.name + "签到状态已更改",
                        });
                        self.kaoqinguanli(self.subject_id);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //考勤管理
        kaoqinguanli: function (id) {
            var self = this;
            // self.kaoqinshow = true;
            self.loading1 = true;
            self.kaoqing_a = [];
            self.kaoqing_as = [];
            self.kaoqing_b = [];
            self.kaoqing_bs = [];
            self.subject_id = id;
            var cid = self.cid ? self.cid : self.$route.params.id;
            self.$http.get("/api/classs/" + cid + "/curriculum/" + id + "/attence")
                .then(function (data) {
                    self.loading1 = false;
                    if (data.data.status == 'ok') {
                        self.kaoqing_a = [];
                        self.kaoqing_as = [];
                        self.kaoqing_b = [];
                        self.kaoqing_bs = [];
                        for (var i = 0; i < data.data.data.student.length; i++) {
                            console.log(data.data.data.student[i].attence_status)
                            if (data.data.data.student[i].attence_status && data.data.data.student[i].attence_status != 0 && data.data.data.student[i].attence_status != 4) {
                                self.kaoqing_b.push(data.data.data.student[i]);
                                self.kaoqing_bs.push(data.data.data.student[i]);
                            } else {
                                self.kaoqing_a.push(data.data.data.student[i]);
                                self.kaoqing_as.push(data.data.data.student[i]);
                            }
                        }
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
        //班级详情
        getClassDetail: function () {
            var self = this;
            var cid = self.cid ? self.cid : self.$route.params.id;
            if(!cid)return;
            self.$http.get("/api/classs/" + cid)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classDetail = data.data.data;
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
    watch: {
        changeid: function () {
            if (this.id != "") {
                this.kaoqinshow = true;
                this.kaoqinguanli(this.id);
                this.getClassDetail();
            }
        }
    },
    created: function () {
        
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.el-dropdown-menu {
    width: 125px;
}

.kaoqinguanli_box {
    overflow: hidden;
    .kqsearchinput {
        width: auto;
        margin-bottom: 15px;
        float: right;
    }
}

.kaoqinbtn {
    text-align: left;
    padding-top: 20px;
}
</style>