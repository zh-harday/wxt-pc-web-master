<template>
    <div class="tuifei">
        <el-dialog title="退费" v-model="tuifeishou">
            <p class="fengexian"></p>
            <el-form ref="form" :model="form" label-width="70px">
    
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="项目名称">
                            {{ tfdata.fee_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="费用类型">
                            {{ tfdata.fee_type == "0"?"报名费":"杂费" }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="缴费金额">
                            {{ tfdata.actual }}元
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="经办人">
                            {{ tfdata.payee }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="缴费时间">
                            {{ tfdata.reg_time | yyyy_mm_dd_M_S }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian_t"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="退款金额">
                            <el-input type="number" v-model="form.actual" placeholder="请输入金额"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="退款账户">
                            <el-select v-model="form.account" placeholder="请选择">
                                <el-option v-for="item in finances" :label="item.account_name+'-'+item.campus_name " :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="备注">
                    <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" placeholder="请输入内容" v-model="form.remark">
                    </el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                                                <el-button @click="tuifeishou = false">取消</el-button>
                                                <el-button type="primary" @click="tuifeiFun">确定</el-button>
                                            </span>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            tuifeishou: false,
            form: {
                actual: "",
                account: "",
                remark: ""
            },
            finances: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : [],//财务账户
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        }
    },
    props: ["change", "tfdata", "sid"],

    watch: {
        change: function (val) {
            this.tuifeishou = true;
            this.change = val;
        },
        tfdata: function (val) {
            this.tfdata = val;
        }
    },
    methods: {
        //退费
        tuifeiFun: function () {
            var self = this;
            if (self.form.actual <= 0) {
                self.$message({
                    showClose: true,
                    message: "退款金额必须大于0",
                    type: 'warning'
                });
                return;
            }
            if (self.form.account == "") {
                self.$message({
                    showClose: true,
                    message: "请选择退款账户",
                    type: 'warning'
                });
                return;
            }
            var times = new Date().getTime() / 1000;
            var formData = new FormData();
            formData.append("actual", self.form.actual);
            formData.append("account", self.form.account);
            formData.append("payee_time", times);
            formData.append("remark", self.form.remark);
            self.$http.post("/api/student/" + self.sid + "/refund/" + self.tfdata.id, formData)
                .then(function (data) {
                    self.tuifeishou = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success1", {
                            self: self,
                            msg: data.data.msg
                        });
                    } else {
                        self.$store.commit("checkError1", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.tuifeishou = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
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
        }
    },
    created: function () {
        // console.log(this.tfdata)
        this.getFinance();
    }
}
</script>