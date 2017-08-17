<template>
    <div>
        <div class="subject_right_top">
            <span>
                                <img :src="selfMessage.face" alt="">
                            </span>
            <div>
                <h1>{{ selfMessage.name }}</h1>
                <p>
                    <span>学号：{{ selfMessage.number }}</span>
                    <span>电话：{{ selfMessage.phone }}</span>
                </p>
                <span @click="$router.go(-1)"><i class="el-icon-arrow-left"></i>返回上一级</span>
            </div>
        </div>
        <div class="cw_detail">
            <div class="cw_left">
                <h1>订单详情</h1>
                <p>订单编号：#{{ caiwuDetail.info.id }}</p>
                <p>经办人：{{ caiwuDetail.info.payee }}</p>
                <p>收费账户：{{ caiwuDetail.info.payee_account }}</p>
                <p>交易时间：{{ caiwuDetail.info.reg_time | yyyy_mm_dd_M_S }}</p>
                <p>备注：{{ caiwuDetail.info.remark }}</p>
            </div>
            <div class="cw_right">
                <div>
                    <div class="r_title">
                        <el-button type="primary" size="small" class="dayinshouju" @click="showDayinfun">打印收据</el-button>
                        <div class="radio_copy" @click="gongzhang = !gongzhang">
                            <s :class="{active:gongzhang}"></s>
                            <span>加盖公章</span>
                        </div>
                        <div class="radio_copy" @click="bmxuzhi = !bmxuzhi">
                            <s :class="{active:bmxuzhi}"></s>
                            <span>打印报名须知</span>
                        </div>
                    </div>
    
                    <el-table :data="caiwuDetail.recordss" style="width: 100%">
                        <el-table-column prop="fee_type" label="收费类型">
                            <template scope="scope">
                                <span v-if="scope.row.fee_type == 0">报名费</span>
                                <span v-if="scope.row.fee_type == 1">杂费</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="fee_name" label="收费项目">
                        </el-table-column>
                        <el-table-column prop="price" label="单价">
                            <template scope="scope">
                                {{ scope.row.price }}元
                            </template>
                        </el-table-column>
                        <el-table-column prop="quantity" label="数量">
                            <template scope="scope">
                                {{ scope.row.quantity }}{{ scope.row.unit }}
                            </template>
                        </el-table-column>
                        <el-table-column label="原价">
                            <template scope="scope">
                                {{ parseFloat(scope.row.quantity * scope.row.price).toFixed(2) }}元
                            </template>
                        </el-table-column>
                        <el-table-column prop="discount" label="折扣">
                            <template scope="scope">
                                {{ scope.row.discount }}%
                            </template>
                        </el-table-column>
                        <el-table-column prop="preferential" label="优惠">
                            <template scope="scope">
                                {{ scope.row.preferential }}元
                            </template>
                        </el-table-column>
                        <el-table-column prop="actual" label="现价">
                            <template scope="scope">
                                {{ scope.row.actual }}元
                            </template>
                        </el-table-column>
                    </el-table>
                    <p class="tongjis">应收合计<span>{{ caiwuDetail.yshj }}</span>元，优惠合计<span>{{ caiwuDetail.yhhj }}</span>元，实收合计<span class="heji">{{ caiwuDetail.sshj }}</span>元</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            gongzhang: true,
            bmxuzhi: false,
            showDayin: false,
            caiwuDetail: {
                info: {},
                recordss: []
            },
            selfMessage: {}
        }
    },
    methods: {
        //获取财务列表
        getFinanceDetail: function () {
            var self = this;
            self.$http.get("/api/finance/records_view/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        data.data.data.yshj = "";
                        data.data.data.yhhj = "";
                        data.data.data.sshj = "";
                        var sh = 0;
                        var yh = 0;
                        var ys = 0;
                        for (var i = 0; i < data.data.data.recordss.length; i++) {
                            if (data.data.data.recordss[i].type == "0") {
                                data.data.data.recordss[i].actual = data.data.data.recordss[i].actual * -1;
                            }
                            sh += Number(data.data.data.recordss[i].actual);
                            ys += (data.data.data.recordss[i].quantity * data.data.data.recordss[i].price);
                            yh += (ys * (100 - data.data.data.recordss[i].discount) / 100 + Number(data.data.data.recordss[i].preferential))
                        }
                        data.data.data.sshj = sh.toFixed(2);
                        data.data.data.yshj = ys.toFixed(2);
                        data.data.data.yhhj = yh.toFixed(2);
                        self.caiwuDetail = data.data.data;
                        self.getStudentDetail(data.data.data.info.student_id);
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
        //获取学生信息
        getStudentDetail: function (id) {
            var self = this;
            self.$http.get("/api/student/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        data.data.data.student[0].face = data.data.data.student[0].face?"http://wx.eduwxt.com"+data.data.data.student[0].face:"http://img.eduwxt.com/assets/images/users/avatar-11.jpg";
                        self.selfMessage = data.data.data.student[0];
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
        //显示打印
        showDayinfun: function () {
            var url = window.location.href.split("/#/");
            var newUrl = url[0] + "/#/Print/" + this.$route.params.id + "?gz=" + this.gongzhang + "&xz=" + this.bmxuzhi;
            window.open(newUrl)
        }
    },
    created: function () {
        this.getFinanceDetail();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.cw_detail {
    width: 100%;
    overflow: hidden;
    position: relative;
    .cw_left {
        width: 270px;
        background-color: #fff;
        position: absolute;
        left: 0;
        top: 0;
        padding: 10px;
        box-sizing: border-box;
        h1 {
            font-size: @h3;
            color: @c1;
            margin-bottom: 10px;
        }
        p {
            font-size: 14px;
            color: @c2;
            padding-bottom: 10px;
        }
    }
    .cw_right {
        width: 100%;
        overflow: hidden;
        box-sizing: border-box;
        padding-left: 290px;
        >div {
            width: 100%;
            background-color: #fff;
            padding: 10px;
            box-sizing: border-box;
            .r_title {
                margin-bottom: 20px;
                overflow: hidden;
                .dayinshouju {
                    float: left;
                    margin-right: 20px;
                }
            }
        }
    }
    .radio_copy {
        float: left;
        overflow: hidden;
        margin-right: 20px;
        cursor: pointer;
        padding: 5px 0;
        s {
            display: block;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid @color;
            position: relative;
            background-color: #fff;
            float: left;
            margin-right: 5px;
            &.active {
                background-color: @color;
                &:after {
                    content: "";
                    display: block;
                    width: 40%;
                    height: 40%;
                    background-color: #fff;
                    position: absolute;
                    border-radius: 50%;
                    top: 30%;
                    left: 30%;
                }
            }
        }
        span {
            display: block;
            float: left;
        }
    }
    .tongjis {
        font-size: 14px;
        color: @c2;
        text-align: right;
        padding: 10px 0;
        span {
            font-size: 18px;
            color: @c1;
            &.heji {
                color: @color;
            }
        }
    }
}

.print {
    width: 500px;
    height: 1000px;
    background-color: red;
    color: #fff;
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    margin: 0 auto;
    z-index: 1000;
}
</style>