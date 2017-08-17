<template>
    <div>
        <h1 v-if="$route.name == 'AllMessage' " class="title">所有通知
            <span></span>
        </h1>
        <h1 v-if="$route.name == 'SchoolMessage'" class="title">员工通知
            <span></span>
            <el-button type="success" size="small" @click="$router.push({name:'Release'})" v-if="yx_yggl_msg">发布新通知</el-button>
        </h1>
        <h1 v-if="$route.name == 'Systemmessage'" class="title">系统通知
            <span></span>
        </h1>
        <ul>
            <router-link v-for="list in messageList.message" tag="li" :to="{name:'notice_detail',params:{id:list.id}}" :class="{active:list.statue != 1 }">
                <h1>
                    <span>{{ list.title }}</span>
                    <time>{{ list.seed_time | times }}</time>
                    <s v-if="list.type != 0">发布人：{{ list.seed_name }}</s>
                    <s v-else>同城学官方</s>
                </h1>
                <p>
                    <span>接收范围：{{ list.campus_name }}</span>
                </p>
            </router-link>
        </ul>
        <div class="fenye" v-if="messageList.count > messageList.message.length ">
            <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="obj.limit" layout="total, prev, pager, next" :total="messageList.count">
            </el-pagination>
        </div>
    </div>
</template>
<script>
module.exports = {
    watch: {
        $route: function (val) {
            if (val.name == 'AllMessage') {
                this.obj.type = "";
                this.obj.search = "";
                this.obj.page = "1";
            }
            if (val.name == 'SchoolMessage') {
                this.obj.type = "1";
                this.obj.search = "";
                this.obj.page = "1";
            }
            if (val.name == 'Systemmessage') {
                this.obj.type = "0";
                this.obj.search = "";
                this.obj.page = "1";
            }
            this.getMessageList();
        }
    },
    data: function () {
        return {
            current_page: 1,
            datas: 50,

            obj: {
                limit: 10,
                page: 1,
                search: "",
                type: ""
            },

            messageList: {
                message: []
            },

            //权限
            yx_yggl_msg: false
        }
    },
    methods: {
        handleCurrentChange: function (val) {
            this.obj.page = val;
            this.getMessageList();
        },
        //获取消息列表
        getMessageList: function () {
            var self = this;
            self.$http.get("/api/message?limit=" + self.obj.limit + "&page=" + self.obj.page + "&search=" + self.obj.search + "&type=" + self.obj.type)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        console.log(data.data.data)
                        self.messageList = data.data.data;
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
        var self = this;
        self.getMessageList();

        self.yx_yggl_msg = self.pdqx(["yx_yggl", "yx_yggl_msg", "yx"]);

    }
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
.title {
    font-size: 16px;
    color: @c1;
    border-bottom: 1px solid #9d9d9d;
    padding-bottom: 14px;
    position: relative;
    font-weight: normal;
    button {
        position: absolute;
        right: 0;
        top: 0;
    }
    span {
        color: @c2;
        font-weight: normal;
    }
}

li {
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
    cursor: pointer;
    &.active {
        h1 {
            span {
                color: @c1;
                &:after {
                    content: "(未读)"
                }
            }
        }
    }
    h1 {
        overflow: hidden;
        span {
            float: left;
            max-width: 440px;
            font-size: 14px;
            color: @c3;
            font-weight: normal;
            .textOverflow();
        }
        s {
            float: right;
            font-weight: normal;
            font-size: 14px;
            color: @c3;
            text-decoration: none;
        }
        time {
            font-size: 14px;
            color: @c3;
            float: right;
            font-weight: normal;
            padding-left: 10px;
        }
    }
    p {
        padding-top: 10px;
        font-size: 14px;
        color: @c3;
        clear: both;
        span {
            padding-right: 10px;
        }
    }
}
</style>