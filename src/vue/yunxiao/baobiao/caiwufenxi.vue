<template>
    <div>
        <el-card class="box-card qushitu">
            <h1 class="data_title">
                    <span>财务数据情况</span>
                    <el-select v-model="xycount" placeholder="请选择校区" size="small" @change="changeCaiwu" v-if="myMessage.campus_id == 1">
                        <el-option label="所有校区" value=""></el-option>
                        <el-option v-for="item in campus" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </h1>
            <div id="caiwu"></div>
        </el-card>
    </div>
</template>
<script>
var myChart1;
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
            xycount: "",
            year: new Date().getFullYear()
        }
    },
    methods: {
        changeCaiwu: function () {
            this.getCaiwuList();
        },
        getCaiwuList: function () {
            var self = this;
            self.$http.get("/api/report/inancial_year/" + self.year + "?campus=" + self.xycount)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.setTable(data.data.data);
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
        setTable: function (data) {
            myChart1 = this.echarts.init(document.getElementById('caiwu'), 'walden');

            var xarr = [];
            var yarr = [];
            var zarr = [];
            for (var k in data["收入"]) {
                xarr.push(k);
                yarr.push(data["收入"][k]);
            }

            for (var k in data["支出"]) {
                zarr.push(data["支出"][k] * -1);
            }

            var option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data: ['支出', '收入']
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
                grid: {
                    left: '0',
                    right: '0',
                    bottom: '0',
                    containLabel: true
                },
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            show: false
                        },
                        data: xarr
                    }
                ],
                series: [

                    {
                        name: '收入',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        data: yarr
                    },
                    {
                        name: '支出',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: true,
                                position: "bottom"
                            }
                        },
                        itemStyle: {
                            normal: {
                                color: "#ff4949"
                            }
                        },
                        data: zarr
                    }
                ]
            }
            myChart1.setOption(option);
        }
    },
    mounted: function () {
        var self = this;
        self.xycount = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$nextTick(function () {
            self.getCaiwuList();
            window.onresize = function () {
                myChart1.resize();
            }
        })

        //权限
        var yx_cwfx = this.pdqx(["yx_cwfx", "yx"]);
        if (!yx_cwfx ) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>
<style lang="less" scoped>
.qushitu {
    width: 100%;
    height: 700px;
}

#caiwu {
    width: 100%;
    height: 600px;
}
</style>