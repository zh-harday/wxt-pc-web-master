<template>
    <div style="height:100%;overflow:hidden">
        <wxleftmeau></wxleftmeau>
        <div class="rightbox" id="rightbox">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
module.exports = {
    created: function () {
        this.$store.commit('setHeadID', 3);
        this.$store.commit('setHeadShow', true);
        this.$store.commit('setYXLeftMeauId', 0);

        this.$store.commit("getNewMsgCount", this);//更新消息

    }
}
</script>