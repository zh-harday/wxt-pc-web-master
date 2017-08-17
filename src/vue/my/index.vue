<template>
    <div class="myHome_and_message">
        <div class="left">
            <div class="box_l1">
                <span>
                    <img :src="myMessage.face+'?'+new Date().getTime()" alt="">
                    <form id="forms" enctype="multipart/form-data">
                        <input type="file" @change="postPhoto" name="newface">
                    </form>
                </span>
                <div>
                    <h1>{{ myMessage.name }}</h1>
                    <el-tag type="success" v-if="myMessage.wecha_id && myMessage.wecha_id != '' ">微校通已绑定</el-tag>
                    <el-tag type="warning" v-else>微校通未绑定</el-tag>
                </div>
            </div>
            <div class="box_l2">
                <router-link tag="span" :to="{name:'myMessage',params:{type:'Message'}}" :class="{active:$route.params.type=='Message'}">个人信息</router-link>
                <router-link tag="span" :to="{name:'myMessage',params:{type:'Password'}}" :class="{active:$route.params.type=='Password'}">密码设置</router-link>
                <router-link tag="span" :to="{name:'myMessage',params:{type:'Binding'}}" :class="{active:$route.params.type=='Binding'}">账号绑定</router-link>
            </div>
        </div>
        <div class="right">
            <div class="box_r1">
                <h1>在职信息</h1>
                <span>工号：{{ myMessage.staff_id }}</span>
                <span>部门：
                    <s v-for="item in department">
                        <s v-for="list in item.group" v-if="myMessage.group_id == list.id">{{ item.name }}</s>
                    </s>
                </span>
                <span>入职时长：{{ myMessage.entry_time | month }}个月</span>
                <span>任职校区：{{ getCampus }}</span>
                <span>职务：
                    <s v-for="item in department">
                        <s v-for="list in item.group" v-if="myMessage.group_id == list.id">{{ list.name }}</s>
                    </s>
                </span>
                <span>是否全职：{{ myMessage.is_full_time }}</span>
            </div>
            <div class="box_r2">
                <!--个人信息-->
                <div class="my_message" v-if="$route.params.type=='Message'">
                    <h1>基本信息</h1>
                    <el-form ref="form" :model="form" label-width="70px">
                        <div class="row_form">
                            <div class="left_form">
                                <el-form-item label="姓名">
                                    <el-input v-model="form.name" disabled></el-input>
                                </el-form-item>
                            </div>
                            <div class="right_form">
                                <el-form-item label="性别">
                                    <el-radio-group v-model="form.sex">
                                        <el-radio label="女"></el-radio>
                                        <el-radio label="男"></el-radio>
                                        <el-radio label="未知"></el-radio>
                                    </el-radio-group>
                                </el-form-item>
                            </div>
                        </div>
    
                        <div class="row_form">
                            <div class="left_form">
                                <el-form-item label="联系电话">
                                    <el-input v-model="form.phone" maxlength="20"></el-input>
                                </el-form-item>
                            </div>
                            <div class="right_form">
                                <el-form-item label="邮箱">
                                    <el-input v-model="form.email" maxlength="50"></el-input>
                                </el-form-item>
                            </div>
                        </div>
                        <div class="row_form">
                            <div class="left_form">
                                <el-form-item label="微信">
                                    <el-input v-model="form.weixin_id" maxlength="20"></el-input>
                                </el-form-item>
                            </div>
                            <div class="right_form">
                                <el-form-item label="QQ">
                                    <el-input v-model="form.qq" maxlength="20"></el-input>
                                </el-form-item>
                            </div>
                        </div>
                        <div class="row_form">
                            <div class="left_form">
                                <el-form-item label="出生日期">
                                    <el-col :span="24">
                                        <el-date-picker type="date" placeholder="选择日期" v-model="form.birthday"></el-date-picker>
                                    </el-col>
                                </el-form-item>
                            </div>
                            <div class="right_form">
                                <el-form-item label="身份证号">
                                    <el-input v-model="form.sfz"></el-input>
                                </el-form-item>
                            </div>
                        </div>
                        <el-form-item label="个人签名">
                            <el-input type="textarea" v-model="form.motto" maxlength="50"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="onSubmit">修改信息</el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <!--密码设置-->
                <div class="setmima" v-if="$route.params.type=='Password'">
                    <h1>密码设置</h1>
                    <p>登陆账号
                        <span>{{ myMessage.job_description }}</span>
                    </p>
                    <el-form ref="form" :model="mm" label-width="80px">
                        <el-form-item label="原密码">
                            <el-input v-model="mm.old" placeholder="请输入原密码" type="password" maxlength="50" id="oldmm"></el-input>
                        </el-form-item>
                        <el-form-item label="新密码">
                            <el-input v-model="mm.news1" placeholder="请输入新密码" type="password" maxlength="50" id="newsmm1"></el-input>
                        </el-form-item>
                        <el-form-item label="重复密码">
                            <el-input v-model="mm.news2" placeholder="请再次输入新密码" type="password" maxlength="50" id="newsmm2"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="xiugaimima">修改密码</el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <!--账号绑定-->
                <div class="zhanghao" v-if="$route.params.type=='Binding' && ( !myMessage.wecha_id || myMessage.wecha_id == '' ) ">
                    <h1>账号绑定</h1>
                    <p>绑定微校通，开启移动办公</p>
                    <i>
                        <img src="/api/user/bind_qrcode" alt="">
                    </i>
                    <div>
                        <span>您的账号暂未绑定微信</span>
                        <p>请使用
                            <span>微信扫描二维码</span>进行绑定</p>
                    </div>
                </div>
                <!--绑定成功-->
                <div class="zhanghao" v-if="$route.params.type=='Binding' && (myMessage.wecha_id && myMessage.wecha_id != '')">
                    <h1>账号绑定
                        <el-button type="warning" @click="unbind">解除绑定</el-button>
                    </h1>
                    <p>绑定微校通，开启移动办公</p>
                    <i>
                        <img src="../../img/my_erweima_success.png" alt="">
                    </i>
                    <div>
                        <span>您的账号已成功绑定微信</span>
                        <p>微信号：{{ myMessage.weixin_id | fltWXH }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {

    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        campus: function () {
            return this.$store.state.campus;
        },
        department: function () {
            return this.$store.state.department;
        },
        getCampus: function () {
            var self = this;
            for (var i = 0; i < self.campus.length; i++) {
                if (self.myMessage.campus_id == 1) {
                    return "所有校区";
                }
                if (self.myMessage.campus_id == self.campus[i].id) {
                    return self.campus[i].name;
                }
            }
            return "";
        }

    },

    filters: {
        month: function (val) {
            var self = this;
            if (val) {
                var month = (+new Date() - +new Date(val * 1000)) / (1000 * 60 * 60 * 24 * 30);
                month = month < 1 ? 1 : parseInt(month);
                return month;
            } else {
                return 0
            }
        },
        fltWXH: function (val) {
            if (val) {
                if (val.length < 3) {
                    return val.substr(1) + "****";
                } else {
                    return val.substr(0, 1) + "****" + val.substr(val.length - 2);
                }
            } else {
                return "****";
            }
        }

    },

    data: function () {
        return {
            cansub: true,
            erweimaSRC: "",
            mm: {
                old: "",
                news1: "",
                news2: ""
            },
            form: {
                name: '',
                sex: '',
                phone: '',
                email: '',
                weixin_id: '',
                qq: '',
                birthday: '',
                motto: '',
                sfz: ''
            }
        }
    },

    methods: {
        //解绑
        unbind: function () {
            var self = this;
            self.$http.post("/api/user/unbind")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "解绑成功"
                        });
                        self.$store.commit('getMyself');
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
        //修改密码
        xiugaimima: function () {
            var self = this;
            if (self.mm.old.trim() == "") {
                document.querySelector("#oldmm input").focus();
                self.$message({
                    showClose: true,
                    message: "原密码不能为空",
                    type: 'warning'
                });
                return;

            }

            if (self.mm.news1.trim() == "") {
                document.querySelector("#newsmm1 input").focus();
                self.$message({
                    showClose: true,
                    message: "新密码不能为空",
                    type: 'warning'
                });
                return;
            }

            if (self.mm.news2 != self.mm.news1) {
                document.querySelector("#newsmm2 input").focus();
                self.$message({
                    showClose: true,
                    message: "两次密码输入不一致",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();
            formData.append("password", self.mm.old);
            formData.append("password2", self.mm.news1);
            self.$http.post("/api/user/password", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.mm = {
                            old: "",
                            news1: "",
                            news2: ""
                        };
                        self.$store.commit("success", {
                            self: self,
                            msg: "密码修改成功"
                        });
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
        //修改个人信息
        onSubmit: function () {
            var self = this;

            var borthday = "";

            if (!self.cansub) {
                return;
            }
            self.cansub = false;


            if (typeof self.form.birthday == 'object') {
                var date = self.form.birthday;
                borthday = date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
            } else {
                borthday = self.form.birthday;
            }

            var formData = new FormData();
            formData.append("phone", self.form.phone);
            formData.append("sex", self.form.sex);
            formData.append("email", self.form.email);
            formData.append("weixin_id", self.form.weixin_id);
            formData.append("birthday", borthday);
            formData.append("qq", self.form.qq);
            formData.append("sfz", self.form.sfz);
            formData.append("motto", self.form.motto);
            console.log(self.form)
            self.$http.post("/api/user", formData)
                .then(function (data) {
                    self.cansub = true;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "个人信息修改成功"
                        });
                        self.$store.commit('getMyself');
                    } else {
                        self.cansub = true;
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
        //修改头像
        postPhoto: function () {
            var self = this;
            var formData = new FormData(document.getElementById("forms"));
            self.$http.post("/api/user/face", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit('getMyself');
                        self.$store.commit("success", {
                            self: self,
                            msg: "头像修改成功"
                        });
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
        //转换生日
        getBorthday: function (val) {
            if (val) {
                var date = new Date(val * 1000);
                return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
            } else {
                return "";
            }
        }
    },
    created: function () {
        var borthday = this.getBorthday(this.myMessage.birthday);
        var self = this;
        this.form = {
            name: this.myMessage.name,
            sex: this.myMessage.sex,
            phone: this.myMessage.phone,
            email: this.myMessage.email,
            weixin_id: this.myMessage.weixin_id,
            qq: this.myMessage.qq,
            birthday: borthday,
            motto: this.myMessage.motto,
            sfz: this.myMessage.sfz
        }
        this.$store.commit('setHeadID', 0);
        this.$store.commit('setHeadShow', true);
        this.$store.commit("sethelpid", "");// 帮助

        this.$store.commit("getNewMsgCount", this);//更新消息

        this.$store.commit("getZQWBM", {
            self: self
        });

    }
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
.el-input {
    width: 100%;
}

.row_form .left_form {
    width: 47%;
}

.row_form .right_form {
    width: 47%;
}

.my_message {
    h1 {
        font-size: @h2;
        color: @c1;
        font-weight: normal;
        margin-bottom: 15px;
    }
    button {
        display: block;
        float: right;
    }
}

.setmima {
    h1 {
        font-size: @h2;
        color: @c1;
        font-weight: normal;
        margin-bottom: 15px;
    }
    p {
        font-size: 14px;
        color: @c2;
        margin-bottom: 15px;
        span {
            padding-left: 22px;
            color: @c2;
        }
    }
    button {
        display: block;
        float: right;
    }
}

.zhanghao {
    h1 {
        font-size: @h2;
        color: @c1;
        font-weight: normal;
        margin-bottom: 5px;
        position: relative;
        button {
            display: block;
            position: absolute;
            right: 0;
        }
    }
    p {
        font-size: 14px;
        color: @c2;
        margin-bottom: 15px;
        span {
            padding-left: 22px;
            color: @c2;
        }
    }
    i {
        display: block;
        width: 170px;
        height: 170px;
        margin: 0 auto;
        margin-bottom: 15px;
    }
    img {
        display: block;
        width: 170px;
    }
    div {
        text-align: center;
        span {
            color: @c2;
        }
        p {
            color: @c2;
            span {
                font-size: 16px;
                color: @c1;
                padding: 0;
            }
        }
    }
}

.el-form-item__label {
    text-align: left;
}
</style>