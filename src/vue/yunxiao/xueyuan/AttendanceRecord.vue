<template>
    <div class="content_two">
        <div class="c_left">
            <h1>考勤统计</h1>
            <div class="b_canvas">
                <span>到课率
                    <i>{{ daokelv }}%</i>
                </span>
                <div id="b_canvas"></div>
            </div>
            <div class="colorList">
                <!--<span class="wcl">
                    <s></s>
                    <i>未处理：1次</i>
                </span>-->
                <span class="qd">
                    <s></s>
                    <i>正常签到：{{ kqtj.attence["1"] }}次</i>
                </span>
                <span class="qjk">
                    <s></s>
                    <i>请假：{{ kqtj.attence["2"] }}次</i>
                </span>
                <!--<span class="wdk">
                    <s></s>
                    <i>未到扣课时：4次</i>
                </span>
                <span class="qjbk">
                    <s></s>
                    <i>请假不扣课时：5次</i>
                </span>-->
                <span class="wdbk">
                    <s></s>
                    <i>未到：{{ kqtj.attence["3"] }}次</i>
                </span>
            </div>
        </div>
        <div class="c_right">
            <el-table :data="kaoqinList.attence" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="start_times" label="上课时间" width="155">
                </el-table-column>
                <el-table-column prop="class_name" label="班级名称">
                </el-table-column>
                <el-table-column prop="subject" label="课程名称">
                </el-table-column>
                <el-table-column prop="subject_teacher" label="授课老师">
                </el-table-column>
                <el-table-column prop="attence_status" label="签到状态">
                    <template scope="scope">
                        <el-tag type="gray" v-if="scope.row.attence_status == 0">未签到</el-tag>
                        <el-tag type="success" v-if="scope.row.attence_status == 1">已签到</el-tag>
                        <el-tag type="success" v-if="scope.row.attence_status == 2">已请假</el-tag>
                        <el-tag type="danger" v-if="scope.row.attence_status == 3">已旷课</el-tag>
                        <el-tag type="warning" v-if="scope.row.attence_status == 4">预请假</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="is_xk" label="扣课时情况" width="110px">
                    <template scope="scope">
                        <el-tag type="danger" v-if="scope.row.is_xk ==1">扣课时</el-tag>
                        <el-tag type="warning" v-if="scope.row.is_xk ==0">不扣课时</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="attence_times" label="考勤时间" width="155">
                </el-table-column>
                <el-table-column prop="name" label="操作" width="120">
                    <template scope="scope">
                        <el-dropdown trigger="click">
                            <el-button type="danger" size="mini">
                                修改考勤
                                <i class="el-icon-caret-bottom el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown" menu-align="end" style="width:125px">
                                <el-dropdown-item>已签到</el-dropdown-item>
                                <el-dropdown-item>已请假-不扣课时</el-dropdown-item>
                                <el-dropdown-item>已请假-扣课时</el-dropdown-item>
                                <el-dropdown-item>未到-不扣课时</el-dropdown-item>
                                <el-dropdown-item>未到-扣课时</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="kaoqinList.count > pageSize">
                <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="pageSize" layout="total, prev, pager, next" :total="kaoqinList.count">
                </el-pagination>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            current_page: 1,
            pageSize: 10,
            loading: false,
            kaoqinList: {
                attence: []
            },
            daokelv:100,
            kqtj: {
               attence:{
                   "1":0,
                   "2":0,
                   "3":0
               } 
            }
        }
    },
    methods: {
        handleCurrentChange: function (val) {
            var self = this;
            self.current_page = val;
            this.getkaoqinList({
                limit: self.pageSize,
                page: self.current_page
            });
        },
        //日期转换
        transformTime: function (val) {//xxxx-xx-xx xx:xx
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate()) + " " + (date.getHours() < 10 ? ("0" + date.getHours()) : date.getHours()) + ":" + (date.getMinutes() < 10 ? ("0" + date.getMinutes()) : date.getMinutes());
        },
        getkaoqinList: function (option) {
            var self = this;
            self.loading = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/attence?limit=" + option.limit + "&page=" + option.page)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.attence.length; i++) {
                            data.data.data.attence[i].attence_times = self.transformTime(data.data.data.attence[i].attence_time);
                            data.data.data.attence[i].start_times = self.transformTime(data.data.data.attence[i].start_time);
                            data.data.data.attence[i].end_times = self.transformTime(data.data.data.attence[i].end_time);
                        }
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
        },
        setKQtongji:function(data){
            var self = this;
            self.kqtj = data;
            var obj = self.kqtj.attence;
            if(obj["1"]+obj["2"]+obj["3"] <= 0){
                self.daokelv = 100;
            }else{
                self.daokelv = parseInt(obj["1"]/(obj["1"]+obj["2"]+obj["3"])*100);
            }

            // 基于准备好的dom，初始化echarts实例
            var myChart = self.echarts.init(document.getElementById('b_canvas'));
            myChart.setOption({
                // color: ['#62b2fd', '#ffe400', '#8f82bc', '#fa9857', '#7bdbb6', '#fa6657'],
                color: ['#3fda9e', '#fa9857','#ff4949'],
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                series: [
                    {
                        name: '考勤统计',
                        type: 'pie',
                        radius: ['50%', '80%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: false,
                                textStyle: {
                                    fontSize: '16',
                                    fontWeight: '100'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: [
                            { value: self.kqtj.attence["1"], name: '正常上课' },
                            { value: self.kqtj.attence["2"], name: '请假' },
                            { value: self.kqtj.attence["3"], name: '旷课' }
                        ]
                    }
                ]
            });
        }
    },
    created: function () {
        var self = this;
        self.$store.commit('setyx_xy_xq_meau_id', 6);
        self.getkaoqinList({
            limit: self.pageSize,
            page: self.current_page
        });
    },
    mounted: function () {
        var self = this;
        self.$nextTick(function () {
            self.$store.commit('getkaoqinTJ', {
                self: self,
                sid: self.$route.params.id,
                type: "attence",
                fun: self.setKQtongji
            });
        })

    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.b_canvas {
    width: 250px;
    height: 250px;
    position: relative;
    >span {
        width: 100%;
        display: block;
        position: absolute;
        top: 105px;
        text-align: center;
        font-size: 16px;
        color: @c2;
        i {
            font-size: 22px;
            color: @color;
            display: block;
            width: 100%;
            font-style: normal;
            font-weight: bold;
        }
    }
}

#b_canvas {
    width: 250px;
    height: 250px;
}

.colorList {
    width: 250px;
    overflow: hidden;
    >span {
        display: block;
        margin-bottom: 10px;
        position: relative;
        margin-left: 20px;
        &.wcl {
            >s {
                background-color: @error;
            }
        }
        &.qd {
            >s {
                background-color: @color;
            }
        }
        &.qjk {
            >s {
                background-color: #fa9857;
            }
        }
        &.wdk {
            >s {
                background-color: #8f82bc;
            }
        }
        &.qjbk {
            >s {
                background-color: #ffe400;
            }
        }
        &.wdbk {
            >s {
                background-color: #ff4949;
            }
        }
        >s {
            display: block;
            width: 16px;
            height: 16px;
            background-color: @color;
            position: absolute;
            left: 0;
            top: 3px;
        }
        >i {
            display: block;
            font-size: 14px;
            color: @c2;
            ;
            padding-left: 20px;
            font-style: normal;
        }
    }
}

.content_two {
    .c_right {
        padding: 0;
        background-color: transparent;
    }
}
</style>