<template>
    <div>
        <h1 class="right_title">
                    课程类别
                    <article>学校课程大类的项目添加及细分课程的设置</article>
                    <el-button type="success" size="small" @click="openAddsubject">添加类别</el-button>
                    <el-button type="success" size="small" @click="$router.push({name:'curriculum_list' , params:{ id:0 }})">课程管理</el-button>
                </h1>
        <ul class="cardList" v-loading.body="loading1" element-loading-text="加载中">
            <li v-for="(list,index) in SubjectLBList" :class="'cark_li'+index%4">
                <div>
                    <i class="el-icon-delete" @click="delSubject(list.id,list.type_name)"></i>
                    <i class="el-icon-edit" @click="openXiugai(list)"></i>
                </div>
                <h2>&nbsp;</h2>
                <h1>{{ list.type_name }}</h1>
                <p>{{ list.remark }}</p>
            </li>
        </ul>
    
        <!--修改教室弹框-->
        <el-dialog :title="title" v-model="dialogFormVisible" size="tiny">
            <p class="fengexian"></p>
            <el-form :model="subject">
                <el-form-item label="名称" :label-width="formLabelWidth">
                    <el-input v-model="subject.type_name" auto-complete="off" maxlength="50" placeholder="请输入类别名称"></el-input>
                </el-form-item>
                <el-form-item label="备注" :label-width="formLabelWidth" maxlength="30">
                    <el-input v-model="subject.remark" placeholder="最多可输入30个字符"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="updateSubject">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {

    data: function () {
        return {
            title: "",
            loading1: false,
            dialogFormVisible: false,
            subject: {
                id: 0,
                type_name: "",
                remark: ""
            },
            formLabelWidth: "40px",
            SubjectLBList: window.sessionStorage.getItem("SubjectLBList") ? JSON.parse(window.sessionStorage.getItem("SubjectLBList")) : [],
        }
    },
    methods: {
        //获取课程类别详情
        getSubjectLB: function () {
            var self = this;
            if (self.SubjectLBList.length == 0) {
                self.loading1 = true;
            }
            self.$http.get("/api/subject/type")
                .then(function (data) {
                    self.loading1 = false;
                    if (data.data.status == 'ok') {
                        self.SubjectLBList = data.data.data;
                        window.sessionStorage.setItem("SubjectLBList", JSON.stringify(self.SubjectLBList))
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
        //打开添加 获取课程类别
        openAddsubject: function () {
            this.dialogFormVisible = true;
            this.title = "添加课程类别";
            this.subject = {
                id: 0,
                type_name: "",
                remark: ""
            };
        },
        //打开修改
        openXiugai: function (data) {
            this.dialogFormVisible = true;
            this.title = "修改课程类别";
            this.subject = {
                id: data.id,
                type_name: data.type_name,
                remark: data.remark
            };
        },
        //添加或修改课程类别
        updateSubject: function () {
            var self = this;
            var formData = new FormData();

            formData.append("type_name", self.subject.type_name);
            formData.append("remark", self.subject.remark);

            var url = self.subject.id == 0 ? "/api/subject/type" : ("/api/subject/type/" + self.subject.id);
            self.$http.post(url, formData)
                .then(function (data) {
                    self.dialogFormVisible = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getSubjectLB();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.dialogFormVisible = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除类别 
        delSubject: function (id, name) {

            var self = this;
            self.$confirm('确定要删除"' + name + '"?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/subject/type/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功",
                            });
                            self.getSubjectLB();
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
        this.$store.commit('setSettingLeftMeau', 4);
        this.getSubjectLB();
    }
}

</script>
