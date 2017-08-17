<template>
    <div class="yx_class">
        <div class="left">
            <h1>课程管理
                    <article>管理各校区开设的课程并查询详情</article>
                </h1>
            <router-link :to="{ name : 'curriculum_list', params:{ id:'All' },query:{ index : -1} }" :class="{ active:subject_meau_id == -1 }">所有课程({{ count }})</router-link>
            <router-link :to="{ name : 'curriculum_list', params:{ id:list.id },query:{ index : index} }" v-for="(list,index) in SubjectLBList" :class="{ active:subject_meau_id == index }">{{ list.type_name}}({{ list.count }})</router-link>
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {

    computed: {
        subject_meau_id: function () {
            return this.$store.state.subject_meau_id;
        }
    },
    data: function () {
        return {
            count: 0,
            SubjectLBList: window.sessionStorage.getItem("SubjectLBList") ? JSON.parse(window.sessionStorage.getItem("SubjectLBList")) : []
        }
    },
    methods: {
        //获取课程分类
        getSubjectfenlei: function (obj) {
            var self = this;
            self.$http.get("/api/subject/type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            data.data.data[i].count = 0;
                            self.getSubjectList(data.data.data[i].id)
                        }
                        self.SubjectLBList = data.data.data;
                        window.sessionStorage.setItem("SubjectLBList", JSON.stringify(self.SubjectLBList));
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
        //获取各科总数
        getSubjectList: function (id) {
            var self = this;
            self.$http.get("/api/subject/count?type_id=" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < self.SubjectLBList.length; i++) {
                            if (self.SubjectLBList[i].id == id) {
                                self.SubjectLBList[i].count = data.data.data.count;
                            }
                        }
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
        //获取课程统计
        getSubjectTJ: function () {
            var self = this;
            self.$http.get("/api/subject/count")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.count = data.data.data.count;
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
        this.$store.commit('setYXLeftMeauId', 5);
        this.getSubjectfenlei();
        this.getSubjectTJ();

    }
}

</script>