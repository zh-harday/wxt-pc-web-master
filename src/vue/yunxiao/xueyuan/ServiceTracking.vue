<template>
    <div>
        <h1 class="fwgz_title">添加跟踪反馈</h1>
        <div class="jiaoji_box">
            <div class="tit_b">
                <el-select v-model="biaoji" placeholder="标记">
                    <el-option v-for="item in biaoqianList" :label="item.name" :value="item.id">
                        <s :style="{ backgroundColor:item.color }" class="bj_xiala"></s>
                        <span class="bj_text">{{ item.name }}</span>
                    </el-option>
                </el-select>
            </div>
            <el-input type="textarea" :autosize="{ minRows: 4, maxRows: 8}" placeholder="请添加您的跟踪反馈" v-model="fankui" style="border:none">
            </el-input>
            <div class="tit_btn">
                <el-button type="primary" @click="tijiaoGZFK">提交</el-button>
            </div>
        </div>
        <h1 class="fwgz_title2">跟踪记录</h1>
        <ul class="gz_list">
            <li v-for="item in zhuizonglilu.track">
                <div class="gz_left">
                    <img :src="item.face" alt="">
                </div>
                <div class="gz_right">
                    <h1 v-for="list in biaoqianList" v-if="item.type_id == list.id">
                        <span>{{ list.name }}</span>
                        <i :style="{ backgroundColor:list.color }"></i>
                    </h1>
                    <p>{{ item.content }}</p>
                    <div class="delsss">
                        <el-button type="danger" size="small" v-if="item.user_id === myMessage.id" @click="delGZjilu(item.student_id,item.id)">删除</el-button>
                    </div>
                    <div>
                        <span>{{ item.staff_name }}
                            <i>{{ item.job_description }}</i>
                        </span>
                        <s>{{ item.time | yyyy_mm_dd_M_S }}</s>
                    </div>
                </div>
            </li>
        </ul>
        <div class="fenye" v-if="zhuizonglilu.count > zhuizonglilu.track.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="option.page" :page-size="option.limit" layout="total, prev, pager, next" :total="zhuizonglilu.count">
            </el-pagination>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            //标签列表
            biaoqianList: window.sessionStorage.getItem("biaoqianList") ? JSON.parse(window.sessionStorage.getItem("biaoqianList")) : [],
            biaoji: "",
            fankui: "",
            zhuizonglilu: {
                track: []
            },

            option: {
                page: 1,
                limit: 5
            }
        }
    },
    methods: {
        //删除跟踪记录
        delGZjilu: function (sid, uid) {
            var self = this;
            self.$confirm('是否要删除此条跟踪记录？', '提示', {
                confirmButtonText: '删除',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {

                self.$http.post("/api/student/service/" + sid + "/del/" + uid)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$message({
                                type: 'success',
                                message: '删除成功!'
                            });
                            self.getZhuizongjilu();
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
        handleCurrentChange: function (val) {
            this.option.page = val;
            this.getZhuizongjilu();
        },
        //提交跟踪反馈
        tijiaoGZFK: function () {
            var self = this;
            if (self.biaoji == "") {
                self.$message({
                    showClose: true,
                    message: "请添加标记",
                    type: "warning"
                })
                return;
            }
            if (self.fankui.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入反馈信息",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();

            formData.append("content", self.fankui);
            formData.append("type", self.biaoji);

            self.$http.post("/api/student/service/" + self.$route.params.id + "/add", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.biaoji = "";
                        self.fankui = "";
                        self.getZhuizongjilu();
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
        //获取追踪记录列表
        getZhuizongjilu: function () {
            var self = this;
            self.$http.get("/api/student/service/" + self.$route.params.id + "?limit=" + self.option.limit + "&page=" + self.option.page)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.zhuizonglilu = data.data.data;
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
        //获取标签
        getBiaoqianList: function () {
            var self = this;
            self.$http.get("/api/intention/track_type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.biaoqianList = data.data.data;
                        window.sessionStorage.setItem("biaoqianList", JSON.stringify(self.biaoqianList));
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
        this.$store.commit('setyx_xy_xq_meau_id', 2);
        this.getBiaoqianList();
        this.getZhuizongjilu();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.fwgz_title {
    font-size: @h3;
    color: @c1;
    padding-bottom: 15px;
}
.delsss{
    text-align: right;
    padding: 5px 0;
}
.fwgz_title2 {
    font-size: @h3;
    color: @c1;
    padding-bottom: 10px;
    margin-bottom: 1px solid #ddd;
}

.jiaoji_box {
    background-color: #fff;
    padding: 20px;
    margin-bottom: 20px;
    .tit_b {
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }
}

.el-textarea__inner {
    border: none;
}

.fankui_texteran {
    textarea {
        border: none;
    }
}

.bj_xiala {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    float: left;
    margin-right: 8px;
}

.bj_text {
    display: block;
    float: left;
    font-size: 14px;
}

.tit_btn {
    text-align: right;
    padding-top: 20px;
}

.gz_list {
    padding: 10px 0;
    li {
        display: flex;
        margin-bottom: 20px;
        .gz_left {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            margin-top: 10px;
            img {
                display: block;
                width: 100%;
                height: 100%;
            }
        }
        .gz_right {
            flex: 1;
            background-color: #fff;
            padding: 10px;
            padding-left: 20px;
            h1 {
                overflow: hidden;
                span {
                    font-size: 14px;
                    color: @c3;
                    display: block;
                    float: right;
                    font-weight: normal;
                }
                i {
                    display: block;
                    width: 16px;
                    height: 16px;
                    border-radius: 50%;
                    float: right;
                    margin-right: 8px;
                    margin-top: 2px;
                }
            }
            p {
                font-size: @h3;
                color: @c1;
                margin-bottom: 20px;
            }
            div {
                overflow: hidden;
                span {
                    display: block;
                    float: left;
                    width: 50%;
                    color: @c2;
                    i {
                        font-style: normal;
                        color: @c3;
                        padding-left: 5px;
                    }
                }
                s {
                    width: 50%;
                    display: block;
                    text-decoration: none;
                    float: right;
                    text-align: right;
                    color: @c3;
                }
            }
        }
    }
}
</style>