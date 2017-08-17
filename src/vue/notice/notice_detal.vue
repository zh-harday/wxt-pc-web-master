<template>
    <div class="notice_d">
        <h1 class="title">通知详情</h1>
        <div class="content">
            <h1 class="cont_title">
                <span>{{ datail.title }}</span>
                <time>{{ datail.seed_time | times }}</time>
            </h1>
            <p class="con_p">
                <span>接受范围：{{ datail.campus_name }}</span>
            </p>
            <div class="cont" v-html="datail.message"></div>
        </div>
    </div>
</template>
<script>
    module.exports = {
        data: function () {
            return {
                datail: window.sessionStorage.getItem("notice_detail"+this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("notice_detail"+this.$route.params.id)):{}
            }
        },
        computed:{
            campus:function(){
                return this.$store.state.campus;
            }
        },
        watch:{
            $route:function(){
                this.getDetail();
            }
        },
        methods: {

            getDetail: function () {
                var self = this;
                self.$http.get("/api/message/" + self.$route.params.id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            for(var i=0;i<self.campus.length;i++){
                                if(data.data.data.campus_id == 1){
                                    data.data.data.campus_name = "所有校区";
                                }
                                if(data.data.data.campus_id == self.campus[i].id){
                                    data.data.data.campus = self.campus[i].name;
                                }
                            }
                            self.datail = data.data.data;
                            window.sessionStorage.setItem("notice_detail"+this.$route.params.id,JSON.stringify(self.datail));
                        } else {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }, function (err) {
                        self.$message({
                            showClose: true,
                            message: "网络错误",
                            type: "error"
                        })
                    })
            }
        },
        created: function () {
            this.$store.commit("getNewMsgCount",this);
            this.getDetail();
        }
    }

</script>
<style lang="less" scoped>
    @import "../../less/color.less";
    .title {
        font-size: @h3;
        color: @c1;
        border-bottom: 1px solid #9d9d9d;
        padding-bottom: 14px;
        position: relative;
        margin-bottom: 20px;
        font-weight: normal;
    }
    .foot{
        padding-top: 20px;
        text-align: right;
    }
    .content {
        padding-bottom: 20px;
        .cont_title {
            overflow: hidden;
            font-weight: normal;
            margin-bottom: 5px;
            span {
                display: block;
                font-size: @h2;
                color: @c1;
                float: left;
            }
            time {
                display: block;
                float: right;
                font-size: 14px;
                color: @c3;
            }
        }
        .con_p {
            font-size: 14px;
            color: @c3;
            padding-right: 10px;
            margin-bottom: 20px;
        }
        .cont {
            img {
                display: block;
                max-width: 580px;
                margin: 10px auto;
            }
        }
    }
</style>