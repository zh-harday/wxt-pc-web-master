<template>
    <div>
        <el-dialog title="课后点评" v-model="kehoudianpingshow">
            <div class="dp_box">
                <div class="dp_left">
                    <el-input placeholder="搜索" icon="search" v-model="search_xy" @input="searchXueyuan">
                    </el-input>
                    <el-button type="primary" size="small" class="pldianping" v-show="pldp" @click="dancipiliangqiehuang(false)">批量点评</el-button>
                    <el-button type="primary" size="small" class="pldianping" v-show="!pldp" @click="dancipiliangqiehuang(true)">单次点评</el-button>
                    <ul>
    
                        <li v-for="item in dianpingStudent.student" @click="dianpingTOright(item)">
                            <div class="dp_li_box0" v-show="!pldp && !item.time " :class="{ active : item.active }">
                            </div>
                            <div class="dp_li_box1">
                                <img :src="item.face" alt="">
                            </div>
                            <div class="dp_li_box2">
                                {{ item.student_name }}
                            </div>
                            <div class="dp_li_box3" :class="{ active : item.time }">
                                <p v-if="!item.time">
                                    <i class="el-icon-star-on"></i> x ?</p>
                                <p v-else>
                                    <i class="el-icon-star-on"></i> x {{ parseInt(item.ev_jl) + parseInt(item.ev_td) + parseInt(item.ev_zs) }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="dp_right" v-loading="loadingdP" element-loading-text="加载中">
                    <div class="dpxyList">
                        <h1>点评学员</h1>
                        <div>
                            <span v-for="item in daidianping">
                                <i>
                                    <img :src="item.face" alt="">
                                </i>
                                <span>{{ item.student_name }}</span>
                            </span>
                        </div>
                    </div>
                    <div v-show="!dpcontent">
                        <div class="dpxycaozuo">
                            <div>
                                <span>课堂纪律:</span>
                                <div>
                                    <el-rate v-model="dpform.jl" id="el-rate__icon"></el-rate>
                                </div>
                            </div>
                            <div>
                                <span>学习态度:</span>
                                <div>
                                    <el-rate v-model="dpform.td"></el-rate>
                                </div>
                            </div>
                            <div>
                                <span>知识掌握:</span>
                                <div>
                                    <el-rate v-model="dpform.zs"></el-rate>
                                </div>
                            </div>
                        </div>
                        <!--标签设置-->
                        <!--<div class="morenlist">
                                <el-tag :key="tag" type="gray" v-for="tag in dynamicTags" @click="alert()" :closable="true" :close-transition="false" @close="handleClose(tag)">
                                    <s @click="clicktag(tag)">{{tag}}</s>
                                </el-tag>
                                <el-input class="input-new-tag" v-if="inputVisible" v-model="inputValue" ref="saveTagInput" size="small" @keyup.enter.native="handleInputConfirm" @blur="handleInputConfirm">
                                </el-input>
                                <el-button v-else class="button-new-tag" size="small" @click="showInput">新增标签</el-button>
                            </div>-->
                        <div class="dpinputbox">
                            <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 5}" placeholder="请输入评语" v-model="dpform.value">
                            </el-input>
                            <div class="photoboxs">
                                <span v-for="(item,index) in imgArr">
                                    <i @click="delImagFun(index)"></i>
                                    <b>
                                        <img :src="item" alt="" @click="showBigimg">
                                    </b>
                                </span>
                                <s v-show="imgArr.length < 3">
                                    <form action="" enctype="multipart/form-data" ref="zyform">
                                        <input type="file" name="file" @change="uploadimg">
                                    </form>
                                </s>
                            </div>
                        </div>
                        <div class="dp_btom">
                            <el-button type="primary" size="small" @click="fabiaodianpingFun">发表点评</el-button>
                        </div>
                    </div>
                    <div v-show="dpcontent">
                        <div class="dpxycaozuo">
                            <div>
                                <span>课堂纪律:</span>
                                <div>
                                    <el-rate disabled v-model="score.jl" id="el-rate__icon"></el-rate>
                                </div>
                            </div>
                            <div>
                                <span>学习态度:</span>
                                <div>
                                    <el-rate disabled v-model="score.td"></el-rate>
                                </div>
                            </div>
                            <div>
                                <span>知识掌握:</span>
                                <div>
                                    <el-rate disabled v-model="score.zs"></el-rate>
                                </div>
                            </div>
                        </div>
                        <div v-html="dpcontent" @click="yulanImg"></div>
                    </div>
                </div>
            </div>
        </el-dialog>
        <album :list="imgArray" :change="showAlbum"></album>
    </div>
