<template>
    <div class="myHome_and_message">
        <div class="left">
            <div class="box_l1">
                <span><img src="../../img/notice_logo.png" alt=""></span>
                <div>
                    通知中心
                </div>
            </div>
            <div class="box_l2">
                <router-link :to="{name:'AllMessage'}" :class="{ active : $route.name=='AllMessage' }" tag="span">
                    <span>所有通知</span>
                </router-link>

                <router-link :to="{name:'SchoolMessage'}" :class="{ active : $route.name=='SchoolMessage' }" tag="span">
                    <span>员工通知</span>
                </router-link>

                <router-link :to="{name:'Systemmessage'}" :class="{ active : $route.name=='Systemmessage' }" tag="span">
                    <span>系统通知</span>
                </router-link>
            </div>

        </div>
        <div class="right">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
    module.exports = {
       
        computed: {
            notice_meau_id: function () {
                return this.$store.state.notice_meau_id;
            }
        },
        created: function () {
            this.$store.commit('setHeadID', 0);
            this.$store.commit('setHeadShow', true);
            this.$store.commit('setYXLeftMeauId', 0);
            this.$store.commit("sethelpid", "");// 帮助

            

            this.$store.commit("getNewMsgCount", this);//更新消息
        }
    }

</script>
<style lang="less" scoped>
    @import "../../less/color.less";
    .box_l1 {
        >div {
            line-height: 68px;
        }
    }
    
    .myHome_and_message {
        .left {
            .box_l1 {
                > span {
                    cursor: default;
                }
            }
        }
    }
</style>