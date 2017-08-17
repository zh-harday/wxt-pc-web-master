<template>
    <div class="yx_class">
        <div class="left">
            <h1>员工管理
                <article>添加教职工账号并对其进行详细设置</article>
            </h1>
            <router-link :to="{ name:'staffList',params:{id:'All'},query:{index:-1} }" :class="{ active:yx_yuangong_meau_id==-1}">全部({{ all }})</router-link>
            <router-link :to="{ name:'staffList',params:{id:list.id},query:{index:index} }" :class="{ active:yx_yuangong_meau_id==index}" v-for="(list,index) in bumen">{{ list.name }}({{ list.count }})</router-link>
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        yx_yuangong_meau_id: function () {
            return this.$store.state.yx_yuangong_meau_id;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            bumen: window.sessionStorage.getItem("bumen") ? JSON.parse(window.sessionStorage.getItem("bumen")) : [],
            all: 0,
        }
    },
    methods: {
        //获取部门列表
        getBumenList: function () {
            var self = this;
            self.$http.get("/api/department")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            data.data.data[i].count = 0;
                            self.getyuangongList(data.data.data[i].id);
                        }
                        self.bumen = data.data.data;
                        window.sessionStorage.setItem("bumen", JSON.stringify(self.bumen));
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
        //获取员工列表
        getyuangongList: function (id) {
            var self = this;
            var ids = id ? id : "";
            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.$http.get("/api/staff?campus_id=" + campus_id + "&department_id=" + ids + "&count=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (ids == "") {
                            self.all = data.data.data.count;
                            return;
                        }
                        for (var i = 0; i < self.bumen.length; i++) {
                            if (self.bumen[i].id == id) {
                                self.bumen[i].count = data.data.data.count;
                                break;
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
        }
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 6);
        this.getBumenList();
        this.getyuangongList();
    }
}

</script>