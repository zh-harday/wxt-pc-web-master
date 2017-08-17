<template>
    <div>
        <!--预览-->
        <div class="yuangong_xiugaixq" v-if="yuangongxiugai">
            <el-form ref="form" :model="selfMessage" :label-width="labelWidth">
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            {{ selfMessage.name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="性别">
                            {{ selfMessage.sex }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="工号">
                            {{ selfMessage.staff_id }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="任职校区">
                            <span v-for="item in campus" v-if="selfMessage.campus_id == item.id">{{ item.name }}</span>
                            <span v-if="selfMessage.campus_id == 1">所有校区</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="部门">
                            {{ selfMessage.department_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="职务">
                            {{ selfMessage.group_name }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="入职时间">
                            {{ selfMessage.entry_time }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="是否全职">
                            {{ selfMessage.is_full_time }}
                        </el-form-item>
                    </el-col>
    
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="职务备注">
                            {{ selfMessage.job_description }}
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="出生日期">
                            {{ selfMessage.birthday }}
                        </el-form-item>
    
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="邮箱">
                            {{ selfMessage.email }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            {{ selfMessage.qq }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="微信">
                            {{ selfMessage.weixin_id }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            {{ selfMessage.phone }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="身份证号">
                            {{ selfMessage.sfz }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="个人备注">
                            {{ selfMessage.remark }}
                        </el-form-item>
                    </el-col>
    
                </el-row>
    
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="工作职能权限" label-width="100px">
                            <span v-if="selfMessage.is_sc == 1" class="zhinengquanxian">市场工作权限</span>
                            <span v-if="selfMessage.is_gw == 1" class="zhinengquanxian">顾问工作权限</span>
                            <span v-if="selfMessage.is_bzr == 1" class="zhinengquanxian">班主任工作权限</span>
                            <span v-if="selfMessage.is_dk == 1" class="zhinengquanxian">代课老师工作权限</span>
                            <span v-if="selfMessage.is_stk == 1" class="zhinengquanxian">试听老师工作权限</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="12">
                        <el-form-item label="登陆账号">
                            <span>{{ selfMessage.account }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="密码">
                            <div class="psd_box">
                                <input v-model="password" type="password" class="input_psd" disabled />
                                <el-button type="text" size="small" @click="openMMbox" class="xiugaimimabtn" v-if="yx_yggl_edit && selfMessages.group_id != 1">修改密码</el-button>
                                <el-button type="text" size="small" @click="openMMbox" class="xiugaimimabtn" v-if="yx_yggl_edit && selfMessages.group_id == 1 && myMessage.group_id == 1">修改密码</el-button>
                            </div>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <div class="bottom">
                    <el-button type="primary" @click="openxiugaiziliao" v-if="yx_yggl_edit">修改资料</el-button>
                </div>
            </el-form>
        </div>
        <!--修改-->
        <div class="yuangong_xiugaixq" v-if="!yuangongxiugai">
            <el-form ref="form" :model="selfMessage" :label-width="labelWidth">
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="姓名">
                            <el-input v-model="selfMessage.name"></el-input>
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
                        <el-form-item label="工号">
                            <el-input v-model="selfMessage.staff_id"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="任职校区">
                            <el-select v-model="selfMessage.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id == 1 && selfMessages.group_id != 1">
                                <el-option label="所有校区" value="1"></el-option>
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                            <el-select v-model="selfMessage.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1 || selfMessages.group_id == 1" disabled>
                                <el-option label="所有校区" value="1"></el-option>
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="部门">
                            <el-select v-model="selfMessage.department_id" placeholder="请选择部门" @change="changeDepartment" v-if="selfMessages.group_id != 1">
                                <el-option :label="list.name" :value="list.id" v-for="list in departmentALL"></el-option>
                            </el-select>
                            <el-select v-model="selfMessage.department_id" placeholder="请选择部门" @change="changeDepartment" v-if="selfMessages.group_id == 1" disabled>
                                <el-option :label="list.name" :value="list.id" v-for="list in departmentALL"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="职务">
                            <el-select v-model="selfMessage.group_id" placeholder="请选择职务" v-if="selfMessages.group_id != 1">
                                <el-option :label="list.name" :value="list.id" v-for="list in group" :disabled="list.id ==1"></el-option>
                            </el-select>
                            <el-select v-model="selfMessage.group_id" placeholder="请选择职务" v-if="selfMessages.group_id == 1" disabled>
                                <el-option label="系统管理员" value="1"></el-option>
                                <el-option label="系统管理员" value=""></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="入职时间">
                            <el-date-picker type="date" @change="setEntry_time" placeholder="选择日期" v-model="selfMessage.entry_time" style="width: 100%;"></el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="是否全职">
                            <el-radio-group v-model="selfMessage.is_full_time">
                                <el-radio label="全职"></el-radio>
                                <el-radio label="兼职"></el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
    
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="职位备注">
                            <el-input v-model="selfMessage.job_description" :autosize="{ minRows: 1, maxRows: 2}" type="textarea" placeholder="请输入职务描述信息"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="出生日期">
                            <el-date-picker type="date" @change="setBorthday" placeholder="选择日期" v-model="selfMessage.birthday" style="width: 100%;"></el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="邮箱">
                            <el-input v-model="selfMessage.email"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="QQ">
                            <el-input v-model="selfMessage.qq"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="8">
                        <el-form-item label="微信">
                            <el-input v-model="selfMessage.weixin_id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="联系电话">
                            <el-input v-model="selfMessage.phone"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="身份证号">
                            <el-input v-model="selfMessage.sfz"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="个人备注">
                            <el-input v-model="selfMessage.remark" :autosize="{ minRows: 1, maxRows: 2}" type="textarea" placeholder="请输员工描述信息"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="24">
                        <el-form-item label="工作职能权限" label-width="100px">
                            <el-checkbox v-model="zhinengList.is_sc">市场工作权限</el-checkbox>
                            <el-checkbox v-model="zhinengList.is_gw">顾问工作权限</el-checkbox>
                            <el-checkbox v-model="zhinengList.is_bzr">班主任工作权限</el-checkbox>
                            <el-checkbox v-model="zhinengList.is_dk">代课老师工作权限</el-checkbox>
                            <el-checkbox v-model="zhinengList.is_stk">试听老师工作权限</el-checkbox>
                        </el-form-item>
    
                    </el-col>
    
                </el-row>
    
                <p class="line"></p>
    
                <el-row :gutter="40">
                    <el-col :span="12">
                        <el-form-item label="登陆账号">
                            <span>{{ selfMessage.account }}</span>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="密码">
                            <div class="psd_box">
                                <input v-model="password" type="password" class="input_psd" disabled />
                                <el-button type="text" size="small" @click="openMMbox" class="xiugaimimabtn" v-if="yx_yggl_edit && selfMessages.group_id != 1">修改密码</el-button>
                                <el-button type="text" size="small" @click="openMMbox" class="xiugaimimabtn" v-if="yx_yggl_edit && selfMessages.group_id == 1 && myMessage.group_id == 1">修改密码</el-button>
                            </div>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <div class="bottom">
                    <el-button type="primary" class="quxiaobtn" @click="yuangongxiugai = true">取消</el-button>
                    <el-button type="primary" @click="xiugaiMessage" :loading="saveLoading">保存</el-button>
                </div>
            </el-form>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            saveLoading: false,
            yuangongxiugai: true,
            labelWidth: "70px",
            canSave: true,
            input_password: false,
            password: "******",
            selfMessage: {},
            selfMessages: {},
            group: [],

            zhinengList: {
                is_sc: false,
                is_gw: false,
                is_bzr: false,
                is_dk: false,
                is_stk: false
            },

            //权限
            yx_yggl_edit: false
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        YGMessage: function () {
            return this.$store.state.YGMessage;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        departmentALL: function () {
            return this.$store.state.department;
        }
    },
    watch: {
        YGMessage: function (data) {
            var obj = data;
            var self = this;
            self.zhinengList.is_sc = obj.is_sc == "1" ? true : false;
            self.zhinengList.is_gw = obj.is_gw == "1" ? true : false;
            self.zhinengList.is_bzr = obj.is_bzr == "1" ? true : false;
            self.zhinengList.is_dk = obj.is_dk == "1" ? true : false;
            self.zhinengList.is_stk = obj.is_stk == "1" ? true : false;
            obj.entry_time = self.getBorthday(obj.entry_time);
            obj.birthday = self.getBorthday(obj.birthday);
            self.selfMessage = obj;
            for (var k in obj) {
                self.selfMessages[k] = obj[k];
            }

        }
    },
    methods: {
        //点击修改密码
        openxiugaiziliao: function () {
            this.yuangongxiugai = false;
            this.changeDepartment(this.selfMessage.department_id);
        },
        setEntry_time: function (val) {
            this.selfMessage.entry_time = val;
        },
        setBorthday: function (val) {
            this.selfMessage.birthday = val;
        },
        //修改个人信息
        xiugaiMessage: function () {
            var self = this;

            if (self.selfMessage.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "姓名不能为空",
                    type: 'warning'
                });
                return;
            }
            if (self.selfMessage.staff_id == "") {
                self.$message({
                    showClose: true,
                    message: "工号不能为空",
                    type: 'warning'
                });
                return;
            }
            if (self.selfMessage.phone == "") {
                self.$message({
                    showClose: true,
                    message: "联系电话不能为空",
                    type: 'warning'
                });
                return;
            }
            // if (!(/^1(3|4|5|7|8)\d{9}$/.test(self.selfMessage.phone))) {
            //     self.$message({
            //         showClose: true,
            //         message: "联系电话为11位手机号码",
            //         type: 'warning'
            //     });
            //     return;
            // }
            var group_id = self.selfMessages.group_id == "1" ? "1" : self.selfMessage.group_id;

            var formData = new FormData();



            formData.append("name", self.selfMessage.name);
            formData.append("email", self.selfMessage.email);
            formData.append("phone", self.selfMessage.phone);
            formData.append("campus_id", self.selfMessage.campus_id);
            formData.append("group_id", group_id);
            formData.append("staff_id", self.selfMessage.staff_id);
            formData.append("sex", self.selfMessage.sex);
            formData.append("entry_time", self.selfMessage.entry_time);
            formData.append("is_full_time", self.selfMessage.is_full_time);
            formData.append("sfz", self.selfMessage.sfz);
            formData.append("birthday", self.selfMessage.birthday);
            formData.append("qq", self.selfMessage.qq);
            formData.append("weixin_id", self.selfMessage.weixin_id);
            formData.append("job_description", self.selfMessage.job_description);
            formData.append("remark", self.selfMessage.remark);



            self.selfMessage.is_sc = self.zhinengList.is_sc ? "1" : "0";
            self.selfMessage.is_gw = self.zhinengList.is_gw ? "1" : "0";
            self.selfMessage.is_bzr = self.zhinengList.is_bzr ? "1" : "0";
            self.selfMessage.is_dk = self.zhinengList.is_dk ? "1" : "0";
            self.selfMessage.is_stk = self.zhinengList.is_stk ? "1" : "0";

            formData.append("is_sc", self.selfMessage.is_sc);
            formData.append("is_gw", self.selfMessage.is_gw);
            formData.append("is_bzr", self.selfMessage.is_bzr);
            formData.append("is_dk", self.selfMessage.is_dk);
            formData.append("is_stk", self.selfMessage.is_stk);

            if (!self.canSave) {
                self.$message({
                    showClose: true,
                    message: "请稍后再试",
                    type: 'warning'
                });
                return;
            }
            self.canSave = false;
            self.saveLoading = true;
            self.$http.post("/api/staff/" + self.$route.params.id, formData)
                .then(function (data) {
                    self.canSave = true;
                    self.saveLoading = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "保存成功"
                        });
                        self.yuangongxiugai = true;
                        self.$store.commit("getMyself");
                        self.$store.commit('getYGMessage', self);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.canSave = true;
                    self.saveLoading = false;
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
                    var gid = self.group[0] ? self.group[0].id : "";
                    gid = gid == 1 ? "" : gid;
                    self.selfMessage.group_id = gid;
                    break;
                }

            }
        },
        //修改密码
        openMMbox: function () {
            var self = this;
            this.$prompt('请输入新密码', '修改密码', {
                type: "password",
                confirmButtonText: '修改',
                cancelButtonText: '取消',
            }).then(function (value) {
                if (value.value == null || value.value.trim() == "") {
                    self.$message({
                        showClose: true,
                        message: "密码不能为空",
                        type: 'warning'
                    });
                    return;
                }
                var formData = new FormData();
                formData.append("password", value.value);
                self.$http.post("/api/staff/" + self.$route.params.id + "/password", formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: "修改成功"
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
            }).catch(function () {

            })
        },
        //转换生日
        getBorthday: function (val) {
            if (val) {
                if (val == "0") {
                    return "";
                }
                if (val.indexOf("-") != -1) {
                    return val;
                }
                var date = new Date(val * 1000);
                return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
            } else {
                return "";
            }
        }
    },
    created: function () {
        var obj = this.YGMessage;
        var self = this;
        self.zhinengList.is_sc = obj.is_sc == "1" ? true : false;
        self.zhinengList.is_gw = obj.is_gw == "1" ? true : false;
        self.zhinengList.is_bzr = obj.is_bzr == "1" ? true : false;
        self.zhinengList.is_dk = obj.is_dk == "1" ? true : false;
        self.zhinengList.is_stk = obj.is_stk == "1" ? true : false;

        obj.entry_time = this.getBorthday(obj.entry_time);
        obj.birthday = this.getBorthday(obj.birthday);

        this.selfMessage = obj;

        for (var k in obj) {
            self.selfMessages[k] = obj[k];
        }


        //权限设置
        this.yx_yggl_edit = this.pdqx(["yx_yggl_edit", "yx_yggl", "yx"]);


    }
}

</script>
<style lang="less" scoped>
.el-select {
    width: 100%;
}

.zhinengquanxian {
    margin-right: 20px;
}

.xiugaimimabtn {
    position: absolute;
    right: 0;
    top: 5px;
}

.el-radio {
    margin-right: 10px;
}
</style>