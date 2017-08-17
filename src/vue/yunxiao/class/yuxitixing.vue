<template>
    <div>
        <!--预习提醒列表-->
        <el-table :data="yuxitixing.prep" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="title" label="标题">
            </el-table-column>
            <el-table-column prop="subject" label="相关课程">
            </el-table-column>
            <el-table-column prop="time" label="发布时间">
                <template scope="scope">
                    {{ scope.row.time | yyyy_mm_dd_M_S }}
                </template>
            </el-table-column>
            <el-table-column prop="teacher" label="发布人">
            </el-table-column>
            <el-table-column label="操作" width="150">
                <template scope="scope">
                    <el-button type="primary" size="mini" @click="chakanxiangqing(scope.row.id,scope.row.teacher)" v-if="yx_bjgl_my_view">查看详情</el-button>
                    <el-button type="danger" size="mini" @click="delYuxitixing(scope.row.id)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
    
        <!--分页-->
        <div class="fenye" v-if="yuxitixing.count > yuxitixing.prep.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="pageSize" layout="total, prev, pager, next" :total="yuxitixing.count">
            </el-pagination>
        </div>
    
        <!--预习提醒详情-->
        <el-dialog title="预习提醒详情" v-model="yuxixiangqing">
            <div class="tk_box">
                <h1>{{ yuxitongzhiDetail.prep.title }}</h1>
                <p>
                    <s>发布人：{{ yuxitongzhiDetail.prep.teacher }}</s>
                    <s>发布时间：{{ yuxitongzhiDetail.prep.time | yyyy_mm_dd_M_S }}</s>
                </p>
                <p>摘要：{{ yuxitongzhiDetail.prep.info.length>0?yuxitongzhiDetail.prep.info:'无' }}</p>
                <div class="content_box_yx" style="margin-bottom:10px" v-html="yuxitongzhiDetail.prep.body"></div>
            </div>
            <div class="tk_box1" v-if="false">
                <h2>暂未查看</h2>
                <ul>
                    <li v-for="n in 4">
                        <div class="tk_left">
                            <img src="../../../img/photo.jpg" alt="">
                        </div>
                        <div class="tk_right">
                            <h1>
                                            露露
                                            <el-button type="text">标记为已读</el-button>
                                        </h1>
                            <p>电话：13809452165</p>
                        </div>
                    </li>
                </ul>
                <h2>已查看</h2>
                <div>
                    <el-tooltip class="item" effect="dark" content="15319333285" placement="bottom" v-for="n in 11">
                        <el-tag type="primary">阿九</el-tag>
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
            yuxixiangqing: false,//预习提醒详情
            loading: false,
            current_page: 1,
            pageSize: 10,
            yuxitixing: {
                prep: []
            },
            yuxitongzhiDetail: {
                prep: {
                    info: ""
                }
            },

            yx_bjgl_my_view: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        handleCurrentChange: function (val) {
            this.current_page = val;
            this.getyuxiList();
        },
        //删除预习提醒
        delYuxitixing: function (id) {
            var self = this;
            self.$confirm('此操作将永久删除该条预习提醒, 是否继续?', '删除预习提醒', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/prep/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getyuxiList();
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
        //查看预习详情
        chakanxiangqing: function (id, name) {
            var self = this;
            self.yuxixiangqing = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/prep/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.yuxitongzhiDetail = data.data.data;
                        self.yuxitongzhiDetail.prep.teacher = name;
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
        //班级预习提醒列表
        getyuxiList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/prep?&page=" + self.current_page + "&limit=" + self.pageSize)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.yuxitixing = data.data.data;
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
        this.getyuxiList();

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>