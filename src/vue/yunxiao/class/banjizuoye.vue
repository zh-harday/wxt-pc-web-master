<template>
    <div>
        <!--班级作业列表-->
        <el-table :data="homemorkList.work" style="width: 100%" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="time" label="发布时间">
                <template scope="scope">
                    {{ scope.row.time | yyyy_mm_dd_M_S }}
                </template>
            </el-table-column>
            <el-table-column prop="title" label="作业标题">
            </el-table-column>
            <el-table-column prop="subject_name" label="相关课程">
            </el-table-column>
            <el-table-column prop="teacher_name" label="发布人">
            </el-table-column>
            <el-table-column prop="" label="收到作业">
            </el-table-column>
            <el-table-column prop="" label="批阅进度">
            </el-table-column>
            <el-table-column label="操作" width="280">
                <template scope="scope">
                    <el-button type="primary" size="mini" @click="chakanxiangqing(scope.row.id,scope.row.teacher_name)" v-if="yx_bjgl_my_view">查看详情</el-button>
                    <el-button type="info" size="mini" @click="dianpingzuoye(scope.row.id,scope.row.teacher_name)" v-if="yx_bjgl_my_qtxx">作业点评</el-button>
                    <el-button type="danger" size="mini" @click="delhomework(scope.row.id)" v-if="yx_bjgl_my_qtxx">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
    
        <!--分页-->
        <div class="fenye" v-if="homemorkList.count > homemorkList.work.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="current_page" :page-size="pageSize" layout="total, prev, pager, next" :total="homemorkList.count">
            </el-pagination>
        </div>
    
        <!--作业详情-->
        <el-dialog title="作业详情" v-model="zuoyexiangqing">
            <div class="tk_box">
                <h1>{{ homemorkDetail.title }}</h1>
                <p>
                    <s>发布人：{{ homemorkDetail.teacher }}</s>
                    <s>发布时间：{{ homemorkDetail.time | yyyy_mm_dd_M_S }}</s>
                </p>
                <p>摘要：{{ homemorkDetail.info.length>0?homemorkDetail.info:'无' }}</p>
                <div class="content_box_yx" style="margin-bottom:10px" v-html="homemorkDetail.body"></div>
            </div>
            <div class="tk_box1" v-if="false">
                <h2>暂未查看</h2>
                <ul>
                    <li v-for="n in 4">
                        <div class="tk_left">
                            <img src="../../../img/photo.jpg" alt="">
                        </div>
                        <div class="tk_right">
                            <h1>
                                露露
                                <el-button type="text">标记为已读</el-button>
                            </h1>
                            <p>电话：13809452165</p>
                        </div>
                    </li>
                </ul>
                <h2>已查看</h2>
                <div>
                    <el-tooltip class="item" effect="dark" content="15319333285" placement="bottom" v-for="n in 11">
                        <el-tag type="primary">阿九</el-tag>
                    </el-tooltip>
                </div>
                <h2>待点评</h2>
                <div>
                    <el-tooltip class="item" effect="dark" content="15319333285" placement="bottom" v-for="n in 11">
                        <el-tag type="warning">阿九</el-tag>
                    </el-tooltip>
                </div>
            </div>
        </el-dialog>
        <!--点评作业-->
        <el-dialog title="作业点评" v-model="zuoyedianping">
            <div class="dp_box">
                <div class="dp_left">
                    <el-input placeholder="搜索" icon="search" v-model="search_xy" @input="searchXueyuan">
                    </el-input>
                    <ul>
                        <li v-for="(item,index) in homemorkDP.dp" @click="dpItemclick(item,index)" :class="{active:stuindex == index}">
                            <div class="dp_li_box1">
                                <img :src="item.face" alt="">
                            </div>
                            <div class="dp_li_box2">
                                {{ item.student_name }}
                            </div>
                            <div class="dp_li_box2">
                                <el-tag type="success" v-if="item.status == 2">已提交</el-tag>
                                <el-tag type="danger" v-else>未提交</el-tag>
                            </div>
                            <div class="dp_li_box3" :class="{active:item.is_comments ==1}">
                                <p v-if="item.is_comments != 1">
                                    <i class="el-icon-star-on"></i> x ?</p>
                                <p v-else>
                                    <i class="el-icon-star-on"></i> x {{ item.score }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="dp_right">
                    <h1 class="zydp_title">
                        <span>学员作业</span>
                        <el-button type="primary" size="mini" @click="chakanxiangqing(work_id,teacher_name)">查看作业题目</el-button>
                    </h1>
                    <div class="zydp_box1">
                        <div class="zydp_box1_left">
                            <span>
                                <img :src="daidianping.face" alt="">
                            </span>
                            <i>{{ daidianping.student_name }}</i>
                        </div>
                        <div class="zydp_box1_right">
                            <div class="zuoyetishi" v-if="daidianping.status != 2">
                                <div>该学员还未查看/提交作业</div>
                            </div>
                            <div class="zuoyebox" v-if="daidianping.status == 2">
                                <p>{{ daidianping.student_work }}</p>
                                <div class="photoandmusic">
                                    <div class="photo" v-if="daidianping.student_work_file.img.length > 0">
                                        <span v-for="item in daidianping.student_work_file.img" @click="showImgFun">
                                            <img :src="item" alt="">
                                        </span>
                                    </div>
                                    <div class="photo music" v-if="daidianping.student_work_file.music.length > 0">
                                        <span @click="playMusic(item,index)" v-for="(item,index) in daidianping.student_work_file.music">
                                            <img src="../../../img/luyinyulan_a1.png" alt="" v-if="playindex == index">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="zydp_title">
                        <span>老师点评</span>
                    </div>
                    <div v-if="daidianping.is_comments != 1 ">
                        <div class="zuoyepingfen">
                            <span>作业评分：</span>
                            <el-rate v-model="dp.star"></el-rate>
                        </div>
                        <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 5}" placeholder="请输入评语" v-model="dp.text">
                        </el-input>
                        <div class="photoboxs">
                            <span v-for="(item,index) in imgArr">
                                <i @click="delImagFun(index)"></i>
                                <b>
                                    <img :src="item" alt="">
                                </b>
                            </span>
                            <s v-show="imgArr.length < 3">
                                <form action="" enctype="multipart/form-data" ref="zyform">
                                    <input type="file" name="file" @change="uploadimg">
                                </form>
                            </s>
                        </div>
                        <div class="dp_btom">
                            <el-button type="primary" size="small" @click="fabudianpingFun">发送点评</el-button>
                        </div>
                    </div>
    
                    <div style="margin-bottom:10px" v-if="daidianping.is_comments == 1" v-html="daidianping.comments">
                    </div>
                    <div class="photoandmusic" v-if="daidianping.is_comments == 1 && daidianping.comments_file != null">
                        <div class="photo" v-if="daidianping.comments_file.img.length > 0">
                            <span v-for="item in daidianping.comments_file.img" @click="showImgFun">
                                <img :src="item" alt="">
                            </span>
                        </div>
                        <div class="photo music" v-if="daidianping.comments_file.music.length > 0">
                            <span @click="playMusic(item,index)" v-for="(item,index) in daidianping.comments_file.music">
                                <img src="../../../img/luyinyulan_a1.png" alt="" v-if="playindex == index">
                            </span>
                        </div>
                    </div>
    
                    <!--<div v-if="daidianping.status == null" style="color:red">
                            学生未提交作业，不能点评。
                        </div>-->
                </div>
            </div>
        </el-dialog>
        <album :list="imgArrays" :change="showAlbum"></album>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            zuoyexiangqing: false,
            zuoyedianping: false,
            teacher_name: "",
            search_xy: "",
            loading: false,
            current_page: 1,
            pageSize: 15,
            stuindex: 0,
            dp: {
                star: 5,
                text: ""
            },
            homemorkList: {
                work: []
            },
            homemorkDetail: {
                info: ""
            },
            homemorkDP: {
                info: "",
                dp: []
            },
            homemorkDPs: {},
            daidianping: {},
            work_id: "",//作业id
            radio: new Audio(),
            playindex: -1,
            interval: "",
            imgArr: [],
            imgArrays: [],
            showAlbum: 0,

            yx_bjgl_my_view: false,
            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        //yulan bigimg
        showImgFun: function (e) {
            var self = this;
            var arr = [];
            if (e.target.tagName == 'IMG') {
                var imgList = e.target.parentNode.parentNode.querySelectorAll("img");
                for (var i = 0; i < imgList.length; i++) {
                    arr.push(imgList[i].src)
                }
            }
            self.imgArrays = arr;
            self.showAlbum = +new Date();
        },
        //删除图片
        delImagFun: function (index) {
            this.imgArr.splice(index, 1);
        },
        //上传图片
        uploadimg: function () {
            var self = this;
            if (self.imgArr.length >= 3) {
                self.$message({
                    showClose: true,
                    message: "最多上传三张图片",
                    type: "warning"
                })
                return;
            }
            var form = this.$refs.zyform;
            var formData = new FormData(form);
            self.$http.post("/api/file/upload", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.imgArr.push(data.data.data.cos_path);
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
        //播放语音
        playMusic: function (src, index) {
            var self = this;
            self.playindex = index;
            if (self.radio.paused) {
                self.radio.src = src;
                self.radio.play();
                self.playAnimate();
            } else {
                self.radio.pause();
                self.playindex = -1;
                clearInterval(self.interval);
            }
        },
        playAnimate: function () {
            var self = this;
            self.interval = setInterval(function () {
                if (self.radio.ended) {
                    self.playindex = -1;
                    clearInterval(self.interval);
                }
            }, 100)
        },
        handleCurrentChange: function (val) {
            this.current_page = val;
            this.getHomeworkList();
        },
        //发表点评
        fabudianpingFun: function () {
            var self = this;
            if (!self.daidianping.student_id) {
                self.$message({
                    showClose: true,
                    message: "请选择未点评的学员",
                    type: "warning"
                })
                return;
            }
            if (self.dp.text.trim() == "" && self.imgArr.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请填写评语或上传图片",
                    type: "warning"
                })
                return;
            }
            var comments_file = {
                img: self.imgArr,
                music: []
            }
            comments_file = JSON.stringify(comments_file);
            var formData = new FormData();
            formData.append("uid", self.daidianping.student_id);
            formData.append("score", self.dp.star);
            formData.append("comments", self.dp.text);
            formData.append("comments_file", comments_file);
            self.$http.post("/api/classs/" + self.$route.params.id + "/work/" + self.work_id + "/comments", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.getdianpingFun(self.work_id);
                        self.daidianping = {};
                        self.dp = {
                            star: 5,
                            text: ""
                        };
                        self.imgArr = [];
                    } else {
                        self.loadingdP = false;
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.loadingdP = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //学员点击
        dpItemclick: function (item, id) {
            var self = this;
            self.stuindex = id;
            self.daidianping = item;
        },
        //点评作业
        dianpingzuoye: function (id, name) {
            this.stuindex = -1;
            this.zuoyedianping = true;
            this.teacher_name = name;
            this.daidianping = {};
            this.homemorkDP = {
                info: "",
                dp: []
            };
            this.work_id = id;
            this.getdianpingFun(id);
        },
        //查看作业详情
        chakanxiangqing: function (id, name) {
            var self = this;
            self.zuoyexiangqing = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/work/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.homemorkDetail = data.data.data;
                        self.homemorkDetail.teacher = name;
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
        //作业点评情况
        getdianpingFun: function (id) {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/work/" + id + "/dp")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        var yarr = [];
                        var narr = [];
                        for (var i = 0; i < data.data.data.dp.length; i++) {
                            data.data.data.dp[i].face = data.data.data.dp[i].face ? ("http://wx.eduwxt.com" + data.data.data.dp[i].face) : ("http://img.eduwxt.com/assets/images/users/avatar-11.jpg");
                            if (data.data.data.dp[i].is_comments) {
                                yarr.push(data.data.data.dp[i]);
                            } else {
                                narr.push(data.data.data.dp[i]);
                            }
                        }
                        data.data.data.dp = narr.concat(yarr);
                        self.homemorkDP = data.data.data;
                        self.homemorkDPs.dp = data.data.data.dp;
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
        //搜索学员
        searchXueyuan: function (val) {
            var self = this;
            self.homemorkDP.dp = [];
            for (var i = 0; i < self.homemorkDPs.dp.length; i++) {
                if (self.homemorkDPs.dp[i].student_name.indexOf(val.trim()) != -1) {
                    self.homemorkDP.dp.push(self.homemorkDPs.dp[i])
                }
            }
            if (self.homemorkDP.dp.length == 0) {
                self.homemorkDP.dp = self.homemorkDPs.dp;
            }
        },
        //删除作业
        delhomework: function (id) {
            var self = this;
            self.$confirm('此操作将永久删除该条作业, 是否继续?', '删除班级作业', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + self.$route.params.id + "/work/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getHomeworkList();
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

            }).catch(function () {

            });
        },
        //班级作业列表
        getHomeworkList: function () {
            var self = this;
            self.loading = true;
            self.$http.get("/api/classs/" + self.$route.params.id + "/work?&page=" + self.current_page + "&limit=" + self.pageSize)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.homemorkList = data.data.data;
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }

    },
    created: function () {
        this.getHomeworkList();

        //查看详情权限
        this.yx_bjgl_my_view = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_all", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.dp_box {
    padding-top: 10px;
    overflow: hidden;
    border-top: 1px solid #ddd;
    position: relative;
}


.dp_left {
    width: 220px;
    height: 100%;
    float: left;
    position: relative;
    top: 0;
    .el-input {
        width: 95%;
    }
    .pldianping {
        display: block;
        margin: 10px 0;
    }
    ul {
        width: 100%;
        height: auto;
        max-height: 400px;
        overflow: hidden;
        overflow-y: auto;
        margin-top: 10px;
        li {
            overflow: hidden;
            padding: 5px 0;
            cursor: pointer;
            &.active {
                background-color: #f2f2f2;
                .dp_li_box1 {
                    border: 1px solid @color;
                }
                .dp_li_box2 {
                    color: @color;
                }
            }
            &:hover {
                background-color: #f8f9f9;
                .dp_li_box1 {
                    border: 1px solid @color;
                }
                .dp_li_box2 {
                    color: @color;
                }
            }
            .dp_li_box1 {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                overflow: hidden;
                float: left;
                margin-top: 3px;
                margin-right: 10px;
                box-sizing: border-box;
                border: 1px solid #fff;
                img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
            }
            .dp_li_box2 {
                display: block;
                height: 40px;
                line-height: 40px;
                color: @c1;
                font-size: 14px;
                float: left;
                margin-right: 5px;
            }
            .dp_li_box3 {
                display: block;
                float: right;
                height: 40px;
                line-height: 40px;
                color: @c1;
                font-size: 14px;
                padding-right: 10px;
                color: #d5d5d5;
                &.active {
                    color: #ffdc00;
                }
            }
        }
    }
}

.dp_right {
    width: 600px;
    float: right;
    .zydp_title {
        font-weight: normal;
        overflow: hidden;
        margin-bottom: 12px;
        >span {
            display: block;
            font-size: @h3;
            color: @c1;
            float: left;
        }
        >button {
            float: right;
        }
    }
    .zydp_box1 {
        overflow: hidden;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
        .zydp_box1_left {
            width: 70px;
            height: 70px;
            float: left;
            >span {
                display: block;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                overflow: hidden;
                border: 1px solid @color;
                margin: 0 auto;
                img {
                    display: block;
                    width: 100%;
                    min-height: 100%;
                }
            }
            >i {
                display: block;
                padding-top: 5px;
                text-align: center;
                font-size: 14px;
                color: @c1;
                font-style: normal;
            }
        }
        .zydp_box1_right {
            float: left;
            width: 530px;
            box-sizing: border-box;
            >.zuoyetishi {
                padding-top: 10px;
                color: @wan;
                margin-bottom: 2px;
            }
            .zuoyebox {
                font-size: 14px;
                color: @c2;
                >p {
                    margin-bottom: 10px;
                }
            }
        }
    }
    .zuoyepingfen {
        overflow: hidden;
        margin-bottom: 10px;
        >span {
            display: block;
            float: left;
        }
        >div {
            float: left;
        }
    }
    .dp_btom {
        text-align: right;
        padding-top: 20px;
    }
}

.photoandmusic {
    overflow: hidden;

    .photo {
        float: left;
        overflow: hidden;
        span {
            display: block;
            width: 50px;
            height: 50px;
            overflow: hidden;
            float: left;
            margin-right: 12px;
            img {
                display: block;
                width: 100%;
                min-height: 100%;
            }
        }
        &.music {
            margin-right: 0;
            span {
                background-image: url(../../../img/luyinyulan.png);
                background-repeat: no-repeat;
                background-size: 100% 100%;
                img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
            }
        }
    }
}
</style>