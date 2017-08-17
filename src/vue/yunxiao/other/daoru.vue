<template>
    <div>
        <!--导入学员-->
        <el-dialog :title="title" v-model="daoruxueyuan" class="daoruxueyuan">
            <p class="fengexian"></p>
    
            <el-form :model="daoruform" label-width="70px">
                <el-form-item label="导入校区">
                    <el-select v-model="daoruform.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id==1">
                        <el-option v-for="item in campus" :value="item.id" :label="item.name">
                        </el-option>
                    </el-select>
                    <el-select v-model="daoruform.campus_id" placeholder="请选择校区" disabled v-if="myMessage.campus_id != 1">
                        <el-option v-for="item in campus" :value="item.id" :label="item.name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="市场专员">
                    <el-select v-model="daoruform.zy_id" placeholder="请选择市场专员" filterable>
                        <el-option v-for="item in zylist" :value="item.id" :label="item.name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="课程顾问">
                    <el-select v-model="daoruform.gw_id" placeholder="请选择课程顾问" filterable>
                        <el-option v-for="item in gwList" :value="item.id" :label="item.name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="招生渠道">
                    <el-select v-model="daoruform.source_id" placeholder="请选择招生渠道">
                        <el-option v-for="item in sourceList" :value="item.id" :label="item.name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="选择文件">
                    <form id="addxueyuanform" enctype="multipart/form-data">
                        <input type="file" name="file">
                    </form>
                    <p class="tishi">只支持固定格式的Excel文件导入</p>
                    <a href="http://wxt-1251355418.cossh.myqcloud.com/conf/eduwxt_student_tp.xls" class="downloadmoban" v-if="type==1">下载Excel模板(正式学员)</a>
                    <a href="http://wxt-1251355418.cossh.myqcloud.com/conf/eduwxt_customer_tp.xls" class="downloadmoban" v-if="type!=1">下载Excel模板(意向学员)</a>
                </el-form-item>
                <div class="bottom">
                    <el-button type="danger" @click="daoruxueyuan = false" class="btns">取消</el-button>
                    <el-button type="primary" @click="daoruTableFun" :loading="loading">导入</el-button>
                </div>
            </el-form>
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
        zylist: function () {
            return this.$store.state.ygSimple;
        }
    },
    props: ["type", "changetime"],
    data: function () {
        return {
            daoruxueyuan: false,
            title: "",
            loading: false,
            daoruform: {
                campus_id: "",
                zy_id: "",
                gw_id: "",
                source_id: "1"
            },

            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
            gwList: []
        }
    },
    watch: {
        changetime: function () {
            this.daoruxueyuan = true;
        }
    },
    methods: {
        //获取招生渠道
        getSource: function () {
            var self = this;
            if (self.sourceList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/source")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.sourceList = data.data.data;
                        window.sessionStorage.setItem("sourceList", JSON.stringify(self.sourceList));
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
        //获取课程顾问
        //获取员工
        getGWTeachList: function () {
            var self = this;
            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.gwList = data.data.data.staff;
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
        //导入学员
        daoruTableFun: function () {
            var self = this;
            if (self.daoruform.campus_id == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: "warning"
                })
                return;
            }
            self.loading = true;
            var fm = document.getElementById("addxueyuanform");
            var formData = new FormData(fm);
            formData.append("campus_id", self.daoruform.campus_id);
            formData.append("zy_id", self.daoruform.zy_id);
            formData.append("gw_id", self.daoruform.gw_id);
            formData.append("source_id", self.daoruform.source_id);
            var url = self.type == "1" ? "/api/file/input_student" : "/api/file/input_customer";
            self.$http.post(url, formData)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "导入成功"
                        });
                        self.daoruxueyuan = false;
                        self.$emit("change");
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
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.daoruform.campus_id = campus_id;
        self.getSource();
        self.getGWTeachList();

        self.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id,
            is_sc: 1
        });

        if (self.type == 1) {
            self.title = "导入学员";
        } else {
            self.title = "导入意向学员";
        }
    }
}
</script>
