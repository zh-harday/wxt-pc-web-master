<template>
    <div class="yx_class">
        <div class="left">
            <h1>学员管理
                <article>对已报名的正式学员进行管理操作</article>
            </h1>
            <div v-if="yx_xygl_all">
                <router-link :to="{ name:'student_list',params:{ type:'All'}}" :class="{active:$route.params.type=='All' && $route.name == 'student_list'}">所有学员({{ obj.All }})</router-link>
                <router-link :to="{ name:'student_list',params:{ type:'birthday'}}" :class="{active:$route.params.type=='birthday' && $route.name == 'student_list'}">生日学员({{ obj.birthday }})</router-link>
                <router-link :to="{ name:'student_list',params:{ type:'no_class'}}" :class="{active:$route.params.type=='no_class' && $route.name == 'student_list'}">未进班学员({{ obj.no_class }})</router-link>
                <router-link :to="{ name:'student_list',params:{ type:'lacking'}}" :class="{active:$route.params.type=='lacking' && $route.name == 'student_list'}">待续费学员({{ obj.lacking }})</router-link>
                <router-link :to="{ name:'student_list',params:{ type:'stop_class'}}" :class="{active:$route.params.type=='stop_class' && $route.name == 'student_list'}">已停课学员({{ obj.stop_class }})</router-link>
            </div>
            <div v-if="yx_xygl_my">
                <router-link :to="{ name:'student_list1',params:{ type:'All'}}" :class="{active:$route.params.type=='All' && $route.name =='student_list1'}">我的所有学员({{ obj1.All }})</router-link>
                <router-link :to="{ name:'student_list1',params:{ type:'birthday'}}" :class="{active:$route.params.type=='birthday' && $route.name =='student_list1'}">生日学员({{ obj1.birthday }})</router-link>
                <router-link :to="{ name:'student_list1',params:{ type:'no_class'}}" :class="{active:$route.params.type=='no_class' && $route.name =='student_list1'}">未进班学员({{ obj1.no_class }})</router-link>
                <router-link :to="{ name:'student_list1',params:{ type:'lacking'}}" :class="{active:$route.params.type=='lacking' && $route.name =='student_list1'}">待续费学员({{ obj1.lacking }})</router-link>
                <router-link :to="{ name:'student_list1',params:{ type:'stop_class'}}" :class="{active:$route.params.type=='stop_class' && $route.name =='student_list1'}">已停课学员({{ obj1.stop_class }})</router-link>
            </div>
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        yx_class_meau_id: function () {
            return this.$store.state.yx_class_meau_id;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            obj: {
                All: 0,
                birthday: 0,
                no_class: 0,
                stop_class: 0,
                lacking: 0
            },
            obj1: {
                All: 0,
                birthday: 0,
                no_class: 0,
                stop_class: 0,
                lacking: 0
            },

            campus_id:"",

            yx_xygl_all:false,
            yx_xygl_my:false
        }
    },
    methods: {
        //获取学员列表
        getXueyuanList: function (obj, my) {
            var self = this;
            var type = obj == 'All' ? '' : obj;
            self.$http.get("/api/student?campus_id="+self.campus_id+"&type=" + type + "&page=1&limit=1&my=" + my)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        if (my == 1) {
                            self.obj1[obj] = data.data.data.count;
                        } else {
                            self.obj[obj] = data.data.data.count;
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
        var self = this;
        self.campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$store.commit('setYXLeftMeauId', 3);
        for (var k in self.obj) {
            self.getXueyuanList(k, "");
        }
        for (var k in self.obj1) {
            self.getXueyuanList(k, 1);
        }

        this.yx_xygl_all = this.pdqx(["yx_xygl_all","yx_xygl", "yx"]);
        this.yx_xygl_my = this.pdqx(["yx_xygl_my","yx_xygl", "yx"]);

        if (!this.yx_xygl_all && !this.yx_xygl_my) {
            self.$router.push({ name: 'worktody' })
        }

    }
}

</script>