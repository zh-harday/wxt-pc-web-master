<template>
    <div>
        <h1 class="right_title">
            员工管理
            <br>
            <el-button type="success" size="small" @click="addNewyuangong" v-if="yx_yggl_add">添加员工</el-button>
            <el-input placeholder="请输员工姓名" icon="search" v-model="obj.search" :on-icon-click="serachFun_xy" class="btn" style="width:200px" @keyup.enter.native="serachFun_xy"></el-input>
        </h1>
        <!--校区筛选-->
        <ul class="campus_list" v-if="myMessage.campus_id == 1">
            <li :class="{ active : campus_id == -2 }" @click="clickCampus(-2,'')">全部</li>
            <li :class="{ active : campus_id == -1 }" @click="clickCampus(-1,1)">所有校区</li>
            <li v-for="(list,index) in campus" :class="{ active : campus_id == index }" @click="clickCampus(index,list.id)">{{ list.name }}</li>
        </ul>
        <!--员工列表-->
        <el-table :data="yuangongList.staff" style="width: 100%" class="table_moban" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="t" label="头像">
                <template scope="scope">
                    <span class="photoBox">
                        <img :src="'http://wx.eduwxt.com/api/face/teacher/'+scope.row.id" alt="">
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="姓名">
            </el-table-column>
            <el-table-column prop="staff_id" label="工号">
            </el-table-column>
            <el-table-column prop="campus_id" label="所属校区">
                <template scope="scope">
                    <span v-if="scope.row.campus_id == 1">所有校区</span>
                    <span v-for="item in campus" v-if="scope.row.campus_id == item.id">
                        {{ item.name }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="department_name" label="部门">
            </el-table-column>
            <el-table-column prop="group_name" label="职务">
            </el-table-column>
            <el-table-column label="微校通">
                <template scope="scope">
                    <el-tag type="danger" v-if="scope.row.wecha_id == '' || scope.row.wecha_id == null ">未绑定</el-tag>
                    <el-tag type="success" v-else>已绑定</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="操作" min-width="250">
                <template scope="scope">
                    <el-button type="success" size="mini" @click="$router.push({name:'staff_detail_xg',params:{id:scope.row.id}})" v-if="yx_yggl_view || yx_yggl_edit ">员工详情</el-button>
                    <el-button type="warning" size="mini" v-if="scope.row.wecha_id != '' && scope.row.wecha_id !=  null " @click="unbaning(scope.row.id)">解除绑定</el-button>
                    <el-button type="info" size="mini" v-else @click="baning(scope.row.id)">立即绑定</el-button>
                    <el-button type="danger" size="mini" v-if="yx_yggl_edit && scope.row.staus ==1" @click="changeStatus(scope.row.id,0)">停用账号</el-button>
                    <el-button type="info" size="mini" v-if="yx_yggl_edit && scope.row.staus ==0" @click="changeStatus(scope.row.id,1)">启用账号</el-button>
                </template>
            </el-table-column>
        </el-table>
    
        <el-dialog title="提示" v-model="imgVisible" size="tiny">
            <img :src="imgsrc" class="imgbox">
            <p class="imgtext">您的账号暂未绑定微信</p>
            <p class="imgtext">请使用
                <span>微信扫描二维码</span>进行绑定</p>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="imgVisible = false">确 定</el-button>
            </span>
        </el-dialog>
    
        <!--分页-->
        <div class="fenye" v-show="yuangongList.count > yuangongList.limit">
            <el-pagination @current-change="handleCurrentChange" :current-page="ygindex" :page-sizes="[15, 20, 30, 40]" :page-size="obj.pageSize" layout="total, prev, pager, next" :total="yuangongList.count">
            </el-pagination>
        </div>
        <!--新增员工-->
        <el-dialog title="添加新员工" v-model="dialogVisible" size="small" :lock-scroll="true">
            <div class="yuangong_xiugaixq">
                <p class="line"></p>
                <el-form ref="form" :model="form" :label-width="labelWidth">
    
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="姓名*">
                                <el-input v-model="form.name" placeholder="请输入姓名"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="性别*">
                                <el-radio-group v-model="form.sex">
                                    <el-radio label="男"></el-radio>
                                    <el-radio label="女"></el-radio>
                                    <el-radio label="其他"></el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="工号*">
                                <el-input v-model="form.staff_id" placeholder="请输入工号"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="任职校区*">
                                <el-select v-model="form.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id == 1">
                                    <el-option label="所有校区" value="1"></el-option>
                                    <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                                </el-select>
                                <el-select v-model="form.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1" disabled>
                                    <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="入职时间*">
                                <el-date-picker type="date" placeholder="请选择入职时间" v-model="form.entry_time" style="width: 100%;" @change="chengeEntrytime"></el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="是否全职*">
                                <el-radio-group v-model="form.is_full_time">
                                    <el-radio label="全职"></el-radio>
                                    <el-radio label="兼职"></el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="部门*">
                                <el-select v-model="form.description_id" placeholder="请选择部门" @change="changeDepartment">
                                    <el-option :label="list.name" :value="list.id" v-for="list in departmentALL"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="职务*">
                                <el-select v-model="form.group_id" placeholder="请选择职务">
                                    <el-option :label="list.name" :value="list.id" v-for="list in group" :disabled="list.id == 1"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-form-item label="职位备注">
                        <el-input :autosize="{ minRows: 1, maxRows: 2}" type="textarea" placeholder="请输入职务描述信息" v-model="form.job_description" max-length="500">
                    </el-form-item>
    
                    <p class="line"></p>
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="出生日期">
                                <el-date-picker type="date" placeholder="选择出生日期" v-model="form.birthday" style="width: 100%;" @change="chengeborthday"></el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="邮箱">
                                <el-input v-model="form.email" placeholder="请输入邮箱"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="QQ">
                                <el-input v-model="form.qq" placeholder="请输入QQ号码"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="微信">
                                <el-input v-model="form.weixin_id" placeholder="请输入微信"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="联系电话*">
                                <el-input v-model="form.phone" placeholder="请输入联系电话"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="身份证号">
                                <el-input v-model="form.sfz" placeholder="请输入身份证号码"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="30">
                        <el-col :span="24">
                            <el-form-item label="个人备注">
                                <el-input :autosize="{ minRows: 1, maxRows: 2}" type="textarea" placeholder="请输员工描述信息" v-model="form.remark" max-length="500">
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <p class="line"></p>
    
                    <el-row :gutter="30">
                        <el-col :span="24">
                            <el-form-item label="工作职能权限" label-width="110px">
                                <el-checkbox v-model="form.is_sc">市场工作权限</el-checkbox>
                                <el-checkbox v-model="form.is_gw">顾问工作权限</el-checkbox>
                                <el-checkbox v-model="form.is_bzr">班主任工作权限</el-checkbox>
                                <el-checkbox v-model="form.is_dk">代课老师工作权限</el-checkbox>
                                <el-checkbox v-model="form.is_stk">试听老师工作权限</el-checkbox>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <p class="line"></p>
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="登陆账号*">
                                <el-input v-model="form.account" placeholder='字母或数字组成'></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="密码*">
                                <div class="psd_box">
                                    <el-input v-model="form.password" type="password" placeholder="请设置密码"></el-input>
                                </div>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <div class="bottom">
                        <el-button type="primary" class="quxiaobtn" @click="dialogVisible = false">取消</el-button>
                        <el-button type="primary" @click="addYuangong">添加</el-button>
                    </div>
                </el-form>
            </div>
    
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
        departmentALL: function () {
            return this.$store.state.department;
        },
        ygindex: function () {
            return this.$store.state.ygindex;
        }
    },
    watch: {
        $route: function (val) {
            var self = this;
            self.$store.commit("setygindex", 1);
            self.left_id = val.query.index;
            self.$store.commit('setyx_yuangong_meau_id', self.left_id);
            self.obj.department_id = self.$route.params.id == 'All' ? "" : self.$route.params.id;
            self.getyuangongList({
                limit: self.obj.pageSize,
                department_id: self.obj.department_id,
                campus_id: self.obj.campus_id,
                search: "",
            })
        }
    },
    data: function () {
        return {
            isEnd: true,
            campus_id: -2,
            left_id: -1,
            loading: false,
            yuangongList: {
                staff: []
            },
            obj: {
                pageSize: 15,
                campus_id: "",
                department_id: "",
                search: ""
            },

            group: [],
            dialogVisible: false,
            labelWidth: "80px",
            form: {
                name: "",
                sex: "男",
                staff_id: "",
                campus_id: "",
                group_id: "",//职位
                description_id: "",
                job_description: "",
                entry_time: "",
                is_full_time: "全职",
                birthday: "",
                email: "",
                qq: "",
                weixin_id: "",
                remark: "",
                phone: "",
                sfz: "",
                account: "",
                password: "",

                is_sc: false,
                is_gw: false,
                is_bzr: false,
                is_dk: false,
                is_stk: false
            },
            imgVisible: false,
            imgsrc: "",


            //权限
            yx_yggl_add: false,
            yx_yggl_edit: false,
            yx_yggl_view: false
        }
    },
    methods: {
        //解绑二维码
        unbaning: function (id) {
            var self = this;
            self.$confirm('是否要将此员工与其微信解除绑定?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.get("/api/staff/" + id + "/unbind")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getyuangongList({
                                limit: self.obj.pageSize,
                                department_id: self.obj.department_id,
                                campus_id: self.obj.campus_id,
                                search: self.obj.search,
                            })

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
            }).catch(function () {

            });

        },
        //绑定二维码
        baning: function (id) {
            this.imgVisible = true;
            this.imgsrc = "/api/staff/" + id + "/bind_qrcode";
        },
        chengeborthday: function (val) {
            this.form.birthday = val;
        },
        chengeEntrytime: function (val) {
            this.form.entry_time = val;
        },
        //修改员工状态
        changeStatus: function (id, status) {
            var formData = new FormData();
            formData.append("staus", status);
            var self = this;
            if (status == 0) {
                self.$confirm('确定要停用该账号？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    self.$http.post("/api/staff/" + id + "/staus", formData)
                        .then(function (data) {
                            if (data.data.status == 'ok') {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: "该账号已被停用"
                                });
                                for (var i = 0; i < self.yuangongList.staff.length; i++) {
                                    if (self.yuangongList.staff[i].id == id) {
                                        self.yuangongList.staff[i].staus = status;
                                        break;
                                    }
                                }
                            } else {
                                self.$store.commit("checkError", {
                                    self: self,
                                    data: data.data
                                });
                            }
                        }, function () {
                            self.dialogVisible = false;
                            self.$message({
                                showClose: true,
                                message: "网络错误",
                                type: "error"
                            })
                        })
                }).catch(() => {

                });
            }
            if (status == 1) {
                self.$confirm('确定要启用该账号？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    self.$http.post("/api/staff/" + id + "/staus", formData)
                        .then(function (data) {
                            if (data.data.status == 'ok') {
                                self.$store.commit("success", {
                                    self: self,
                                    msg: "启用成功"
                                });
                                for (var i = 0; i < self.yuangongList.staff.length; i++) {
                                    if (self.yuangongList.staff[i].id == id) {
                                        self.yuangongList.staff[i].staus = status;
                                        break;
                                    }
                                }
                            } else {
                                self.$store.commit("checkError", {
                                    self: self,
                                    data: data.data
                                });
                            }
                        }, function () {
                            self.dialogVisible = false;
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
        //添加员工
        addYuangong: function () {
            var self = this;
            if (self.form.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入姓名",
                    type: 'warning'
                });
                return;
            }
            if (self.form.staff_id.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入工号",
                    type: 'warning'
                });
                return;
            }
            if (self.form.campus_id.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: 'warning'
                });
                return;
            }
            if (self.form.entry_time == "") {
                self.$message({
                    showClose: true,
                    message: "请设置入职时间",
                    type: 'warning'
                });
                return;
            }
            if (self.form.group_id == "") {
                self.$message({
                    showClose: true,
                    message: "请选择职位",
                    type: 'warning'
                });
                return;
            }

            if (!(/^1(3|4|5|7|8)\d{9}$/.test(self.form.phone))) {
                self.$message({
                    showClose: true,
                    message: "联系电话为11位手机号码",
                    type: 'warning'
                });
                return;
            }

            var patten = /^[a-zA-Z]\w{3,15}$/ig;

            if (!patten.test(self.form.account)) {
                self.$message({
                    showClose: true,
                    message: "登陆账号格式不正确",
                    type: 'warning'
                });
                return;
            }
            if (self.form.password.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入密码",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();
            formData.append("account", self.form.account);
            formData.append("password", self.form.password);
            formData.append("name", self.form.name);
            formData.append("email", self.form.email);
            formData.append("phone", self.form.phone);
            formData.append("campus_id", self.form.campus_id);
            formData.append("group_id", self.form.group_id);
            formData.append("job_description", self.form.job_description);
            formData.append("staff_id", self.form.staff_id);
            formData.append("sex", self.form.sex);
            formData.append("entry_time", self.form.entry_time);
            formData.append("is_full_time", self.form.is_full_time);
            formData.append("sfz", self.form.sfz);
            formData.append("birthday", self.form.birthday);
            formData.append("qq", self.form.qq);
            formData.append("weixin_id", self.form.weixin_id);
            formData.append("remark", self.form.remark);

            self.form.is_sc = self.form.is_sc ? "1" : "0";
            self.form.is_gw = self.form.is_gw ? "1" : "0";
            self.form.is_bzr = self.form.is_bzr ? "1" : "0";
            self.form.is_dk = self.form.is_dk ? "1" : "0";
            self.form.is_stk = self.form.is_stk ? "1" : "0";
            formData.append("is_sc", self.form.is_sc);
            formData.append("is_gw", self.form.is_gw);
            formData.append("is_bzr", self.form.is_bzr);
            formData.append("is_dk", self.form.is_dk);
            formData.append("is_stk", self.form.is_stk);

            self.$http.post("/api/staff", formData)
                .then(function (data) {

                    if (data.data.status == 'ok') {
                        self.dialogVisible = false;
                        self.$store.commit("success", {
                            self: self,
                            msg: "添加成功"
                        });
                        self.$store.commit("setygindex", 1);
                        self.getyuangongList({
                            limit: self.obj.pageSize,
                            department_id: "",
                            campus_id: "",
                            search: self.obj.search,
                        })
                        self.campus_id = -2;
                        self.$store.commit('setyx_yuangong_meau_id', -1);
                        self.form = {
                            name: "",
                            sex: "男",
                            staff_id: "",
                            campus_id: "",
                            group_id: "",//职位
                            description_id: "",
                            job_description: "",
                            entry_time: "",
                            is_full_time: "全职",
                            birthday: "",
                            email: "",
                            qq: "",
                            weixin_id: "",
                            remark: "",
                            phone: "",
                            sfz: "",
                            account: "",
                            password: "",

                            is_sc: false,
                            is_gw: false,
                            is_bzr: false,
                            is_dk: false,
                            is_stk: false
                        }
                    } else {
                        self.form.is_sc = false;
                        self.form.is_gw = false;
                        self.form.is_bzr = false;
                        self.form.is_dk = false;
                        self.form.is_stk = false;
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.dialogVisible = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //改变部门-
        changeDepartment: function (val) {
            var self = this;
            for (var i = 0; i < self.departmentALL.length; i++) {
                if (self.departmentALL[i].id == val) {
                    self.group = self.departmentALL[i].group;
                    self.form.group_id = self.group[0] ? self.group[0].id : "";
                    self.form.group_id = self.form.group_id == 1 ? "" : self.form.group_id;
                    break;
                }

            }
        },
        //打开新增员工
        addNewyuangong: function () {
            this.dialogVisible = true;
        },
        //搜素
        serachFun_xy: function () {
            var self = this;
            self.$store.commit("setygindex", 1);
            self.getyuangongList({
                limit: self.obj.pageSize,
                department_id: "",
                campus_id: "",
                search: self.obj.search,
            })
            self.campus_id = -2;
            self.$store.commit('setyx_yuangong_meau_id', -1)
        },
        //点击校区筛选
        clickCampus: function (index, id) {
            var self = this;
            if (!self.isEnd) {
                self.$message({
                    showClose: true,
                    message: "数据加载中，请稍后",
                    type: 'warning'
                });
                return;
            }
            self.$store.commit("setygindex", 1);
            self.isEnd = false;
            self.campus_id = index;
            self.obj.campus_id = id;

            self.getyuangongList({
                limit: self.obj.pageSize,
                department_id: self.obj.department_id,
                campus_id: self.obj.campus_id,
                search: "",
            })
        },
        //获取员工列表
        getyuangongList: function (obj) {
            var self = this;
            self.loading = true;
            var limit = obj.limit;
            var department_id = obj.department_id;
            var campus_id = self.myMessage.campus_id == "1" ? obj.campus_id : self.myMessage.campus_id;
            var search = obj.search;
            self.$http.get("/api/staff?page=" + self.ygindex + "&limit=" + limit + "&department_id=" + department_id + "&campus_id=" + campus_id + "&search=" + search)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.yuangongList = data.data.data;
                        self.loading = false;
                        self.isEnd = true;
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
        },
        //点击页码
        handleCurrentChange: function (val) {
            var self = this;
            self.$store.commit("setygindex", val);
            self.getyuangongList({
                limit: self.obj.pageSize,
                department_id: self.obj.department_id,
                campus_id: self.obj.campus_id,
                search: self.obj.search,
            })
        }
    },
    created: function () {
        var self = this;
    
        self.form.campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.getyuangongList({
            page: self.ygindex,
            limit: self.obj.pageSize,
            department_id: self.obj.department_id,
            campus_id: self.obj.campus_id,
            search: self.obj.search,
        })

        //权限设置
        self.yx_yggl_add = self.pdqx(["yx_yggl_add","yx_yggl", "yx"]);
        self.yx_yggl_edit = self.pdqx(["yx_yggl_edit","yx_yggl", "yx"]);
        self.yx_yggl_view = self.pdqx(["yx_yggl_view","yx_yggl", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.imgbox {
    width: 170px;
    display: block;
    margin: 0 auto;
}

.imgtext {
    text-align: center;
    font-size: 14px;
    color: @c2;
    span {
        color: @c1;
        font-size: @h2;
    }
}

.photoBox {
    display: block;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    overflow: hidden;
    img {
        display: block;
        width: 34px;
        min-height: 34px;
    }
}

.yuangong_xiugaixq {
    padding: 10px;
    padding-top: 0;
}

.el-select {
    width: 100%;
}

.el-dialog__body {
    padding-top: 10px;
}
</style>