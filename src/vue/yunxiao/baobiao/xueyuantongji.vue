<template>
    <div>
        <!--学员总数增长趋势-->
        <el-card class="box-card qushitu">
            <h1 class="data_title">
                    <span>学员数量增长趋势</span>
                    <el-select v-model="xycount" placeholder="请选择校区" size="small" @change="changeStudentCount" v-if="myMessage.campus_id == 1">
                        <el-option label="所有校区" value=""></el-option>
                        <el-option v-for="item in campus" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </h1>
            <div id="xueyuanzongshu"></div>
        </el-card>
    
        <el-row :gutter="20" class="twos">
            <el-col :span="12">
                <el-card class="box-card tw">
                    <h1 class="data_title">
                            <span>学员性别分布总览</span>
                            <el-select v-model="sexid" placeholder="请选择校区" size="small" @change="sexcahnge" v-if="myMessage.campus_id == 1">
                                <el-option label="所有校区" value=""></el-option>
                                <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </h1>
                    <div id="sexbl"></div>
                </el-card>
            </el-col>
            <el-col :span="12">
                <el-card class="box-card tw">
                    <h1 class="data_title">
                            <span>学员状态分布总览</span>
                            <el-select v-model="ztid" placeholder="请选择校区" size="small" @change="ztchange" v-if="myMessage.campus_id == 1">
                                <el-option label="所有校区" value=""></el-option>
                                <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </h1>
                    <div id="stausfenbu"></div>
                </el-card>
            </el-col>
        </el-row>
    
        <el-row :gutter="20" class="twos">
            <el-col :span="12">
                <el-card class="box-card qushitu">
                    <h1 class="data_title">
                            <span>学员班级分布</span>
                            <el-select v-model="classid" placeholder="请选择校区" size="small" @change="changeClass" v-if="myMessage.campus_id == 1">
                                <el-option label="所有校区" value=""></el-option>
                                <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </h1>
                    <div id="banjifenbu"></div>
                </el-card>
            </el-col>
            <el-col :span="12">
                <el-card class="box-card qushitu">
                    <h1 class="data_title">
                            <span>学员课程分布</span>
                            <el-select v-model="subid" placeholder="请选择校区" size="small" @change="changeSub" v-if="myMessage.campus_id == 1">
                                <el-option label="所有校区" value=""></el-option>
                                <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                </el-option>
                            </el-select>
                        </h1>
                    <div id="subjectfenbu"></div>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>
