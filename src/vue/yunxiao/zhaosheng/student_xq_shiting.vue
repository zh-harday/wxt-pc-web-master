<template>
    <div>
        <el-table :data="shitingjilu.booking" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="start_time" label="试听时间">
                <template scope="scope">
                    {{ scope.row.start_time | yyyy_mm_dd_M_S }}
                </template>
            </el-table-column>
            <el-table-column prop="subject_name" label="预报科目">
            </el-table-column>
            <el-table-column prop="title" label="试听课名称">
            </el-table-column>
            <el-table-column prop="staff_name" label="邀约人">
            </el-table-column>
            <el-table-column prop="teacher" label="试听课老师">
            </el-table-column>
            <el-table-column prop="status" label="到会情况">
                <template scope="scope">
                    <el-tag type="success" v-if="scope.row.status == 1">已到</el-tag>
                    <el-tag type="danger" v-if="scope.row.status != 1">未到</el-tag>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
    module.exports = {
        data: function () {
            return {
                loading:false,
                shitingjilu: {
                    booking:[]
                }
            }
        },
        methods: {
            getShitingjilu: function () {
                var self = this;
                self.loading = true;
                self.$http.get("/api/lecture/intention/"+self.$route.params.id)
                    .then(function (data) {
                        self.loading = false;
                        if (data.data.status == 'ok') {
                            self.shitingjilu = data.data.data;
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
            this.getShitingjilu();
        }
    }

</script>