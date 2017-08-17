<template>
    <div class="content_two">
        <div class="c_left">
            <h1>缴费报名<span>(单位：元)</span></h1>
            <div class="tatil">
                {{ zj }}
                <span>共计缴费</span>
            </div>
            <div class="two">
                <div>
                    <div>
                        退费
                        <span>{{ tf }}</span>
                    </div>
                </div>
                <div>
                    <div>
                        实收
                        <span>{{ ss }}</span>
                    </div>
                </div>
                <i></i>
            </div>
            <!--<p class="yx_times">入学时间：2016-06-23</p>-->
        </div>
        <div class="c_right">
            <el-table :data="baomingjiefeiList.recordss" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="orders_id" label="收据单号">
                </el-table-column>
                <el-table-column prop="reg_time" label="交易时间" width="160">
                    <template scope="scope">
                        {{ scope.row.reg_time | yyyy_mm_dd_M_S }}
                    </template>
                </el-table-column>
                <el-table-column prop="payee_account" label="交易账户">
                </el-table-column>
                <el-table-column prop="payee" label="经办人">
                </el-table-column>
                <el-table-column prop="fee_name" label="项目名称" min-width="150">
                    <template scope="scope">
                        <span class="tags">{{ scope.row.fee_name }}</span>
                        <el-tag type="primary" v-if="scope.row.fee_mode == 1">赠送</el-tag>
                        <el-tag type="warning" v-if="scope.row.fee_mode == 2">转结</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="type" label="收/退费">
                    <template scope="scope">
                        <el-tag type="danger" v-if="scope.row.type == 0">退费</el-tag>
                        <el-tag type="success" v-else>收费</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="lx" label="费用类型">
                    <template scope="scope">
                        <span v-if="scope.row.fee_type == 0">报名费</span>
                        <span v-if="scope.row.fee_type == 1">杂费</span>
                    </template>
                </el-table-column>
                <el-table-column prop="actual" label="金额">
                    <template scope="scope">
                        {{ scope.row.actual }}元
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="150">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="$router.push({name:'Finance_detail',params:{id:scope.row.orders_id}})">查看详情</el-button>
                        <el-button v-if="yx_cwgl_tf && scope.row.type != 0" type="warning" size="mini" @click="tuifeiFun(scope.row)">退费</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-if="baomingjiefeiList.count > baomingjiefeiList.recordss.length">
                <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="pageSize" layout="total, prev, pager, next" :total="baomingjiefeiList.count">
                </el-pagination>
            </div>
        </div>
        <!--退费-->
        <tuifei :change="tfchange" :tfdata="tfdata" :sid="$route.params.id"></tuifei>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            pageSize: 10,
            current_page: 1,
            loading: false,
            tfchange: "",
            tfdata: {},
            baomingjiefeiList: {
                recordss: []
            },
            zj: 0,
            tf: 0,
            ss: 0,

            yx_cwgl_tf: false
        }
    },
    methods: {
        //退费
        tuifeiFun: function (obj) {
            this.tfchange = new Date().getTime();
            this.tfdata = obj;
        },
        handleCurrentChange: function (val) {
            var self = this;
            self.current_page = val;
            self.getHomeworkList({
                limit: self.pageSize,
                page: self.current_page
            })
        },
        getHomeworkList: function (option) {
            var self = this;
            self.loading = true;
            self.$http.get("/api/student/" + self.$route.params.id + "/recordss?limit=" + option.limit + "&page=" + option.page)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.baomingjiefeiList = data.data.data;
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
        getZongji: function (option) {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id + "/recordss")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        var all = 0;
                        var tf = 0;
                        var ss = 0;
                        for (var i = 0; i < data.data.data.recordss.length; i++) {

                            if (data.data.data.recordss[i].type == "0") {
                                tf += Number(data.data.data.recordss[i].actual);
                                ss -= Number(data.data.data.recordss[i].actual);
                            } else {
                                ss += Number(data.data.data.recordss[i].actual);
                                all += Number(data.data.data.recordss[i].actual);
                            }
                        }
                        self.zj = all.toFixed(2);
                        self.tf = tf.toFixed(2);
                        self.ss = ss.toFixed(2);
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
        self.$store.commit('setyx_xy_xq_meau_id', 3);
        self.getHomeworkList({
            limit: self.pageSize,
            page: self.current_page
        });
        self.getZongji();

        //权限设置
        this.yx_cwgl_tf = this.pdqx(["yx_cwgl_tf", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.c_left {
    height: 270px;
}

.tags {
    display: block;
    float: left;
    margin-right: 5px;
}

.c_right {
    padding: 0;
    background-color: transparent;
}

.tatil {
    padding: 30px 0;
    font-size: 30px;
    color: @blue;
    text-align: center;
    span {
        display: block;
        font-weight: normal;
        font-size: @p;
        color: @c1;
        padding-top: 5px;
    }
}

.two {
    overflow: hidden;
    position: relative;
    >i {
        display: block;
        width: 1px;
        height: 60%;
        background-color: #ddd;
        position: absolute;
        top: 20%;
        left: 50%;
    }
    >div {
        width: 50%;
        box-sizing: border-box;
        float: left;
        >div {
            display: block;
            font-size: @p;
            color: @c1;
            float: right;
            padding-left: 10px;
            >span {
                color: @color;
                display: block;
                word-break: break-all;
                /*支持IE，chrome，FF不支持*/
                word-wrap: break-word;
                /*支持IE，chrome，FF*/
            }
        }
        &:first-child {
            >div {
                display: block;
                font-size: @p;
                color: @c1;
                float: left;
                padding-left: 0;
                padding-right: 10px;
                >span {
                    color: @error;
                }
            }
        }
    }
}

.yx_times {
    font-size: 14px;
    color: @c3;
    padding-top: 30px;
    text-align: center;
    padding-bottom: 10px;
}
</style>