<template>
    <div>
        <h1 class="right_title">
            清理数据
            <article>可以快速清理各个模块中的数据</article>
        </h1>
        <el-table :data="tableData" style="width: 100%">
            <el-table-column prop="name" label="模块">
            </el-table-column>
            <el-table-column prop="number" label="数据值">
            </el-table-column>
            <el-table-column prop="mark" label="说明" min-width="380">
            </el-table-column>
            <el-table-column label="操作">
                <template scope="scope">
                    <el-button type="danger" :loading="scope.row.loading" size="mini" @click="cleanFun(scope.row,scope.$index)">清除数据</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            tableData: [
                {
                    id: 1,
                    name: "班级模块",
                    number: 0,
                    mark: "删除班级模块时，班级下的所有信息将被同步删除！",
                    type: "classs",
                    loading: false
                },
                {
                    id: 2,
                    name: "学员模块",
                    number: 0,
                    mark: "删除学员模块时，学员下的所有信息将被同步删除！",
                    type: "student",
                    loading: false
                }, {
                    id: 3,
                    name: "意向学员模块",
                    number: 0,
                    mark: "删除意向学员模块时，意向学员下的所有信息将被同步删除！",
                    type: "intention",
                    loading: false
                }, {
                    id: 4,
                    name: "财务流水模块",
                    number: 0,
                    mark: "删除财务流水模块时，财务流水下的所有信息将被同步删除！",
                    type: "finance",
                    loading: false
                }
            ]
        }
    },
    methods: {
        cleanFun: function (obj, index) {
            var self = this;
            this.$confirm('此操作将永久删除“' + obj.name + '”中的信息, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'error'
            }).then(function () {
                self.tableData[index].loading = true;
                self.$http.post("/api/clean/module/"+obj.type)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$message({
                                type: 'success',
                                message: data.data.msg
                            })
                            self.tableData[index].loading = false;
                            if (obj.type == 'classs') {
                                self.getclassCount();
                            }
                            if (obj.type == 'student') {
                                self.getxyCount();
                            }
                            if (obj.type == 'intention') {
                                self.getyxxyCount();
                            }
                            if (obj.type == 'finance') {
                                self.getcaiwuCount();
                            }
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
            }).catch(function () {

            });
        },
        getcaiwuCount: function () {
            var self = this;
            self.$http.get("/api/finance/records?limit=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.tableData[3].number = data.data.data.count;
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
        getyxxyCount: function () {
            var self = this;
            self.$http.get("/api/intention?limit=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.tableData[2].number = data.data.data.count;
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
        getxyCount: function () {
            var self = this;
            self.$http.get("/api/student?limit=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.tableData[1].number = data.data.data.count;
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
        getclassCount: function () {
            var self = this;
            self.$http.get("/api/classs?&limit=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.tableData[0].number = data.data.data.count;
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
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 9);
        this.getclassCount();
        this.getxyCount();
        this.getyxxyCount();
        this.getcaiwuCount();
    }
}
</script>