</template>
<script>
module.exports = {
    props: ["changedps", "id", "cid"],
    data: function () {
        return {
            //显示课后点评
            kehoudianpingshow: false,

            //课后点评-搜索学员
            search_xy: "",
            pldp: true,//课后点评-批量
            //点评状态-右边需要点评学员
            daidianping: [],
            //点评学员列表
            dianpingStudent: {
                student: []
            },
            dianpingStudents: {
                student: []
            },
            //点评form
            loadingdP: false,
            imgArr: [],
            dpcontent:null,
            score:{
                jl:0,
                td:0,
                zs:0
            },
            //点评form
            dpform: {
                jl: 5,
                td: 5,
                zs: 5,
                value: ""
            },
            imgArray:[],
            showAlbum:0
        }
    },
    watch: {
        changedps: function () {
            this.kehoudianpingshow = true;
        },
        id: function (val) {
            this.dianpingStudent.student = [];
            this.getDianpingList(this.cid, val);
            this.daidianping = [];
            this.dpform = {
                jl: 5,
                td: 5,
                zs: 5,
                value: ""
            }
        }
    },
    methods: {
        showBigimg:function(e){
            var self = this;
            if(e.target.tagName != 'IMG')return;
            self.imgArray = [];
            var imgList =e.target.parentNode.parentNode.parentNode.querySelectorAll("img");
            var arr = [];
            for(var i=0;i<imgList.length;i++){
                arr.push(imgList[i].src);
            }
            self.imgArray = arr;
            this.showAlbum = +new Date();
        },
        //预览图片
        yulanImg:function(e){
            var self = this;
            if(e.target.tagName != 'IMG')return;
            self.imgArray = [];
            var imgList =e.target.parentNode.parentNode.querySelectorAll("img");
            var arr = [];
            for(var i=0;i<imgList.length;i++){
                arr.push(imgList[i].src);
            }
            self.imgArray = arr;
            this.showAlbum = +new Date();
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
        //点评操作
        dianpingFun: function (obj, j) {
            var self = this;

            var html = "<div class='ktydpimgList'>"
            for (var i = 0; i < self.imgArr.length; i++) {
                html += '<span><img src="' + self.imgArr[i] + '"/></span>'
            }
            html += "</div>";

            html = "<p>" + self.dpform.value + "</p>" + html;

            var formData = new FormData();
            formData.append("sid", obj.student_id);
            formData.append("ev_jl", self.dpform.jl);
            formData.append("ev_td", self.dpform.td);
            formData.append("ev_zs", self.dpform.zs);
            formData.append("ev_info", html);
            self.$http.post("/api/classs/" + self.cid + "/curriculum/" + self.id + "/evaluation", formData)
                .then(function (data) {
                    self.loadingdP = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getDianpingList(self.cid, self.id);
                        self.daidianping = [];
                    } else {
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

        //发表点评
        fabiaodianpingFun: function () {
            var self = this;
            if (self.daidianping.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请选择点评学员",
                    type: "warning"
                })
                return;
            }
            if (self.dpform.value.trim() == "" && self.imgArr.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请填写点评内容",
                    type: "warning"
                })
                return;
            }
            self.loadingdP = true;
            for (var j = 0; j < self.daidianping.length; j++) {
                self.dianpingFun(self.daidianping[j], j);
            }
        },
        //搜索学员
        searchXueyuan: function (val) {
            var self = this;
            self.dianpingStudent.student = [];
            for (var i = 0; i < self.dianpingStudents.student.length; i++) {
                if (self.dianpingStudents.student[i].student_name.indexOf(val.trim()) != -1) {
                    self.dianpingStudent.student.push(self.dianpingStudents.student[i]);
                }
            }
            if (self.dianpingStudent.student.length == 0) {
                self.dianpingStudent.student = self.dianpingStudents.student;
            }
        },
        //单次点评  批量点评切换
        dancipiliangqiehuang: function (a) {
            var self = this;
            self.pldp = a;
            self.daidianping = [];
        },
        //点击点评学员
        dianpingTOright: function (item) {
            var self = this;
            if (item.time) {
                self.dpcontent = item.ev_info;
                self.score.jl = item.ev_jl;
                self.score.td = item.ev_td;
                self.score.zs = item.ev_zs;
                self.daidianping = [];
            }else{
                self.dpcontent = null;
                self.score.jl = 0;
                self.score.td = 0;
                self.score.zs = 0;
                if(self.daidianping.length > 0 && self.daidianping[0].time != null){
                    self.daidianping = [];
                }
            }
            if (!self.pldp) {//批量点评
                for (var i = 0; i < self.daidianping.length; i++) {
                    if (self.daidianping[i].student_id == item.student_id) {
                        self.$message({
                            showClose: true,
                            message: "该学员已在待点评状态下",
                            type: "warning"
                        })
                        return;
                    }
                }
                self.daidianping.push(item);
                for (var k = 0; k < self.dianpingStudent.student.length; k++) {
                    if (self.dianpingStudent.student[k].student_id == item.student_id) {
                        self.dianpingStudent.student[k].active = true;
                        break;
                    }
                }
            } else {
                self.daidianping = [];
                self.daidianping.push(item);
            }
        },
        //获取点评状态学员列表列表
        getDianpingList: function (cid, id) {
            var self = this;
            self.$http.get("/api/classs/" + cid + "/curriculum/" + id + "/evaluation")
                .then(function (data) {
                    var yarr = [];
                    var narr = [];
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.student.length; i++) {
                            data.data.data.student[i].active = false;
                            data.data.data.student[i].face = data.data.data.student[i].face ? ("http://wx.eduwxt.com" + data.data.data.student[i].face) : ("http://img.eduwxt.com/assets/images/users/avatar-11.jpg");
                            if (data.data.data.student[i].time) {
                                yarr.push(data.data.data.student[i])
                            } else {
                                narr.push(data.data.data.student[i])
                            }
                        }
                        self.dianpingStudent.student = narr.concat(yarr);
                        self.dianpingStudents.student = narr.concat(yarr);
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
    }
}
</script>
<style lang="less">
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
    .el-input {
        width: 95%;
    }
    .pldianping {
        display: block;
        margin: 10px 0;
    }
    ul {
        width: 100%;
        height: 316px;
        overflow: hidden;
        overflow-y: auto;
        li {
            overflow: hidden;
            padding: 5px 0;
            cursor: pointer;
            &:hover {
                background-color: #f8f9f9;
                .dp_li_box1 {
                    border: 1px solid @color;
                }
                .dp_li_box2 {
                    color: @color;
                }
            }
            .dp_li_box0 {
                width: 16px;
                margin-right: 5px;
                height: 16px;
                margin-top: 12px;
                background-color: #fff;
                border: 1px solid @color;
                float: left;
                border-radius: 50%;
                position: relative;
                &.active {
                    background-color: @color;
                    &:after {
                        content: "";
                        width: 6px;
                        height: 6px;
                        border-radius: 50%;
                        background-color: #fff;
                        position: absolute;
                        top: 5px;
                        left: 5px;
                    }
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
    .dpxyList {
        margin-bottom: 10px;
        h1 {
            font-size: @h3;
            font-weight: normal;
            color: @c1;
            margin-bottom: 10px;
        }
        >div {
            width: auto;
            height: 60px;
            display: inline-block;
            overflow: hidden;
            margin: 0 auto;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            >span {
                display: block;
                width: 80px;
                height: 60px;
                margin: 0 auto;
                float: left;
                >i {
                    display: block;
                    width: 40px;
                    height: 40px;
                    overflow: hidden;
                    border-radius: 50%;
                    border: 1px solid @color;
                    margin-left: 20px;
                    img {
                        display: block;
                        width: 100%;
                        height: 100%;
                    }
                }
                >span {
                    display: block;
                    text-align: center;
                    height: 20px;
                    line-height: 20px;
                    font-size: 14px;
                    color: @c2;
                }
            }
        }
    }
    .dpxycaozuo {
        padding-top: 10px;
        border-top: 1px solid #ddd;
    }
}

.dpxycaozuo {
    overflow: hidden;
    >div {
        width: 190px;
        float: left;
        overflow: hidden;
        margin-right: 10px;
        margin-bottom: 10px;
        >span {
            display: block;
            float: left;
            height: 20px;
            line-height: 20px;
            font-size: 14px;
            color: @c2;
            margin-right: 5px;
        }
        >div {
            float: left;
        }
    }
}

.morenlist {
    .el-input {
        width: 80px;
    }
    .el-tag {
        margin-right: 10px;
        cursor: pointer;
        s {
            text-decoration: none;
        }
    }
}

.dpinputbox {
    width: 100%;
    height: auto;
    padding: 10px 0;
}

.dp_btom {
    text-align: right;
}
</style>
