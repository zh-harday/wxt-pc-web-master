<template>
    <div class="yx_class yx_classs">
        <div class="left">
            <h1>基础宣传
                        <a href=""></a>
                        <article>快速制作用于线上宣传的微网站及海报。</article>
                    </h1>
    
            <el-menu default-active="1-1" class="el-menu-vertical-demo" theme="light" unique-opened="true" @open="selectFun">
                <el-submenu index="1">
                    <template slot="title"><img src="../../img/wx_xc_page.png" alt="">微网站</template>
                    <el-menu-item index="1-1"><a :href="wxurl[1].type['0'].link[0].url" target="xuanchuan">首页配置</a></el-menu-item>
                    <el-menu-item index="1-2"><a :href="wxurl[1].type['0'].link[1].url" target="xuanchuan">模板管理</a></el-menu-item>
                    <el-menu-item index="1-3"><a :href="wxurl[1].type['0'].link[2].url" target="xuanchuan">分类管理</a></el-menu-item>
                    <el-menu-item index="1-4"><a :href="wxurl[1].type['0'].link[3].url" target="xuanchuan">文章管理</a></el-menu-item>
                    <el-menu-item index="1-5"><a :href="wxurl[1].type['0'].link[4].url" target="xuanchuan">首页幻灯片</a></el-menu-item>
                    <el-menu-item index="1-6"><a :href="wxurl[1].type['0'].link[5].url" target="xuanchuan">轮播背景图</a></el-menu-item>
                    <el-menu-item index="1-7"><a :href="wxurl[1].type['0'].link[6].url" target="xuanchuan">底部导航菜单</a></el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><img src="../../img/wx_xc_haibao.png">微场景</template>
                    <el-menu-item index="2-1"><a :href="wxurl[1].type['1'].link[0].url" target="xuanchuan">创建场景</a></el-menu-item>
                    <!--<el-menu-item index="2-2"><a href="http://lin.bupu.net/index.php?m=User&c=app&a=myAllScenes" target="xuanchuan">我的场景</a></el-menu-item>-->
                    <el-menu-item index="2-2"><a :href="wxurl[1].type['1'].link[1].url" target="xuanchuan">我的场景</a></el-menu-item>
                    <el-menu-item index="2-3"><a :href="wxurl[1].type['1'].link[2].url" target="xuanchuan">场景关键词</a></el-menu-item>
                </el-submenu>
                <el-submenu index="3">
                    <template slot="title"><img src="../../img/wx_xc_qudao.png">微渠道</template>
                    <el-menu-item index="3-1"><a :href="wxurl[1].type['2'].link[0].url" target="xuanchuan">推广海报</a></el-menu-item>
                    <el-menu-item index="3-2"><a :href="wxurl[1].type['2'].link[1].url" target="xuanchuan">渠道二维码</a></el-menu-item>
                    <el-menu-item index="3-3"><a :href="wxurl[1].type['2'].link[2].url" target="xuanchuan">二维码扫描分析</a></el-menu-item>
                </el-submenu>
                <el-submenu index="4">
                    <template slot="title"><img src="../../img/wx_tuwenxiaoxi.png">图文消息</template>
                    <el-menu-item index="4-1"><a :href="wxurl[1].type['3'].link[0].url" target="xuanchuan">关注回复</a></el-menu-item>
                    <el-menu-item index="4-2"><a :href="wxurl[1].type['3'].link[1].url" target="xuanchuan">文本回复</a></el-menu-item>
                    <el-menu-item index="4-3"><a :href="wxurl[1].type['3'].link[2].url" target="xuanchuan">语音回复</a></el-menu-item>
                    <el-menu-item index="4-4"><a :href="wxurl[1].type['3'].link[3].url" target="xuanchuan">多图文回复</a></el-menu-item>
                    <el-menu-item index="4-5"><a :href="wxurl[1].type['3'].link[4].url" target="xuanchuan">微信图文群发</a></el-menu-item>
                    <el-menu-item index="4-6"><a :href="wxurl[1].type['3'].link[5].url" target="xuanchuan">模板消息群发</a></el-menu-item>
                    <el-menu-item index="4-7"><a :href="wxurl[1].type['3'].link[6].url" target="xuanchuan">回答不上来配置</a></el-menu-item>
                    <el-menu-item index="4-8"><a :href="wxurl[1].type['3'].link[7].url" target="xuanchuan">原文链接生成工具</a></el-menu-item>
                </el-submenu>
            </el-menu>
        </div>
        <div class="right1">
            <iframe :src="wxurl[1].type['0'].link[0].url" width="100%" height="100%" frameborder="0" seamless="seamless" name="xuanchuan"></iframe>
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
                this.$store.commit("sethelpid", "weiwangzhan")
            }
            if (index == 2) {
                this.$store.commit("sethelpid", "weichangjing")
            }
            if (index == 3) {
                this.$store.commit("sethelpid", "weiqudao")
            }
            if (index == 4) {
                this.$store.commit("sethelpid", "tuwenxiaoxi")
            }
        }
    },
    created: function () {
        this.$store.commit("sethelpid", "weiwangzhan")

        var wx_jcxc = this.pdqx(["wx_jcxc", "wx"]);
        if (!wx_jcxc) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>