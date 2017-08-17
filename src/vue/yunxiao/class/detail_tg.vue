<template>
    <div class="yx_class">
        <div class="left">
            <h1>班级管理
                <a href=""></a>
                <article>对已开设的班级进行日常教学管理工作</article>
            </h1>
            <router-link :to="{name:'ttpToday'}" :class="{ active : $route.name=='ttpToday' }">今日托管</router-link>
            <router-link :to="{name:'ManagedRecord'}" :class="{ active : $route.name=='ManagedRecord' }">托管记录</router-link>
            <router-link :to="{name:'tg_classNotice'}" :class="{ active : $route.name == 'tg_classNotice' || $route.name == 'tgclass_fabu_tongzhi' }">班级通知</router-link>
            <router-link :to="{name:'studentlist1'}" :class="{ active : $route.name=='studentlist1' }">学员列表</router-link>
            <router-link :to="{name:'tg_class_log'}" :class="{ active : $route.name=='tg_class_log' }">操作日志</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                    <img src="../../../img/yx_class_right_logo.png" alt="">
                </span>
                <div>
                    <h1>{{ classDetail.class_name }}</h1>
                    <p>
                        <span>所在校区：{{ classDetail.campus_name }}</span>
                        <span>班主任：{{ classDetail.teacher_name }}</span>
                        <span>学员人数：{{ xueyuanCount.count }}/{{ classDetail.max_student}}</span>
                        <span>收费类型：{{ parseInt(classDetail.price) }}元/{{ classDetail.fee_method | fee_method }}</span>
                        <span>开课时间：{{ classDetail.reg_time | yyyy_mm_dd }}</span>
                        <span>
                            开设课程：
                            <span v-for="(list,index) in classDetail.subject">
                                <span v-if="index == classDetail.subject.length-1">{{ list.subject_name }}</span>
                                <span v-else>{{ list.subject_name }}，</span>
                            </span>
                        </span>
                        <el-button type="primary" class="pk_btns" size="small" @click="$router.push({name:'tgclass_fabu_tongzhi'})" v-if="yx_bjgl_my_qtxx && $route.name == 'tg_classNotice'">发布班级通知</el-button>
                    </p>
                    <p v-if="classDetail.remark != ''">
                        <span>班级描述：{{ classDetail.remark }}</span>
                    </p>
                    <span @click="$router.go(-1)">
                        <i class="el-icon-arrow-left"></i>返回上一级</span>
    
                </div>
            </div>
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
            classDetail: {
                subject: []
            },
            xueyuanCount:{},
            classroomList: [],

            yx_bjgl_my_qtxx:false
        }
    },
    methods: {
        //获取班级学员人数
        getxueyuanCount: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/student/count")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.xueyuanCount = data.data.data;
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
        //班级详情
        getClassDetail: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classDetail = data.data.data;
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
        //获取教室列表
        getClassroom: function () {
            var self = this;
            self.$http.get("/api/room")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classroomList = data.data.data;
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
        this.$store.commit('setYXLeftMeauId', 4);
        this.getClassDetail();
        this.getClassroom();
        this.getxueyuanCount();
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
</style>