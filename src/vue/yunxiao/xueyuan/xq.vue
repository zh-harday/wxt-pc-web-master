<template>
    <div class="yx_class">
        <div class="left">
            <h1>学员详情
                        <article>在此查看并管理正式学员</article>
                    </h1>
            <router-link :to="{ name:'student_dtl_m',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==1}">学员资料</router-link>
            <router-link :to="{ name:'ServiceTracking',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==2}">服务跟踪</router-link>
            <router-link :to="{ name:'Registration',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==3}">缴费记录</router-link>
            <router-link :to="{ name:'RegisterClass',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==4}">已报班级</router-link>
            <router-link :to="{ name:'student_dtl_Comment',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==5}">点评记录</router-link>
            <router-link :to="{ name:'AttendanceRecord',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==6}">考勤记录</router-link>
            <router-link :to="{ name:'HomeworkReview',params:{id:$route.params.id}}" :class="{active:yx_xy_xq_meau_id==7}">作业记录</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                            <img :src="selfMessage.student[0].face">
                        </span>
                <div>
                    <h1>{{ selfMessage.student[0].name }}</h1>
                    <p>
                        <span>学号：{{ selfMessage.student[0].number }}</span>
                        <span>电话：{{ selfMessage.student[0].phone }}</span>
                    </p>
                    <span @click="$router.go(-1)"><i class="el-icon-arrow-left"></i>返回上一级</span>
                    <div class="btn_box" v-if="yx_cwgl_jf">
                        <el-button type="primary" size="mini" @click="$router.push({name:'newRegistration',params:{id:$route.params.id}})">报名缴费</el-button>
                        <el-button type="primary" size="mini" @click="$router.push({name:'renewRegistration',params:{id:$route.params.id}})">续报学费</el-button>
                        <!--<el-button type="primary" size="mini" @click="tuifeishow = true">办理退费</el-button>-->
                    </div>
                </div>
            </div>
            <router-view></router-view>
        </div>
        <!--退费-->
        <!--<el-dialog title="办理退费" v-model="tuifeishow" class="paike_box">
                    <p class="fengexian"></p>
                    <el-form ref="form" :model="tuifeiform" label-width="70px">
                        <el-form-item label="退费类型">
                            <el-radio-group v-model="tuifeiform.resource">
                                <el-radio label="学费"></el-radio>
                                <el-radio label="杂费"></el-radio>
                            </el-radio-group>
                        </el-form-item>
                        <div class="xuefei" v-if="tuifeiform.resource == '学费'">
                            <el-table :data="tableData" style="width: 100%">
                                <el-table-column prop="name" label="已报班级">
                                </el-table-column>
                                <el-table-column prop="name" label="计费方式">
                                </el-table-column>
                                <el-table-column prop="name" label="单价">
                                </el-table-column>
                                <el-table-column prop="name" label="剩余期次">
                                </el-table-column>
                                <el-table-column prop="name" label="报名期次">
                                </el-table-column>
                                <el-table-column prop="name" label="实缴费用">
                                </el-table-column>
                                <el-table-column prop="name" label="扣除期次" width="160">
                                    <template scope="scope">
                                        <el-input v-model="scope.row.name" placeholder="请输入扣款期次" size="small"></el-input>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="name" label="退费金额" width="160">
                                    <template scope="scope">
                                        <el-input v-model="scope.row.name" placeholder="请输入退款金额" size="small"></el-input>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <p class="tuifei_text">共计退费项目<span>0</span>项，扣除课时<span>0</span>期/<span>0</span>次，退费
                                <s>0</s>元</p>
                            <p class="fengexian"></p>
        
                            <el-row :gutter="20">
                                <el-col :span="8">
                                    <el-form-item label="经办人">
                                        <el-input v-model="tuifeiform.jbr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="8">
                                    <el-form-item label="退费时间">
                                        <el-input v-model="tuifeiform.tftime"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="8">
                                    <el-form-item label="退费账户">
                                        <el-input v-model="tuifeiform.tfzh"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
        
                            <el-form-item label="备注">
                                <el-input v-model="tuifeiform.bz" type="textarea" :autosize="{ minRows: 2, maxRows: 4}"></el-input>
                            </el-form-item>
                        </div>
                        <div class="zafei" v-if="tuifeiform.resource == '杂费'">
                            <el-table :data="tableData" style="width: 100%">
                                <el-table-column prop="name" label="费用名称">
                                </el-table-column>
                                <el-table-column prop="name" label="单位">
                                </el-table-column>
                                <el-table-column prop="name" label="单价">
                                </el-table-column>
                                <el-table-column prop="name" label="已买数量">
                                </el-table-column>
                                <el-table-column prop="name" label="实缴费用">
                                </el-table-column>
                                <el-table-column prop="name" label="退费金额" width="160">
                                    <template scope="scope">
                                        <el-input v-model="scope.row.name" placeholder="请输入退款金额" size="small"></el-input>
                                    </template>
                                </el-table-column>
                            </el-table>
                            <p class="tuifei_text">共计退费项目<span>0</span>项，退费
                                <s>0</s>元</p>
                            <p class="fengexian"></p>
        
                            <el-row :gutter="20">
                                <el-col :span="8">
                                    <el-form-item label="经办人">
                                        <el-input v-model="tuifeiform.jbr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="8">
                                    <el-form-item label="退费时间">
                                        <el-input v-model="tuifeiform.tftime"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :span="8">
                                    <el-form-item label="退费账户">
                                        <el-input v-model="tuifeiform.tfzh"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
        
                            <el-form-item label="备注">
                                <el-input v-model="tuifeiform.bz" type="textarea" :autosize="{ minRows: 2, maxRows: 4}"></el-input>
                            </el-form-item>
                        </div>
        
                    </el-form>
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="tuifeishow = false">取消</el-button>
                        <el-button type="primary" @click="tuifeishow = false">确定退费</el-button>
                    </span>
                </el-dialog>-->
    </div>
</template>
<script>
module.exports = {
    computed: {
        yx_xy_xq_meau_id: function () {
            return this.$store.state.yx_xy_xq_meau_id;
        }
    },
    data: function () {
        return {
            // tuifeishow: false,
            // tuifeiform: {
            //     resource: "学费",
            //     jbr: "",
            //     tftime: "2017-03-11",
            //     tfzh: "",
            //     bz: ""
            // },
            tableData: [
                {
                    name: "xxx"
                }
            ],
            selfMessage: {
                student: [
                    {}
                ]
            },

            //权限
            yx_cwgl_jf: false
        }
    },
    methods: {
        //获取个人信息
        getMessage: function () {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        data.data.data.student[0].face = data.data.data.student[0].face?"http://wx.eduwxt.com"+data.data.data.student[0].face:"http://img.eduwxt.com/assets/images/users/avatar-11.jpg";
                        self.selfMessage = data.data.data;
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
        }
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 3);
        this.getMessage();

        //权限设置
        var yx_xygl_all = this.pdqx(["yx_xygl_all", "yx"]);
        var yx_xygl_my = this.pdqx(["yx_xygl_my", "yx"]);

        if (!yx_xygl_all && !yx_xygl_my) {
            self.$router.push({ name: 'worktody' })
        }

        //权限设置
        this.yx_cwgl_jf = this.pdqx(["yx_cwgl_jf", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.btn_box {
    position: absolute;
    right: 0;
    bottom: 0;
}

.tuifei_text {
    text-align: right;
    padding: 10px 0;
    span {
        font-size: 16px;
        padding: 0 5px;
    }
    s {
        color: @error;
        font-size: 16px;
        text-decoration: none;
    }
}
</style>