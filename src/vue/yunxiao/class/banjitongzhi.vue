<template>
    <div>
        <div class="class_tongzhi">
            <div class="class_notice_search">
                <span>班级通知</span>
                <el-input placeholder="搜索" icon="search" v-model="search" :on-icon-click="searchFun" @keyup.enter.native="searchFun">
                </el-input>
            </div>
            <el-table :data="classNoticeList.score" style="width: 100%">
                <el-table-column prop="title" label="通知标题">
                </el-table-column>
                <el-table-column prop="time" label="发布时间">
                    <template scope="scope">
                        {{ scope.row.time | yyyy_mm_dd_M_S }}
                    </template>
                </el-table-column>
                <el-table-column prop="from_name" label="发布人">
                </el-table-column>
                <el-table-column label="操作" width="180">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="chakanclassNotice(scope.row.id,scope.row.from_name)" v-if="yx_bjgl_my_view">查看</el-button>
                        <el-button type="danger" size="mini" @click="delclassNoticeFun(scope.row.id)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="classNoticeList.count > classNoticeList.score.length">
                <el-pagination @current-change=" " :current-page="current_page" :page-size="10" layout="total, prev, pager, next" :total="classNoticeList.count">
                </el-pagination>
            </div>
        </div>
        <!--班级通知详情-->
        <el-dialog title="班级通知详情" v-model="banjitongzhishow">
            <div class="tk_box">
                <h1>{{ classNoticeDetail.title }}</h1>
                <p>
                    <s>发布人：{{classNoticeDetail.teacher}}</s>
                    <s>发布时间：{{ classNoticeDetail.time | yyyy_mm_dd_M_S }}</s>
                </p>
                <p>摘要：{{ classNoticeDetail.info?classNoticeDetail.info:"无" }}</p>
                <div class="content_box_yx" style="margin-bottom:10px" v-html="classNoticeDetail.body"></div>
            </div>
            <div class="tk_box1" v-if="classNoticeDetail.is_receipt == 1">
                <h2>暂未查看</h2>
                <ul>
                    <li v-for="list in classNoticeDetail.receipt" v-if="list.re_time == null">
                        <div class="tk_left">
                            <span>{{ list.student_name.substr(0,1) }}</span>
                        </div>
                        <div class="tk_right">
                            <h1>{{ list.student_name }}</h1>
                            <p>电话：{{ list.phone }}</p>
                        </div>
                    </li>
                </ul>
                <h2>已查看</h2>
                <div>
                    <el-tooltip class="item" effect="dark" :content="list.phone" placement="bottom" v-for="list in classNoticeDetail.receipt" v-if="list.re_time != null">
                        <el-tag type="primary">{{ list.student_name }}</el-tag>
                    </el-tooltip>
                </div>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            search: "",
            current_page: 1,
            banjitongzhishow: false,
            classNoticeDetail: {},
            classNoticeList: {
                score: []
            },

            yx_bjgl_my_view: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        //搜索
        searchFun: function () {
            self.$message({
                showClose: true,
                message: "未开通",
                type: "error"
            })
        },
        //页码点击
        pageClickFun: function (val) {
            this.current_page = val;
            this.getClassNotice();
        },
        //删除班级通知
        delclassNoticeFun: function (id) {
            var self = this;
            self.$confirm('是否要删除这条班级通知?', '删除班级通知', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/notice/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getClassNotice();
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
        //查看班级通知
        chakanclassNotice: function (id, name) {
            this.banjitongzhishow = true;
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/notice/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classNoticeDetail = data.data.data;
                        self.classNoticeDetail.teacher = name;
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
        //获取班级通知
        getClassNotice: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/notice?limit=10&page=" + self.current_page)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classNoticeList = data.data.data;
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
        this.getClassNotice();

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.class_tongzhi {
    background-color: #fff;
    padding: 20px;
}

.class_notice_search {
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