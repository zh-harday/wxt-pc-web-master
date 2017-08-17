<template>
    <div class="box">
        <el-form :model="subjectDatail">
            <el-form-item label="课程名称" :label-width="formLabelWidth">
                <el-input v-model="subjectDatail.subject" auto-complete="off" placeholder="请输入课程名称"></el-input>
            </el-form-item>
            <div class="row_form">
                <div class="left_form">
                    <el-form-item label="课程类别" :label-width="formLabelWidth">
                        <el-select v-model="subjectDatail.type_id" placeholder="请选择课程类别" disabled>
                            <el-option :label="list.type_name" v-model="list.id" v-for="list in SubjectLBList"></el-option>
                        </el-select>
                    </el-form-item>
                </div>
                <div class="right_form">
                    <el-form-item label="校区" :label-width="formLabelWidth">
                        <el-select v-model="subjectDatail.campus_id" placeholder="请选择校区" disabled>
                            <el-option :label="allCampus_name" :value="allCampus_id"></el-option>
                            <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                        </el-select>
                    </el-form-item>
                </div>
            </div>
    
            <el-form-item label="授课老师" :label-width="formLabelWidth">
                <div class="subject_teacher">
                    <div class="sub_left">
                        <el-input placeholder="输入老师姓名，按回车搜索" icon="search" v-model="searchVal" @input="serachTeach">
                        </el-input>
                        <el-table :data="teacher_list" style="width: 100%" height="235" @row-click="changeTeach">
                            <el-table-column prop="name" label="姓名">
                            </el-table-column>
                            <el-table-column prop="campus_id" label="校区">
                                <template scope="scope">
                                    <span v-if="scope.row.campus_id == 1">所有校区</span>
                                    <span v-if="scope.row.campus_id != 1 && scope.row.campus_id == item.id" v-for="item in campus">{{ item.name }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="department_id" label="部门">
                                <template scope="scope">
                                    <span v-for="item in department" v-if="item.id == scope.row.department_id">{{ item.name }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="group_id" label="职位">
                                <template scope="scope">
                                    <span v-for="item in department" v-if="item.id == scope.row.department_id">
                                            <span v-for="list in item.group" v-if="list.id == scope.row.group_id">{{ list.name }}</span>
                                    </span>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                    <div class="sub_right">
                        <h1>已选老师</h1>
                        <el-tag v-for="tag in tags" :closable="true" type="success" @close="delTeacher(tag.id)">
                            {{tag.name}}
                        </el-tag>
                        <p v-show="tags.length == 0">请在左边点击选择授课老师</p>
                    </div>
                </div>
            </el-form-item>
    
            <el-form-item label="课程介绍" :label-width="formLabelWidth">
                <el-input v-model="subjectDatail.remark" maxlength="100" placeholder="最多可输入50个字符" type="textarea" :rows="2"></el-input>
            </el-form-item>
            <div class="button">
                <el-button type="primary" class="btns" @click="$router.go(-1)">取消</el-button>
                <el-button type="primary" @click="xiugaiSubject">保存</el-button>
            </div>
        </el-form>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            formLabelWidth: "80px",
            allCampus_name: "所有校区",
            allCampus_id: "1",

            SubjectLBList: window.sessionStorage.getItem("SubjectLBList") ? JSON.parse(window.sessionStorage.getItem("SubjectLBList")) : [],
            subjectDatail: {},

            searchVal: "",
            tags: [],

            teacher_list: [],
            obj: {//用于搜索员工
                current_page: "",
                pageSize: "",
                campus_id: "",
                department_id: "",
                search: ""
            },

            tc_campus_id: "",

            yx_kcgl:false
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
        department: function () {
            return this.$store.state.department;
        }
    },
    watch: {
        ygSimple: function (val) {
            this.teacher_list = val;
        }
    },
    methods: {

        //修改课程
        xiugaiSubject: function () {
            var self = this;
            if (self.subjectDatail.subject.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写课程名称",
                    type: 'warning'
                });
                return;
            }
            if (self.tags.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请选择授课老师",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();
            var arr = [];
            for (var i = 0; i < self.tags.length; i++) {
                arr.push(self.tags[i].id)
            }
            formData.append("teacher", arr);
            formData.append("subject", self.subjectDatail.subject);
            formData.append("campus_id", self.subjectDatail.campus_id);
            formData.append("type_id", self.subjectDatail.type_id);
            formData.append("remark", self.subjectDatail.remark);
            self.$http.post("/api/subject/" + self.subjectDatail.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "修改成功",
                        });
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
        //搜索老师
        serachTeach: function (val) {
            var self = this;
            if (val == "") {
                self.$store.commit('getYGList', {
                    self: self,
                    campus_id: self.tc_campus_id,
                    is_dk: 1
                });
                return;
            }
            var arr = [];
            for (var i = 0; i < self.ygSimple.length; i++) {
                if (self.ygSimple[i].name.indexOf(val) != -1) {
                    arr.push(self.ygSimple[i]);
                }
            }
            if (arr.length == 0) {
                return;
            }
            self.teacher_list = arr;
        },
        //点击行选择老师
        changeTeach: function (row, event, column) {
            var self = this;
            for (var i = 0; i < this.tags.length; i++) {
                if (this.tags[i].id == row.id) {
                    this.$message({
                        showClose: true,
                        message: "已经选择了该老师，请不要重选择",
                        type: 'warning'
                    });
                    return;
                }
            }
            this.tags.push(row);
        },

        //选择 删除老师
        delTeacher: function (a) {
            var self = this;
            for (var i = 0; i < self.tags.length; i++) {
                if (a == self.tags[i].id) {
                    self.tags.splice(i, 1);
                    break;
                }
            }
        },
        //获取课程分类
        getSubjectfenlei: function (obj) {
            var self = this;
            self.$http.get("/api/subject/type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.SubjectLBList = data.data.data;
                        window.sessionStorage.setItem("SubjectLBList", JSON.stringify(self.SubjectLBList));
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
        //获取课程详情
        getSubjectDetail: function () {
            var self = this;
            self.$http.get("/api/subject/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (self.myMessage.campus_id != "1" && self.myMessage.campus_id != data.data.data.campus_id) {
                            self.$router.push({ name: 'worktody' })
                            return;
                        }
                        self.subjectDatail = data.data.data;
                        var arr = [];
                        for (var i = 0; i < self.subjectDatail.teacher.length; i++) {
                            arr.push({
                                id: self.subjectDatail.teacher[i].teacher_id,
                                name: self.subjectDatail.teacher[i].teacher
                            })
                        }
                        self.tags = arr;
                        self.tc_campus_id = self.subjectDatail.campus_id;
                        self.$store.commit('getYGList', {
                            self: self,
                            campus_id: self.tc_campus_id,
                            is_dk: 1
                        });
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

    },
    created: function () {
        this.getSubjectfenlei();
        this.getSubjectDetail();
        this.$store.commit('setsubject_detail_meau_id', 3);
        
        this.yx_kcgl = this.pdqx(["yx_kcgl", "yx"]);
        if (!this.yx_kcgl) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.el-input {
    width: 100%;
}

.el-select {
    width: 100%;
}

.box {
    background-color: #fff;
    padding: 20px;
}

.el-textarea {
    width: 100%;
}

.row_form {
    .right_form {
        text-align: right;
    }
}

.el-input {
    margin-bottom: 10px;
}

.button {
    padding: 10px 0;
    text-align: right;
}

.btns {
    background-color: #ddd;
    color: #fff;
    border: 1px solid #ddd;
    &:hover {
        opacity: .8;
    }
    &:active {
        opacity: .5;
    }
}
</style>