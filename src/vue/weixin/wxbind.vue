<template>
    <div class="wxbind">
        <iframe src="http://wx.eduwxt.com/index.php?g=School&m=Weixin&a=v2pre_auth_code" width="100%" height="100%" frameborder="0" seamless="seamless" name="wxbind"></iframe>
    </div>
</template>
<script>
    module.exports = {
        created:function(){
            this.$store.commit('setHeadID', 3);
            this.$store.commit('setHeadShow', true);
        }
    }
</script>
<style>
.wxbind {
    height: 100%;
    overflow: hidden;
    padding-top: 60px;
    box-sizing: border-box;
}
</style>

