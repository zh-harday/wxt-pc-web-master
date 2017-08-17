<template>
    <div>
        <h1 class="right_title">
            财务管理<br>
            <el-input placeholder="请输入姓名、订单号和备注" icon="search" v-model="option.search" :on-icon-click="serachFun" class="btn" @focus="isFocus = true"
                @blur="isFocus = false"  @keyup.enter.native="serachFun"></el-input>
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
                <span>收款账户：</span>
                <ul>
                    <li :class="{active:option.payee_account_id == ''}" @click="skzhFun('')">全部</li>
                    <li :class="{active:option.payee_account_id== item.id}" v-for="item in finances" :alt="item.id" @click="skzhFun(item.id)">{{ item.account_name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>收/退费：</span>
                <ul>
                    <li :class="{active:option.type == ''}" @click="shoutuifeiClick('')">全部</li>
                    <li :class="{active:option.type == '1'}" @click="shoutuifeiClick('1')">收费</li>
                    <li :class="{active:option.type == '0'}" @click="shoutuifeiClick('0')">退费</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>费用类型：</span>
                <ul>
                    <li :class="{active:option.fee_type == ''}" @click="feiyongleixingClick('')">全部</li>
                    <li :class="{active:option.fee_type == '0'}" @click="feiyongleixingClick('0')">报名费</li>
                    <li :class="{active:option.fee_type == '1'}" @click="feiyongleixingClick('1')">杂费</li>
                </ul>
            </div>
            <div class="saixuan_box last">
                <span>其他：</span>
                <el-select v-model="option.payee_id" placeholder="请选择经办人" filterable @change="jinbanrenchange">
                    <el-option label="全部" value="0"></el-option>
                    <el-option v-for="item in ygSimple" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <el-date-picker style="height:36px;" @change="changeTimeFun" v-model="option.date" type="daterange" :picker-options="pickerOptions2" placeholder="选择时间范围" align="left">
                </el-date-picker>
            </div>
        </div>
    
        <el-table :data="finances_ls.subject" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="orders_id" label="流水单号">
            </el-table-column>
            <el-table-column prop="payee_times" label="交易时间" width="120">
            </el-table-column>
            <el-table-column prop="student_name" label="学员姓名">
            </el-table-column>
            <el-table-column prop="student_number" label="学员学号">
            </el-table-column>
            <el-table-column prop="campus_name" label="所在校区">
            </el-table-column>
            <el-table-column prop="payee_account" label="交易账户">
            </el-table-column>
            <el-table-column prop="payee" label="经办人">
            </el-table-column>
            <el-table-column prop="type" label="收/退费">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.type==1">收费</el-tag>
                    <el-tag type="danger" v-if="scope.row.type==0">退费</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="fee_type" label="费用类型">
                <template scope="scope">
                    <span v-if="scope.row.fee_type==1">杂费</span>
                    <span v-if="scope.row.fee_type==0">报名费</span>
                </template>
            </el-table-column>
            <el-table-column prop="fee_mode" label="收费方式">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.fee_mode==0">正常收费</el-tag>
                    <el-tag type="warning" v-if="scope.row.fee_mode==1">赠送</el-tag>
                    <el-tag type="warning" v-if="scope.row.fee_mode==2">转结</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="fee_name" label="项目名称">
            </el-table-column>
            <el-table-column prop="actual" label="金额">
                <template scope="scope">
                    {{ scope.row.actual}}元
                </template>
            </el-table-column>
            <el-table-column prop="name" label="操作" width="160">
                <template scope="scope">
                    <el-button type="primary" size="mini" @click="$router.push({name:'Finance_detail',params:{id:scope.row.orders_id}})">查看详情</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-if="finances_ls.subject.length < finances_ls.count">
            <el-pagination @current-change="handleCurrentChange" :current-page="cwindex" :page-sizes="[10, 20, 30, 40]" :page-size="option.limit" layout="total, prev, pager, next" :total="finances_ls.count">
            </el-pagination>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            isFocus: false,
            loading: false,
            skzh: "",
            option: {
                limit: 10,
                search: "",
                campus_id: "",
                type: "",
                fee_type: "",
                payee_account_id: "",
                payee_id: "",
                start_time: "",
                end_time: "",
                date: ""
            },

            finances: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : [],//财务账户
            finances_ls: window.sessionStorage.getItem("finances_ls") ? JSON.parse(window.sessionStorage.getItem("finances_ls")) : {
                subject: []
            },//财务列表

            pickerOptions2: {
                shortcuts: [{
                    text: '最近一周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近一个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近三个月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
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
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        cwindex: function () {
            return this.$store.state.cwindex;
        }
    },
    methods: {
        //时间段选择
        changeTimeFun: function (val) {
            var arr = val.split(" - ");
            var self = this;
            self.option.search = "";
            if (arr.length == 1) {
                self.option.start_time = "";
                self.option.end_time = "";
                self.getFinanceList(self.option);
            } else {
                self.option.start_time = new Date(arr[0].split("-")[0],arr[0].split("-")[1]-1,arr[0].split("-")[2])/1000;
                self.option.end_time = new Date(arr[1].split("-")[0],arr[1].split("-")[1]-1,arr[1].split("-")[2])/1000;
                self.getFinanceList(self.option);
            }
        },
        //收款账户点击
        skzhFun: function (id) {
            var self = this;
            self.option.search = "";
            self.option.payee_account_id = id;
            self.getFinanceList(self.option);
        },
        //收退费点击
        shoutuifeiClick: function (id) {
            var self = this;
            self.option.search = "";
            self.option.type = id;
            self.getFinanceList(self.option);
        },
        //收费l类型
        feiyongleixingClick: function (id) {
            var self = this;
            self.option.search = "";
            self.option.fee_type = id;
            self.getFinanceList(self.option);
        },
        //经办人
        jinbanrenchange: function (id) {
            var self = this;
            self.option.search = "";
            self.option.payee_id = id;
            self.getFinanceList(self.option);
        },
        //点击分页
        handleCurrentChange: function (val) {
            var self = this;
            self.$store.commit("setcwindex",val);
            self.getFinanceList(self.option);
        },
        //搜索
        serachFun: function () {
            var self = this;
            self.$store.commit("setcwindex",1);
            self.option.campus_id = "";
            self.getFinanceList(self.option);
        },
        //校区点击
        canpus_click: function (val) {
            var self = this;
            self.option.search = "";
            self.$store.commit("setcwindex",1);
            self.option.campus_id = val == "0" ? "" : val;
            self.getFinanceList(self.option);
        },
        //获取财务账号列表
        getFinance: function () {
            var self = this;
            if (self.finances.length > 0) {
                return;
            }
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
                        self.finances = data.data.data;
                        window.sessionStorage.setItem("finances", JSON.stringify(self.finances))
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
        //日期转换
        transformTime: function (val) {//xxxx-xx-xx
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
        },
        //获取财务列表
        getFinanceList: function () {
            var self = this;
            self.loading = true;
            var campus_id = self.myMessage.campus_id == "1" ? self.option.campus_id : self.myMessage.campus_id;
            self.$http.get("/api/finance/records?page=" + self.cwindex + "&limit=" + self.option.limit + "&search=" + self.option.search + "&campus_id=" + campus_id + "&type=" + self.option.type + "&fee_type=" + self.option.fee_type + "&payee_account_id=" + self.option.payee_account_id + "&payee_id=" + self.option.payee_id + "&start_time=" + self.option.start_time + "&end_time=" + self.option.end_time)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.subject.length; i++) {
                            data.data.data.subject[i].reg_times = self.transformTime(data.data.data.subject[i].reg_time);
                            data.data.data.subject[i].payee_times = self.transformTime(data.data.data.subject[i].payee_time);
                            for (var j = 0; j < self.campus.length; j++) {
                                if (self.campus[j].id == data.data.data.subject[i].campus_id) {
                                    data.data.data.subject[i].campus_name = self.campus[j].name;
                                    break;
                                }
                                if (data.data.data.subject[i].campus_id == 1) {
                                    data.data.data.subject[i].campus_name = "所有校区";
                                }
                            }

                        }
                        self.finances_ls = data.data.data;
                        window.sessionStorage.setItem("finances_ls", JSON.stringify(self.finances_ls))
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
        self.$store.commit("setyx_cw_leftMeau_id", 1);
        self.getFinanceList(self.option);
        self.getFinance();
        //获取经办人
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$store.commit("getYGList", {
            self: self,
            campus_id: campus_id
        });

    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.el-input {
    width: 200px;
}

.xy_serach_box {
    margin-bottom: 20px;
}

.el-select {
    width: 150px;
    margin-bottom: 10px;
    margin-right: 10px;
}
.right_title{
    .btn{
        width: 220px;
    }
}
</style>