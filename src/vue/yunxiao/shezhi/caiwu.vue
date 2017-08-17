<template>
    <div>
        <h1 class="right_title">
            财务账户
            <article>学校收支账户的添加及修改</article>
            <el-button type="success" size="small" @click="openAdd">添加账户</el-button>
        </h1>
        <tabs :list="titleList" :datas="finances" v-on:tabfun="tabCallBack" text="校区" :all="true"></tabs>
        <ul class="cardList" v-loading="loading1">
            <li v-for="(list,index) in finance" :class="'cark_li'+index%4">
                <div>
                    <i class="el-icon-edit" @click="openXiugai(list)"></i>
                    <i class="el-icon-delete" @click="delcaiwu(list.id,list.account_name)"></i>
                </div>
                <h2>{{ list.campus_name }}</h2>
                <h1>{{ list.account_name }}</h1>
                <p>{{ list.remark }}</p>
            </li>
        </ul>
        <!--修改财务弹框-->
        <el-dialog :title="title" v-model="dialogFormVisible" size="tiny" v-loading="loading" element-loading-text="加载中">
            <el-form :model="caiwu">
                <el-form-item label="名称" :label-width="formLabelWidth">
                    <el-input v-model="caiwu.account_name" auto-complete="off" maxlength="20" placeholder="请输入账户名称"></el-input>
                </el-form-item>
                <el-form-item label="校区" :label-width="formLabelWidth" v-show="caiwu.type == 2">
                    <el-select v-model="campus_list" placeholder="请选择校区" @change="changeCampus" ref="selectCaiwuCampus">
                        <el-option label="所有校区" value="1"></el-option>
                        <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="备注" :label-width="formLabelWidth">
                    <el-input v-model="caiwu.remark" maxlength="50" placeholder="最多可输入50个字符"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="updateCaiwu">确 定</el-button>
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
            title: "",
            dialogFormVisible: false,
            loading: false,
            loading1: false,
            formLabelWidth: "40px",
            caiwu: {
                id: 0,
                campus_id: 0,
                account_name: "",
                remark: "",
                type: 0
            },

            campus_list: this.campus,
            finances: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : [],
            finance: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : []
        }
    },
    methods: {
        tabCallBack: function (id) {
            var self = this;
            if (id == 0) {
                self.finance = self.finances;
                return;
            }
            var arr = [];
            for (var i = 0; i < self.finances.length; i++) {
                if (self.finances[i].campus_id == id) {
                    arr.push(self.finances[i]);
                }
            }
            self.finance = arr;
        },
        //获取财务列表
        getFinance: function () {
            var self = this;
            if (self.finances.length == 0) {
                self.loading1 = true;
            }
            self.$http.get("/api/finance/account")
                .then(function (data) {
                    self.loading1 = false;
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
                        self.finance = data.data.data;
                        window.sessionStorage.setItem("finances", JSON.stringify(self.finances))
                    } else {
                        self.loading1 = false;
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
        //打开修改 财务账户
        openXiugai: function (data) {
            this.title = "修改财务账户";
            this.dialogFormVisible = true;
            this.caiwu = {
                id: data.id,
                campus_id: data.campus_id,
                account_name: data.account_name,
                remark: data.remark,
                type: 1
            }
        },
        //打开添加 财务账户
        openAdd: function () {
            this.title = "新增财务账户";
            this.dialogFormVisible = true;
            this.caiwu = {
                id: 0,
                campus_id: 0,
                account_name: "",
                remark: "",
                type: 2
            }
        },
        //选择校区
        changeCampus: function () {
            this.caiwu.campus_id = this.$refs.selectCaiwuCampus.value;
        },
        //修改 新增财务账户
        updateCaiwu: function () {
            var self = this;
            var formData = new FormData();

            formData.append("account_name", self.caiwu.account_name);
            formData.append("remark", self.caiwu.remark);
            if (self.caiwu.type == 2) {
                if (self.caiwu.campus_id == 0) {
                    self.$message({
                        showClose: true,
                        message: "请选择校区",
                        type: 'warning'
                    });
                    return;
                }
                formData.append("campus_id", self.caiwu.campus_id);
            }

            self.loading = true;

            var url = self.caiwu.type == 1 ? ("/api/finance/account/" + self.caiwu.id) : "/api/finance/account";
            self.$http.post(url, formData)
                .then(function (data) {
                    self.dialogFormVisible = false;
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getFinance();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.dialogFormVisible = false;
                    self.loading = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除财务账户
        delcaiwu: function (id, name) {

            var self = this;
            self.$confirm('确定要删除"' + name + '"?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/finance/account/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功",
                            });
                            self.getFinance();
                        } else {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }, function (err) {

                    })
            }).catch(function () {

            });
        }
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 5);
        this.getFinance();
        this.titleList = this.campus;
        for (var i = 0; i < this.titleList.length; i++) {
            this.titleList[i].num = 0;
        }
    }
}

</script>
<style lang="less" scoped>
.el-select {
    width: 100%;
}
</style>