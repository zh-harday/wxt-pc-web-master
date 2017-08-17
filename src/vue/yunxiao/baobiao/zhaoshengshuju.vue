<template>
    <div>
        <div v-if="yx_bb_zssj">
            <!--实时招生数据-->
            <div class="bb_row1">
                <el-card class="box-card zsdata">
                    <h1 class="data_title">
                        <span>实时招生数据</span>
                        <el-select v-model="zs_v" placeholder="请选择校区" size="small" @change="changeData" v-if="myMessage.campus_id==1">
                            <el-option label="所有校区" value=""></el-option>
                            <el-option v-for="item in campus" :label="item.name" :value="item.id">
                            </el-option>
                        </el-select>
                    </h1>
                    <ul>
                        <li style="width:100%">
                            <h2>本月</h2>
                            <el-row :gutter="10">
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_newBM.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增报名班次</p>
                                            <span>{{ shishidata.month.student }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_newYX.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增意向学员</p>
                                            <span>{{ shishidata.month.intetion }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_newCW.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>收入流水汇总</p>
                                            <span>{{ shishidata.month.orders }}元</span>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                        </li>
                        <li>
                            <h2 class="benzhou">本周</h2>
                            <el-row :gutter="10">
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_yxxy.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增报名班次</p>
                                            <span>{{ shishidata.week.student }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_yxxy1.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增意向学员</p>
                                            <span>{{ shishidata.week.intetion }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_yxxy2.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>收入流水汇总</p>
                                            <span>{{ shishidata.week.orders }}元</span>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                        </li>
                        <li>
                            <h2 class="zuotian">昨天</h2>
                            <el-row :gutter="10">
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_yx1.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增报名班次</p>
                                            <span>{{ shishidata.yesterday.student }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/yx_data_cw1.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>新增意向学员</p>
                                            <span>{{ shishidata.yesterday.intetion }}</span>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col :span="8">
                                    <div class="ulli">
                                        <div class="zsdata_left">
                                            <img src="../../../img/shouruliushuiicon.png" alt="">
                                        </div>
                                        <div class="zsdata_right">
                                            <p>收入流水汇总</p>
                                            <span>{{ shishidata.yesterday.orders }}元</span>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                        </li>
                    </ul>
                </el-card>
                <el-card class="box-card yxxymx">
                    <h1 class="data_title">
                        <span>意向学员新增明细</span>
                        <el-select v-model="yx_v" placeholder="请选择校区" size="small" @change="chengenewyxFun" v-if="myMessage.campus_id==1">
                            <el-option label="所有校区" value=""></el-option>
                            <el-option v-for="item in campus" :label="item.name" :value="item.id">
                            </el-option>
                        </el-select>
                        <s class="zs">手动录入</s>
                        <s>微信提交</s>
                    </h1>
                    <div id="yixiangmx"></div>
                    <div class="bb_bottom">
                        <a href="javascript:;" :class="{active:weekids==0}" @click="clickweek(0)">本周</a>
                        <a href="javascript:;" :class="{active:weekids<0}" @click="clickweek(-1)">上一周</a>
                        <a href="javascript:;" :class="{active:weekids>0}" @click="clickweek(1)">下一周</a>
                    </div>
                </el-card>
            </div>
            <!--意向学员增长趋势-->
            <el-card class="box-card qushitu">
                <h1 class="data_title">
                    <span>意向学员总数增长趋势</span>
                    <el-select v-model="yxzs_v" placeholder="请选择校区" size="small" @change="changeAddcount" v-if="myMessage.campus_id==1">
                        <el-option label="所有校区" value=""></el-option>
                        <el-option v-for="item in campus" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </h1>
                <div id="qushi"></div>
            </el-card>
            <!--年度新增汇总-->
            <el-card class="box-card qushitu">
                <h1 class="data_title">
                    <span>年度新增报班汇总</span>
                    <el-select v-model="baobanhz" placeholder="请选择" size="small" @change="changeAddnewFun" v-if="myMessage.campus_id==1">
                        <el-option label="所有校区" value=""></el-option>
                        <el-option v-for="item in campus" :label="item.name" :value="item.id">
                        </el-option>
                    </el-select>
                </h1>
                <div id="banbaohz"></div>
            </el-card>
        </div>
        <div v-if="!yx_bb_zssj" class="wuquanxian">无权限</div>
    </div>
</template>
<script>
var myChart1, myChart2, myChart3;
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
            //实时数据
            zs_v: "",
            shishidata: {
                month: {},
                week: {},
                yesterday: {},
                today: {}
            },
            // 意向
            yx_v: "",
            weekid: 0,
            weekids: 0,
            //意向学员总数
            yxzs_v: "",
            year: new Date().getFullYear(),

            //新增报班汇总
            baobanhz: "",

            yx_bb_zssj: false
        }
    },
    methods: {
        //获取实时招生数据统计
        getShishiData: function () {
            var self = this;
            self.$http.get("/api/report/recruit_hot?campus=" + self.zs_v)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.shishidata = data.data.data;
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
        //实时数据选择校区
        changeData: function () {
            this.getShishiData();
        },
        //获取意向学员数据
        getYXstudent: function () {
            var self = this;
            self.$http.get("/api/report/intetion_week/" + self.weekid + "?campus=" + self.yx_v)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.inittable(data.data.data)
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
        //意向week点击
        clickweek: function (id) {
            if (id == 0) {
                if (this.weekid == 0) {
                    this.weekids = 0;
                    return
                } else {
                    this.weekid = 0;
                    this.weekids = 0;
                }
            }
            if (id < 0) {
                this.weekid--;
                this.weekids = -1;
            }
            if (id > 0) {
                this.weekid++;
                this.weekids = 1;
            }
            this.getYXstudent();
        },
        //新增意向选择校区
        chengenewyxFun: function () {
            this.getYXstudent();
        },
        //意向学员总数新增
        getYxcount: function () {
            var self = this;
            self.$http.get("/api/report/intetion_year/" + self.year + "?campus=" + self.yxzs_v)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.addNewStudent(data.data.data);
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
        //选择校区
        changeAddcount: function () {
            this.getYxcount();
        },

        //年度新增把baoban
        getBaoban: function () {
            var self = this;
            self.$http.get("/api/report/baoban_year/" + self.year + "?campus=" + self.baobanhz)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.addbaoban(data.data.data);
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
        //选择学校
        changeAddnewFun: function () {
            this.getBaoban();
        },


        //意向学员新增明细
        inittable: function (data) {
            myChart1 = this.echarts.init(document.getElementById('yixiangmx'), 'walden');
            var luru = {
                k: [],
                v: []
            };
            var wx = {
                k: [],
                v: []
            }
            for (var k in data.intetion) {
                var key = "周" + k.substr(2, 1);
                luru.k.push(key);
                luru.v.push(data.intetion[k]);
            }
            for (var k in data.intetion_wx) {
                var key = "周" + k.substr(2, 1);
                wx.k.push(key);
                wx.v.push(data.intetion_wx[k]);
            }
            var option = {
                title: {
                    text: data.week,
                    textAlign: "left",
                    textStyle: {
                        color: "#a2a2a2",
                        fontWeight: "none",
                        fontSize: "14px"
                    }
                },
                //提示框组件
                tooltip: {
                    trigger: 'axis'
                },
                //确定坐标系的位置
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
                // calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        triggerEvent: true,
                        data: luru.k
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '微信提交',
                        type: 'bar',
                        data: wx.v,
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        markLine: {
                            data: [
                                { type: 'average', name: '平均值' }
                            ]
                        }
                    },
                    {
                        name: '手动录入',
                        type: 'bar',
                        data: luru.v,
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        markLine: {
                            data: [
                                { type: 'average', name: '平均值' }
                            ]
                        }
                    }
                ]
            };
            myChart1.setOption(option);
        },
        //意向学员总数增长趋势
        addNewStudent: function (data) {
            myChart2 = this.echarts.init(document.getElementById('qushi'), 'walden');
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
                        name: '意向学员总数',
                        type: 'line',
                        data: yarr,
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        markLine: {
                            data: [
                                { type: 'average', name: '平均值' }
                            ]
                        }
                    }
                ]
            }
            myChart2.setOption(option);

        },
        //年度新增报班汇总
        addbaoban: function (data) {
            myChart3 = this.echarts.init(document.getElementById('banbaohz'), 'walden');
            var xarr = [];
            var yarr = [];
            for (var k in data) {
                xarr.push(k);
                yarr.push(data[k]);
            }
            var option = {
                //提示框组件
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
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
                    right: '50px',
                    bottom: '20px',
                    containLabel: true
                },
                // calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            alignWithLabel: true
                        },
                        data: xarr
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '报班学员增长',
                        type: 'bar',
                        data: yarr,
                        barWidth: '50%',
                        label: {
                            normal: {
                                show: true,
                                position: "top"
                            }
                        },
                        markLine: {
                            data: [
                                { type: 'average', name: '平均值' }
                            ]
                        }
                    }
                ]
            };
            myChart3.setOption(option);
        }
    },
    mounted: function () {
        var self = this;
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.zs_v = campus_id;
        self.yx_v = campus_id;
        self.yxzs_v = campus_id;
        self.baobanhz = campus_id;

        //权限
        self.yx_bb_zssj = this.pdqx(["yx_bb_zssj", "yx"]);

        self.$nextTick(function () {
            if (self.yx_bb_zssj){
                self.getShishiData();
                self.getYXstudent();
                self.getYxcount();
                self.getBaoban();
                window.onresize = function () {
                    console.log(myChart1)
                    myChart1.resize();
                    myChart2.resize();
                    myChart3.resize();
                }
            }
        })


        // if (!yx_bb_zssj) {
        //     this.$router.push({ name: 'worktody' })
        // }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.bb_row1 {
    display: flex;
    margin-bottom: 20px;
}

