<template>
    <div class="login_box" v-loading.fullscreen.lock="fullscreenLoading">
        <div class="login">
            <img src="../../img/Sign_in.png" alt="">
            <h3>后台登陆</h3>
            <div class="login_row" :class="{ daishuru:user_type==1,shuru:user_type==2,error:user_type==3 }">
                <span>
                            <img src="../../img/login_user_a.png" alt="" v-show="user_type ==1">
                            <img src="../../img/login_user_b.png" alt="" v-show="user_type ==2">
                            <img src="../../img/login_user_c.png" alt="" v-show="user_type ==3">
                        </span>
                <input type="text" class="user" @focus="user_type=2" ref="zhanghaoinput" @blur="user_type=1" @input="user_type=2" placeholder="请输入用户名" v-model="user.name">
            </div>
            <div class="login_row" :class="{ daishuru:psd_type==1,shuru:psd_type==2,error:psd_type==3 }">
                <span>
                            <img src="../../img/login_mima_a.png" alt="" v-show="psd_type ==1">
                            <img src="../../img/login_mima_b.png" alt="" v-show="psd_type ==2">
                            <img src="../../img/login_mima_c.png" alt="" v-show="psd_type ==3">
                        </span>
                <el-input type="password" class="psd" @focus="psd_type=2" @blur="psd_type=1" @input="psd_type=2" placeholder="请输入密码" v-model="user.psd" @keyup.enter.native="loginFun"></el-input>
            </div>
            <p>如忘记密码 请与在线客服联系</p>
            <button type="button" class="ligon_btn" @click="loginFun">登陆</button>
        </div>
    </div>
</template>
<script>
module.exports = {

    data: function () {
        return {
            fullscreenLoading: false,
            user_type: 1,
            psd_type: 1,
            user: {
                name: "",
                psd: ""
            }
        }
    },
    methods: {
        //登陆
        loginFun: function () {
            var self = this;

            if (self.user.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "账号不能为空",
                    type: 'warning'
                });
                return;
            }

            if (self.user.psd.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "密码不能为空",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();

            formData.append("account", self.user.name);
            formData.append("password", self.user.psd);
            self.fullscreenLoading = true;
            self.$http.post("/api/login", formData)
                .then(function (data) {

                    if (data.data.status == 'ok') {
                        self.$store.commit('setLogin', true);
                        self.$router.push({ name: 'worktody' });
                        self.fullscreenLoading = false;
                    } else {
                        self.fullscreenLoading = false;
                        self.$refs.zhanghaoinput.focus();
                        self.user.name = "";
                        self.user.psd = "";
                        self.$message({
                            showClose: true,
                            message: data.data.msg,
                            type: 'warning'
                        });
                    }
                }, function (err) {
                    self.fullscreenLoading = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }

    },
    created: function () {
        this.$store.commit('setHeadShow', false);
    }
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
.login_box {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background-image: url("../../img/login_bj.jpg");
    background-position: center bottom;
    background-size: 100% auto;
    background-repeat: no-repeat;
    background-color: #f8f8f8;
}

.login {
    width: 430px;
    height: 600px;
    .autocenter();
     ::-webkit-input-placeholder {
        /* WebKit browsers */
        color: @c3;
        font-family: "微软雅黑";
    }
     :-moz-placeholder {
        /* Mozilla Firefox 4 to 18 */
        color: @c3;
        font-family: "微软雅黑";
    }
     ::-moz-placeholder {
        /* Mozilla Firefox 19+ */
        color: @c3;
        font-family: "微软雅黑";
    }
     :-ms-input-placeholder {
        /* Internet Explorer 10+ */
        color: @c3;
        font-family: "微软雅黑";
    }
    >p {
        font-size: 14px;
        color: #d4d4d4;
        padding: 10px 0;
        text-align: right;
        margin-bottom: 20px;
    }
    >img {
        display: block;
        margin: 0 auto;
        margin-bottom: 10px;
        height: 48px;
    }
    h3 {
        font-size: 22px;
        color: @c2;
        text-align: center;
        margin-bottom: 46px;
    }
}

.login_row {
    overflow: hidden;
    position: relative;
    height: 32px;
    padding-bottom: 8px;
    margin-top: 30px;
    &.daishuru {
        border-bottom: 1px solid #dedede;
    }
    &.shuru {
        border-bottom: 1px solid @color;
    }
    &.error {
        border-bottom: 1px solid #ff94b5;
        i {
            display: block;
        }
    }
    span {
        display: block;
        width: 26px;
        height: 26px;
        position: absolute;
        z-index: 2;
        left: 0;
        img {
            display: block;
            width: 26px;
            height: 26px;
        }
    }
    i {
        display: none;
        height: 26px;
        line-height: 26px;
        position: absolute;
        z-index: 2;
        right: 0;
        color: @error;
        font-size: 18px;
        float: right;
    }
    input {
        display: block;
        height: 26px;
        line-height: 26px;
        width: 100%;
        position: absolute;
        left: 0;
        top: 0;
        z-index: 1;
        border: none;
        background-color: transparent;
        outline: none;
        padding-left: 38px;
        box-sizing: border-box;
        font-size: 16px;
    }
}

.ligon_btn {
    width: 143px;
    height: 38px;
    display: block;
    margin: 0 auto;
    background-color: @color;
    font-size: 18px;
    color: #fff;
    outline: none;
    border: none;
    cursor: pointer;
    &:active {
        opacity: .5;
    }
}


</style>