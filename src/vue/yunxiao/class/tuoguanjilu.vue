<template>
    <div>
        <div class="content_two">
            <datetmp title="托管日期" :month-date="monthDate" v-on:changetime="changetime" v-on:readyfun="readyFun"></datetmp>
            <div class="c_right">
                <h1 class="c_right_h1">托管记录</h1>
                <el-table :data="kaoqinList.tuoguan" style="width: 100%" v-loading="loading" element-loading-text="加载中">
    
                    <el-table-column prop="start_time" label="签到时间">
                        <template scope="scope">
                            {{ scope.row.time | yyyy_mm_dd_M_S_week }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="student_name" label="姓名"></el-table-column>
                    <el-table-column prop="status" label="签到状态">
                        <template scope="scope">
                            <el-tag type="gray" v-if="scope.row.status == 2">签退</el-tag>
                            <el-tag type="success" v-if="scope.row.status == 1">签到</el-tag>
                            <el-tag type="warning" v-if="scope.row.status == 3">请假</el-tag>
                            <el-tag type="danger" v-if="scope.row.status == 0">未到</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column prop="teacher_name" label="操作老师"></el-table-column>
                    <el-table-column prop="remark" label="备注">
                        <template scope="scope">
                            <span>{{ (!scope.row.remark || scope.row.remark == '' || scope.row.remark == 'undefined')?'--':scope.row.remark }}</span>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
    
        </div>
        <div class="fenye" v-show="kaoqinList.count > kaoqinList.tuoguan.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="page" :page-size="limit" layout="total, prev, pager, next" :total="kaoqinList.count">
            </el-pagination>
        </div>
    
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            //已上课程列表
            loading: false,
            limit: 15,
            page: 1,
            day: "",
            monthDate: [],
            kaoqinList: {
                count: 0,
                tuoguan: []
            },

            yx_bjgl_my_view: false,
            yx_bjgl_my_xskq: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        changetime: function (data) {
            this.day = data;
            this.page = 1;
            this.gettuoguanjilu();
        },
        readyFun: function (arr, datetime) {
            this.checkkaoqin(arr, datetime);
        },
        //分页
        handleCurrentChange: function (val) {
            this.page = val;
            this.gettuoguanjilu();
         },
        //检查考勤记录
        checkkaoqin: function (arr, datetime) {

            var self = this;
            var date = datetime.year + "-" + (datetime.month + 1);
            console.log(date);
            self.$http.get("/api/classs/" + self.$route.params.id + "/tuoguan/month/" + date)
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
        gettuoguanjilu: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/tuoguan/log?limit=" + self.limit + "&page=" + self.page + "&day=" + self.day)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.kaoqinList = data.data.data;
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
        this.gettuoguanjilu();
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