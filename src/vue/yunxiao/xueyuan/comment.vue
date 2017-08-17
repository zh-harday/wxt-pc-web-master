<template>
    <div class="content_two">
        <div class="c_left">
            <h1>综合评价</h1>
            <div id="zonghetongji"></div>
        </div>
        <div class="c_right">
            <ul class="sx_class" v-if="false">
                <li :class="{active:class_id == 1}" @click="classClickFun(1)">全部</li>
                <li :class="{active:class_id == 2}" @click="classClickFun(2)">一班</li>
                <li :class="{active:class_id == 3}" @click="classClickFun(3)">二班</li>
            </ul>
            <el-table :data="shangke.evaluation" width="100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="name" label="上课时间">
                    <template scope="scope">
                        {{ scope.row.start_time | yyyy_mm_dd_M_S }}
                    </template>
                </el-table-column>
                <el-table-column prop="class_name" label="班级名称">
                </el-table-column>
                <el-table-column prop="subject_name" label="课程名称">
                </el-table-column>
                <el-table-column prop="teacher" label="授课老师">
                </el-table-column>
                <el-table-column label="课堂总评">
                    <template scope="scope">
                        {{ parseInt(scope.row.jl) + parseInt(scope.row.td) + parseInt(scope.row.zs) }}星
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="点评时间">
                    <template scope="scope">
                        {{ scope.row.start_time | yyyy_mm_dd_M_S }}
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="操作" width="100">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="pingjiaxiangqing(scope.row)">查看详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="shangke.count > shangke.evaluation.length">
                <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-sizes="[10, 20, 30, 40]" :page-size="pageSize" layout="total, prev, pager, next" :total="shangke.count">
                </el-pagination>
            </div>
        </div>
        <!--评价详情-->
        <el-dialog title="上课点评详情" v-model="dialogVisible" size="tiny" custom-class="shangkejiludianpingbox">
            <p class="fengexian"></p>
            <div class="shangkedianping">
                <el-row>
                    <el-col :span="3">
                        班级名称：
                    </el-col>
                    <el-col :span="21">
                        {{ dpDetail.class_name }}
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        相关课程：
                    </el-col>
                    <el-col :span="21">
                        {{ dpDetail.subject_name }}
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        上课时间：
                    </el-col>
                    <el-col :span="21"> {{ dpDetail.start_time | yyyy_mm_dd_M_S }} </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        下课时间：
                    </el-col>
                    <el-col :span="21"> {{ dpDetail.end_time | yyyy_mm_dd_M_S }} </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        上课时长：
                    </el-col>
                    <el-col :span="21"> {{ (dpDetail.end_time - dpDetail.start_time) | long_time }}分钟 </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        课堂纪律：
                    </el-col>
                    <el-col :span="21">
                        <el-rate v-model="dpDetail.jl" disabled show-text text-color="#ff9900" text-template="{value}">
                        </el-rate>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        学习态度：
                    </el-col>
                    <el-col :span="21">
                        <el-rate v-model="dpDetail.td" disabled show-text text-color="#ff9900" text-template="{value}">
                        </el-rate>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        知识掌握：
                    </el-col>
                    <el-col :span="21">
                        <el-rate v-model="dpDetail.zs" disabled show-text text-color="#ff9900" text-template="{value}">
                        </el-rate>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3">
                        老师评语：
                    </el-col>
                    <el-col :span="21">
                        <div v-html="dpDetail.info"></div>
                    </el-col>
                </el-row>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>

module.exports = {
    data: function () {
        return {
            class_id: 1,
            shangke: {
                evaluation: []
            },
            current_page: 1,
            pageSize: 10,
            dialogVisible: false,
            dpDetail: {}
        }
    },
    methods: {
        //点击班级筛选
        classClickFun: function (id) {
            this.class_id = id;
        },
        handleCurrentChange: function (val) {
            this.current_page = val;
            this.getshangkeDP();
        },
        //综合统计
        getZonghetongji: function () {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id + "/stats_radar")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        var obj = data.data.data;
                        obj.qd = obj.qd == 0 ? 5 : obj.qd;
                        obj.jl = obj.jl == 0 ? 5 : obj.jl;
                        obj.td = obj.td == 0 ? 5 : obj.td;
                        obj.zy = obj.zy == 0 ? 5 : obj.zy;
                        obj.zs = obj.zs == 0 ? 5 : obj.zs;
                        var datas = obj;
                        var myChart = self.echarts.init(document.getElementById('zonghetongji'));
                        myChart.setOption({

                            tooltip: {
                                trigger: 'axis'
                            },

                            radar: [
                                {
                                    indicator: [
                                        { text: '课堂纪律', max: 5 },
                                        { text: '学习态度', max: 5 },
                                        { text: '作业完成', max: 5 },
                                        { text: '知识掌握', max: 5 },
                                        { text: '上课签到', max: 5 }
                                    ],
                                    center: ['50%', '50%'],
                                    radius: 65
                                }
                            ],
                            series: [
                                {
                                    type: 'radar',
                                    color: ['#3fda9e'],
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    itemStyle: { normal: { areaStyle: { type: 'default' } } },
                                    data: [
                                        {
                                            value: [datas.jl, datas.td, datas.zy, datas.zs, datas.qd],
                                            name: '综合统计'
                                        }
                                    ]
                                }
                            ]
                        });
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
        //上课点评记录
        getshangkeDP: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/evaluation?&page=" + self.current_page + "&limit=" + self.pageSize)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.shangke = data.data.data;
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
        //查看评价详情
        pingjiaxiangqing: function (obj) {
            this.dialogVisible = true;
            this.dpDetail = obj;
        }
    },
    filters: {
        long_time: function (val) {
            var result = Math.ceil(val / 60);
            return result;
        }
    },
    created: function () {
        this.$store.commit('setyx_xy_xq_meau_id', 5);
        this.getshangkeDP();
    },
    mounted: function () {
        this.$nextTick(function () {
            this.getZonghetongji();
        })
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
#zonghetongji {
    width: 250px;
    height: 250px;
}

.sx_class {
    overflow: hidden;
    margin-top: 20px;
    margin-bottom: 10px;
    li {
        float: left;
        margin-right: 15px;
        cursor: pointer;
        font-size: 14px;
        color: @c2;
        margin-bottom: 10px;
        &.active {
            font-weight: bold;
            color: @c1;
        }
    }
}

.shangkedianping {
    .el-row {
        padding-bottom: 10px;
        color: @c2;
    }
}
</style>