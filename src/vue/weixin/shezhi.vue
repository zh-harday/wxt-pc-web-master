<template>
    <div class="yx_class yx_classs">
        <div class="left">
            <h1>微信设置
                                <a href=""></a>
                                <article>在此进行公众号与微校通系统的匹配设置。</article>
                            </h1>
            <el-menu default-active="1-1" class="el-menu-vertical-demo" theme="light" unique-opened="true" @open="selectFun">
                <el-submenu index="1">
                    <template slot="title"><img src="../../img/wx_set_chushihua.png" alt="">初始设置</template>
                    <el-menu-item index="1-1"><a :href="wxurl[0].type['0'].link[0].url" target="shezhi">公众号设置</a></el-menu-item>
                    <el-menu-item index="1-2"><a :href="wxurl[0].type['0'].link[1].url" target="shezhi">校区LBS定位</a></el-menu-item>
                    <el-menu-item index="1-3"><a :href="wxurl[0].type['0'].link[2].url" target="shezhi">模板消息配置</a></el-menu-item>
                    <el-menu-item index="1-4"><a :href="wxurl[0].type['0'].link[3].url" target="shezhi">快捷关注配置</a></el-menu-item>
                    <el-menu-item index="1-5"><a :href="wxurl[0].type['0'].link[4].url" target="shezhi">IP黑名单防刷票</a></el-menu-item>
                    <el-menu-item index="1-6"><a :href="wxurl[0].type['0'].link[5].url" target="shezhi">自动获取粉丝权限</a></el-menu-item>
                </el-submenu>
                <el-submenu index="2">
                    <template slot="title"><img src="../../img/wx_set_fensi.png">粉丝设置</template>
                    <el-menu-item index="2-1"><a :href="wxurl[0].type['1'].link[0].url" target="shezhi">粉丝管理</a></el-menu-item>
                    <el-menu-item index="2-2"><a :href="wxurl[0].type['1'].link[1].url" target="shezhi">分组管理</a></el-menu-item>
                    <el-menu-item index="2-3"><a :href="wxurl[0].type['1'].link[2].url" target="shezhi">粉丝行为管理</a></el-menu-item>
                    <el-menu-item index="2-4"><a :href="wxurl[0].type['1'].link[3].url" target="shezhi">会员信息管理</a></el-menu-item>
                </el-submenu>
                <el-submenu index="3">
                    <template slot="title"><img src="../../img/wx_set_kefu.png">客服管理</template>
                    <el-menu-item index="3-1"><a :href="wxurl[0].type['2'].link[0].url" target="shezhi">微信人工服务</a></el-menu-item>
                    <el-menu-item index="3-2"><a :href="wxurl[0].type['2'].link[1].url" target="shezhi">网页客服管理</a></el-menu-item>
                    <el-menu-item index="3-3"><a :href="wxurl[0].type['2'].link[2].url" target="shezhi">聊天推广内容</a></el-menu-item>
                    <el-menu-item index="3-4"><a :href="wxurl[0].type['2'].link[2].url" target="shezhi">聊天个人板块</a></el-menu-item>
                </el-submenu>
                <el-submenu index="4">
                    <template slot="title"><img src="../../img/wx_set_zidingyi.png">自定义菜单</template>
                    <el-menu-item index="4-1"><a :href="wxurl[0].type['3'].link[0].url" target="shezhi">普通自定义菜单</a></el-menu-item>
                    <el-menu-item index="4-2"><a :href="wxurl[0].type['3'].link[1].url" target="shezhi">个性化自定义菜单</a></el-menu-item>
                </el-submenu>
            </el-menu>
        </div>
        <div class="right1">
            <iframe :src="wxurl[0].type['0'].link[0].url" width="100%" height="100%" frameborder="0" seamless="seamless" name="shezhi"></iframe>
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
                this.$store.commit("sethelpid", "chushihuashezhi")
            }
            if (index == 2) {
                this.$store.commit("sethelpid", "fensishezhi")
            }
            if (index == 3) {
                this.$store.commit("sethelpid", "kefushezhi")
            }
            if (index == 4) {
                this.$store.commit("sethelpid", "zidingyicaidan")
            }
        }
    },
    created: function () {
        this.$store.commit("sethelpid", "chushihuashezhi")

        var wx_wxsz = this.pdqx(["wx_wxsz", "wx"]);
        if (!wx_wxsz) {
            this.$router.push({ name: 'worktody' })
        }
    }
}
</script>