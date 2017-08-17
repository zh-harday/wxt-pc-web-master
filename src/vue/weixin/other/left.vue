<template>
    <div class="leftMeau">
        <ul>
            <router-link v-if="wx_jcxc" tag="li" :to="{name:'Propaganda'}" :class="{ active:$route.name =='Propaganda' }">
                <img src="../../../img/wx_xuanchuan_a.png" v-show="$route.name =='Propaganda'">
                <img src="../../../img/wx_xuanchuan.png" v-show="$route.name !='Propaganda'">
                <span>宣传</span>
            </router-link>
            <router-link v-if="wx_hdyx" tag="li" :to="{name:'Interaction'}" :class="{ active:$route.name =='Interaction' }">
                <img src="../../../img/wx_hudong_a.png" v-show="$route.name =='Interaction'">
                <img src="../../../img/wx_hudong.png" v-show="$route.name !='Interaction'">
                <span>互动</span>
            </router-link>
            <router-link v-if="wx_zshd" tag="li" :to="{name:'activity'}" :class="{ active:$route.name =='activity' }">
                <img src="../../../img/wx_huodong_a.png" v-show="$route.name =='activity'">
                <img src="../../../img/wx_huodong.png" v-show="$route.name !='activity'">
                <span>活动</span>
            </router-link>
            <router-link v-if="wx_xyds" tag="li" :to="{name:'ShoppingMall'}" :class="{ active:$route.name =='ShoppingMall' }">
                <img src="../../../img/wx_dianpu_a.png" v-show="$route.name =='ShoppingMall'">
                <img src="../../../img/wx_dianpu.png" v-show="$route.name !='ShoppingMall'">
                <span>商城</span>
            </router-link>
        </ul>
        <ul class="bottom_list" v-if="wx_wxsz">
            <router-link tag="li" :to="{name:'Setup'}" :class="{ active:$route.name =='Setup' }">
                <img src="../../../img/yx_setting_a.png" v-show="$route.name =='Setup'">
                <img src="../../../img/yx_setting.png" v-show="$route.name !='Setup'">
                <span>设置</span>
            </router-link>
        </ul>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            wx_wxsz:false,
            wx_jcxc:false,
            wx_hdyx:false,
            wx_zshd:false,
            wx_xyds:false
        }
    },
    created: function () {
        this.wx_wxsz = this.pdqx(["wx_wxsz", "wx"]);
        this.wx_jcxc = this.pdqx(["wx_jcxc", "wx"]);
        this.wx_hdyx = this.pdqx(["wx_hdyx", "wx"]);
        this.wx_zshd = this.pdqx(["wx_zshd", "wx"]);
        this.wx_xyds = this.pdqx(["wx_xyds", "wx"]);
    }

}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.bottom_list {
    width: 80px;
    position: fixed;
    bottom: 0;
    left: 0;
}
</style>