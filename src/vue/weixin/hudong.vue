<template>
    <div class="yx_class yx_classs">
        <div class="left">
            <h1>互动营销
                        <a href=""></a>
                        <article>提供全方位移动互联网家校互动解决方案。</article>
                    </h1>
            <el-menu default-active="1-1" class="el-menu-vertical-demo" theme="light" unique-opened="true" @open="selectFun">
                <el-submenu index="1">
                    <template slot="title"><img src="../../img/wx_xiangshang.png" alt="">线上互动</template>
                    <el-menu-item index="1-1"><a :href="wxurl[2].type['0'].link[0].url" target="hudong">预约报名</a></el-menu-item>
                    <el-menu-item index="1-2"><a :href="wxurl[2].type['0'].link[1].url" target="hudong">教师名片</a></el-menu-item>
                    <el-menu-item index="1-3"><a :href="wxurl[2].type['0'].link[2].url" target="hudong">校园论坛</a></el-menu-item>
                    <el-menu-item index="1-4"><a :href="wxurl[2].type['0'].link[3].url" target="hudong">校园相册</a></el-menu-item>
                    <el-menu-item index="1-5"><a :href="wxurl[2].type['0'].link[4].url" target="hudong">简易投票</a></el-menu-item>
                    <el-menu-item index="1-6"><a :href="wxurl[2].type['0'].link[5].url" target="hudong">调查问卷</a></el-menu-item>
                    <el-menu-item index="1-7"><a :href="wxurl[2].type['0'].link[6].url" target="hudong">校园留言板</a></el-menu-item>
                    <el-menu-item index="1-8"><a :href="wxurl[2].type['0'].link[7].url" target="hudong">活动邀请函</a></el-menu-item>
                    <el-menu-item index="1-9"><a :href="wxurl[2].type['0'].link[8].url" target="hudong">分享数据汇总</a></el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><img src="../../img/wx_xianxia.png">线下互动</template>
                    <el-menu-item index="2-1"><a :href="wxurl[2].type['2'].link[0].url" target="hudong">活动签到</a></el-menu-item>
                    <el-menu-item index="2-2"><a :href="wxurl[2].type['2'].link[1].url" target="hudong">现场摇一摇</a></el-menu-item>
                    <el-menu-item index="2-3"><a :href="wxurl[2].type['2'].link[2].url" target="hudong">微信大屏幕</a></el-menu-item>
                </el-submenu>
            </el-menu>
        </div>
        <div class="right1">
            <iframe :src="wxurl[2].type['0'].link[0].url" width="100%" height="100%" frameborder="0" seamless="seamless" name="hudong"></iframe>
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
                this.$store.commit("sethelpid", "xianshanghudong")
            }
            if (index == 2) {
                this.$store.commit("sethelpid", "xianxiahudong")
            }
        }
    },
    created:function(){
        this.$store.commit("sethelpid", "xianshanghudong");

        var wx_hdyx = this.pdqx(["wx_hdyx", "wx"]);
        if (!wx_hdyx) {
            this.$router.push({ name: 'worktody' })
        }
    }
}
</script>