<template>
    <div class="studentServer">
        <h1>跟进标签设置</h1>
        <ul class="addSubjectList">
            <li v-for="item in biaoqianList">
                <i :style="{ backgroundColor:item.color }"></i>
                <div>{{ item.name }}</div>
                <span>
                    <i class="el-icon-edit" @click="openbianjiFun($event,item.name)"></i>
                </span>
                <div class="zhaoshengqudao_edit">
                    <el-input v-model="qudaoVal" placeholder="请输入内容"></el-input>
                    <el-button type="success" @click="bianjiOK($event,item)">确认</el-button>
                    <el-button @click="bianjiNO">取消</el-button>
                </div>
            </li>
        </ul>
        <h1>报名须知设置</h1>
        <div class="baomingxuzhi">
            <p>{{ bmxz?bmxz.length:0 }}/300</p>
            <el-input type="textarea" :autosize="{ minRows: 4}" placeholder="请输入内容" v-model="bmxz" maxlength="300">
            </el-input>
            <div class="bottom">
                <el-button type="success" @click="saveBmxz">保存</el-button>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            qudaoVal: "",
            bmxz: "",
            bcval:"",
            biaoqianList: window.sessionStorage.getItem("biaoqianList") ? JSON.parse(window.sessionStorage.getItem("biaoqianList")) : [],
        }
    },
    methods: {
        //获取报名须知
        getBMXZ:function(){
            var self = this;
            self.$http.get("/api/config/bmxz")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.bmxz = data.data.data;
                        self.bcval = data.data.data;
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
        //保存报名须知
        saveBmxz: function () {
            var self = this;
            if (self.bmxz.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入报名须知",
                    type: "warning"
                })
                return;
            }
            if (self.bmxz.trim() == self.bcval) {
                self.$message({
                    showClose: true,
                    message: "请更改后保存",
                    type: "warning"
                })
                return;
            }
            var formData = new FormData();
            formData.append("bmxz", self.bmxz);
            self.$http.post("/api/config/bmxz", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
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
        //获取标签
        getBiaoqianList: function () {
            var self = this;
            self.$http.get("/api/intention/track_type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.biaoqianList = data.data.data;
                        window.sessionStorage.setItem("biaoqianList", JSON.stringify(self.biaoqianList));
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
        //打开编辑
        openbianjiFun: function ($event, name) {
            if ($event.target.tagName != 'I') {
                return;
            }
            $event.target.parentNode.parentNode.querySelector(".zhaoshengqudao_edit").style.display = "block";
            this.qudaoVal = name;
        },
        //确认编辑
        bianjiOK: function ($event, obj) {
            if ($event.target.tagName == 'SPAN') {
                $event.target.parentNode.parentNode.style.display = "none";
            }
            if ($event.target.tagName == 'BUTTON') {
                $event.target.parentNode.style.display = "none";
            }

            var self = this;

            if (self.qudaoVal.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "名称不能为空",
                    type: 'warning'
                });
                return;
            }
            var formData = new FormData();
            formData.append("name", self.qudaoVal);
            formData.append("color", obj.color);

            self.$http.post("/api/intention/track_type/" + obj.id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getBiaoqianList();
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
        //取消编辑
        bianjiNO: function ($event) {
            if ($event.target.tagName == 'SPAN') {
                $event.target.parentNode.parentNode.style.display = "none";
            }
            if ($event.target.tagName == 'BUTTON') {
                $event.target.parentNode.style.display = "none";
            }
        }
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 8);
        this.getBiaoqianList();
        this.getBMXZ();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.studentServer {
    >h1 {
        font-size: @h3;
        color: #666;
    }
}

.addSubjectList {
    overflow: hidden;
    margin-bottom: 10px;
    >li {
        >i {
            display: block;
            float: left;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-top: 20px;
            margin-left: 10px;
            border: 1px solid @c1;
        }
        >div {
            width: 240px;
            padding-left: 10px;
            &.zhaoshengqudao_edit {
                width: 100%;
            }
        }
        >span {
            width: 40px;
            color: @c3;
        }
    }
}

.baomingxuzhi {
    width: 100%;
    padding: 20px;
    background-color: #fff;
    margin-top: 20px;
    >p {
        font-size: 14px;
        padding-bottom: 10px;
        color: @c3;
    }
    .bottom {
        padding-top: 20px;
        text-align: right;
    }
}
</style>