<template>
    <div>
        <div class="fabuyuxi_box">
            <h1 v-if="$route.name=='class_fabu_yuxitixing'">发布预习提醒</h1>
            <h1 v-if="$route.name=='class_fabu_zuoye'">发布作业</h1>
            <h1 v-if="$route.name=='class_fabu_tongzhi' || $route.name == 'tgclass_fabu_tongzhi'">发布班级通知</h1>
            <el-form ref="form" :model="form" label-width="50px">
                <el-row v-if="$route.name!='class_fabu_tongzhi' && $route.name != 'tgclass_fabu_tongzhi'">
                    <el-col :span="24">
                        <el-form-item label="标题">
                            <el-input v-model="form.title" placeholder="请输入标题"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row v-if="$route.name=='class_fabu_tongzhi' || $route.name == 'tgclass_fabu_tongzhi'" :gutter="50">
                    <el-col :span="16">
                        <el-form-item label="标题">
                            <el-input v-model="form.title" placeholder="请输入标题"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="是否需要回执" label-width="100px">
                            <el-radio-group v-model="is_receipt">
                                <el-radio :label="1">是</el-radio>
                                <el-radio :label="0">否</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="50" v-if="$route.name!='class_fabu_tongzhi' && $route.name != 'tgclass_fabu_tongzhi'">
                    <el-col :span="12">
                        <el-form-item label="课程">
                            <el-select v-model="form.subject" placeholder="请选择课程">
                                <el-option :label="item.subject_name" :value="item.id" v-for="item in classDetail.subject"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否需要回执" label-width="100px">
                            <el-radio-group v-model="is_receipt">
                                <el-radio :label="1">是</el-radio>
                                <el-radio :label="0">否</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="摘要">
                            <el-input v-model="form.info" placeholder="请输入摘要信息"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <el-form-item label="正文">
                            <vue-html5-editor :content="form.content" :height="350" @change="updateData"></vue-html5-editor>
                        </el-form-item>
                    </el-col>
                </el-row>
                <div class="fabuyuxu_bottom">
                    <!--发布班级通知-->
                    <el-button type="primary" @click="fabuClassNotice" v-if="$route.name == 'class_fabu_tongzhi' || $route.name == 'tgclass_fabu_tongzhi' ">发布</el-button>
                    <el-button type="primary" @click="fabuHomework" v-if="$route.name == 'class_fabu_zuoye' ">发布</el-button>
                    <el-button type="primary" @click="fabuyuxitixing" v-if="$route.name == 'class_fabu_yuxitixing' ">发布</el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            classDetail: window.sessionStorage.getItem("classDetail" + this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("classDetail" + this.$route.params.id)) : {
                subject: []
            },
            is_receipt: 1,
            form: {
                title: "",
                info: "",
                content: "",
                subject: ""
            },

            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        updateData: function (val) {
            this.form.content = val;
        },
        //发布作业
        fabuHomework: function () {
            var self = this;
            if (self.form.title.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写标题",
                    type: 'warning'
                });
                return;
            }
            if (self.form.subject.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择课程",
                    type: 'warning'
                });
                return;
            }
            if (self.form.content.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写内容",
                    type: 'warning'
                });
                return;
            }
            var formData = new FormData();
            formData.append("title", self.form.title);
            formData.append("info", self.form.info);
            formData.append("subject_id", self.form.subject);
            formData.append("is_receipt", self.is_receipt);
            formData.append("body", self.form.content);

            self.$http.post("/api/classs/" + self.$route.params.id + "/work", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.$router.go(-1);
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
        //发布班级通知
        fabuClassNotice: function () {
            var self = this;
            if (self.form.title.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写标题",
                    type: 'warning'
                });
                return;
            }
            if (self.form.content.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写内容",
                    type: 'warning'
                });
                return;
            }
            var formData = new FormData();
            formData.append("title", self.form.title);
            formData.append("info", self.form.info);
            formData.append("is_receipt", self.is_receipt);
            formData.append("body", self.form.content);

            self.$http.post("/api/classs/" + self.$route.params.id + "/notice", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.$router.go(-1);
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
        //发布预习提醒
        fabuyuxitixing: function () {
            var self = this;
            if (self.form.title.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写标题",
                    type: 'warning'
                });
                return;
            }
            if (self.form.subject.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择课程",
                    type: 'warning'
                });
                return;
            }
            if (self.form.content.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写内容",
                    type: 'warning'
                });
                return;
            }
            var formData = new FormData();
            formData.append("title", self.form.title);
            formData.append("info", self.form.info);
            formData.append("is_receipt", self.is_receipt);
            formData.append("subject_id", self.form.subject);
            formData.append("body", self.form.content);

            self.$http.post("/api/classs/" + self.$route.params.id + "/prep", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.$router.go(-1);
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
        //班级详情
        getClassDetail: function () {
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
        }
    },
    created: function () {
        if (this.$route.name != 'class_fabu_tongzhi') {
            this.getClassDetail();
        }

        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);

        if (!this.yx_bjgl_my_qtxx) {
            this.$router.go(-1);
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.fabuyuxi_box {
    background-color: #fff;
    padding: 20px;
    >h1 {
        font-size: @h3;
        color: @c2;
        font-weight: normal;
        padding-bottom: 15px;
    }
    .el-select {
        width: 100%;
    }
}

.fabuyuxu_bottom {
    text-align: right;
    padding: 10px 0;
}
</style>