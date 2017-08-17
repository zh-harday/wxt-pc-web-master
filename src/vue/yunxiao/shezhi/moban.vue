<template>
    <div>
        <h1 class="right_title">
                模板消息
                <article>授权开通教育培训行业模板消息功能</article>
                <el-button type="success" class="btn" @click="InitializationMoban" size="small" v-if="canClick">初始化模板消息系统</el-button>
                <el-button type="success" class="btn" size="small" v-if="!canClick" disabled>初始化模板消息系统</el-button>
            </h1>
    
        <el-table :data="tableData" style="width: 100%" class="table_moban" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="sort" label="序号" width="70">
            </el-table-column>
            <el-table-column prop="title" label="模板名称" width="220">
            </el-table-column>
            <el-table-column prop="short" label="模板编号" width="220">
            </el-table-column>
            <el-table-column prop="demo" label="发送示例">
            </el-table-column>
            <el-table-column label="状态" width="100">
                <template scope="scope">
                    <img src="../../../img/error_1.png" alt="" v-if="!scope.row.temp_id">
                    <img src="../../../img/success_1.png" alt="" v-if="scope.row.temp_id">
                </template>
            </el-table-column>
            <el-table-column label="操作" width="100">
                <template scope="scope">
                    <el-button type="success" size="mini" @click="one_InitializationMoban(scope.row)">重新获取</el-button>
                </template>
            </el-table-column>
        </el-table>
        <p class="wan">模板消息功能仅支持认证服务号，若授权失败请检查公众号中该功能是否开通。</p>
    </div>
</template>
<script>
module.exports = {

    data: function () {
        return {
            tableData: window.sessionStorage.getItem("mobanMessage") ? JSON.parse(window.sessionStorage.getItem("mobanMessage")) : [],
            loading: false,
            canClick: true//模板消息是否可点击
        }
    },
    methods: {
        //获取模板消息
        getMoban: function () {
            var self = this;
            if (self.tableData.length == 0) {
                self.loading = true;
            }
            self.$http.get("/api/weixin/template")
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.tableData = data.data.data;
                        window.sessionStorage.setItem("mobanMessage", JSON.stringify(self.tableData))
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
        //初始化模板消息
        InitializationMoban: function () {
            var self = this;
            self.loading = true;
            if (!self.canClick) {
                return;
            }
            self.canClick = false;
            self.$http.post("/api/weixin/template_init")
                .then(function (data) {
                    self.loading = false;
                    self.canClick = true;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "初始化成功",
                        });
                        self.tableData = data.data.data;
                        window.sessionStorage.setItem("mobanMessage", JSON.stringify(self.tableData))
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.canClick = true;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //初始化单条
        one_InitializationMoban: function (obj) {
            var self = this;
            self.loading = true;
            if (!self.canClick) {
                return;
            }
            self.canClick = false;
            var formData = new FormData();
            formData.append("short", obj.short);
            self.$http.post("/api/weixin/template_get", formData)
                .then(function (data) {
                    self.loading = false;
                    self.canClick = true;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "获取成功",
                        });
                        self.getMoban();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.canClick = true;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 2);
        this.getMoban();
    }
}

</script>
<style lang="less" scoped>
.table_moban {
    img {
        height: 20px;
        vertical-align: middle;
    }
}
</style>