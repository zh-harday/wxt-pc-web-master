<template>
    <div>
        <h1 class="fwgz_title">添加跟踪反馈</h1>
        <div class="jiaoji_box">
            <div class="tit_b">
                <el-select v-model="form.type" placeholder="标记反馈信息类型">
                    <el-option v-for="item in biaoqianList" :label="item.name" :value="item.id">
                        <s :style="{ backgroundColor:item.color }" class="bj_xiala"></s>
                        <span class="bj_text">{{ item.name }}</span>
                    </el-option>
                    <router-link :to="{ name : 'track' }" class="biaoqiansetting">
                        <i class="el-icon-setting"></i>设置</router-link>
                </el-select>
                <el-checkbox v-model="form.task">设定跟进计划提醒</el-checkbox>
                <el-date-picker v-model="form.next_time" :picker-options="pickerOptions0" type="date" placeholder="选择下次跟进时间" v-if="form.task">
                </el-date-picker>
                <el-date-picker v-model="form.next_time" type="datetime" placeholder="选择下次跟进时间" disabled v-if="!form.task">
                </el-date-picker>
            </div>
            <textarea cols="30" rows="10" v-model="form.content" class="fankui_text" placeholder="请输入您的跟踪反馈"></textarea>
            </el-input>
            <div class="tit_btn">
                <el-button type="primary" @click="tijiaoGZFK">提交</el-button>
            </div>
        </div>
        <h1 class="fwgz_title2">跟踪记录</h1>
        <ul class="gz_list">
            <li v-for="(item,index) in zhuizonglilu.track">
                <div class="gz_left">
                    <span>
                        <img :src="'http://img.eduwxt.com'+item.face" alt="">
                    </span>
                    <s>{{ item.staff_name }}</s>
                    <i>{{ item.job_description }}</i>
                </div>
                <div class="gz_right">
                    <h1>
                        <time>{{ item.time | yyyy_mm_dd_M_S }}</time>
                        <span v-for="list in biaoqianList" v-if="item.type_id == list.id">{{ list.name }}</span>
                        <i v-for="list in biaoqianList" v-if="item.type_id == list.id" :style="{ backgroundColor:list.color }"></i>
                    </h1>
                    <p>{{ item.content }}</p>
    
                    <div>
                        <el-tag type="success" v-if="item.task_id != 0 && index==0">
                            <i class="el-icon-time"></i>{{ YBstudentDetail.next_time | yyyy_mm_dd }}
                        </el-tag>
                        <el-button type="danger" size="small" v-if="item.user_id === myMessage.id" @click="delGZjilu(item.intention_id,item.id)">删除</el-button>
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
        },
        YBstudentDetail: function () {
            return this.$store.state.YBstudentDetail;
        }
    },
    data: function () {
        return {
            form: {
                content: "",
                type: "",
                next_time: "",
                next_user: "",
                task: false//是否添加计划提醒 不提醒留空 提醒 1
            },
            //服务跟踪
            option: {
                limit: 5,
                page: 1
            },

            tj: true,
            //追踪记录
            zhuizonglilu: {
                track: []
            },
            //标签列表
            biaoqianList: window.sessionStorage.getItem("biaoqianList") ? JSON.parse(window.sessionStorage.getItem("biaoqianList")) : [],

            pickerOptions0: {
                disabledDate: function (time) {
                    return time.getTime() < +new Date() - (1000 * 60 * 60 * 24)
                }
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

                self.$http.post("/api/intention/"+sid+"/track/"+uid+"/del")
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
        //分页点击
        handleCurrentChange: function (val) {
            this.option.page = val;
            this.getZhuizongjilu();
        },
        //提交跟踪反馈
        tijiaoGZFK: function () {
            var self = this;

            if (self.form.type == "") {
                self.$message({
                    showClose: true,
                    message: "请添加标记",
                    type: "warning"
                })
                return;
            }
            if (self.form.task) {
                if (self.form.next_time == '') {
                    self.$message({
                        showClose: true,
                        message: "请选择提醒日期",
                        type: "warning"
                    })
                    return;
                }
            }
            if (self.form.content.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入跟踪反馈",
                    type: "warning"
                });
                return;
            }

            var formData = new FormData();
            var task = self.form.task ? "1" : "";
            var next_time = "";
            if (self.form.next_time != "") {
                next_time = self.form.next_time.getTime() / 1000 + (60 * 60 * 8);
            }

            formData.append("content", self.form.content);
            formData.append("type", self.form.type);
            formData.append("next_time", next_time);
            formData.append("next_user", "");
            formData.append("task", task);

            self.form = {
                content: "",
                type: "",
                next_time: "",
                next_user: "",
                task: false//是否添加计划提醒 不提醒留空 提醒 1
            };

            if (!self.tj) {
                return;
            }
            self.tj = false;

            self.$http.post("/api/intention/" + self.$route.params.id + "/track", formData)
                .then(function (data) {
                    self.tj = true;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.getZhuizongjilu();
                        self.$store.commit("getYBsutudent", self.$route.params.id);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.tj = true;
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
            self.$http.get("/api/intention/" + self.$route.params.id + "/track?limit=" + self.option.limit + "&page=" + self.option.page)
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
    font-weight: normal;
}

.fwgz_title2 {
    font-size: @h3;
    color: @c1;
    padding-bottom: 10px;
    font-weight: normal;
    border-bottom: 1px solid #ddd;
}

.biaoqiansetting {
    display: block;
    height: 36px;
    line-height: 36px;
    text-align: center;
    font-size: 14px;
    color: @c2;
    border-top: 1px solid #ddd;
    i {
        margin-right: 2px;
        color: @c3;
    }
}

.jiaoji_box {
    background-color: #fff;
    padding: 20px;
    margin-bottom: 20px;
    .tit_b {
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
        border-radius: 0;
        .el-select {
            width: 160px;
            margin-right: 20px;
        }
        .el-date-editor {
            width: 160px;
            margin-left: 10px;
        }
    }
}

.el-textarea__inner {
    border: none;
}

.fankui_text {
    border: none;
    outline: none;
    display: block;
    width: 100%;
    height: 100px;
    padding: 10px;
    box-sizing: border-box;
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
            width: 100px;
            height: 100px;
            padding-top: 10px;
            >span {
                display: block;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                overflow: hidden;
                margin: 0 auto;
                img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
            }
            >s {
                display: block;
                text-decoration: none;
                text-align: center;
                font-size: 14px;
                color: @c1;
            }
            >i {
                display: block;
                font-style: normal;
                text-align: center;
                font-size: 13px;
                color: @c3;
            }
        }
        .gz_right {
            flex: 1;
            background-color: #fff;
            padding: 10px;
            padding-left: 20px;
            >h1 {
                overflow: hidden;
                border-bottom: 1px solid #ddd;
                padding-bottom: 10px;
                >time {
                    font-size: 14px;
                    color: @c2;
                    font-weight: normal;
                    display: block;
                    float: left;
                }
                >span {
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
            >p {
                font-size: @h3;
                color: @c1;
                padding-top: 10px;
            }
            >div {
                text-align: right;
                margin-top: 20px;
            }
        }
    }
}
</style>