.bb_bottom {
    height: 20px;
    width: 100%;
    left: 0;
    bottom: 0;
    text-align: right;
    a {
        margin-left: 10px;
        color: @color;
        text-decoration: underline;
        font-size: 14px;
        &.active {
            color: @c3;
        }
    }
}

.zsdata {
    width: 440px;
    margin-right: 20px;
    li {
        border-bottom: 1px dashed #eee;
        overflow: hidden;
        padding-bottom: 10px;
        padding-top: 10px;
        &:last-child {
            border: none;
        }
        h2 {
            height: 25px;
            font-size: @h3;
            color: @c1;
            font-weight: normal;
            clear: both;
            line-height: 25px;
            position: relative;
            padding-left: 10px;
            margin-bottom: 5px;
            &.benzhou {
                &:after {
                    background-color: @blue;
                }
            }
            &.zuotian {
                &:after {
                    background-color: @wan;
                }
            }
            &:after {
                content: "";
                display: block;
                position: absolute;
                top: 5px;
                left: 0;
                height: 16px;
                width: 2px;
                background-color: @color;
            }
        }
    }
    .ulli {
        display: flex;
        .zsdata_left {
            width: 28px;
            margin-right: 5px;
            img {
                display: block;
                width: 28px;
            }
        }
        .zsdata_right {
            flex: 1;
            p {
                font-size: 14px;
                color: @c3;
            }
            span {
                display: block;
                font-size: 16px;
                color: @c1;
            }
        }
    }
}

.yxxymx {
    flex: 1;
}

.qushitu {
    width: 100%;
    height: 400px;
    margin-bottom: 20px;
    position: relative;
}

#yixiangmx {
    width: 100%;
    height: 290px;
}

#banbaohz {
    height: 350px;
}

#qushi {
    height: 350px;
}

.wuquanxian {
    width: 100%;
    height: 500px;
    text-align: center;
    line-height: 500px;
    font-size: 14px;
    color: @c3;
}
</style>