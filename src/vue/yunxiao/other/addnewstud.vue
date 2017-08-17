<template>
    <div>
        <!--新增学员-->
        <el-dialog title="新增学员" v-model="addxueyuanModel">
            <el-form ref="form" :model="selfMessage" :label-width="labelWidth" class="yuangong_xiugaixq">
                <h1 class="xyxq_title">基础信息(必填)</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            <el-input v-model="selfMessage.name" placeholder="请输入姓名" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="性别">
                            <el-radio-group v-model="selfMessage.sex">
                                <el-radio label="男"></el-radio>
                                <el-radio label="女"></el-radio>
                                <el-radio label="未知"></el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            <el-input v-model="selfMessage.phone" placeholder="请输入联系电话" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="校区">
                            <el-select v-model="selfMessage.campus_id" placeholder="请选择校区" style="width:100%" v-if="myMessage.campus_id == 1" @change="changecampus_add">
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                            <el-select v-model="selfMessage.campus_id" placeholder="请选择校区" style="width:100%" disabled v-if="myMessage.campus_id != 1">
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学号">
                            <el-input v-model="selfMessage.number" placeholder="请输入学号" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="入学日期">
                            <el-date-picker type="datetime" placeholder="选择日期" v-model="selfMessage.reg_time" style="width: 100%;" @change="changeregTime"></el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">其他资料</h1>
    
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="就读学校">
                            <el-input v-model="selfMessage.school" placeholder="请输入就读学校" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年级">
                            <el-select v-model="selfMessage.grade_id" placeholder="请选择年级" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in gradeList"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="出生日期">
                            <el-date-picker type="date" placeholder="选择日期" v-model="selfMessage.birthday" style="width: 100%;" @change="changeborthday"></el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="电子邮箱">
                            <el-input v-model="selfMessage.email" placeholder="请输入邮箱" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            <el-input v-model="selfMessage.qq" placeholder="请输入QQ" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="微信">
                            <el-input v-model="selfMessage.weixin" placeholder="请输入微信" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="地址">
                            <el-input v-model="selfMessage.address" placeholder="请输入地址" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="备注">
                            <el-input v-model="selfMessage.remark" placeholder="请输入备注信息" max-length="50"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">归属信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="市场专员">
                            <el-select filterable v-model="selfMessage.zy_id" placeholder="请选择市场专员" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in ygSimple"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="课程顾问">
                            <el-select filterable v-model="selfMessage.gw_id" placeholder="请选择课程顾问" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in guwenList"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">联系人信息</h1>
                <div class="tables">
                    <el-row :gutter="20">
                        <el-col :span="6">
                            姓名
                        </el-col>
                        <el-col :span="6">
                            关系
                        </el-col>
                        <el-col :span="6">
                            联系电话
                        </el-col>
                        <el-col :span="6">
                            工作
                        </el-col>
                    </el-row>
                    <el-row :gutter="20">
                        <el-col :span="6">
                            <el-input v-model="lianxiren.contact_name" placeholder="请输入姓名" max-length="50"></el-input>
                        </el-col>
                        <el-col :span="6">
                            <el-select v-model="lianxiren.relation" placeholder="请选择">
                                <el-option v-for="item in options" :label="item.name" :value="item.name">
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="6">
                            <el-input v-model="lianxiren.phone" placeholder="请输入电话" max-length="50"></el-input>
                        </el-col>
                        <el-col :span="6">
                            <el-input v-model="lianxiren.job" placeholder="请输入职务" max-length="50"></el-input>
                        </el-col>
                    </el-row>
                </div>
                <div class="bottom">
                    <el-button type="danger" @click="addxueyuanModel = false" class="btns">取消</el-button>
                    <el-button type="primary" @click="addNewStudent">提交</el-button>
                </div>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    props: ["changetimes"],
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        }
    },
    watch: {
        changetimes: function () {
            this.addxueyuanModel = true;
        }
    },
    data: function () {
        return {
            //增加学员弹出层
            addxueyuanModel: false,
            //form 表单
            labelWidth: "70px",
            //新增学员信息
            selfMessage: {
                campus_id: "",
                name: "",
                number: "",
                sex: "男",
                birthday: "",
                school: "",
                grade_id: "",
                phone: "",
                email: "",
                weixin: "",
                qq: "",
                address: "",
                remark: "",
                reg_time: "",
                sales_id: "",//销售人员id
                intention_id: "",//招生追踪关联id

                zy_id: "",
                gw_id: ""
            },
            //联系人列表
            lianxiren: {
                contact_name: "",
                relation: "",
                phone: "",
                job: ""
            },
            //联系人关系
            options: [
                {
                    name: "父亲"
                }, {
                    name: "母亲"
                }, {
                    name: "爷爷"
                }, {
                    name: "奶奶"
                }, {
                    name: "其他"
                }
            ],

            guwenList: [],

            //年级列表
            gradeList: window.sessionStorage.getItem("gradeList") ? JSON.parse(window.sessionStorage.getItem("gradeList")) : [],
        }
    },
    methods: {
        //生日格式化
        changeborthday: function (val) {
            this.selfMessage.birthday = val;
        },
        //入学日期格式化
        changeregTime: function (val) {
            this.selfMessage.reg_time = val;
        },
        //增加联系人
        addLianxiren: function (id) {
            var self = this;

            if (self.lianxiren.contact_name.trim() == "") {
                if (self.selfMessage.campus_id.trim() == "") {
                    // self.$message({
                    //     showClose: true,
                    //     message: "联系人添加失败，未选择校区",
                    //     type: "warning"
                    // })
                    return;
                }
                return;
            }
            if (self.lianxiren.relation.trim() == "") {
                // self.$message({
                //     showClose: true,
                //     message: "联系人添加失败，未填写联系人姓名",
                //     type: "warning"
                // })
                return;
            }
            if (self.lianxiren.phone.trim() == "") {
                // self.$message({
                //     showClose: true,
                //     message: "联系人添加失败，未填写联系人电话",
                //     type: "warning"
                // })
                return;
            }


            var formData = new FormData();
            formData.append("contact_name", self.lianxiren.contact_name);
            formData.append("relation", self.lianxiren.relation);
            formData.append("phone", self.lianxiren.phone);
            formData.append("job", self.lianxiren.job);

            self.$http.post("/api/student/" + id + "/contact", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {

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
        //新增学员
        addNewStudent: function () {
            var self = this;

            if (self.selfMessage.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "姓名不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.selfMessage.phone.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "联系电话不能为空",
                    type: "warning"
                })
                return;
            }
            if (self.selfMessage.campus_id.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: "warning"
                })
                return;
            }
            if (self.selfMessage.number.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写学号",
                    type: "warning"
                })
                return;
            }
            if (self.selfMessage.reg_time == "") {
                self.$message({
                    showClose: true,
                    message: "请选择入学日期",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();
            formData.append("campus_id", self.selfMessage.campus_id);
            formData.append("name", self.selfMessage.name);
            formData.append("number", self.selfMessage.number);
            formData.append("sex", self.selfMessage.sex);
            formData.append("birthday", self.selfMessage.birthday);
            formData.append("school", self.selfMessage.school);
            formData.append("grade_id", self.selfMessage.grade_id);
            formData.append("phone", self.selfMessage.phone);
            formData.append("email", self.selfMessage.email);
            formData.append("weixin", self.selfMessage.weixin);
            formData.append("qq", self.selfMessage.qq);
            formData.append("address", self.selfMessage.address);
            formData.append("remark", self.selfMessage.remark);
            formData.append("reg_time", self.selfMessage.reg_time);
            formData.append("sales_id", self.selfMessage.sales_id);
            formData.append("intention_id", self.selfMessage.intention_id);

            formData.append("zy_id", self.selfMessage.zy_id);
            formData.append("gw_id", self.selfMessage.gw_id);

            self.$http.post("/api/student", formData)
                .then(function (data) {
                    self.addxueyuanModel = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "添加成功"
                        });
                        self.selfMessage = {
                            campus_id: "",
                            name: "",
                            number: "",
                            sex: "男",
                            birthday: "",
                            school: "",
                            grade_id: "",
                            phone: "",
                            email: "",
                            weixin: "",
                            qq: "",
                            address: "",
                            remark: "",
                            reg_time: "",
                            sales_id: "",//销售人员id
                            intention_id: "",//招生追踪关联id

                            zy_id: "",
                            gw_id: ""
                        };
                        self.option = {
                            page: 1,
                            limit: 10,
                            search: self.selfMessage.name,
                            campus_id: "",
                            class_id: "",
                            start_time: "",
                            end_time: ""
                        };
                        //更新学员信息
                        self.$store.commit("updatestudentFun");
                        self.addLianxiren(data.data.data.id);//添加紧急联系人
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.addxueyuanModel = false;
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
        //新增学员选择校区
        changecampus_add: function (val) {
            var self = this;
            //更新市场专员
            this.$store.commit("getYGList", {
                self: self,
                campus_id: val,
                is_sc: 1
            })
            self.getKechengguwen(val);
        },
        //获取课程顾问
        getKechengguwen: function (campus_id) {
            var self = this;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.guwenList = data.data.data.staff;
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
    },
    created: function () {
        var self = this;
        self.getgradeList();
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$store.commit("getYGList", {
            self: self,
            campus_id: campus_id,
            is_sc: 1
        })
        self.getKechengguwen(campus_id);
        this.selfMessage.campus_id = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;
    }
}
</script>
<style lang="less" scoped>
.bottom {
    margin-top: 10px;
}

.tables {
    .el-row {
        margin-bottom: 20px;
    }
    .el-select {
        width: 100%;
    }
}
</style>

