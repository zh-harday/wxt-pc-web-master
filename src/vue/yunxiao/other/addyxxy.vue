<template>
    <div>
        <!--添加学员-->
        <el-dialog title="添加意向学员" v-model="yixiangshow" class="addStudent">
            <el-form :model="studentForm">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="姓名*" :label-width="formLabelWidth">
                            <el-input v-model="studentForm.name" auto-complete="off" placeholder="请输入姓名"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年龄*" :label-width="formLabelWidth">
                            <el-input v-model="studentForm.age" auto-complete="off" placeholder="请输入年龄"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话*" :label-width="formLabelWidth">
                            <el-input v-model="studentForm.phone" auto-complete="off" placeholder="请输入联系电话"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <div class="addnewStudentYX" :class="{active:toDown}">
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="意向校区*" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id == 1" @change="changeCam">
                                    <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                                <el-select v-model="studentForm.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1" disabled>
                                    <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="预报科目*" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.subject_id" placeholder="请选择预报科目">
                                    <el-option v-for="item in YBsubjectList" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="招生渠道*" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.source_id" placeholder="请选择招生渠道">
                                    <el-option v-for="item in sourceList" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="跟进状态" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.status_id" placeholder="请选择跟进状态">
                                    <el-option v-for="item in intention" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="意向级别" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.level_id" placeholder="请选择意向级别">
                                    <el-option v-for="item in levelList" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="市场专员" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.zy_id" placeholder="请选择市场专员">
                                    <el-option v-for="item in ygSimple" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="课程顾问" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.gw_id" placeholder="请选择课程顾问">
                                    <el-option v-for="item in teachList" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <p class="fengexian2"></p>
    
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="生日" :label-width="formLabelWidth">
                                <el-date-picker v-model="studentForm.birthday" type="date" placeholder="选择生日">
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="学校" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.school" placeholder="请输入学校"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="年级" :label-width="formLabelWidth">
                                <el-select v-model="studentForm.grade" placeholder="请选择年级">
                                    <el-option v-for="item in gradeList" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="性别" :label-width="formLabelWidth">
                                <el-radio class="radio" v-model="studentForm.sex" label="男">男</el-radio>
                                <el-radio class="radio" v-model="studentForm.sex" label="女">女</el-radio>
                                <el-radio class="radio" v-model="studentForm.sex" label="未知">未知</el-radio>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="邮箱" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.email" placeholder="请输入邮箱"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="QQ" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.qq" placeholder="请输入QQ"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="微信" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.weixin" placeholder="请输入微信"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="16">
                            <el-form-item label="家庭地址" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.address" placeholder="请输入家庭地址"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="20">
                        <el-col :span="24">
                            <el-form-item label="备注" :label-width="formLabelWidth">
                                <el-input v-model="studentForm.remark" placeholder="请输入备注" type="textares"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>
                <div class="todowm" @click="toDownFun">
                    <i class="el-icon-arrow-down" v-show="!toDown"></i>
                    <i class="el-icon-arrow-up" v-show="toDown"></i>
                </div>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="yixiangshow = false">取消</el-button>
                <el-button type="primary" @click="addNewStudent">添加</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    watch: {
        changeshow: function () {
            this.yixiangshow = true;
        }
    },
    props: ["changeshow"],
    data: function () {
        return {
            yixiangshow: false,
            formLabelWidth: "75px",
            toDown: false,
            studentForm: {
                campus_id: "",
                name: "",
                age: "",
                phone: "",
                status_id: "1",
                subject_id: "",
                source_id: "",
                level_id: "2",
                zy_id: "",
                gw_id: "",
                birthday: "",
                school: "",
                address: "",
                grade: "",
                weixin: "",
                qq: "",
                email: "",
                remark: "",
                sex: "未知"
            },
            //预报科目
            YBsubjectList: window.sessionStorage.getItem("YBsubjectList") ? JSON.parse(window.sessionStorage.getItem("YBsubjectList")) : [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
            //跟进状态
            intention: window.sessionStorage.getItem("intention") ? JSON.parse(window.sessionStorage.getItem("intention")) : [],
            //意向级别
            levelList: window.sessionStorage.getItem("levelList") ? JSON.parse(window.sessionStorage.getItem("levelList")) : [],
            //员工列表
            teachList: [],
            gradeList: window.sessionStorage.getItem("gradeList") ? JSON.parse(window.sessionStorage.getItem("gradeList")) : [],
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
    },
    methods: {
        //选择意向校区
        changeCam: function (val) {
            var self = this;
            self.getGWTeachList(val);
            self.$store.commit('getYGList', {
                self: self,
                campus_id: val,
                is_sc: 1
            });
        },
        //预报科目
        getYBsubject: function () {
            var self = this;
            if (self.YBsubjectList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/subject")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.YBsubjectList = data.data.data;
                        window.sessionStorage.setItem("YBsubjectList", JSON.stringify(self.YBsubjectList));
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
        //获取跟进状态
        getIntention: function () {
            var self = this;
            if (self.intention.length > 0) {
                self.getLevel();
                return;
            }
            self.$http.get("/api/intention/status")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.intention = data.data.data;
                        window.sessionStorage.setItem("intention", JSON.stringify(self.intention));
                        self.getLevel();
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
        //获取意向级别列表
        getLevel: function () {
            var self = this;
            if (self.levelList.length > 0) {
                self.getSource();
                return;
            }
            self.$http.get("/api/intention/level")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.levelList = data.data.data;
                        window.sessionStorage.setItem("levelList", JSON.stringify(self.levelList));
                        self.getSource();
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
        //添加新的预报学员
        addNewStudent: function () {
            var self = this;

            if (self.studentForm.name.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "学员姓名不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.studentForm.age.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "年龄不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.studentForm.phone.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "电话号码不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.studentForm.campus_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择意向校区",
                    type: "warning"
                })
                return;
            }
            if (self.studentForm.subject_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择预报科目",
                    type: "warning"
                })
                return;
            }
            if (self.studentForm.source_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择招生渠道",
                    type: "warning"
                })
                return;
            }

            if (self.studentForm.birthday != '') {
                self.studentForm.birthday = self.studentForm.birthday.getTime() / 1000;
            }

            var formData = new FormData();
            for (var k in self.studentForm) {
                formData.append(k, self.studentForm[k]);
            }

            self.$http.post("/api/intention", formData)
                .then(function (data) {
                    self.yixiangshow = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.studentForm = {
                            campus_id: "",
                            name: "",
                            age: "",
                            phone: "",
                            status_id: "1",
                            subject_id: "",
                            source_id: "",
                            level_id: "2",
                            zy_id: "",
                            gw_id: "",
                            birthday: "",
                            school: "",
                            address: "",
                            grade: "",
                            weixin: "",
                            qq: "",
                            email: "",
                            remark: "",
                            sex: ""
                        }
                        self.$emit("change");
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.yixiangshow = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取员工
        getGWTeachList: function (campus_id) {
            var self = this;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.teachList = data.data.data.staff;
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
        //获取年级列表
        getgradeList: function () {
            var self = this;
            if (self.gradeList.length > 0) {
                return;
            }
            self.$http.get("/api/config/grade")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.gradeList = data.data.data;
                        window.sessionStorage.setItem("gradeList", JSON.stringify(self.gradeList));
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
        //打开 关闭
        toDownFun: function () {
            this.toDown = !this.toDown;
        }
    },
    created: function () {
        var self = this;
        self.getYBsubject();
        self.getSource();
        self.getIntention();
        self.getLevel();
        self.getgradeList();
        var campus_id = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;
        self.getGWTeachList(campus_id);
        self.studentForm.campus_id = campus_id;
        self.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id,
            is_sc: 1
        });
    }
}
</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.addStudent {
    .el-select {
        width: 100%; // margin-bottom: 10px;
    }
    .el-form-item {
        // margin-bottom: 10px;
    }
}

.addnewStudentYX {
    height: 50px;
    overflow: hidden;
    transition: height .3s;
    &.active {
        height: 430px;
    }
}

.todowm {
    height: 30px;
    font-size: 20px;
    text-align: center;
    line-height: 30px;
    cursor: pointer;
    font-weight: normal;
    color: @c3;
    &:hover {
        background-color: #f2f2f2;
    }
}
</style>

