<template>
    <div class="yx_class">
        <div class="left">
            <h1>财务管理
                    <a href=""></a>
                    <article>查看学校各级账户的详细收支情况</article>
                </h1>
            <router-link :to="{name:'Finance_mx'}" :class="{ active:yx_cw_leftMeau_id==1}">收费明细</router-link>
            <!--<a href="javascript:void(0)" :class="{ active:yx_cw_leftMeau_id==2}">财务统计</a>-->
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {

    computed: {
        yx_cw_leftMeau_id: function () {
            return this.$store.state.yx_cw_leftMeau_id;
        }
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 7);
        var yx_cwgl_log = this.pdqx(["yx_cwgl_log", "yx"]);
        if (!yx_cwgl_log) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>