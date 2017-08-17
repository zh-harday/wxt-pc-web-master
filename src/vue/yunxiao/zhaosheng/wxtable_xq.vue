<template>
    <div class="yx_class">
        <div class="left">
            <h1>招生追踪
                    <article>管理意向学员并反馈跟进情况</article>
                </h1>
            <router-link :to="{ name:'wxtableMsg',params:{id:$route.params.id}}" :class="{active:$route.name=='wxtableMsg'}">表单信息</router-link>
            <router-link :to="{ name:'bmXQ',params:{id:$route.params.id}}" :class="{active:$route.name=='bmXQ'}">报名详情</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                        <img src="../../../img/yx_sub_xq_logo.png" alt="">
                    </span>
                <div>
                    <h1>{{ wxtablemsg.form.title }}</h1>
                    <div class="xiangqing">
                        <el-row :gutter="20">
    
                            <el-col :span="8">
                                浏览：{{ wxtablemsg.form.view_count }}
                            </el-col>
                            <el-col :span="8">
                                报名：{{ wxtablemsg.form.student_count }}
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :span="8">
                                表单ID：{{ wxtablemsg.form.id }}
                            </el-col>
                            <el-col :span="8">
                                招生来源：{{ wxtablemsg.form.source }}
                            </el-col>
                            <el-col :span="8">
                                发布时间：{{ wxtablemsg.form.add_time | yyyy_mm_dd }}
                            </el-col>
                        </el-row>
                    </div>
                    <span @click="$router.go(-1)"><i class="el-icon-arrow-left"></i>返回上一级</span>
                    <div class="btn_box">
                        <el-button type="success" size="mini" @click="tingyongWXtable" v-if="status == 0">&nbsp;&nbsp;&nbsp;&nbsp;启用&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                        <el-button type="warning" size="mini" @click="tingyongWXtable" v-else>&nbsp;&nbsp;&nbsp;&nbsp;停用&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                        <el-button type="danger" size="mini" @click="delwxtableconfrim">&nbsp;&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                    </div>
                </div>
            </div>
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            status: "",
            statusCount: false,

            wxtablemsg: {
                form: {}
            }
        }
    },
    methods: {
        //停用 启用微信表单
        tingyongWXtable: function () {
            var self = this;
            if (!self.statusCount) {
                self.$message({
                    showClose: true,
                    message: "请稍后...",
                    type: "warning"
                })
                return;
            }
            self.statusCount = false;
            var formData = new FormData();
            var status = self.status == 1 ? "0" : "1";
            formData.append("status", status);
            self.$http.post("/api/free_form/" + self.$route.params.id + "/status", formData)
                .then(function (data) {
                    self.statusCount = true;
                    if (data.data.status == 'ok') {
                         self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        //获取微信表单
                        self.getTableMsg();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.statusCount = true;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除微信表单确认
        delwxtableconfrim: function () {
            var self = this;
            var title = self.wxtablemsg.form.student_count > 0 ? "该表单已有学员报名信息，是否确认删除？" : "是否删除该微信表单?"
            self.$confirm(title, '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.delwxtable();
            }).catch(function () {

            });
        },
        //删除微信表单
        delwxtable: function () {
            var self = this;
            self.$http.post("/api/free_form/" + self.$route.params.id + "/del")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                         self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        //跳转微信表单列表页
                        self.$router.push({ name: 'wxtable' });
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
        //获取表单详情
        getTableMsg: function () {
            var self = this;
            self.$http.get("/api/free_form/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.wxtablemsg = data.data.data;
                        self.statusCount = true;
                        self.status = data.data.data.form.status;
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
        this.$store.commit('setYXLeftMeauId', 2);
        this.getTableMsg();

        //权限设置
        var yx_zszz_wxbd = this.pdqx(["yx_zszz", "yx_zszz_wxbd"]);
        if (!yx_zszz_wxbd) {
            this.$router.push({ name: 'worktody' });
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.btn_box {
    position: absolute;
    right: 0;
    bottom: 2px;
}

.xiangqing {
    width: 530px;
}
</style>