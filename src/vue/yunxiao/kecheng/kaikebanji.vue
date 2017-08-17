<template>
    <div>
        <div class="list">
            <el-table :data="classList" style="width: 100%" class="table_moban" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="class_name" label="班级名称">
                </el-table-column>
                </el-table-column>
                <el-table-column prop="teacher_name" label="班主任">
                </el-table-column>
                <el-table-column prop="start_time" label="开课时间">
                    <template scope="scope">
                        {{ scope.row.start_time | yyyy_mm_dd }}
                    </template>
                </el-table-column>
                <el-table-column prop="student_count" label="学员人数">
                </el-table-column>
                <el-table-column prop="staus" label="班级状态">
                    <template scope="scope">
                        <el-tag type="danger" v-if="scope.row.staus == -1">已停用</el-tag>
                        <el-tag type="success" v-if="scope.row.staus == 0">已启用</el-tag>
                        <el-tag type="success" v-if="scope.row.staus == 1">上课中</el-tag>
                        <el-tag type="warning" v-if="scope.row.staus == 2">预报名</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="subject_count.end" label="累计已上课时">
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
                classList: []
            }
        },
        methods: {
            //查看课程详情
            getSubjectDetail: function () {
                var self = this;
                self.loading = true;
                self.$http.get("/api/subject/" + self.$route.params.id)
                    .then(function (data) {
                        self.loading = false;
                        if (data.data.status == 'ok') {
                            self.subjectDetail = data.data.data;
                            for (var i = 0; i < data.data.data.class.length; i++) {
                                self.getClassDetail(data.data.data.class[i].id, i);
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
            //获取班级详情
            getClassDetail: function (id, i) {
                var self = this;
                self.$http.get("/api/classs/" + id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.getPaikeCOunt(data.data.data, i);
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
            //获取排课统计
            getPaikeCOunt: function (obj, i) {
                var self = this;
                self.$http.get("/api/classs/" + obj.id + "/curriculum/count")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            obj.subject_count = data.data.data;
                            self.getStudentCount(obj, i);
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
            //学员统计
            getStudentCount: function (obj, i) {
                var self = this;
                self.$http.get("/api/classs/" + obj.id + "/student/count")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            var arr = [];
                            obj.student_count = data.data.data.count;
                            self.classList.push(obj);
                            if (i == self.subjectDetail.class.length - 1) {
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
            this.$store.commit('setsubject_detail_meau_id', 1);
            this.getSubjectDetail();
        }
    }

</script>