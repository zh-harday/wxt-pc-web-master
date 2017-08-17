<template>
    <div class="yx_class">
        <div class="left">
            <h1>课程详情
                <article>管理各校区开设的课程并查询详情</article>
            </h1>
            <router-link :to="{name:'curriculum_detail_class',params:{id:$route.params.id}}" :class="{active:subject_detail_meau_id==1}">开课班级</router-link>
            <router-link :to="{name:'curriculum_detail_teach',params:{id:$route.params.id}}" :class="{active:subject_detail_meau_id==2}">授课老师</router-link>
            <router-link :to="{name:'curriculum_detail_info',params:{id:$route.params.id}}" :class="{active:subject_detail_meau_id==3}" v-if="yx_kcgl && (myMessage.campus_id == 1 || myMessage.campus_id== subjectDatail.campus_id)">修改课程</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                    <img src="../../../img/yx_sub_xq_logo.png" alt="">
                </span>
                <div>
                    <h1>{{ subjectDatail.subject }}</h1>
                    <p>
                        <span>课程类别：{{ subjectDatail.type_name }}</span>
                        <span>所属校区：{{ subjectDatail.campus_name }}</span>
                        <span>创建时间：{{ subjectDatail.time | yyyy_mm_dd }}</span>
                        <span>累计上课：未知</span>
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
        subject_detail_meau_id: function () {
            return this.$store.state.subject_detail_meau_id;
        },
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            SubjectLBList: window.sessionStorage.getItem("SubjectLBList") ? JSON.parse(window.sessionStorage.getItem("SubjectLBList")) : [],
            subjectDatail: {},
            yx_kcgl:false
        }
    },
    methods: {
        //获取课程详情
        getSubjectDetail: function () {
            var self = this;
            self.$http.get("/api/subject/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < self.campus.length; i++) {
                            if (data.data.data.campus_id == 1) {
                                data.data.data.campus_name = "所有校区";
                                break;
                            }
                            if (data.data.data.campus_id == self.campus[i].id) {
                                data.data.data.campus_name = self.campus[i].name;
                                break;
                            }
                        }
                        if (self.SubjectLBList.length == 0) {
                            self.getSubjectfenlei(data.data.data);
                            return;
                        }

                        for (var k = 0; k < self.SubjectLBList.length; k++) {
                            if (data.data.data.type_id == self.SubjectLBList[k].id) {
                                data.data.data.type_name = self.SubjectLBList[k].type_name;
                            }
                        }
                        self.subjectDatail = data.data.data;

                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading1 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取课程分类
        getSubjectfenlei: function (obj) {
            var self = this;
            self.$http.get("/api/subject/type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.SubjectLBList = data.data.data;
                        window.sessionStorage.setItem("SubjectLBList", JSON.stringify(self.SubjectLBList));

                        for (var k = 0; k < self.SubjectLBList.length; k++) {
                            if (obj.type_id == self.SubjectLBList[k].id) {
                                obj.type_name = self.SubjectLBList[k].type_name;
                            }
                        }
                        self.subjectDatail = obj;

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
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 5);
        this.getSubjectDetail();

        //权限设置
        this.yx_kcgl = this.pdqx(["yx_kcgl", "yx"]);
    }
}

</script>
