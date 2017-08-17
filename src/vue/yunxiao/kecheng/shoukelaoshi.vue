<template>
    <div>
        <div class="list">
            <el-table :data="teachList" style="width: 100%" class="table_moban" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="face" label="头像">
                    <template scope="scope">
                        <span class="photoBox"><img :src="'http://img.eduwxt.com'+scope.row.face" alt=""></span>
                    </template>
                </el-table-column>
                </el-table-column>
                <el-table-column prop="name" label="姓名">
                </el-table-column>
                <el-table-column prop="sex" label="性别">
                    <template scope="scope">
                        {{ scope.row.sex == ''?'其他':scope.row.sex }}
                    </template>
                </el-table-column>
                <el-table-column prop="staff_id" label="工号">
                </el-table-column>
                <el-table-column prop="campus_name" label="所属校区">
                </el-table-column>
                <el-table-column prop="department_name" label="部门">
                </el-table-column>
                <el-table-column prop="group_name" label="职务">
                </el-table-column>
                <el-table-column prop="entry_time" label="在职时长">
                    <template scope="scope">
                        {{ scope.row.entry_time | month }}个月
                    </template>
                </el-table-column>
                <el-table-column prop="" label="累计课时">
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>
<script>
    module.exports = {
        data: function () {
            return {
                loading: false,

                subjectDetail: {},
                teachList: []
            }
        },
        computed: {
            campus: function () {
                return this.$store.state.campus;
            }
        },
        methods: {
            //查看课程详情
            getSubjectDetail: function () {
                var self = this;
                self.loading = true;
                self.$http.get("/api/subject/" + self.$route.params.id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.subjectDetail = data.data.data;
                            for (var i = 0; i < data.data.data.teacher.length; i++) {
                                self.getTeachDetail(data.data.data.teacher[i].teacher_id, i);
                            }
                        } else {
                            self.loading = false;
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
            //获取老师详情
            getTeachDetail: function (id, i) {
                var self = this;
                self.$http.get("/api/staff/" + id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            if (data.data.data.staff[0].campus_id == 1) {
                                data.data.data.staff[0].campus_name = "所有校区";
                            } else {
                                for (var k = 0; k < self.campus.length; k++) {
                                    data.data.data.staff[0].campus_id == self.campus[k].id;
                                    data.data.data.staff[0].campus_name = self.campus[k].name;
                                    break;
                                }
                            }

                            self.teachList.push(data.data.data.staff[0]);
                            if (i == self.subjectDetail.teacher.length - 1) {
                                self.loading = false;
                            }
                        } else {
                            self.loading = false;
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
            this.$store.commit('setsubject_detail_meau_id', 2);
            this.getSubjectDetail();
        }
    }

</script>
<style lang="less" scoped>
    .photoBox {
        width: 30px;
        height: 30px;
        display: block;
        border-radius: 50%;
        overflow: hidden;
        img {
            display: block;
            width: 100%;
            height: 100%;
        }
    }
</style>