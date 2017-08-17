<template>
    <div class="yx_class">
        <div class="left">
            <h1>全栖报表
                <article>联通校内各项重要数据，为决策提供有力依据</article>
            </h1>
            <!--<router-link v-if="yx_bb_zssj" :to="{name:'zhaoshengshuju'}" :class="{ active : $route.name == 'zhaoshengshuju' }">-->
            <router-link :to="{name:'zhaoshengshuju'}" :class="{ active : $route.name == 'zhaoshengshuju' }">
                <img src="../../../img/p_yx_table_zs.png" alt="" v-show="$route.name == 'zhaoshengshuju'">
                <img src="../../../img/p_yx_table_zs_a.png" alt="" v-show="$route.name != 'zhaoshengshuju'">
                <span>招生数据</span>
            </router-link>
    
            <router-link v-if="yx_xytj" :to="{name:'xueyuantongji'}" :class="{ active : $route.name == 'xueyuantongji' }">
                <img src="../../../img/p_yx_table_xy.png" alt="" v-show="$route.name == 'xueyuantongji'">
                <img src="../../../img/p_yx_table_xy_a.png" alt="" v-show="$route.name != 'xueyuantongji'">
                <span>学员统计</span>
            </router-link>
    
            <router-link v-if="yx_cwfx" :to="{name:'caiwufenxi'}" :class="{ active : $route.name == 'caiwufenxi' }">
                <img src="../../../img/p_yx_table_cw.png" alt="" v-show="$route.name == 'caiwufenxi'">
                <img src="../../../img/p_yx_table_cw_a.png" alt="" v-show="$route.name != 'caiwufenxi'">
                <span>财务分析</span>
            </router-link>
     
    
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            yx_bb_zssj: false,
            yx_xytj: false,
            yx_cwfx: false
        }
    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 1);
        //权限判断
        this.yx_bb_zssj = this.pdqx(["yx_bb_zssj", "yx_bb", "yx"]);
        this.yx_xytj = this.pdqx(["yx_xytj", "yx_bb", "yx"]);
        this.yx_cwfx = this.pdqx(["yx_cwfx", "yx_bb", "yx"]);
    }
}

</script>