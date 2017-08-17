<template>
    <div class="yx_class">
        <div class="left">
            <h1>员工详情
                <article>在此查看并管理学校教职工</article>
            </h1>
            <router-link :class="{active:$route.name =='staff_detail_xg'}" :to="{name:'staff_detail_xg',params:{id:$route.params.id}}">员工信息</router-link>
            <router-link :class="{active:$route.name =='CurriculumList'}" :to="{name:'CurriculumList',params:{id:$route.params.id}}">排课信息</router-link>
            <router-link v-if="yx_yggl_edit" :class="{active:$route.name == 'Jurisdiction'}" :to="{name:'Jurisdiction',params:{id:$route.params.id}}">个人权限</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                    <img :src="YGMessage.face" alt="">
                </span>
                <div>
                    <h1>{{ YGMessage.name }}</h1>
                    <p>
                        <span>工号：{{ YGMessage.staff_id }}</span>
                        <span>部门：{{ YGMessage.department_name }}</span>
                        <span>职务：{{ YGMessage.group_name }}</span>
                    </p>
                    <p>
                        <span>{{ YGMessage.motto }}</span>
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
        YGMessage: function () {
            return this.$store.state.YGMessage;
        }
    },
    data: function () {
        return {
            yx_yggl_edit: false
        }
    },
    methods: {

    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 6);
        this.$store.commit('getYGMessage', this);

        this.yx_yggl_edit = this.pdqx(["yx_yggl_edit", "yx"]);
        var yx_yggl_view = this.pdqx(["yx_yggl_view", "yx"]);
        if (!this.yx_yggl_edit && !yx_yggl_view) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>