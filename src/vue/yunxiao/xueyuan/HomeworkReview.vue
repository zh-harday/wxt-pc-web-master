<template>
    <div class="content_two">
        <div class="c_left">
            <h1>批改作业</h1>
            <div id="main"></div>
        </div>
        <div class="c_right">
            <el-table :data="HomeworkList.work" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="time" label="时间">
                    <template scope="scope">
                        {{ scope.row.time | yyyy_mm_dd }}
                    </template>
                </el-table-column>
                <el-table-column prop="title" label="作业标题">
                </el-table-column>
                <el-table-column prop="subject" label="相关课程">
                </el-table-column>
                <el-table-column prop="score" label="作业评级">
                    <template scope="scope">
                        <span v-if="scope.row.comments_time == 0">未评价</span>
                        <span v-else>{{ scope.row.score }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="teacher" label="点评教师">
                    <template scope="scope">
                        <span v-if="scope.row.comments_time == 0">--</span>
                        <span v-else>{{ scope.row.teacher }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="comments_time" label="点评时间">
                    <template scope="scope">
                        <span v-if="scope.row.comments_time == 0">--</span>
                        <span v-else>{{ scope.row.comments_time | yyyy_mm_dd }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="100">
                    <template scope="scope">
                        <el-button type="primary" size="mini" v-if="scope.row.comments_time == 0" disabled>批改详情</el-button>
                        <el-button type="primary" size="mini" v-else @click="dpxiangqing(scope.row)">批改详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="HomeworkList.count > HomeworkList.work.length">
                <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-sizes="[10, 20, 30, 40]" :page-size="pageSize" layout="total, prev, pager, next" :total="HomeworkList.count">
                </el-pagination>
            </div>
        </div>
        <!--作业点评详情-->
        <el-dialog :title="dp.title" v-model="zuoyedianping" size="tiny">
            <div>
                {{ dp.comments }}
            </div>
            <time>{{ dp.comments_time | yyyy_mm_dd }}</time>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="zuoyedianping = false">确定</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            current_page: 1,
            pageSize: 10,
            loading: false,
            zuoyedianping: false,
            HomeworkList: {
                work: []
            },
            dp: {
                class_name: "",
                teacher: "",
                subject: "",
                id: "",
                title: "",
                time: "",
                score: "",
                comments: "",
                comments_time: ""
            },

            zytj: {
                score: {}
            }
        }
    },
    methods: {
        //获取作业统计
        setTongji: function (data) {
            var self = this;
            self.zytj = data;

            // 基于准备好的dom，初始化echarts实例
            var myChart = self.echarts.init(document.getElementById('main'));
            myChart.setOption({
                color: ['#50b5ff'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: ['一星', '两星', '三星', '四星', '五星'],
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '星级次数',
                        type: 'bar',
                        barWidth: '60%',
                        data: [self.zytj.score["1"], self.zytj.score["2"], self.zytj.score["3"], self.zytj.score["4"], self.zytj.score["5"]]
                    }
                ]
            });
        },
        handleCurrentChange: function (val) {
            this.current_page = val;
            this.getHomeworkDP();
        },
        //查看点评详情
        dpxiangqing: function (obj) {
            this.zuoyedianping = true;
            this.dp = obj;
        },
        //作业点评记录
        getHomeworkDP: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/work?&page=" + self.current_page + "&limit=" + self.pageSize)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.HomeworkList = data.data.data;
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
        var self = this;
        self.$store.commit('setyx_xy_xq_meau_id', 7);
        self.getHomeworkDP();

    },
    mounted: function () {
        var self = this;
        self.$nextTick(function () {
            self.$store.commit('getkaoqinTJ', {
                self: self,
                sid: self.$route.params.id,
                type: "work",
                fun: self.setTongji
            });

        })

    }
}

</script>
<style lang="less" scoped>
#main {
    width: 250px;
    height: 250px;
}

.content_two {
    .c_right {
        padding: 0;
        background-color: transparent;
    }
}
</style>