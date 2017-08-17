<template>
    <div>
        <!--展示-->
        <div class="wxtableMsg" v-if="tableShow">
            <h1>表单信息
                    <el-button type="primary" size="mini" @click="xiugaitablemsg">&nbsp;&nbsp;&nbsp;&nbsp;修改&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                </h1>
            <div class="wx_table_box">
                <div class="wx_table_box_left">
                    <img src="../../../img/phone_box_msg.jpg" alt="">
                    <span>{{ wxtablemsg.form.title }}</span>
                    <div class="wx_table_box_left_content">
                        <div>
                            <div>
                                <div class="phone_banner">
                                    <img :src="wxtablemsg.form.pic" alt="">
                                    <h1>{{ wxtablemsg.form.title }}</h1>
                                    <p class="wxtable_p1">
                                        <span><img src="../../../img/wxtable_liulan.png" alt="">{{ wxtablemsg.form.add_time | yyyy_mm_dd1 }}</span>
                                        <s><img src="../../../img/wxtable_time.png" alt="">{{ wxtablemsg.form.view_count }}</s>
                                    </p>
                                    <p class="wxtable_p2">
                                        <img src="../../../img/wxtable_iphones.png" alt="">{{ wxtablemsg.form.phone }}
                                    </p>
                                    <p class="wxtable_p3">
                                        <img src="../../../img/address_icon.png" alt="">{{ wxtablemsg.form.address }}
                                        <i class="el-icon-arrow-right"></i>
                                    </p>
                                </div>
                                <div class="wxtable_text">
                                    <div class="wx_table_cont_box" v-html="wxtablemsg.form.body"></div>
                                </div>
                                <div class="wxtable_bttom">
                                    <span>电话咨询</span>
                                    <s>我要报名</s>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wx_table_box_right">
                    <el-form label-width="90px">
                        <el-form-item label="表单标题">
                            <span>{{ wxtablemsg.form.title }}</span>
                        </el-form-item>
    
                        <el-row :gutter="20">
                            <el-col :span="12">
                                <el-form-item label="咨询电话">
                                    {{ wxtablemsg.form.phone }}
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="招生来源">
                                    {{ wxtablemsg.form.source }}
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="20">
    
                            <el-col :span="12">
                                <el-form-item label="适用校区">
                                    <span v-for="item in campus" v-if="wxtablemsg.form.campus_id == item.id">{{ item.name }}</span>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="表单负责人">
                                    {{ wxtablemsg.form.fzr }}
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="20">
    
                            <el-col :span="24">
                                <el-form-item label="提交信息">
                                    <span v-if="wxtablemsg.form.field.name" class="meslist">姓名</span>
                                    <span v-if="wxtablemsg.form.field.age" class="meslist">年龄</span>
                                    <span v-if="wxtablemsg.form.field.phone" class="meslist">电话</span>
                                    <span v-if="wxtablemsg.form.field.sex" class="meslist">性别</span>
                                    <span v-if="wxtablemsg.form.field.birthday" class="meslist">生日</span>
                                    <span v-if="wxtablemsg.form.field.school" class="meslist">学校</span>
                                    <span v-if="wxtablemsg.form.field.grade" class="meslist">年级</span>
                                    <span v-if="wxtablemsg.form.field.address" class="meslist">家庭住址</span>
                                    <span v-if="wxtablemsg.form.field.remark" class="meslist">备注信息</span>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="20">
    
                            <el-col :span="24">
                                <el-form-item label="学校地址">
                                    {{ wxtablemsg.form.address }}
                                    <el-button type="primary" size="small" @click="chakanditu">查看地图</el-button>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-form-item label="表单内容">
                            <div class="zhengwen" v-html="wxtablemsg.form.body">
                            </div>
                        </el-form-item>
    
                    </el-form>
                </div>
            </div>
    
        </div>
        <!--修改-->
        <div class="wxtableMsg_xiugai" v-if="!tableShow">
            <h1>表单信息</h1>
            <div class="wx_table_box">
                <div class="wx_table_box_left">
                    <img src="../../../img/phone_box_msg.jpg" alt="">
                    <span>{{ form.title }}</span>
                    <div class="wx_table_box_left_content">
                        <div>
                            <div>
                                <div class="phone_banner">
                                    <img :src="wxtablemsg.form.pic" alt="">
                                    <h1>{{ form.title }}</h1>
                                    <p class="wxtable_p1">
                                        <span><img src="../../../img/wxtable_liulan.png" alt="">{{ new Date().toLocaleDateString() }}</span>
                                        <s><img src="../../../img/wxtable_time.png" alt="">{{ form.view_count }}</s>
                                    </p>
                                    <p class="wxtable_p2">
                                        <img src="../../../img/wxtable_iphones.png" alt="">{{ form.phone }}
                                    </p>
                                    <p class="wxtable_p3">
                                        <img src="../../../img/address_icon.png" alt="">{{ form.address }}
                                    </p>
                                </div>
                                <div class="wxtable_text">
                                    <div class="wx_table_cont_box" v-html="form.body"></div>
                                </div>
                                <div class="wxtable_bttom">
                                    <span>电话咨询</span>
                                    <s>我要报名</s>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wx_table_box_right">
                    <el-form label-width="90px" :model="form">
                        <el-form-item label="表单标题">
                            <el-input v-model="form.title"></el-input>
                        </el-form-item>
    
                        <el-row :gutter="30">
                            <el-col :span="24">
                                <el-form-item label="封面图片">
                                    <div class="two_box">
                                        <el-button type="success" size="small" @click="opendupload">上传图片</el-button>
                                        <p>
                                            为了您的展示效果，建议依据微信官方规定，上传大小为900*500的封面图，也可选择默认图片
                                        </p>
                                    </div>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="30">
                            <el-col :span="12">
                                <el-form-item label="咨询电话">
                                    <el-input v-model="form.phone" placeholder="请输入电话"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="招生渠道">
                                    <el-select v-model="form.source_id" placeholder="请选择招生渠道" filterable>
                                        <el-option v-for="item in sourceList" :label="item.name" :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
    
                        </el-row>
    
                        <el-row :gutter="30">
    
                            <el-col :span="12">
                                <el-form-item label="适用校区">
                                    <el-select v-model="form.campus_id" placeholder="请选择校区" disabled>
                                        <el-option v-for="item in campus" :label="item.name" :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="表单负责人">
                                    <el-select v-model="form.user_id" filterable placeholder="请选择表单负责人">
                                        <el-option v-for="item in ygSimple" :label="item.name" :value="item.id">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="30">
                            <el-col :span="24">
                                <el-form-item label="提交信息">
                                    <el-checkbox-group v-model="field">
                                        <el-checkbox label="name" disabled>姓名</el-checkbox>
                                        <el-checkbox label="age" disabled>年龄</el-checkbox>
                                        <el-checkbox label="phone" disabled>电话</el-checkbox>
                                        <el-checkbox label="sex">性别</el-checkbox>
                                        <el-checkbox label="birthday">生日</el-checkbox>
                                        <el-checkbox label="school">学校</el-checkbox>
                                        <el-checkbox label="grade">年级</el-checkbox>
                                        <el-checkbox label="address">家庭住址</el-checkbox>
                                        <el-checkbox label="remark">备注信息</el-checkbox>
                                    </el-checkbox-group>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-row :gutter="30">
                            <el-col :span="24">
                                <el-form-item label="学校地址">
                                    <el-input v-model="form.address" placeholder="请输地址,并在地图上标记"></el-input>
                                    <el-button type="primary" size="small" @click="openBaiduMap">地图标记</el-button>
                                    <span class="tishiwenzi">(请在地图上标记出学校所在位置)</span>
                                </el-form-item>
                            </el-col>
                        </el-row>
    
                        <el-form-item label="表单正文">
                            <vue-html5-editor :content="form.body" :height="200" @change="updateData"></vue-html5-editor>
                        </el-form-item>
    
                        <div class="xiugai_bottom">
                            <el-button size="small" @click="tableShow = true">&nbsp;&nbsp;&nbsp;&nbsp;取消&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                            <el-button type="primary" size="small" @click="xiugaiWXtableFun">&nbsp;&nbsp;&nbsp;&nbsp;修改&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                        </div>
                    </el-form>
                </div>
            </div>
        </div>
        <!--设置封面-->
        <el-dialog title="上传图片" v-model="dialogVisible">
            <p class="fengexian"></p>
            <h6 class="zidingyifengmian">自定义封面</h6>
            <div class="wxtable_up">
    
                <el-upload class="upload-demo" drag action="/api/file/upload" show-file-list="false" :on-success="uploadSuccess">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">只能上传jpg/png文件</div>
                </el-upload>
            </div>
            <div class="uploadbottom">
                <h1>默认封面</h1>
                <span :class="{active:imgClick == 'url1'}" @click="morenimgclick('url1')">
                        <img :src="img.url1" alt="">
                        <i class="el-icon-check"></i>   
                    </span>
                <span :class="{active:imgClick == 'url2'}" @click="morenimgclick('url2')">
                        <img :src="img.url2" alt="">
                        <i class="el-icon-check"></i>      
                    </span>
                <span :class="{active:imgClick == 'url3'}" @click="morenimgclick('url3')">
                        <img :src="img.url3" alt="">
                        <i class="el-icon-check"></i>      
                    </span>
            </div>
            <span slot="footer" class="dialog-footer">
                    <el-button @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="querenfengmian">确 定</el-button>
                </span>
        </el-dialog>
        <!--百度地图-->
        <el-dialog title="百度地图" v-model="baiduMap">
            <maps :height="height" :longitude="longitude" :latitude="latitude" :canclick="canclickBaidu" v-on:getmappoint="getMapData"></maps>
            <span slot="footer" class="dialog-footer">
                    <el-button @click="baiduMap = false">取消</el-button>
                    <el-button type="primary" @click="baiduMap = false">确定</el-button>
                </span>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    data: function () {
        return {
            tableShow: true,
            dialogVisible: false,
            baiduMap: false,
            canclickBaidu: false,


            height: 300,
            longitude: 108.95309828,
            latitude: 34.2777999,

            imgClick: "url1",
            img: {
                url1: "http://wxt-1251355418.file.myqcloud.com/file/s12/201704/96c0f6c9777d65aaa066caf02db4e87a.png",
                url2: "http://wxt-1251355418.file.myqcloud.com/file/s12/201704/7e09c03476fab4bd744231ef4e036c5a.png",
                url3: "http://wxt-1251355418.file.myqcloud.com/file/s12/201704/d998736eb81b5783c0917c0b0215d925.png"
            },
            wxtablemsg: {
                form: {
                    field: {}
                }
            },
            form: {
                title: "",
                pic: "",
                campus_id: "",
                source_id: "",
                user_id: "",
                phone: "",
                address: "",
                field: {
                    name: true,
                    age: true,
                    phone: true,
                    sex: true,
                    birthday: false,
                    school: false,
                    grade: false,
                    address: false,
                    remark: false
                },
                body: "",
                point: {}
            },
            field: [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : []
        }
    },
    methods: {
        //提交修改
        xiugaiWXtableFun: function () {
            var self = this;
            if (self.form.title.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入表单名称",
                    type: 'warning'
                });
                return;
            }
            if (self.form.pic.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请选择表单封面",
                    type: 'warning'
                });
                return;
            }
            if (self.form.phone.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入电话",
                    type: 'warning'
                });
                return;
            }
            if (self.form.source_id.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请选择招生渠道",
                    type: 'warning'
                });
                return;
            }
            if (self.form.user_id.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请选择表单负责人",
                    type: 'warning'
                });
                return;
            }
            if (self.form.campus_id.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请选择适用校区",
                    type: 'warning'
                });
                return;
            }
            if (self.form.address.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请填写学校地址",
                    type: 'warning'
                });
                return;
            }
            if (!self.form.point.lng) {
                self.$message({
                    showClose: true,
                    message: "请在地图上标记地址",
                    type: 'warning'
                });
                return;
            }
            if (self.form.body.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入表单正文",
                    type: 'warning'
                });
                return;
            }

            self.form.point = JSON.stringify(self.form.point);

            var formData = new FormData();
            for (var k in self.form.field) {
                self.form.field[k] = false;
            }
            for (var i = 0; i < self.field.length; i++) {
                self.form.field[self.field[i]] = true;
            }

            self.form.field = JSON.stringify(self.form.field);

            for (var key in self.form) {
                formData.append(key, self.form[key]);
            }

            self.$http.post("/api/free_form/" + self.$route.params.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        self.getTableMsg();
                        self.tableShow = true;
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
        //修改表单切换
        xiugaitablemsg: function () {
            var self = this;
            this.tableShow = false;
            for (var key in self.form) {
                self.form[key] = self.wxtablemsg.form[key];
            }
            for (var k in self.form.field) {
                if (self.form.field[k]) {
                    self.field.push(k);
                }
            }
        },

        //获取招生渠道
        getSource: function () {
            var self = this;
            if (self.sourceList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/source")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.sourceList = data.data.data;
                        window.sessionStorage.setItem("sourceList", JSON.stringify(self.sourceList));
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
        //获取表单详情
        getTableMsg: function () {
            var self = this;
            self.$http.get("/api/free_form/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.wxtablemsg = data.data.data;
                        self.status = data.data.data.form.status;
                        self.$store.commit("getYGList", {
                            self: self,
                            campus_id: self.wxtablemsg.form.campus_id
                        })
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
        //确认微信表单封面
        querenfengmian: function () {
            this.dialogVisible = false;
        },
        //点击微信表单封面
        morenimgclick: function (url) {
            this.imgClick = url;
            this.form.pic = this.img[url];
        },
        //打开上传图片
        opendupload: function () {
            this.dialogVisible = true;
            this.form.pic = this.img[this.imgClick];
        },

        //获取地图点击的参数
        getMapData: function (data) {
            this.form.point = data;
        },
        //查看地图
        chakanditu: function () {
            var point = this.wxtablemsg.form.point;
            this.longitude = point.lng;
            this.latitude = point.lat;

            this.baiduMap = true;
            this.canclickBaidu = false;
        },
        //打开编辑百度地图
        openBaiduMap: function () {
            var point = this.wxtablemsg.form.point;
            this.longitude = point.lng;
            this.latitude = point.lat;

            this.baiduMap = true;
            this.canclickBaidu = true;
        },
        //上次成功
        uploadSuccess: function (data) {
            this.form.pic = data.data.cos_path;
            this.imgClick = "";
        },
        //编辑器 
        updateData: function (val) {
            this.form.body = val;
        }
    },
    created: function () {
        this.getTableMsg();
        this.getSource();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.zidingyifengmian {
    font-size: 16px;
    color: @c2;
    font-weight: normal;
}

.uploadbottom {
    padding-top: 20px;
    overflow: hidden;
    h1 {
        font-size: 16px;
        color: @c2;
        padding: 10px 0;
        border-top: 1px dotted #ddd;
        font-weight: normal;
    }
    >span {
        display: block;
        float: left;
        width: 200px;
        margin-right: 10px;
        margin-bottom: 10px;
        position: relative;
        cursor: pointer;
        >i {
            display: none;
            font-size: 20px;
        }
        &.active {
            i {
                display: block;
                position: absolute;
                right: 10px;
                bottom: 10px;
                font-size: 14px;
                color: @color;
            }
        }
        img {
            display: block;
            width: 100%;
        }
    }
}

.wxtable_up {
    width: 360px;
    margin: 0 auto;
}

.two_box {
    display: flex;
    padding-top: 5px;
    button {
        display: block;
        width: 78px;
        height: 34px;
        margin-right: 10px;
    }
    p {
        flex: 1;
        line-height: 17px;
    }
}

.wxtableMsg {
    padding: 20px;
    background-color: #fff;
    >h1 {
        font-size: @h3;
        color: @c1;
        font-weight: normal;
        margin-bottom: 10px;
        >button {
            float: right;
        }
    }
    .el-form-item {
        margin-bottom: 0;
    }
}

.wxtableMsg_xiugai {
    padding: 20px;
    background-color: #fff;
    >h1 {
        font-size: @h3;
        color: @c1;
        font-weight: normal;
        margin-bottom: 10px;
    }
    .el-select {
        width: 100%;
    }
}

.zhengwen {
    padding-top: 8px;
    line-height: 22px;
}

.tupianzhanshi {
    overflow: hidden;
    padding-top: 12px;
    >span {
        display: block;
        float: left;
        width: 200px;
        margin-right: 10px;
        overflow: hidden;
        >img {
            display: block;
            width: 200px;
        }
    }
    >s {
        display: block;
        float: left;
        width: 100px;
        text-decoration: none;
        >img {
            display: block;
            width: 100px;
        }
    }
}

.xiugai_bottom {
    text-align: right;
}

.meslist {
    margin-right: 5px;
}
</style>