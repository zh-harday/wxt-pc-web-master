<template>
    <div class="yx_setting">
        <div class="left">
            <h1>
                系统设置
                <article>对学校基础信息进行初始设置</article>
            </h1>
            <router-link :to="{name:'campus'}" :class="{ active : setting_meau_id==1 }">
                <img src="../../../img/yx_set_xiaoqu.svg" alt="" v-show="setting_meau_id!=1">
                <img src="../../../img/yx_set_xiaoqu_a.svg" alt="" v-show="setting_meau_id==1">
                <span>校区设置</span>
            </router-link>
    
            <router-link :to="{name:'classroom'}" :class="{ active : setting_meau_id==3 }">
                <img src="../../../img/yx_set_classroom.png" alt="" v-show="setting_meau_id!=3">
                <img src="../../../img/yx_set_classroom_a.png" alt="" v-show="setting_meau_id==3">
                <span>教室设置</span>
            </router-link>
    
            <router-link :to="{name:'curriculums'}" :class="{ active : setting_meau_id==4 }">
                <img src="../../../img/yx_set_kechengleibie.svg" alt="" v-show="setting_meau_id!=4">
                <img src="../../../img/yx_set_kechengleibie_a.svg" alt="" v-show="setting_meau_id==4">
                <span>课程类别</span>
            </router-link>
    
            <router-link :to="{name:'Finance_set'}" :class="{ active : setting_meau_id==5 }">
                <img src="../../../img/yx-set_caiwu.svg" alt="" v-show="setting_meau_id!=5">
                <img src="../../../img/yx-set_caiwu_a.svg" alt="" v-show="setting_meau_id==5">
                <span>财务账户</span>
            </router-link>
    
            <router-link :to="{name:'fees'}" :class="{ active : setting_meau_id==6 }">
                <img src="../../../img/yx_set_xuezafei.svg" alt="" v-show="setting_meau_id!=6">
                <img src="../../../img/yx_set_xuezafei_a.svg" alt="" v-show="setting_meau_id==6">
                <span>学杂费用</span>
            </router-link>
    
            <router-link :to="{name:'track'}" :class="{ active : setting_meau_id==8 }">
                <img src="../../../img/yx_set_zhaosheng.png" alt="" v-show="setting_meau_id!=8">
                <img src="../../../img/yx_set_zhaosheng_a.png" alt="" v-show="setting_meau_id==8">
                <span>学员服务</span>
            </router-link>
    
            <router-link :to="{name:'organization'}" :class="{ active : setting_meau_id==7 }">
                <img src="../../../img/yx_set_zuzhi.svg" alt="" v-show="setting_meau_id!=7">
                <img src="../../../img/yx_set_zuzhi_a.svg" alt="" v-show="setting_meau_id==7">
                <span>组织权限</span>
            </router-link>
    
            <router-link :to="{name:'moban'}" :class="{ active : setting_meau_id==2 }">
                <img src="../../../img/yx_set_moban.svg" alt="" v-show="setting_meau_id!=2">
                <img src="../../../img/yx_set_moban_a.svg" alt="" v-show="setting_meau_id==2">
                <span>模板消息</span>
            </router-link>
    
            <router-link :to="{name:'clean'}" :class="{ active : setting_meau_id==9 }" v-if="myMessage.group_id == 1">
                <img src="../../../img/clean_a.svg" alt="" v-show="setting_meau_id!=9">
                <img src="../../../img/clean.svg" alt="" v-show="setting_meau_id==9">
                <span>清理数据</span>
            </router-link>
    
        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {

    computed: {
        setting_meau_id: function () {
            return this.$store.state.setting_meau_id
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }

    },
    created: function () {
        this.$store.commit('setYXLeftMeauId', 9);
        var gzkb_tsyj = this.pdqx(["yx_xtsz", "yx"]);
        if (!gzkb_tsyj) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>