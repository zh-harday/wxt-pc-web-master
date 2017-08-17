<template>
    <div class="yx_class yx_classs">
        <div class="left">
            <h1>校园电商
                                <a href=""></a>
                                <article>打造学校专属的线上微商城。</article>
                            </h1>
            <el-menu default-active="1-1" class="el-menu-vertical-demo" theme="light" unique-opened="true" @open="selectFun">
                <el-submenu index="1">
                    <template slot="title"><img src="../../img/wx_shangcheng.png" alt="">微信商城</template>
                    <el-menu-item index="1-1"><a :href="wxurl[3].type['0'].link[1].url" target="shangcheng">商城系统</a></el-menu-item>
                    <el-menu-item index="1-2"><a :href="wxurl[3].type['0'].link[0].url" target="shangcheng">团购系统</a></el-menu-item>
                    <el-menu-item index="1-3"><a :href="wxurl[3].type['0'].link[2].url" target="shangcheng">支付设置</a></el-menu-item>
                    <el-menu-item index="1-4"><a :href="wxurl[3].type['0'].link[3].url" target="shangcheng">证书管理</a></el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><img src="../../img/wx_sc_zhangdan.png">收款账单</template>
                    <el-menu-item index="2-1"><a :href="wxurl[3].type['3'].url" target="shangcheng">微信支付账单</a></el-menu-item>
                    <el-menu-item index="2-2"><a :href="wxurl[3].type['2'].url" target="shangcheng">平台支付对账</a></el-menu-item>
                </el-submenu>
            </el-menu>
        </div>
        <div class="right1">
            <iframe :src="wxurl[3].type['0'].link[1].url" width="100%" height="100%" frameborder="0" seamless="seamless" name="shangcheng"></iframe>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        wxurl: function () {
            return this.$store.state.wxurl;
        }
    },
    methods: {
        selectFun: function (index) {
            if (index == 1) {
                this.$store.commit("sethelpid", "weixinshangcheng")
            }
            if (index == 2) {
                this.$store.commit("sethelpid", "shoukuanzhangdan")
            }
        }
    },
    created: function () {
        this.$store.commit("sethelpid", "weixinshangcheng")

        var wx_xyds = this.pdqx(["wx_xyds", "wx"]);
        if (!wx_xyds) {
            this.$router.push({ name: 'worktody' })
        }
    }
}
</script>