<template>
    <div>
        <!--成绩管理-->
        <div class="chengjiliebiao">
            <div class="chengji_search">
                <span>成绩管理</span>
                <el-input placeholder="搜索" icon="search" v-model="option.search" :on-icon-click="searchFun" @keyup.enter.native="searchFun">
                </el-input>
            </div>
            <el-table :data="chengjiList.score" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="title" label="考试标题">
                </el-table-column>
                <el-table-column prop="time" label="发布时间">
                    <template scope="scope">
                        {{ scope.row.time | yyyy_mm_dd }}
                    </template>
                </el-table-column>
                <el-table-column prop="teacher" label="发布人">
                </el-table-column>
                <el-table-column prop="status" label="通知状态">
                    <template scope="scope">
                        <el-tag type="primary" v-if="scope.row.status == 1">已通知</el-tag>
                        <el-tag type="warning" v-else>未通知</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="250">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="getChengjixq(scope.row.id)" v-if="yx_bjgl_my_view">查看成绩</el-button>
                        <el-button type="primary" size="mini" v-if="yx_bjgl_my_qtxx && scope.row.status != 1" @click="fabuNotice(scope.row)">发送通知</el-button>
                        <el-button type="danger" size="mini" @click="decchengji(scope.row.id,scope.row.title)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="chengjiList.count > chengjiList.score.length">
                <el-pagination @current-change="pageClickFun" :current-page="option.page" :page-sizes="[10, 20, 30, 40]" :page-size="option.limit" layout="total, prev, pager, next" :total="20">
                </el-pagination>
            </div>
        </div>
        <!--查看成绩-->
        <el-dialog :title="chengjixiangqing.title" v-model="chengjiZQshow">
            <el-table :data="chengjixiangqing.student" v-loading="loading1" element-loading-text="加载中">
                <el-table-column property="number" label="学号"></el-table-column>
                <el-table-column property="name" label="姓名"></el-table-column>
                <el-table-column :label="list" v-for="(list,index) in chengjixiangqing.subject">
                    <template scope="scope">
                        {{ JSON.parse(scope.row.score)[index] }}
                    </template>
                </el-table-column>
            </el-table>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    computed: {
        chengji: function () {
            return this.$store.state.chengji;
        }
    },
    watch: {
        chengji: function (val) {
            this.getChengjiList(this.option);
        }
    },
    data: function () {
        return {
            search: "",
            current_page: 1,
            chengjiList: {
                score: []
            },

            loading: false,
            loading1: false,
            chengjiZQshow: false,
            option: {
                page: 1,
                limit: 10,
                search: ""
            },
            chengjixiangqing: {},

            yx_bjgl_my_view: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        //发送成绩通知
        fabuNotice:function(obj){
            var self = this;
            self.$http.post("/api/classs/"+obj.class_id+"/score/notice/"+obj.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "成绩通知已发送",
                        });
                        self.getChengjiList(self.option);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.addclassshow = false;
                    self.loading2 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //点击页码
        pageClickFun: function (val) {
            this.option.page = val;
            this.getChengjiList(this.option);
        },
        //删除成绩
        decchengji: function (id, name) {
            var self = this;
            self.$confirm('是否要删除"' + name + '"这条成绩?', '删除成绩', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/score/" + id + "/del")
                    .then(function (data) {
                        self.loading1 = false;
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功！"
                            });
                            self.option.page = 1;
                            self.getChengjiList(this.option);
                        } else {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }, function (err) {
                        self.loading1 = false;
                        self.$message({
                            showClose: true,
                            message: "网络错误",
                            type: "error"
                        })
                    })

            }).catch(function () {

            });
        },
        //搜索
        searchFun: function () {
            this.option.page = 1;
            this.getChengjiList(this.option);
        },
        //获取成绩列表
        getChengjiList: function (option) {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/score?page=" + option.page + "&limit=" + option.limit + "&search=" + option.search)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.chengjiList = data.data.data;
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
        //获取成绩详情
        getChengjixq: function (id) {
            var self = this;
            self.chengjixiangqing = {};
            self.loading1 = true;
            self.chengjiZQshow = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/score/" + id)
                .then(function (data) {
                    self.loading1 = false;
                    if (data.data.status == 'ok') {
                        self.chengjixiangqing = data.data.data;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading1 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        this.getChengjiList(this.option);

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.chengjiliebiao {
    background-color: #fff;
    padding: 20px;
}

.chengji_search {
    text-align: right;
    margin-bottom: 20px;
    >span {
        display: block;
        float: left;
        font-size: @h3;
        color: @c1;
    }
    .el-input {
        width: auto;
    }
}
</style>