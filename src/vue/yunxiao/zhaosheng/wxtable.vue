<template>
    <div>
        <h1 class="right_title">
                我的学员<br>
                <el-button type="success" size="small" @click="$router.push({name:'AddnewTable'})">添加新表单</el-button>
                <el-input placeholder="搜索" icon="search" v-model="option.search" @keyup.enter.native="serachFun" :on-icon-click="serachFun"
                    class="btn"></el-input>
            </h1>
        <div class="xy_serach_box">
            <div class="saixuan_box" v-if="myMessage.campus_id == 1">
                <span>校区：</span>
                <ul>
                    <li :class="{active:(option.campus_id == '')}" @click="canpus_click('')">全部</li>
                    <li :class="{active:(option.campus_id == item.id )}" v-for="item in campus" @click="canpus_click(item.id)">{{ item.name }}</li>
                </ul>
            </div>
            <div class="saixuan_box">
                <span>表单状态：</span>
                <ul>
                    <li :class="{active:option.status==''}" @click="clickStatus('')">全部</li>
                    <li :class="{active:option.status=='1'}" @click="clickStatus(1)">已启用</li>
                    <li :class="{active:option.status=='0'}" @click="clickStatus(0)">已停用</li>
                </ul>
            </div>
        </div>
        <ul class="wxtable_list">
            <li v-for="item in wxtable.form">
                <span>
                        <img src="../../../img/yx_zhaosheng_bmz.png" alt="" v-if="item.status == 1">
                        <img src="../../../img/yx_zhaosheng_yty.png" alt="" v-if="item.status == 0">
                    </span>
                <router-link :to="{name:'wxtableMsg',params:{id:item.id}}" tag="div" class="photobox">
                    <img :src="item.pic" alt="">
                    <span>表单ID：{{ item.id }}</span>
                </router-link>
                <h1>{{ item.title }}</h1>
                <p>已报名：{{ item.student_count }}人</p>
                <p class="bottom">
                    <span>浏览{{ item.view_count }}</span>
                </p>
                <div class="saomiao">
                    <img src="../../../img/yx_zhaosheng_ewm_a.png" alt="">
                    <img src="../../../img/yx_zhaosheng_ewm.png" alt="">
                    <span>扫码分享</span>
                    <div class="saomiao_box">
                        <span><img :src="item.qrcode" alt=""></span>
                        <p>URL
                            <el-input v-model="item.url" placeholder="请输入内容" size="small"></el-input>
                            <el-button type="primary" size="small" @click="copy($event)">复制</el-button>
                        </p>
                    </div>
                </div>
            </li>
        </ul>
        <div class="fenye" v-if="wxtable.count > wxtable.form.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="option.page" :page-size="option.limit" layout="total, prev, pager, next" :total="wxtable.count">
            </el-pagination>
        </div>
    </div>
</template>
<script>
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            //微信表单参数
            option: {
                limit: 8,
                page: 1,
                search: "",
                campus_id: "",
                status: ""
            },
            //微信表单
            wxtable: {
                form: []
            },
            form: {
                campus_id: ""
            }
        }
    },
    methods: {
        handleCurrentChange:function(val){
            this.option.page = val;
            this.getWXtableList();
        },
        //获取微信表单列表
        getWXtableList: function () {
            var self = this;
            self.option.campus_id = self.myMessage.campus_id == "1" ? self.option.campus_id : self.myMessage.campus_id;
            self.$http.get("/api/free_form?limit=" + self.option.limit + "&page=" + self.option.page + "&search=" + self.option.search + "&campus_id=" + self.option.campus_id + "&status=" + self.option.status)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.wxtable = data.data.data;
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
        },
        //点击表单状态
        clickStatus: function (id) {
            this.option.search = "";
            this.option.page = 1;
            this.option.status = id;
            this.getWXtableList();
        },
        //点击校区
        canpus_click: function (id) {
            this.option.search = "";
            this.option.page = 1;
            this.option.campus_id = id;
            this.getWXtableList();
        },
        //搜索
        serachFun: function () {
            this.option.page = 1;
            this.option.campus_id = "";
            this.option.status = "";
            this.getWXtableList();
        },
        //复制
        copy: function (e) {
            var target = e.path[2].querySelector("div input");
            target.select();
            document.execCommand("Copy"); // 执行浏览器复制命令
            this.$message({
                message: '复制成功！',
                type: 'success'
            });
        }

    },
    created: function () {
        this.getWXtableList();

        //权限设置
        var yx_zszz_wxbd = this.pdqx(["yx_zszz", "yx_zszz_wxbd","yx"]);
        if (!yx_zszz_wxbd) {
            this.$router.push({ name: 'worktody' });
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.xy_serach_box {
    margin-bottom: 20px;
}

.el-select {
    margin-right: 10px;
    width: 150px;
}

.btn {
    width: 200px;
}

.wxtable_list {
    overflow: hidden;
    >li {
        width: 300px;
        height: 265px;
        background-color: #fff;
        float: left;
        margin-right: 20px;
        margin-bottom: 20px;
        box-sizing: border-box;
        padding: 10px;
        position: relative;
        >span {
            display: block;
            position: absolute;
            width: 38px;
            height: 75px;
            top: 0;
            left: 10px;
            z-index: 1;
            img {
                display: block;
                width: 38px;
                height: 75px;
            }
        }
        >.photobox {
            width: 100%;
            height: 156px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            &:hover {
                >img {
                    transform: scale(1.2);
                }
            }
            img {
                display: block;
                width: 100%;
                min-height: 156px;
                transition: transform 200ms;
            }
            >span {
                display: block;
                position: absolute;
                left: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, .2);
                width: 100%;
                height: 20px;
                line-height: 20px;
                color: #fff;
                font-size: 12px;
                padding-left: 10px;
                box-sizing: border-box;
            }
        }
        >h1 {
            font-weight: normal;
            font-size: @h3;
            color: @c1;
            padding-top: 12px;
            .textOverflow();
            margin-bottom: 10px;
        }
        >p {
            padding-bottom: 10px;
            font-size: 14px;
            color: @color;
            &.bottom {
                >span {
                    margin-right: 10px;
                    color: @c2;
                    img {
                        height: 14px;
                        margin-right: 5px;
                    }
                }
            }
        }
        >.saomiao {
            width: 60px;
            height: 30px;
            position: absolute;
            bottom: 10px;
            right: 10px;
            >:first-child {
                display: none;
            }
            &:hover {
                .saomiao_box {
                    display: block;
                }
                >:first-child {
                    display: inline-block;
                }
                >:nth-child(2) {
                    display: none;
                }
            }
            >img {
                display: inline-block;
                width: 26px;
                height: 26px;
            }
            >span {
                display: inline-block;
                width: 30px;
                float: right;
                line-height: 1;
                padding-left: 2px;
                box-sizing: border-box;
            }
        }
    }
}

.saomiao_box {
    display: none;
    width: 200px;
    height: 150px;
    background-color: #fff;
    box-shadow: 0px 0px 20px 0px rgba(0, 0, 0, .1);
    position: absolute;
    top: -150px;
    right: 0;
    z-index: 2;
    border: 1px solid #ddd;
    >p {
        padding: 10px;
        .el-input {
            width: 100px;
        }
    }
    >span {
        display: block;
        width: 100%;
        height: 100px;
        border-bottom: 1px solid #eee;
        padding: 5px;
        box-sizing: border-box;
        img {
            display: block;
            height: 100%;
            margin: 0 auto;
        }
    }
}
</style>