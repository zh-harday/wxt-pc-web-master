<template>
    <div>
        <div class="content_two">
            <div class="c_left">
                <h1>排课日历</h1>
                <div id="month">
                    <div class="title">
                        <div class="date">
                            <i class="el-icon-d-arrow-left i1" @click="backyear"></i>
                            <i class="el-icon-arrow-left i2" @click="backmounth"></i>
                            <span>{{date.year}}年{{date.month+1}}月</span>
                            <i class="el-icon-arrow-right i3" @click="nextmounth"></i>
                            <i class="el-icon-d-arrow-right i4" @click="nextyear"></i>
                        </div>
                        <div class="week_title">
                            <span>一</span>
                            <span>二</span>
                            <span>三</span>
                            <span>四</span>
                            <span>五</span>
                            <span>六</span>
                            <span>七</span>
                        </div>
                        <div>
                            <span v-for="(list,index) in monthDate" @click="clickDate(index,list.day)">
                                <span :class="{active:!list.empt,click:clickN==index}">
                                    {{list.day}}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="c_right">
                <h1 class="c_right_h1">
                    排课列表
                    <span :class="{active:paikeData.type == 'end'}" @click="saixuanFun('end')">已上</span>
                    <span :class="{active:paikeData.type == 'wait'}" @click="saixuanFun('wait')">未上</span>
                    <span :class="{active:paikeData.type == 'all'}" @click="saixuanFun('all')">全部</span>
                </h1>
                <el-table :data="paikeList.curriculum" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                    <el-table-column prop="subject" label="课程名称">
                    </el-table-column>
                    <el-table-column prop="class_name" label="上课班级">
                    </el-table-column>
                    <el-table-column prop="start_time" label="上课时间" min-width="200">
                        <template scope="scope">
                            {{ scope.row.start_time | yyyy_mm_dd_M_S_week }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="room_name" label="授课教室">
                    </el-table-column>
                    <el-table-column prop="status" label="课程状态">
                        <template scope="scope">
                            <el-tag type="danger" v-if="scope.row.status == 0 && scope.row.isYanwu == 0">已延误</el-tag>
                            <el-tag type="primary" v-if="scope.row.status == 0 && scope.row.isYanwu == 1">即将开课</el-tag>
                            <el-tag type="warning" v-if="scope.row.status == 0 && scope.row.isYanwu == 2">待上课</el-tag>
                            <el-tag type="success" v-if="scope.row.status == 1">上课中</el-tag>
                            <el-tag type="gray" v-if="scope.row.status == 2">已下课</el-tag>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="fenye" v-show="paikeList.count > paikeList.curriculum.length">
                    <el-pagination @current-change="handleCurrentChange" :current-page="paikeData.page" :page-size="paikeData.limit" layout="total, prev, pager, next" :total="paikeList.count">
                    </el-pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        paikeCount: function () {
            return this.$store.state.paikeCount;
        }
    },
    watch: {
        paikeCount: function () {
            this.paikeData.page = 1;
            this.getPaike();
        }
    },
    data: function () {
        return {
            kaoqinid: "",
            classDetail: window.sessionStorage.getItem("classDetail" + this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("classDetail" + this.$route.params.id)) : {
                subject: []
            },
            subjectDetail: {},
            classroomList: [],
            loading: false,
            //调课
            paikeData: {
                limit: 10,
                page: 1,
                type: "all"
            },
           
            //课程调整弹框
            monthDate: [],
            date: {
                year: new Date().getFullYear(),
                month: new Date().getMonth()
            },
            clickN: null,

            paikeList: {
                curriculum: []
            },

            pickerOptions0: {
                disabledDate: function (time) {
                    return time.getTime() < +new Date() - (1000 * 60 * 60 * 24);
                }
            },
            changeDate: ""
        }
    },
    methods: {
        //检查排课
        checkPaikeFun: function (arr) {
            var self = this;
            var date = self.date.year + "-" + (self.date.month + 1);
            self.$http.get("/api/staff/month_curriculum/" + self.$route.params.id + "/" + date )
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
        //筛选
        saixuanFun:function(val){
            var self = this;
            self.paikeData.page = 1;
            self.paikeData.type = val;
            self.changeDate = "";
            self.getPaike();
        },
        //点击页码
        handleCurrentChange: function (val) {
            this.paikeData.page = val;
            this.getPaike();
        },
        //获取课程列表
        getPaike: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/staff/curriculum/" + self.$route.params.id + "?&day=" + self.changeDate + "&type=" + self.paikeData.type + "&page=" + self.paikeData.page + "&limit=" + self.paikeData.limit)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.curriculum.length; i++) {
                            var ltime = new Date(data.data.data.curriculum[i].start_time * 1000);
                            var lyear = ltime.getFullYear();
                            var lmonth = ltime.getMonth();
                            var lday = ltime.getDate();
                            if (ltime < (new Date().getTime() - 1000 * 60 * 60 * new Date().getHours())) {
                                data.data.data.curriculum[i].isToday = 0;
                            } else if (lyear == new Date().getFullYear() && lmonth == new Date().getMonth() && lday == new Date().getDate()) {
                                data.data.data.curriculum[i].isToday = 1;
                            } else {
                                data.data.data.curriculum[i].isToday = 2;
                            }

                            if (ltime < (new Date().getTime() - 1000 * 60 * 30)) {
                                data.data.data.curriculum[i].isYanwu = 0;
                            } else if (lyear == new Date().getFullYear() && lmonth == new Date().getMonth() && lday == new Date().getDate()) {
                                data.data.data.curriculum[i].isYanwu = 1;
                            } else {
                                data.data.data.curriculum[i].isYanwu = 2;
                            }

                        }
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
       
        //初始化时间 09:30:00
        getTimes: function (val) {
            var n = new Date(val * 1000);
            var year = n.getFullYear();
            var month = n.getMonth();
            var day = n.getDate();
            var hours = n.getHours();
            var min = n.getMinutes();
            return new Date(year, month, day, hours, min);
        },
        //格式化时间
        getDatea: function (val) {//2017-01-11
            if (!val) return "";
            var date = new Date(val * 1000);
            return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
        },
        
      
        //上一年
        backyear: function () {
            var self = this;
            self.date.year--;
            self.creatTable(+new Date(self.date.year, self.date.month, 1));
            this.paikeData.page = 1;
            this.changeDate = "";
            this.clickN = null;
            self.getPaike();
        },
        //下一年
        nextyear: function () {
            var self = this;
            self.date.year++;
            self.creatTable(+new Date(self.date.year, self.date.month, 1));
            this.paikeData.page = 1;
            this.changeDate = "";
            this.clickN = null;
            self.getPaike();
        },
        //上一个月
        backmounth: function () {
            var self = this;
            self.date.month--;
            if (self.date.month < 0) {
                self.date.month = 11;
                self.date.year--;
            }
            self.creatTable(+new Date(self.date.year, self.date.month, 1));
            this.paikeData.page = 1;
            this.changeDate = "";
            this.clickN = null;
            self.getPaike();
        },
        //下一个月
        nextmounth: function () {
            var self = this;
            self.date.month++;
            if (self.date.month > 11) {
                self.date.month = 0;
                self.date.year++;
            }
            self.creatTable(+new Date(self.date.year, self.date.month, 1));
            this.paikeData.page = 1;
            this.changeDate = "";
            this.clickN = null;
            self.getPaike();
        },
        //点击日期
        clickDate: function (index, n) {
            if (!n) {
                return;
            }
            this.clickN = index;
            this.paikeData.page = 1;
            this.changeDate = this.date.year + "-" + (this.date.month + 1) + "-" + n;
            this.getPaike();
        },
        //创建排课日历
        creatTable: function (datas) {

            datas = datas ? datas : new Date();
            var date = new Date(datas)

            var year = date.getFullYear();
            var month = date.getMonth();
            var startDay = new Date(year, month, 1); //取当年当月中的第一天

            month++;
            if (month > 11) {
                year++;
                month = 0;
            }
            var new_date = new Date(year, month, 1); //取下月中的第一天

            var endDay = (new Date(new_date.getTime() - 1000 * 60 * 60 * 24)).getDate();//获取当月最后一天日期

            var week = startDay.getDay();//获取 当月第一天的星期

            week = week == 0 ? 7 : week;


            var x = 1;//日历
            var arr = [];//存放当月每天，及每天的课程

            var len = 35;//根据当月起始星期 以及天数判断多少容器
            if (week > 5 && endDay > 30) {
                len = 42;
            } else if (week > 6 && endDay >= 30) {
                len = 42;
            } else {
                len = 35;
            }

            for (var i = 1; i <= len; i++) {//循环容器
                if (i < week) {//如果小于起始星期 插入空数据
                    arr.push({
                        item: {},
                        empt: true
                    });
                } else if (x > endDay) {//如果大于起始星期 插入空数据
                    arr.push({
                        item: {},
                        empt: true
                    });
                } else {
                    var obj = {};//存放每天日期及课程
                    obj.day = x;
                    month = month < 10 ? ("0" + parseInt(month)) : parseInt(month);
                    x = x < 10 ? ("0" + parseInt(x)) : parseInt(x);
                    obj.date = month + "-" + x;
                    obj.item = {},
                        obj.empt = true
                    arr.push(obj);
                    x++;
                }

            }
            this.checkPaikeFun(arr);
        }
    },
    created: function () {
        this.creatTable(new Date());
        this.getPaike();

    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less"; //我的课表 班级课表公共样式 月
#month {
    width: 250px;
    height: auto;
    margin: 0 auto;
    margin-bottom: 10px;
    padding-top: 20px;
    .title {
        width: 100%;
        background-color: #fff;
        position: relative;
        z-index: 1;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 10px;
        div {
            overflow: hidden;
            &.date {
                width: 100%;
                background-color: #fff;
                position: relative;
                box-sizing: border-box;
                padding: 0 5px;
                margin-bottom: 5px;
                >span {
                    display: block;
                    float: left;
                    width: 100px;
                    height: 30px;
                    text-align: center;
                    font-size: 16px;
                    color: @c2;
                    line-height: 30px;
                }
                >i {
                    display: block;
                    width: 20px;
                    height: 30px;
                    line-height: 30px;
                    text-align: center;
                    color: #99a9bf;
                    font-size: 14px;
                    float: left;
                    cursor: pointer;
                    font-weight: normal;
                    &.i1 {
                        margin-right: 10px;
                    }
                    &.i2 {
                        margin-right: 20px;
                    }
                    &.i3 {
                        margin-left: 20px;
                        margin-right: 10px;
                    }
                    &.i4 {}
                }
            }
            &.week_title {
                color: @c3;
                >span {
                    border-top: none;
                }
            }
            >span {
                display: block;
                height: 35px;
                width: 35px;
                line-height: 35px;
                text-align: center;
                font-size: 14px;
                float: left;
                position: relative;
                >span {
                    display: block;
                    width: 35px;
                    height: 35px;
                    line-height: 35px;
                    position: absolute;
                    top: 0;
                    left: 0;
                    color: @c1;
                    cursor: pointer;
                    &.active {
                        color: @color;
                        font-weight: bold;
                        &:after {
                            content: "";
                            display: block;
                            width: 4px;
                            height: 4px;
                            border-radius: 100%;
                            background-color: @color;
                            position: absolute;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            margin: 0 auto;
                        }
                    }
                    &.click {
                        background-color: @color;
                        color: #fff;
                        font-weight: normal;
                        &:after {
                            content: "";
                            display: none;
                        }
                    }
                }
            }
        }
    }
}

.tk_tishi {
    font-size: 14px;
    color: @error;
}

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
        margin-left: 5px;
        cursor: pointer;
        &.active{
            color: @color;
        }
    }
}
</style>