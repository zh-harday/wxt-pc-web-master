<template>
    <div class="yx_class">
        <div class="left">
            <h1>
                招生追踪
                <article>管理意向学员并反馈跟进情况</article>
            </h1>
            <ul id="zhaosheng_meau">
    
                <li class="bottom_line" v-if="yx_zszz_syxygl">
                    <router-link :to="{name:'recruit_list',params:{ type:'Student' }}" tag="h1" :class="{ active: $route.params.type == 'Student' }">所有学员({{ StudentCount.all }})</router-link>
                    <router-link :to="{name:'recruit_list',params:{ type:'NotArranged' }}" tag="h1" :class="{ active: $route.params.type == 'NotArranged' }">未安排试听({{ StudentCount.wap }})</router-link>
                    <router-link :to="{name:'recruit_list',params:{ type:'AlreadyArranged' }}" tag="h1" :class="{ active: $route.params.type == 'AlreadyArranged' }">已安排试听({{ StudentCount.yap }})</router-link>
                </li>
                <li class="bottom_line" v-if="yx_zszz_glxygl">
                    <router-link :to="{name:'recruit_my',params:{ type:'all' }}" tag="h1" :class="{ active: $route.params.type == 'all' }">我的学员({{ myStudentCount.all }})</router-link>
                    <router-link :to="{name:'recruit_my',params:{ type:'reg' }}" tag="h1" :class="{ active: $route.params.type == 'reg' }">我添加的({{ myStudentCount.reg }})</router-link>
                    <router-link :to="{name:'recruit_my',params:{ type:'zy' }}" tag="h1" :class="{ active: $route.params.type == 'zy' }">市场关系({{ myStudentCount.zy }})</router-link>
                    <router-link :to="{name:'recruit_my',params:{ type:'gw' }}" tag="h1" :class="{ active: $route.params.type == 'gw' }">顾问关系({{ myStudentCount.gw }})</router-link>
                    <router-link :to="{name:'recruit_my',params:{ type:'gz' }}" tag="h1" :class="{ active: $route.params.type == 'gz' }">跟踪关系({{ myStudentCount.gz }})</router-link>
                </li>
                <li>
                    <router-link v-if="yx_zszz_yyst" :to="{name:'Audition'}" class="next_meau" tag="h1" :class="{ active: $route.name == 'Audition' }">预约试听管理</router-link>
                    <router-link :to="{name:'wxtable'}" class="next_meau" tag="h1" :class="{ active: $route.name == 'wxtable'}" v-if="yx_zszz_wxbd && $route.name == 'wxtable'">微信表单设置</router-link>
                    <router-link :to="{name:'wxtable'}" class="next_meau" tag="h1" :class="{ active:$route.name == 'AddnewTable'}" v-if="yx_zszz_wxbd && $route.name != 'wxtable'">微信表单设置</router-link>
                    <router-link v-if="yx_zszs_sz" :to="{name:'EnrollmentTracking'}" class="next_meau" tag="h1" :class="{ active: $route.name == 'EnrollmentTracking' }">招生追踪设置</router-link>
                </li>
            </ul>
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            StudentCount:{
                all:0,
                wap:0,
                yap:0
            },
            myStudentCount: {
                all: 0,
                reg: 0,
                zy: 0,
                gw: 0,
                gz: 0
            },

            yx_zszz: false,
            yx_zszz_glxygl: false,
            yx_zszz_syxygl: false,
            yx_zszz_yyst: false,
            yx_zszz_wxbd: false,
            yx_zszs_sz: false
        }
    },
    methods: {
        //获取招生追踪学员列表
        getStudentList: function (type) {
            var self = this;
            var campus_id = self.myMessage.campus_id == '1' ? "" : self.myMessage.campus_id;
            var lecture = "";
            if(type == "wap"){
                lecture = 0;
            }else if(type == "yap"){
                lecture = 1;
            }
            var url = "/api/intention?limit=1&campus_id=" + campus_id + "&lecture="+lecture;
            self.$http.get(url)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.StudentCount[type] = data.data.data.count;
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
        //我的
        getMyStudentList: function (type) {
            var self = this;
            var campus_id = self.myMessage.campus_id == '1' ? "" : self.myMessage.campus_id;
            var url = "/api/intention/my?limit=1&campus_id=" + campus_id + "&type=" + type;
            self.$http.get(url)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.myStudentCount[type] = data.data.data.count;
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
        this.$store.commit('setYXLeftMeauId', 2);
        this.yx_zszz = this.pdqx(["yx_zszz", "yx_zszz_glxygl", "yx_zszz_syxygl", "yx_zszz_yyst", "yx_zszz_wxbd", "yx_zszs_sz", "yx"]);
        this.yx_zszz_syxygl = this.pdqx(["yx_zszz", "yx_zszz_syxygl", "yx"]);
        this.yx_zszz_glxygl = this.pdqx(["yx_zszz", "yx_zszz_glxygl", "yx"]);
        this.yx_zszz_yyst = this.pdqx(["yx_zszz", "yx_zszz_yyst", "yx"]);
        this.yx_zszz_wxbd = this.pdqx(["yx_zszz", "yx_zszz_wxbd", "yx"]);
        this.yx_zszs_sz = this.pdqx(["yx_zszz", "yx_zszs_sz", "yx"]);


        if (!this.yx_zszz) {
            this.$router.push({ name: 'worktody' });
        }

        if(this.yx_zszz_syxygl){
            this.getStudentList("all");
            this.getStudentList("wap");
            this.getStudentList("yap");
        }

        if (this.yx_zszz_glxygl) {
            this.getMyStudentList("all");
            this.getMyStudentList("reg");
            this.getMyStudentList("zy");
            this.getMyStudentList("gw");
            this.getMyStudentList("gz");
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
#zhaosheng_meau {
    >li {
        &.bottom_line {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        >h1 {
            padding: 10px 25px;
            color: @c2;
            margin-bottom: 0;
            overflow: hidden;
            white-space: nowrap;
            font-weight: normal;
            font-size: 14px;
            cursor: pointer;
            &:hover {
                background-color: rgba(0, 0, 0, .05);
                color: @c1;
            }
            &.active {
                background-color: rgba(0, 0, 0, .1);
                color: @c1;
                &:hover {
                    background-color: rgba(0, 0, 0, .05);
                    color: @c1;
                }
            }
        }
        >ul {
            >li {
                padding: 10px 25px;
                padding-left: 30px;
                color: #828282;
                overflow: hidden;
                white-space: nowrap;
                font-size: 14px;
                cursor: pointer;
                &:hover {
                    background-color: rgba(0, 0, 0, .05);
                    color: @c1;
                }
                &.active {
                    background-color: rgba(0, 0, 0, .1);
                    color: @c1;
                    &:hover {
                        background-color: rgba(0, 0, 0, .05);
                        color: @c1;
                    }
                }
            }
        }
    }
}
</style>