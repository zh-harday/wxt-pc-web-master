<template>
    <div class="ygquanxian">
        <div class="quanxian_title">
            <i v-if="ygqx.group_id ==1">系统管理员权限不允许修改</i>
            <s v-if="ygqx.group_id !=1">
                <el-button type="success" size="small" @click="saveYGquanxian" :loading="saveqx">&nbsp;&nbsp;&nbsp;&nbsp;保存&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
            </s>
        </div>
        <p class="tishi">提示：个人权限的优先级高于职务权限。</p>
        <div class="quanxin_box">
            <el-tree :data="qxpeizhi" show-checkbox node-key="id" :default-checked-keys="ygqx.authority" :default-expanded-keys="ygqx.authority" :props="defaultProps" ref="tree1">
            </el-tree>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        qxpeizhi: function () {
            return this.$store.state.qxpeizhi;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            saveqx: false,
            formLabelWidth: "70px",
            ygqx: {
                authority: []
            },

            defaultProps: {
                children: 'children',
                label: 'label'
            }
        }
    },
    methods: {
        //获取员工权限
        getYGquanxian: function () {
            var self = this;
            self.$http.get("/api/staff/authority/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.ygqx = data.data.data;
                        self.$refs.tree1.setCheckedKeys(self.ygqx.authority);
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
        //保存员工权限
        saveYGquanxian: function () {
            var self = this;
            self.ygqx.authority = self.$refs.tree1.getCheckedKeys();
            if (self.ygqx.authority.length == 0) {
                self.$confirm('您没有给该员工选择任何权限，是否继续保存?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.gengaiquanxian();
                }).catch(function () {
                    return false;
                });
                return;
            }
            self.gengaiquanxian();
        },
        //上次更新
        gengaiquanxian: function () {
            var self = this;
            self.saveqx = true;
            var formData = new FormData();
            var quanxian = JSON.stringify(self.ygqx.authority);
            formData.append("authority", quanxian);

            self.$http.post("/api/staff/authority/" + self.$route.params.id, formData)
                .then(function (data) {
                    self.saveqx = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "员工权限修改成功"
                        });
                        if (self.$route.params.id == self.myMessage.id) {
                            self.$store.commit('getMyself');
                        }
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.saveqx = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        this.getYGquanxian();
        if (this.qxpeizhi.length == 0) {
            this.$store.commit('getPeizhi');
        }

        var yx_yggl_edit = this.pdqx(["yx_yggl_edit","yx_yggl", "yx"]);
        if (!yx_yggl_edit) {
            this.$router.push({ name: 'worktody' })
        }

    }
}
</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.quanxin_box {
    .el-tree {
        border: 1px solid #ddd;
    }
}

.ygquanxian {
    background-color: #fff;
    padding: 20px;
}

.tishi {
    font-weight: normal;
    color: @c3;
    font-size: 14px;
    margin-bottom: 20px;
}

.quanxian_title {
    overflow: hidden;
    >i{
        font-size: 16px;
        color: @error;
        display: block;
        font-style: normal;
        padding-bottom: 10px;
    }
    span {
        display: block;
        float: left;
        font-size: 16px;
        color: @c1;
    }
    s {
        display: block;
        float: right;
        text-decoration: none;
    }
}
</style>