<script>
var myChart1, myChart2, myChart3, myChart4, myChart5;
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            zs_v: "",
            year: new Date().getFullYear(),
            //学员总数
            xycount: "",
            //学员
            sexid: "",
            ztid: "",
            //class分布
            classid: "",
            //课程
            subid: ""
        }
    },
    methods: {
        //学员总数增长趋势
        getStudentCountFun: function () {
            var self = this;
            self.$http.get("/api/report/student_year/" + self.year + "?campus=" + self.xycount)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.xueyuanzongshu(data.data.data);
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
        changeStudentCount: function () {
            this.getStudentCountFun();
        },
        xueyuanzongshu: function (data) {
            myChart1 = this.echarts.init(document.getElementById('xueyuanzongshu'), 'walden');
            var xarr = [];
            var yarr = [];
            for (var k in data) {
                xarr.push(k);
                yarr.push(data[k]);
            }
            var option = {
                grid: {
                    left: '0',
                    right: '50px',
                    bottom: '20px',
                    containLabel: true
                },
                toolbox: {
                    show: true,
                    feature: {
                        //数据视图
                        dataView: { show: true, readOnly: false },
                        //折线图切换
                        // magicType: { show: true, type: ['line', 'bar'] },
                        //刷新
                        restore: { show: true },
                        //保存图片
                        saveAsImage: { show: true }
                    }
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        animation: false,
                        label: {
                            backgroundColor: '#ccc',
                            borderColor: '#aaa',
                            borderWidth: 1,
                            shadowBlur: 0,
                            shadowOffsetX: 0,
                            shadowOffsetY: 0,
                            textStyle: {
                                color: '#222'
                            }
                        }
                    }
                },

                xAxis: [
                    {
                        type: 'category',
                        triggerEvent: true,
                        splitLine: {
                            show: false
                        },
                        data: xarr
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        splitLine: {
                            show: false
                        }
                    }
                ],
                series: [
                    {
                        name: '学员总数增长趋势',
                        type: 'line',
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        data: yarr,
                        markLine: {
                            data: [
                                { type: 'average', name: '平均值' }
                            ]
                        }
                    }
                ]
            }
            myChart1.setOption(option);
        },

        //男女比例
        getSexbl: function (id) {
            var self = this;
            self.$http.get("/api/report/student_sort?campus=" + self.sexid)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (id == 1) {
                            self.nannvbili(data.data.data);
                        } else if (id == 2) {
                            self.zhuangtaifenbu(data.data.data);
                        } else {
                            self.nannvbili(data.data.data);
                            self.zhuangtaifenbu(data.data.data);
                        }
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
        //nannv校区
        sexcahnge: function (val) {
            this.getSexbl(1, val);
        },
        //选择人数
        ztchange: function (val) {
            this.getSexbl(2, val);
        },
        nannvbili: function (data) {
            myChart2 = this.echarts.init(document.getElementById('sexbl'), 'walden');
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                        //数据视图
                        dataView: { show: true, readOnly: false },
                        //折线图切换
                        // magicType: { show: true, type: ['line', 'bar'] },
                        //刷新
                        restore: { show: true },
                        //保存图片
                        saveAsImage: { show: true }
                    }
                },
                tooltip: {
                    trigger: 'item',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                legend: {
                    orient: 'horizontal',
                    left: 'letf',
                    data: ['男', '女', '未知']
                },
                series: [
                    {
                        type: 'pie',
                        radius: '50%',
                        center: ['55%', '40%'],
                        data: [
                            { value: data["男"], name: '男' },
                            { value: data["女"], name: '女' },
                            { value: data["未知"], name: '未知' }
                        ]
                    }
                ]
            };
            myChart2.setOption(option);
        },
        //学员状态分布
        zhuangtaifenbu: function (data) {
            myChart3 = this.echarts.init(document.getElementById('stausfenbu'), 'walden');
            var option = {
                toolbox: {
                    show: true,
                    feature: {
                        //数据视图
                        dataView: { show: true, readOnly: false },
                        //折线图切换
                        // magicType: { show: true, type: ['line', 'bar'] },
                        //刷新
                        restore: { show: true },
                        //保存图片
                        saveAsImage: { show: true }
                    }
                },
                tooltip: {
                    trigger: 'item',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'letf',
                    data: ['正常上课', '未进班', '已停课', '待续费']
                },
                series: [
                    {
                        type: 'pie',
                        radius: ['50%', '90%'],
                        // center: ['50%', '34%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '20',
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: [
                            { value: data["正常上课学员"], name: '正常上课' },
                            { value: data["未入班学员"], name: '未进班' },
                            { value: data["停课学员"], name: '已停课' },
                            { value: data["待续费学员"], name: '待续费' }
                        ]
                    }
                ]
            };
            myChart3.setOption(option);
        },


        //学员班级分布
        getClassStudent: function () {
            var self = this;
            self.$http.get("/api/report/student_class?campus=" + self.classid)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.banjifenbu(data.data.data);
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
        changeClass: function (val) {
            this.getClassStudent(val);
        },
        banjifenbu: function (data) {

            var self = this;

            var xarr = [];
            var yarr = [];
            for (var i = 0; i < data.length; i++) {
                xarr.push(data[i].name);
                yarr.push(data[i].value);
            }

            myChart4 = this.echarts.init(document.getElementById('banjifenbu'), 'walden');

            var yMax = yarr.length == 0 ? 0 : yarr[0];
            var dataShadow = [];

            for (var k = 1; k < yarr.length; k++) {
                if (yarr[k] > yMax) {
                    yMax = yarr[k];
                }
            }

            for (var i = 0; i < yarr.length; i++) {
                dataShadow.push(yMax);
            }

            var option = {
                grid: {
                    left: '2px',
                    right: '0',
                    bottom: '20px',
                    containLabel: true
                },
                toolbox: {
                    show: true,
                    feature: {
                        //数据视图
                        dataView: { show: true, readOnly: false },
                        //折线图切换
                        // magicType: { show: true, type: ['line', 'bar'] },
                        //刷新
                        restore: { show: true },
                        //保存图片
                        saveAsImage: { show: true }
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                xAxis: {
                    data: xarr,
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLine: {
                        show: false
                    },
                    z: 10
                },
                yAxis: {
                    splitLine: {
                        show: false
                    },
                    axisLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        textStyle: {
                            color: '#999'
                        }
                    }
                },
                dataZoom: [
                    {
                        type: 'inside'
                    }
                ],
                series: [
                    { // For shadow
                        type: 'bar',
                        name: "最热班级人数",
                        itemStyle: {
                            normal: { color: 'rgba(0,0,0,0.05)' }
                        },
                        barGap: '-100%',
                        barCategoryGap: '40%',
                        data: dataShadow,
                        animation: false,
                        z: -1
                    },
                    {
                        type: 'bar',
                        name: "当前班级人数",
                        itemStyle: {
                            normal: {
                                color: new self.echarts.graphic.LinearGradient(
                                    0, 0, 0, 1,
                                    [
                                        { offset: 0, color: '#83bff6' },
                                        { offset: 0.5, color: '#188df0' },
                                        { offset: 1, color: '#188df0' }
                                    ]
                                )
                            },
                            emphasis: {
                                color: new self.echarts.graphic.LinearGradient(
                                    0, 0, 0, 1,
                                    [
                                        { offset: 0, color: '#83bff6' },
                                        { offset: 0.5, color: '#188df0' },
                                        { offset: 1, color: '#188df0' }
                                    ]
                                )
                            }
                        },
                        data: yarr,
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        }
                    }
                ]
            };

            // Enable data zoom when user click bar.
            var zoomSize = 6;
            myChart4.on('click', function (params) {
                myChart4.dispatchAction({
                    type: 'dataZoom',
                    startValue: xarr[Math.max(params.dataIndex - zoomSize / 2, 0)],
                    endValue: xarr[Math.min(params.dataIndex + zoomSize / 2, yarr.length - 1)]
                });
            });


            myChart4.setOption(option);
        },
        //学员课程分布
        getSubjectfenbu: function () {
            var self = this;
            self.$http.get("/api/report/student_subject?campus=" + self.subid)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.subjectfenbu(data.data.data);
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
        changeSub: function (val) {
            this.getSubjectfenbu(val);
        },
        subjectfenbu: function (data) {

            var self = this;

            myChart5 = this.echarts.init(document.getElementById('subjectfenbu'), 'walden');

            var xarr = [];
            var yarr = [];
            var zarr = [];
            for (var i = 0; i < data.length; i++) {
                xarr.push(data[i].name);
                yarr.push(data[i].student_count);
                zarr.push(data[i].class_count);
            }


            var yMax = yarr.length == 0 ? 0 : yarr[0];
            var dataShadow = [];

            for (var k = 1; k < yarr.length; k++) {
                if (yarr[k] > yMax) {
                    yMax = yarr[k];
                }
            }

            for (var i = 0; i < yarr.length; i++) {
                dataShadow.push(yMax);
            }

            var option = {
                grid: {
                    left: '0',
                    right: '0',
                    bottom: '20px',
                    containLabel: true
                },
                toolbox: {
                    show: true,
                    feature: {
                        //数据视图
                        dataView: { show: true, readOnly: false },
                        //折线图切换
                        // magicType: { show: true, type: ['line', 'bar'] },
                        //刷新
                        restore: { show: true },
                        //保存图片
                        saveAsImage: { show: true }
                    }
                },
                tooltip: {
                    trigger: 'axis'
                },
                dataZoom: [
                    {
                        type: 'inside'
                    }
                ],
                xAxis: {
                    data: xarr,
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLine: {
                        show: false
                    },
                    z: 10
                },
                yAxis: {
                    axisLine: {
                        show: false
                    },
                    splitLine: {
                        show: false
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        textStyle: {
                            color: '#999'
                        }
                    }
                },

                series: [
                    { // For shadow
                        type: 'bar',
                        name: "最热课程学员数量",
                        itemStyle: {
                            normal: { color: 'rgba(0,0,0,0.05)' }
                        },
                        barGap: '-100%',
                        barCategoryGap: '40%',
                        data: dataShadow,
                        animation: false
                    },
                    {
                        type: 'bar',
                        name: "当前课程学员数量",
                        itemStyle: {
                            normal: {
                                color: new self.echarts.graphic.LinearGradient(
                                    0, 0, 0, 1,
                                    [
                                        { offset: 0, color: '#83bff6' },
                                        { offset: 0.5, color: '#188df0' },
                                        { offset: 1, color: '#188df0' }
                                    ]
                                )
                            },
                            emphasis: {
                                color: new self.echarts.graphic.LinearGradient(
                                    0, 0, 0, 1,
                                    [
                                        { offset: 0, color: '#83bff6' },
                                        { offset: 0.5, color: '#188df0' },
                                        { offset: 1, color: '#188df0' }
                                    ]
                                )
                            }
                        },
                        data: yarr,
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        }
                    }
                ]
            };

            // Enable data zoom when user click bar.
            var zoomSize = 6;
            myChart5.on('click', function (params) {
                myChart5.dispatchAction({
                    type: 'dataZoom',
                    startValue: xarr[Math.max(params.dataIndex - zoomSize / 2, 0)],
                    endValue: xarr[Math.min(params.dataIndex + zoomSize / 2, yarr.length - 1)]
                });
            });


            myChart5.setOption(option);
        }
    },
    mounted: function () {
        var self = this;
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.xycount = campus_id;
        self.sexid = campus_id;
        self.ztid = campus_id;
        self.classid = campus_id;
        self.subid = campus_id;
        self.$nextTick(function () {
            self.getStudentCountFun();
            self.getSexbl(3);
            self.getClassStudent();
            self.getSubjectfenbu();
            window.onresize = function () {
                myChart1.resize();
                myChart2.resize();
                myChart3.resize();
                myChart4.resize();
                myChart5.resize();
            }
        })

        //权限
        var yx_xytj = this.pdqx(["yx_xytj", "yx"]);
        if (!yx_xytj) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.qushitu {
    width: 100%;
    height: 400px;
    margin-bottom: 20px;
}

.twos {
    margin-bottom: 20px;
}

.tw {
    width: 100%;
    height: 300px;
}

#xueyuanzongshu {
    height: 350px;
}

#sexbl {
    height: 334px;
}

#stausfenbu {
    height: 234px;
}

#banjifenbu {
    height: 350px;
}

#subjectfenbu {
    height: 350px;
}

#qushi {
    height: 350px;
}
</style>