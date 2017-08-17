<template>
    <div class="yx_class yx_classs">
        <div class="left">
            <h1>招生活动
                                <a href=""></a>
                                <article>创建基于微信社交的线上招生活动。</article>
                            </h1>
            <el-menu default-active="1-1" class="el-menu-vertical-demo" theme="light" unique-opened="true" @open="selectFun">
                <el-submenu index="1">
                    <template slot="title"><img src="../../img/wx_xc_hd_choujiang.png" alt="">抽奖类</template>
                    <el-menu-item index="1-1"><a :href="wxurl[2].type['1'].link[0].url" target="huodong">刮刮卡</a></el-menu-item>
                    <el-menu-item index="1-2"><a :href="wxurl[2].type['1'].link[1].url" target="huodong">大转盘</a></el-menu-item>
                    <el-menu-item index="1-3"><a :href="wxurl[2].type['1'].link[2].url" target="huodong">九宫格</a></el-menu-item>
                    <el-menu-item index="1-4"><a :href="wxurl[2].type['1'].link[3].url" target="huodong">砸金蛋</a></el-menu-item>
                    <el-menu-item index="1-5"><a :href="wxurl[2].type['1'].link[4].url" target="huodong">水果机</a></el-menu-item>
                    <el-menu-item index="1-6"><a :href="wxurl[2].type['1'].link[5].url" target="huodong">摇一摇</a></el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><img src="../../img/wx_hd_jizan.png">集赞类</template>
                    <el-menu-item index="2-1"><a :href="wxurl[2].type['1'].link[7].url" target="huodong">摇钱树</a></el-menu-item>
                    <el-menu-item index="2-2"><a :href="wxurl[2].type['1'].link[8].url" target="huodong">拆礼盒</a></el-menu-item>
                    <el-menu-item index="2-3"><a :href="wxurl[2].type['1'].link[9].url" target="huodong">图文投票</a></el-menu-item>
                    <el-menu-item index="2-4"><a :href="wxurl[2].type['1'].link[10].url" target="huodong">爱心募捐</a></el-menu-item>
                    <el-menu-item index="2-5"><a :href="wxurl[2].type['1'].link[11].url" target="huodong">集字游戏</a></el-menu-item>
                    <el-menu-item index="2-6"><a :href="wxurl[2].type['1'].link[12].url" target="huodong">分享助力</a></el-menu-item>
                    <el-menu-item index="2-7"><a :href="wxurl[2].type['1'].link[13].url" target="huodong">优惠接力</a></el-menu-item>
                    <el-menu-item index="2-8"><a :href="wxurl[2].type['1'].link[14].url" target="huodong">人气冲榜</a></el-menu-item>
                </el-submenu>
                <el-submenu index="3">
                    <template slot="title"><img src="../../img/wx_hd_hongbao.png">红包类</template>
                    <el-menu-item index="3-1"><a :href="wxurl[2].type['1'].link[17].url" target="huodong">粉丝红包</a></el-menu-item>
                    <el-menu-item index="3-2"><a :href="wxurl[2].type['1'].link[18].url" target="huodong">微信红包</a></el-menu-item>
                    <el-menu-item index="3-3"><a :href="wxurl[2].type['1'].link[19].url" target="huodong">合体红包</a></el-menu-item>
                </el-submenu>
                <el-submenu index="4">
                    <template slot="title"><img src="../../img/wx_quweihuodong.png">趣味类</template>
                    <el-menu-item index="4-1"><a :href="wxurl[2].type['1'].link[16].url" target="huodong">趣味测试</a></el-menu-item>
                    <el-menu-item index="4-2"><a :href="wxurl[2].type['1'].link[15].url" target="huodong">一战到底</a></el-menu-item>
                    <el-menu-item index="4-3"><a :href="wxurl[2].type['1'].link[20].url" target="huodong">我要上头条</a></el-menu-item>
                </el-submenu>
                <el-submenu index="5">
                    <template slot="title"><img src="../../img/wx_hd_youhui.png">促销类</template>
                    <el-menu-item index="5-1"><a :href="wxurl[3].type['1'].link[0].url" target="huodong">微众筹</a></el-menu-item>
                    <el-menu-item index="5-2"><a :href="wxurl[3].type['1'].link[1].url" target="huodong">微秒杀</a></el-menu-item>
                    <el-menu-item index="5-3"><a :href="wxurl[3].type['1'].link[2].url" target="huodong">微砍价</a></el-menu-item>
                    <el-menu-item index="5-4"><a :href="wxurl[3].type['1'].link[3].url" target="huodong">微拍卖</a></el-menu-item>
                    <el-menu-item index="5-5"><a :href="wxurl[3].type['1'].link[4].url" target="huodong">降价拍</a></el-menu-item>
                    <el-menu-item index="5-6"><a :href="wxurl[3].type['1'].link[5].url" target="huodong">拼团购</a></el-menu-item>
                    <el-menu-item index="5-7"><a :href="wxurl[3].type['1'].link[6].url" target="huodong">一元夺宝</a></el-menu-item>
                </el-submenu>
                <el-submenu index="6">
                    <template slot="title"><img src="../../img/wx_hd_h5.png">H5营销</template>
                    <el-menu-item index="6-1"><a :href="wxurl[2].type['3'].link[1].url" target="huodong">我的H5游戏</a></el-menu-item>
                    <el-menu-item index="6-2"><a :href="wxurl[2].type['3'].link[2].url" target="huodong">游戏库</a></el-menu-item>
                    <el-menu-item index="6-3"><a :href="wxurl[2].type['3'].link[3].url" target="huodong">我的H5活动</a></el-menu-item>
                    <el-menu-item index="6-4"><a :href="wxurl[2].type['3'].link[4].url" target="huodong">活动库</a></el-menu-item>
                    <el-menu-item index="6-5"><a :href="wxurl[2].type['3'].link[0].url" target="huodong">H5游戏设置</a></el-menu-item>
                </el-submenu>
            </el-menu>
        </div>
        <div class="right1">
            <iframe :src="wxurl[2].type['1'].link[0].url" width="100%" height="100%" frameborder="0" seamless="seamless" name="huodong"></iframe>
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
                this.$store.commit("sethelpid", "choujianglei")
            }
            if (index == 2) {
                this.$store.commit("sethelpid", "jizanlei")
            }
            if (index == 3) {
                this.$store.commit("sethelpid", "hongbaolei")
            }
            if (index == 4) {
                this.$store.commit("sethelpid", "quweilei")
            }
            if (index == 5) {
                this.$store.commit("sethelpid", "chuxiaolei")
            }
            if (index == 6) {
                this.$store.commit("sethelpid", "h5yingxiao")
            }
        }
    },
    created: function () {
        this.$store.commit("sethelpid", "choujianglei")

        var wx_zshd = this.pdqx(["wx_zshd", "wx"]);
        if (!wx_zshd) {
            this.$router.push({ name: 'worktody' })
        }
    }
}
</script>