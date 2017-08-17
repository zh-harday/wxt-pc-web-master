<template>
    <div>
        <!--学员列表-->
        <el-table :data="classStudentList.student" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="number" label="学号">
            </el-table-column>
            <el-table-column prop="name" label="学员姓名">
            </el-table-column>
            <el-table-column prop="sex" label="性别">
            </el-table-column>
            <el-table-column prop="phone" label="联系方式">
            </el-table-column>
            <el-table-column prop="add_class_time" label="报名时间" v-if="$route.name != 'studentlist1'">
                <template scope="scope">
                    {{ scope.row.add_class_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column prop="start_time" label="报名时间" v-if="$route.name == 'studentlist1'">
                <template scope="scope">
                    {{ scope.row.start_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column prop="add_class_time" label="到期时间" v-if="$route.name == 'studentlist1'">
                <template scope="scope">
                    {{ scope.row.end_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column prop="buy_quantity" label="报名期次" v-if="$route.name != 'studentlist1'">
            </el-table-column>
            <el-table-column prop="kx" label="已消课时" v-if="$route.name != 'studentlist1'">
            </el-table-column>
            <el-table-column prop="count.qiandao" label="签到次数" v-if="$route.name == 'studentlist1'">
            </el-table-column>
            <el-table-column prop="count.qiantui" label="签退次数" v-if="$route.name == 'studentlist1'">
            </el-table-column>
            <el-table-column prop="count.qingjia" label="请假次数" v-if="$route.name == 'studentlist1'">
            </el-table-column>
            <el-table-column prop="leave_count" label="请假次数" v-if="$route.name != 'studentlist1'">
            </el-table-column>
            <!--<el-table-column prop="" label="平均课单价">
                                                                                </el-table-column>-->
            <el-table-column prop="student_status" label="状态" v-if="$route.name != 'studentlist1'">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.student_status == 1">正常</el-tag>
                    <el-tag type="danger" v-if="scope.row.student_status == 0">停课</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="320" v-if="$route.name != 'studentlist1'">
                <template scope="scope">
                    <el-button type="primary" size="mini" v-if="yx_bjgl_my_qtxx && classDetail.fee_method != 'cycle'" @click="tiaozhengkeci(scope.row)">调整课次</el-button>
                    <el-button type="warning" size="mini" @click="tingkeFun(scope.row.id,0,scope.row.name)" v-if=" yx_bjgl_my_qtxx && scope.row.student_status == 1 ">停课</el-button>
                    <el-button type="primary" size="mini" @click="tingkeFun(scope.row.id,1,scope.row.name)" v-if=" yx_bjgl_my_qtxx && scope.row.student_status == 0  ">复课</el-button>
                    <el-button type="primary" size="mini" @click="openzhuanbanFun(scope.row)" v-if="yx_bjgl_my_qtxx">转班</el-button>
                    <el-button type="info" size="mini" @click="xufeiFun(scope.row.id)" v-if="yx_cwgl_jf && classDetail.fee_method != 'cycle' ">续费</el-button>
                    <el-button type="danger" size="mini" @click="delxueyuanFun(scope.row.id,scope.row.name)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="260" v-if="$route.name == 'studentlist1'">
                <template scope="scope">
                    <el-button type="primary" size="mini" v-if="yx_bjgl_my_qtxx && classDetail.fee_method == 'month'" @click="tgtiaoke(scope.row)">调课</el-button>
                    <el-button type="primary" size="mini" @click="openzhuanbanFun(scope.row)" v-if="yx_bjgl_my_qtxx">转班</el-button>
                    <el-button type="info" size="mini" @click="tgxufeiFun(scope.row)" v-if="yx_cwgl_jf">续费</el-button>
                    <el-button type="danger" size="mini" @click="delxueyuanFun(scope.row.id,scope.row.name)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
    
        <!--分页-->
        <div class="fenye" v-if="classStudentList.count > classStudentList.student.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="pageSize" layout="total, prev, pager, next" :total="classStudentList.count">
            </el-pagination>
        </div>
        <!--普通 转班-->
        <zhuanban :classmsg="classmsg" :changetime="changetime" @change="zhuanbanchange"></zhuanban>
        <!--续费-->
        <el-dialog title="续费" v-model="xuefeishow" size="small">
            <el-form ref="form" :model="xuefeiform" label-position="top">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="当前班级">
                            {{ classDetail.class_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="计费方式">
                            {{ classDetail.fee_method | fee_method }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="单价">
                            {{ classDetail.price }}元
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="续报次数">
                            <el-input v-model="xuefeiform.count" type="number" min="1"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="应缴金额">
                            {{ (xuefeiform.count*classDetail.price).toFixed(2) }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="实缴金额">
                            <el-input v-model="xuefeiform.sj" type="number" min="0"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="收款人">
                            <el-input v-model="myMessage.name" disabled></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="收款账户">
                            <el-select v-model="xuefeiform.caiwuzh" placeholder="请选择账户">
                                <el-option v-for="item in finance" :label="item.account_name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="收款时间">
                            <el-date-picker v-model="xuefeiform.nowTime" type="date" placeholder="选择日期">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="备注">
                    <el-input v-model="xuefeiform.remark" type="textarea"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="xuefeishow = false">取消</el-button>
                <el-button type="primary" @click="querenxufeiFun">确定续费</el-button>
            </span>
        </el-dialog>
        <!--续费-->
        <el-dialog title="续费" v-model="tgxuefeishow" size="small">
            <el-form ref="form" :model="xuefeiform" label-position="top">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="当前班级">
                            {{ classDetail.class_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="开始日期">
                            {{ tgstudent.start_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="结束日期">
                            {{ tgstudent.end_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="单价">
                            {{ classDetail.price }}/月
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="续报次数">
                            <el-input v-model="xuefeiform.count" type="number" min="1"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="到期时间">
                            <el-date-picker v-model="starttime" type="date" placeholder="选择日期" :picker-options="pickerOptions0">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="应缴金额">
                            {{ (xuefeiform.count*classDetail.price).toFixed(2) }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="实缴金额">
                            <el-input v-model="xuefeiform.sj" type="number" min="0"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="收款人">
                            <el-input v-model="myMessage.name" disabled></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="收款账户">
                            <el-select v-model="xuefeiform.caiwuzh" placeholder="请选择账户">
                                <el-option v-for="item in finance" :label="item.account_name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="收款时间">
                            <el-date-picker v-model="xuefeiform.nowTime" type="date" placeholder="选择日期">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="备注">
                    <el-input v-model="xuefeiform.remark" type="textarea"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="tgxuefeishow = false">取消</el-button>
                <el-button type="primary" @click="querenxufeiFun">确定续费</el-button>
            </span>
        </el-dialog>
    
        <!--调课次 普通班级-->
        <el-dialog title="调整课次" v-model="kecishow" size="tiny" custom-class="setwith">
            <el-form ref="form_kc" :model="kc" label-position="left" class="tiaokebox">
                <p class="fengexian"></p>
                <h1>{{ kc.name }}</h1>
                <p>{{ kc.number }}</p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="报名课次">
                            {{ kc.buy_quantity }}次
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="已消课次">
                            {{ kc.kx }}次
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="剩余课次">
                            {{ kc.buy_quantity - kc.kx }}次
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian1"></p>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="数量">
                            <el-input v-model="kc.quantity" max-length="50" size="small"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12" class="chengeaddcut">
                        <el-radio-group v-model="kc.type">
                            <el-radio label="add">增加课次</el-radio>
                            <el-radio label="cut">减少课程</el-radio>
                        </el-radio-group>
                    </el-col>
                </el-row>
                <el-form-item label="" style="padding-top:10px">
                    <el-input v-model="kc.remark" type="textarea" :rows="2" placeholder="请输入调课原因"></el-input>
                </el-form-item>
    
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="kecishow = false">取 消</el-button>
                <el-button type="primary" @click="kctzfun">确 定</el-button>
            </span>
        </el-dialog>
    
        <!-- 调课 托管班级 -->
        <el-dialog title="调整到期时间" v-model="tgkecishow" size="tiny" custom-class="setwith">
            <el-form ref="form_kc" :model="kc" label-position="left" class="tiaokebox">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="学员姓名">
                            {{ tgkc.name }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="10">
                        <el-form-item label="入班时间">
                            {{ tgkc.start_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="到期时间">
                            {{ tgkc.end_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian1"></p>
                <el-row :gutter="20">
                    <el-col :span="24" class="chengeaddcut">
                        <el-form-item label="调整后到期时间">
                            <el-date-picker v-model="tgkc.end_times" type="date" placeholder="选择日期" :picker-options="pickerOptions0">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="" style="padding-top:10px">
                    <el-input v-model="tgkc.remark" type="textarea" :rows="2" placeholder="请输入调整原因"></el-input>
                </el-form-item>
    
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="tgkecishow = false">取 消</el-button>
                <el-button type="primary" @click="tgkctzfun">确 定</el-button>
            </span>
        </el-dialog>
    
    </div>
</template>
<script>
module.exports = {
    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        campus: function () {
            return this.$store.state.campus;
        }
    },
    data: function () {
        return {
            loading: false,
            kecishow: false,
            tgkecishow: false,
            xuefeishow: false,
            tgxuefeishow: false,
            student_id: "",
            current_page: 1,
            pageSize: 15,
            kc: {
                sid: "",
                name: "",
                number: "",
                buy_quantity: "",
                kx: "",
                type: "add",
                quantity: 0,
                remark: ''
            },

            tgkc: {
                sid: "",
                name: "",
                number: "",
                start_time: "",
                end_time: "",
                end_times: "",
                remark: ''
            },

            tgstudent: {},
            starttime: "0",

            pickerOptions0: {
                disabledDate: function (time) {
                    return time.getTime() < new Date().getTime();
                }
            },

            xuefeiform: {
                caiwuzh: "",
                nowTime: new Date().getTime(),
                count: 1,
                sj: 0,
                remark: ""
            },
            student_id: "",
            finance: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : [],
            classStudentList: {
                student: []
            },
            classDetail: {},

            //转班相关
            changetime: 0,
            classmsg: {
                class_id: "",
                student_id: "",
                class_name: "",
                class_fee_method: "",
                count: "",//剩余课次
                campus_id: "",
                start_time: "",
                end_time: "",
                class_type: "",
                student_name: ""
            },

            //权限
            yx_cwgl_jf: false,

            yx_bjgl_my_view: false,
            yx_bjgl_my_qtxx: false

        }
    },
    methods: {
        //托管调课
        tgtiaoke: function (obj) {
            this.tgkecishow = true;
            this.tgkc.sid = obj.id;
            this.tgkc.name = obj.name;
            this.tgkc.number = obj.number;
            this.tgkc.start_time = obj.start_time;
            this.tgkc.end_time = obj.end_time;
        },
        //托管班级调课确定
        tgkctzfun: function () {
            var self = this;
            if (self.tgkc.end_times == '') {
                self.$message({
                    showClose: true,
                    message: "请选择调整后时间",
                    type: "warning"
                })
                return;
            }
            if (self.tgkc.remark.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入调整原因",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();
            formData.append("end_time", new Date(self.tgkc.end_times).getTime() / 1000);
            formData.append("remark", self.tgkc.remark);

            self.$http.post("/api/classs/" + self.$route.params.id + "/" + self.tgkc.sid + "/tuoguan/extime", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.tgkc = {
                            sid: "",
                            name: "",
                            number: "",
                            start_time: "",
                            end_time: "",
                            end_times: "",
                            remark: ''
                        }
                        self.tgkecishow = false;
                        self.getClassStudentList();
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
        //普通班级调课确定
        kctzfun: function () {
            var self = this;

            if (!isNaN(parseInt(self.kc.quantity))) {
                if (parseInt(self.kc.quantity) <= 0) {
                    self.$message({
                        showClose: true,
                        message: "调整课程必须大于0",
                        type: "warning"
                    })
                    return;
                }
                if (parseInt(self.kc.quantity) > (self.kc.buy_quantity - self.kc.kx) && self.kc.type == 'cut') {
                    self.$message({
                        showClose: true,
                        message: "减少的课次不能小于剩余课次",
                        type: "warning"
                    })
                    return;
                }
            } else {
                self.$message({
                    showClose: true,
                    message: "输入的剩余课次不正确",
                    type: "warning"
                })
                return;
            }

            if (self.kc.remark.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入调课原因",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();
            formData.append("quantity", self.kc.quantity);
            formData.append("remark", self.kc.remark);
            formData.append("type", self.kc.type);

            self.$http.post("/api/classs/" + self.$route.params.id + "/student/" + self.kc.sid + "/edit", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.kc = {
                            sid: "",
                            name: "",
                            number: "",
                            buy_quantity: "",
                            kx: "",
                            type: "add",
                            quantity: 0,
                            remark: ''
                        }
                        self.kecishow = false;
                        self.getClassStudentList();
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
        //调整课次
        tiaozhengkeci: function (data) {
            this.kc.name = data.name;
            this.kc.number = data.number;
            this.kc.buy_quantity = data.buy_quantity;
            this.kc.kx = data.kx;
            this.kc.sid = data.id;
            this.kecishow = true;
        },
        //续费
        querenxufeiFun: function () {
            var self = this;
            var nowTime;
            if (self.xuefeiform.count < 1) {
                self.$message({
                    showClose: true,
                    message: "续保次数大于1",
                    type: 'warning'
                });
                return;
            }

            if (self.starttime == '0' && self.classDetail.class_type == '1') {
                self.$message({
                    showClose: true,
                    message: "请选择到期时间",
                    type: 'warning'
                });
                return;
            }

            if (self.xuefeiform.caiwuzh == '') {
                self.$message({
                    showClose: true,
                    message: "请选择收费账户",
                    type: 'warning'
                });
                return;
            }

            if (typeof self.xuefeiform.nowTime == 'number') {
                nowTime = self.xuefeiform.nowTime / 1000;
            } else {
                nowTime = self.xuefeiform.nowTime.getTime() / 1000;
            }
            var formData = new FormData();
            formData.append("quantity", self.xuefeiform.count);
            formData.append("actual", self.xuefeiform.sj);
            formData.append("payee_time", nowTime);
            formData.append("account", self.xuefeiform.caiwuzh);
            formData.append("remark", self.xuefeiform.remark);
            formData.append("fee_mode", 0);

            var url = "";

            if (self.classDetail.class_type == '1') {
                url = "/api/classs/" + self.classDetail.id + "/" + self.student_id + "/tuoguan/renewal/";
                formData.append("end_time", new Date(self.starttime).getTime() / 1000);
            }
            if (self.classDetail.class_type == '0') {
                url = "/api/student/" + self.student_id + "/renewal/" + self.classDetail.id;
            }

            if (self.xuefeiform.sj == 0 || !Number(self.xuefeiform.sj)) {
                self.$confirm('实际缴费金额为0或无效，是否继续？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.$http.post(url, formData)
                        .then(function (data) {
                            if (data.data.status == 'ok') {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: data.data.msg,
                                });
                                self.getClassStudentList();
                                self.xuefeiform = {
                                    caiwuzh: "",
                                    nowTime: new Date().getTime(),
                                    count: 1,
                                    sj: 0,
                                    remark: ""
                                }
                                self.starttime = "0";
                                self.xuefeishow = false;
                                self.tgxuefeishow = false;
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
                }).catch(function () {
                    self.xuefeishow = false;
                    self.tgxuefeishow = false;
                    self.$message({
                        type: 'warning',
                        message: '已放弃缴费'
                    });
                });
                return;
            }

            self.$http.post(url, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getClassStudentList();
                        self.xuefeiform = {
                            caiwuzh: "",
                            nowTime: new Date().getTime(),
                            count: 1,
                            sj: 0,
                            remark: ""
                        }
                        self.starttime = "0";
                        self.xuefeishow = false;
                        self.tgxuefeishow = false;
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
        //获取财务列表
        getFinance: function () {
            var self = this;
            self.$http.get("/api/finance/account")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            for (var j = 0; j < self.campus.length; j++) {
                                if (self.campus[j].id == data.data.data[i].campus_id) {
                                    data.data.data[i].campus_name = self.campus[j].name;
                                    break;
                                }
                                if (data.data.data[i].campus_id == 1) {
                                    data.data.data[i].campus_name = "所有校区";
                                }
                            }
                        }
                        self.finance = data.data.data;
                        window.sessionStorage.setItem("finances", JSON.stringify(self.finance))
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
            this.current_page = val;
            this.getClassStudentList();
        },
        //班级详情
        getClassDetail: function () {
            var self = this;
            if (!self.classDetail.id) {
                self.$http.get("/api/classs/" + self.$route.params.id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.classDetail = data.data.data;
                            //初始化转班数据
                            self.classmsg.class_id = data.data.data.id;
                            self.classmsg.class_name = data.data.data.class_name;
                            self.classmsg.class_fee_method = data.data.data.fee_method;
                            self.classmsg.campus_id = data.data.data.campus_id;
                            self.classmsg.class_type = data.data.data.class_type;
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
        //班级学员列表
        getClassStudentList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/student?&page=" + self.current_page + "&limit=" + self.pageSize)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.classStudentList = data.data.data;
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
        //停课
        tingkeFun: function (id, status, name) {
            var self = this;
            var title = status == 1 ? "复课" : "停课"
            self.$confirm('是否要对学员进行' + title + '操作？', title, {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                var formData = new FormData();
                formData.append("status", status);
                self.$http.post("/api/classs/" + self.$route.params.id + "/student/" + id + "/status", formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            if (status == 0) {

                                self.$store.commit("success", {
                                    self: self,
                                    msg: name + "已停课",
                                });
                            }

                            if (status == 1) {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: name + "已复课",
                                });
                            }
                            self.getClassStudentList();
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
        //打开转班
        openzhuanbanFun: function (obj) {
            var self = this;
            if (self.classmsg.campus_id == "" || self.classmsg.class_id == "" || self.classmsg.class_name == "" || self.classmsg.class_fee_method == "") {
                self.$message({
                    showClose: true,
                    message: "请稍后...",
                    type: "warning"
                })
                return;
            }
            this.classmsg.student_id = obj.id;
            this.classmsg.student_name = obj.name;
            this.classmsg.start_time = obj.start_time;
            this.classmsg.end_time = obj.end_time;
            this.classmsg.count = obj.buy_quantity - obj.kx;//剩余课次
            this.changetime = new Date().getTime();
        },
        //转班成功回掉
        zhuanbanchange: function () {
            this.getClassStudentList();
        },

        //续费
        xufeiFun: function (id) {
            this.student_id = id;
            this.xuefeishow = true;
        },
        //续费
        tgxufeiFun: function (obj) {
            this.tgstudent = obj;
            this.student_id = obj.id;
            this.starttime = obj.end_time * 1000 + this.xuefeiform.count * 1000 * 60 * 60 * 24 * 30;
            this.tgxuefeishow = true;
        },


        //删除学员
        delxueyuanFun: function (id, name) {
            var self = this;
            self.$confirm('是否把学员从此班级中删除?', '删除学员', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/student/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + '已从此班级中删除！'
                            });
                            this.getClassStudentList();
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
        }
    },
    created: function () {
        this.getClassStudentList();
        this.getClassDetail();
        this.getFinance();

        this.yx_cwgl_jf = this.pdqx(["yx_cwgl_jf", "yx_cwgl", "yx"]);

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less">
@import "../../../less/color.less";
.setwith {
    width: 500px;
}

.fengexian1 {
    border-top: 1px solid #eee;
    height: 10px;
}

.tiaokebox {
    .el-form-item {
        margin-bottom: 10px;
    }
    h1 {
        font-size: 16px;
        text-align: center;
        padding-bottom: 10px;
        color: @c2;
    }
    p {
        font-size: 14px;
        color: @c2;
        text-align: center;
        margin-bottom: 10px;
    }
    .el-input {
        width: auto;
    }
    .chengeaddcut {
        line-height: 36px;
    }
}

.fengexian2 {
    width: 100%;
    border-top: 1px solid #eee;
    height: 22px;
}
</style>