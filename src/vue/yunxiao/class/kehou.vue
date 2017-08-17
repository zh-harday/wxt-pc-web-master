<template>
    <div>
        <div class="content_two">
            <datetmp title="上课日历" :month-date="monthDate" v-on:changetime="changetime" v-on:readyfun="readyFun"></datetmp>
            <div class="c_right">
                <h1 class="c_right_h1">已上课程列表</h1>
                <el-table :data="paikeList.curriculum" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                    <el-table-column prop="subject" label="课程名称" min-width="95px">
                    </el-table-column>
                    <el-table-column prop="start_time" label="上课时间" min-width="200px">
                        <template scope="scope">
                            {{ scope.row.start_time | yyyy_mm_dd_M_S_week }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="teacher_name" min-width="120px" label="授课老师"></el-table-column>
                    <el-table-column prop="room_name" label="授课教室" min-width="95px">
                    </el-table-column>
                    <el-table-column prop="status" label="课程状态" min-width="95px">
                        <template scope="scope">
                            <el-tag type="gray" v-if="scope.row.status == 2">已下课</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="280">
                        <template scope="scope">
                            <el-button type="primary" size="mini" @click="kaoqinguanli(scope.row.id)" v-if="yx_bjgl_my_xskq">考勤管理</el-button>
                            <el-button type="info" size="mini" @click="openkehoudianping(scope.row.class_id,scope.row.id)" v-if="yx_bjgl_my_qtxx">课后点评</el-button>
                            <el-button type="warning" size="mini" @click="xueyuanhuiping(scope.row.id)" v-if="yx_bjgl_my_view">学员回评</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
    
        </div>
        <div class="fenye" v-show="paikeList.count > paikeList.curriculum.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="paikeData.page" :page-size="paikeData.limit" layout="total, prev, pager, next" :total="paikeList.count">
            </el-pagination>
        </div>
        <!--课后点评-->
        <dianp :id="dpid" :cid="$route.params.id" :changedps="changdp"></dianp>
        <kaoqin :id="kaoqinid" :changeid="kaoqinid_change"></kaoqin>
        <!--学员回评-->
        <el-dialog title="学员回评" v-model="xueyuanhuipingshow">
            <ul class="xueyuanhuipinglist" v-loading="loadingHP" element-loading-text="加载中">
                <li v-for="item in xueyuanhuipinglist.student">
                    <span>
                        <i>
                            <img :src="item.face" alt="">
                        </i>
                        <span>{{ item.student_name }}</span>
                    </span>
                    <div>
                        <h1>
                            <el-rate v-model="item.teacher_score" disabled>
                            </el-rate>
                            <span class="xueyuanhuiping_time">{{ item.reply_time | yyyy_mm_dd_M_S }}</span>
                        </h1>
                        <p>{{ item.reply_info }}</p>
                    </div>
                </li>
                <li v-show="xueyuanhuipinglist.student.length == 0" class="zanwushuju">暂无数据</li>
            </ul>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            //已上课程列表
            loading: false,
            kaoqinid: "",
            kaoqinid_change: "",

            //显示学员回评
            xueyuanhuipingshow: false,
            //左边日期
            monthDate: [],
            //左边日期
        
            //排课列表
            paikeList: {
                curriculum: []
            },
            //排课列表提交数据
            paikeData: {
                limit: 13,
                page: 1,
                type: "end"
            },

            //记录正在操作的课程id
            subject_id: "",
            //回评
            loadingHP: false,
            xueyuanhuipinglist: {
                student: []
            },

            changeDate: "",

            changdp:0,
            dpid:"",


            yx_bjgl_my_view: false,
            yx_bjgl_my_xskq: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        changetime:function(data){
            this.paikeData.page = 1;
            this.changeDate = data;
            this.getPaike();
        },
        readyFun:function(arr,datetime){
            this.checkPaikeFun(arr,datetime);
            this.getPaike();
        },
        openkehoudianping:function(cid,id){
            this.dpid = id;
            this.changdp = +new Date();
        },
        //分页
        handleCurrentChange: function (val) {
            this.paikeData.page = val;
            this.getPaike();
        },
        //打开考勤管理
        kaoqinguanli: function (id) {
            this.kaoqinid = id;
            this.kaoqinid_change = new Date().getTime();
        },
        //检查排课
        checkPaikeFun: function (arr,datetime) {
            var self = this;
            var date = datetime.year + "-" + (datetime.month + 1);
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum/month/" + date + "?type=end")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < arr.length; i++) {
                            for (var k in data.data.data) {
                                if (parseInt(k) == arr[i].day) {
                                    arr[i].empt = false;
                                    break;
                                }
                            }
                        }
                        self.monthDate = arr;
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
          
        //获取课程列表
        getPaike: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum?day=" + self.changeDate + "&type=" + self.paikeData.type + "&page=" + self.paikeData.page + "&limit=" + self.paikeData.limit)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.paikeList = data.data.data;
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
        //学员回评
        xueyuanhuiping: function (id) {
            var self = this;
            self.xueyuanhuipingshow = true;
            self.loadingHP = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum/" + id + "/evaluation_student")
                .then(function (data) {
                    self.loadingHP = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.student.length; i++) {
                            data.data.data.student[i].face = data.data.data.student[i].face ? ("http://wx.eduwxt.com" + data.data.data.student[i].face) : ("http://img.eduwxt.com/assets/images/users/avatar-11.jpg");
                        }
                        self.xueyuanhuipinglist = data.data.data;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loadingHP = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        
        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //考勤
        this.yx_bjgl_my_xskq = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_xskq", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less"; //我的课表 班级课表公共样式 月
.c_right_h1 {
    font-size: @h3;
    color: @c1;
    padding: 10px 0;
    font-weight: normal;
    margin-bottom: 10px;
    span {
        float: right;
        font-size: 14px;
        color: @c2;
        font-weight: normal;
        padding-top: 5px;
    }
}


.xueyuanhuipinglist {
    height: 400px;
    overflow: hidden;
    overflow-y: auto;
    padding-right: 10px;
    border-top: 1px solid #ddd;
    li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        overflow: hidden;
        display: flex;
        &.zanwushuju {
            border: none;
            display: block;
            padding: 0;
            height: 400px;
            width: 100%;
            line-height: 400px;
            text-align: center;
            font-size: 14px;
            color: @c2;
        }
        >span {
            display: block;
            width: 70px;
            height: 70px;
            margin-top: 10px;
            >i {
                display: block;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                overflow: hidden;
                img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
            }
            span {
                display: block;
                height: 30px;
                line-height: 30px;
                font-size: 14px;
                color: @c1;
            }
        }
        >div {
            flex: 1;
            h1 {
                font-weight: normal;
                position: relative;
                .el-rate {
                    height: auto;
                }
                .xueyuanhuiping_time {
                    position: absolute;
                    right: 0;
                    bottom: 0;
                    font-size: 12px;
                    color: @c3;
                }
            }
            p {
                font-size: 14px;
                color: @c2;
                clear: both;
                padding-top: 10px;
            }
        }
    }
}
</style>