<template>
    <div>
        <!--学员列表-->
        <el-table :data="logList.log" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="student_name" label="学员">
            </el-table-column>
            <el-table-column prop="teacher_name" label="操作人">
            </el-table-column>
            <el-table-column prop="time" label="操作时间">
                <template scope="scope">
                    {{ scope.row.time | yyyy_mm_dd_M_S }}
                </template>
            </el-table-column>
            <el-table-column prop="action" label="操作">
            </el-table-column>
            <el-table-column prop="detail" label="操作详情">
            </el-table-column>
            
        </el-table>
    
        <!--分页-->
        <div class="fenye" v-if="logList.count > logList.log.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="page" :page-size="limit" layout="total, prev, pager, next" :total="logList.count">
            </el-pagination>
        </div>
    
    </div>
</template>
<script>
module.exports = {

    data: function () {
        return {
            loading: false,
            limit: 15,
            page: 1,
            logList:{
                log:[],
                count:0
            }
        }
    },
    methods: {

        //点击页码
        handleCurrentChange: function (val) {
            this.page = val;
            this.getLogList();
        },

        //获取操作日志
        getLogList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/class_log?&page=" + self.page + "&limit=" + self.limit)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.logList = data.data.data;
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
        }
    },
    created: function () {
        this.getLogList();
    }
}

</script>
<style lang="less">
@import "../../../less/color.less";
</style>