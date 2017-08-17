<template>
    <div>
        <!--展示信息-->
        <div class="yuangong_xiugaixq zanshi" v-if="show">
            <el-form ref="form" :model="selfMessage" label-width="70px">
                <h1 class="xyxq_title">基础信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            <span>{{ selfMessage.student[0].name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="性别">
                            <span>{{ selfMessage.student[0].sex }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            <span>{{ selfMessage.student[0].phone }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="校区">
                            <span>{{ selfMessage.student[0].campus_name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学号">
                            <span>{{ selfMessage.student[0].number }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="入学日期">
                            <span>{{ selfMessage.student[0].reg_times }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">其他资料</h1>
    
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="就读学校">
                            <span>{{ selfMessage.student[0].school }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年级">
                            <span v-for="list in gradeList" v-if="selfMessage.student[0].grade_id ==list.id">{{ list.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="出生日期">
                            <span>{{ selfMessage.student[0].birthdays }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="电子邮箱">
                            <span>{{ selfMessage.student[0].email }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            <span>{{ selfMessage.student[0].qq }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="微信">
                            <span>{{ selfMessage.student[0].weixin }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="地址">
                            <span>{{ selfMessage.student[0].address }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="备注">
                            <span>{{ selfMessage.student[0].remark }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">归属信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="市场专员">
                            <span v-for="list in ygSimple" v-if="selfMessage.student[0].zy_id ==list.id">{{ list.name }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="课程顾问">
                            <span v-for="list in guwenList" v-if="selfMessage.student[0].gw_id ==list.id">{{ list.name }}</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
                <h1 class="xyxq_title">联系人信息</h1>
                <el-table :data="lianxirenList" style="width: 100%">
                    <el-table-column prop="contact_name" label="姓名">
                    </el-table-column>
                    <el-table-column prop="relation" label="关系">
                    </el-table-column>
                    <el-table-column prop="phone" label="联系电话">
                    </el-table-column>
                    <el-table-column prop="job" label="工作">
                    </el-table-column>
                </el-table>
                <div class="bottom">
                    <el-button type="danger" @click="delxueyuan(selfMessage.student[0].id,selfMessage.student[0].name)">删除学员</el-button>
                    <el-button type="primary" @click="show = false">修改信息</el-button>
                </div>
            </el-form>
        </div>
        <!--修改信息-->
        <div class="yuangong_xiugaixq" v-if="!show">
            <el-form ref="form" :model="selfMessage" label-width="70px">
                <h1 class="xyxq_title">基础信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            <el-input v-model="selfMessage.student[0].name"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="性别">
                            <el-radio-group v-model="selfMessage.student[0].sex">
                                <el-radio label="男"></el-radio>
                                <el-radio label="女"></el-radio>
                                <el-radio label="未知"></el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            <el-input v-model="selfMessage.student[0].phone"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="校区">
                            <el-select disabled v-model="selfMessage.student[0].campus_name" placeholder="请选择校区" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学号">
                            <el-input v-model="selfMessage.student[0].number"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="入学日期">
                            <el-date-picker type="date" placeholder="选择日期" v-model="selfMessage.student[0].reg_times" style="width: 100%;" placeholder="请选择入学日期" @change="changeregtime"></el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <p class="line"></p>
                <h1 class="xyxq_title">其他资料</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="就读学校">
                            <el-input v-model="selfMessage.student[0].school" placeholder="请输入学校"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="年级">
                            <el-select v-model="selfMessage.student[0].grade_id" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in gradeList" placeholder="请选择年级"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="出生日期">
                            <el-date-picker type="date" placeholder="选择日期" v-model="selfMessage.student[0].birthdays" style="width: 100%;" placeholder="请选择日期" @change="changeborthday"></el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="电子邮箱">
                            <el-input v-model="selfMessage.student[0].email" placeholder="请输入邮箱"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            <el-input v-model="selfMessage.student[0].qq" placeholder="请输入QQ"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="微信">
                            <el-input v-model="selfMessage.student[0].weixin" placeholder="请输入微信"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="地址">
                            <el-input v-model="selfMessage.student[0].address" placeholder="请输入地址"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="30">
                    <el-col :span="24">
                        <el-form-item label="备注">
                            <el-input v-model="selfMessage.student[0].remark" placeholder="请输入备注信息"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <p class="line"></p>
                <h1 class="xyxq_title">归属信息</h1>
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="市场专员">
                            <el-select v-model="selfMessage.student[0].zy_id" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in ygSimple" placeholder="请选择市场专员"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="课程顾问">
                            <el-select v-model="selfMessage.student[0].gw_id" style="width:100%">
                                <el-option :label="list.name" :value="list.id" v-for="list in guwenList" placeholder="请选择课程顾问"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <p class="line"></p>
                <h1 class="xyxq_title">联系人信息
                    <el-button type="primary" size="mini" @click="openLianxiren">添加联系人</el-button>
                </h1>
                <el-table :data="lianxirenList" style="width: 100%">
                    <el-table-column prop="contact_name" label="姓名">
                    </el-table-column>
                    <el-table-column prop="relation" label="关系">
                    </el-table-column>
                    <el-table-column prop="phone" label="联系电话">
                    </el-table-column>
                    <el-table-column prop="job" label="工作">
                    </el-table-column>
                    <el-table-column label="操作">
                        <template scope="scope">
                            <el-button type="primary" size="mini" @click="delLianxiren(scope.row.id)">删除联系人</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="bottom">
                    <el-button type="danger" @click="show = true" class="btns">取消</el-button>
                    <el-button type="primary" @click="xiugaiMessage">保存</el-button>
                </div>
            </el-form>
        </div>
        <!--添加联系人弹窗-->
        <el-dialog title="添加联系人" v-model="lianxirenshow" size="tiny">
            <el-form :model="lianxiren" label-width="80px">
                <el-form-item label="姓名*">
                    <el-input v-model="lianxiren.contact_name" auto-complete="off" placeholder="请输入姓名"></el-input>
                </el-form-item>
                <el-form-item label="关系*">
                    <el-select v-model="lianxiren.relation" placeholder="请选择关系">
                        <el-option :label="list.name" :value="list.name" v-for="list in options"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="联系电话*">
                    <el-input v-model="lianxiren.phone" auto-complete="off" placeholder="请输入联系电话"></el-input>
                </el-form-item>
                <el-form-item label="工作">
                    <el-input v-model="lianxiren.job" auto-complete="off" placeholder="请输入工作"></el-input>
                </el-form-item>
                <div class="bottoms">
                    <el-button @click="lianxirenshow = false">取 消</el-button>
                    <el-button type="primary" @click="addLianxiren">确 定</el-button>
                </div>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            show: true,
            canSave: true,
            //学员信息
            selfMessages: {
                student: [
                    {}
                ]
            },
            selfMessage: {
                student: [
                    {}
                ]
            },
            //联系人信息
            lianxirenList: [],
            lianxirenshow: false,
            // 添加联系人
            lianxiren: {
                contact_name: "",
                relation: "",
                phone: "",
                job: ""
            },
            //年级列表
            gradeList: window.sessionStorage.getItem("gradeList") ? JSON.parse(window.sessionStorage.getItem("gradeList")) : [],
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

            guwenList: []
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        }
    },
    methods: {
        //获取课程顾问
        getKechengguwen: function (campus_id) {
            var self = this;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.guwenList = data.data.data.staff;
                    } else {
                        store.commit("checkError", {
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
        //选择生日
        changeborthday: function (val) {
            this.selfMessage.student[0].birthdays = val;
        },
        //请选择入学日期
        changeregtime: function (val) {
            this.selfMessage.student[0].reg_times = val;
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
        //删除学员
        delxueyuan: function (id, name) {
            var self = this;

            this.$confirm('是否要删除该学员所有个人信息？', '删除学员', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'error'
            }).then(function () {
                self.$http.post("/api/student/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "已被删除"
                            });
                            var  yx_xygl_all = self.pdqx(["yx_xygl_all", "yx"]);
                            var  yx_xygl_my = self.pdqx(["yx_xygl_my", "yx"]);
                            if(yx_xygl_all){
                                self.$router.push({ name: 'student_list',params:{ type:'All'} });
                            }else if(!yx_xygl_all && yx_xygl_my){
                                self.$router.push({ name: 'student_list1',params:{ type:'All'} });
                            }else{
                                self.$router.push({ name: 'worktody'});
                            }
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
        //日期转换
        transformTime: function (val) {
            if (val == "0" || val == '') {
                return "";
            }
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
        },
        //打开新增联系人
        openLianxiren: function () {
            this.lianxirenshow = true;
            this.lianxiren = {
                contact_name: "",
                relation: "",
                phone: "",
                job: ""
            }
        },
        //修改个人信息
        xiugaiMessage: function () {
            var self = this;
            var cansaves = false;
            for (var k in self.selfMessage.student[0]) {
                if (self.selfMessages.student[0][k] != self.selfMessage.student[0][k]) {
                    cansaves = true;
                }
            }

            if (!cansaves) {
                self.$message({
                    showClose: true,
                    message: "请修改信息后再进行保存",
                    type: 'warning'
                });
                return;
            }

            if (self.selfMessage.student[0].name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "姓名不能为空",
                    type: 'warning'
                });
                return;
            }

            if (self.selfMessage.student[0].phone.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "联系电话不能为空",
                    type: 'warning'
                });
                return;
            }

            if (self.selfMessage.student[0].number.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "学号不能为空",
                    type: 'warning'
                });
                return;
            }

            if (self.selfMessage.student[0].reg_time.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "入学时间不能为空",
                    type: 'warning'
                });
                return;
            }




            var formData = new FormData();
            formData.append("name", self.selfMessage.student[0].name);
            formData.append("number", self.selfMessage.student[0].number);
            formData.append("sex", self.selfMessage.student[0].sex);
            formData.append("birthday", self.selfMessage.student[0].birthdays);
            formData.append("school", self.selfMessage.student[0].school);
            formData.append("grade_id", self.selfMessage.student[0].grade_id);
            formData.append("phone", self.selfMessage.student[0].phone);
            formData.append("email", self.selfMessage.student[0].email);
            formData.append("weixin", self.selfMessage.student[0].weixin);
            formData.append("qq", self.selfMessage.student[0].qq);
            formData.append("reg_time", self.selfMessage.student[0].reg_times);
            formData.append("address", self.selfMessage.student[0].address);
            formData.append("remark", self.selfMessage.student[0].remark);
            formData.append("zy_id", self.selfMessage.student[0].zy_id);
            formData.append("gw_id", self.selfMessage.student[0].gw_id);

            if (!self.canSave) {
                self.$message({
                    showClose: true,
                    message: "请稍后再试",
                    type: 'warning'
                });
                return;
            }
            self.canSave = false;
            self.$http.post("/api/student/" + self.$route.params.id, formData)
                .then(function (data) {
                    self.canSave = true;
                    if (data.data.status == 'ok') {
                        for (var k in self.selfMessage.student[0]) {
                            self.selfMessages.student[0][k] = self.selfMessage.student[0][k];
                        }
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.show = true;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.canSave = true;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //增加联系人
        addLianxiren: function () {
            var self = this;

            if (self.lianxiren.contact_name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "姓名不能为空",
                    type: 'warning'
                });
                return;
            }
            if (self.lianxiren.relation.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择与学员之间的关系",
                    type: 'warning'
                });
                return;
            }
            if (self.lianxiren.phone.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "电话不能为空",
                    type: 'warning'
                });
                return;
            }


            var formData = new FormData();
            formData.append("contact_name", self.lianxiren.contact_name);
            formData.append("relation", self.lianxiren.relation);
            formData.append("phone", self.lianxiren.phone);
            formData.append("job", self.lianxiren.job);

            self.$http.post("/api/student/" + self.$route.params.id + "/contact", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.lianxirenshow = false;
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.getLianxiren();
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
        //删除联系人
        delLianxiren: function (id) {
            var self = this;
            self.$confirm('确定删除该联系人?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/student/" + self.$route.params.id + "/del_contact/" + id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: "删除成功",
                            });
                            self.getLianxiren();
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
        //获取联系人
        getLianxiren: function () {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id + "/contact")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.lianxirenList = data.data.data;
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
        //获取个人信息
        getMessage: function () {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.student.length; i++) {
                            data.data.data.student[i].reg_times = self.transformTime(data.data.data.student[i].reg_time)
                            data.data.data.student[i].birthdays = self.transformTime(data.data.data.student[i].birthday)
                            for (var k = 0; k < self.campus.length; k++) {
                                if (data.data.data.student[i].campus_id == self.campus[k].id) {
                                    data.data.data.student[i].campus_name = self.campus[k].name;
                                    break;
                                }
                            }
                        }

                        self.selfMessage = data.data.data;
                        //获取市场专员和顾问
                        self.$store.commit("getYGList", {
                            self: self,
                            campus_id: self.selfMessage.student[0].campus_id,
                            is_sc: 1
                        })
                        self.getKechengguwen(self.selfMessage.student[0].campus_id);

                        for (var k in self.selfMessage.student[0]) {
                            self.selfMessages.student[0][k] = self.selfMessage.student[0][k];
                        }
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
        this.$store.commit('setyx_xy_xq_meau_id', 1);
        this.getMessage();
        this.getLianxiren();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.btns {
    background-color: #ddd;
    border: none;
    &:hover {
        opacity: .8;
    }
    &:active {
        opacity: .5;
    }
}

.bottom {
    text-align: right;
    padding: 10px 0;
    padding-top: 20px;
}

.bottoms {
    text-align: right;
}

.el-select {
    width: 100%;
}

.xyxq_title {
    button {
        float: right;
    }
}

.el-dialog__body {
    padding: 0;
}

.zanshi {
    .el-form-item {
        margin-bottom: 0;
    }
    .line {
        margin: 10px 0 20px 0;
    }
}
</style>