<template>
    <div>
        <el-tabs v-model="tabActive" type="card">
            <el-tab-pane label="招生渠道设置" name="first">
                <el-button type="primary" size="mini" @click="addZhaoshengqudao">添加招生渠道</el-button>
                <ul class="addSubjectList">
                    <li v-for="item in sourceList">
                        <div>{{ item.name }}</div>
                        <span>
                            <i class="el-icon-edit" @click="openbianjiFun($event,item.name)"></i>
                            <i class="el-icon-delete2" @click="delZhaoshengqudao(item.id,0)"></i>
                        </span>
                        <div class="zhaoshengqudao_edit">
                            <el-input v-model="qudaoVal" placeholder="请输入内容"></el-input>
                            <el-button type="success" @click="yubaobianjiOK($event,item.id,0)">确认</el-button>
                            <el-button @click="bianjiNO">取消</el-button>
                        </div>
                    </li>
                </ul>
            </el-tab-pane>
            <el-tab-pane label="预报科目设置" name="second">
                <el-button type="primary" size="mini" @click="addYubaokemu">添加预报科目</el-button>
                <ul class="addSubjectList">
                    <li v-for="item in YBsubjectList">
                        <div>{{ item.name }}</div>
                        <span>
                            <i class="el-icon-edit" @click="openbianjiFun($event,item.name)"></i>
                            <i class="el-icon-delete2" @click="delZhaoshengqudao(item.id,1)"></i>
                        </span>
                        <div class="zhaoshengqudao_edit">
                            <el-input v-model="qudaoVal" placeholder="请输入内容"></el-input>
                            <el-button type="success" @click="yubaobianjiOK($event,item.id,1)">确认</el-button>
                            <el-button @click="bianjiNO">取消</el-button>
                        </div>
                    </li>
                </ul>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            tabActive: "first",
            qudaoVal: "",

            //预报科目
            YBsubjectList: window.sessionStorage.getItem("YBsubjectList") ? JSON.parse(window.sessionStorage.getItem("YBsubjectList")) : [],
            //招生渠道
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
        }
    },
    methods: {
        //打开编辑
        openbianjiFun: function ($event, name) {
            if ($event.target.tagName != 'I') {
                return;
            }
            $event.target.parentNode.parentNode.querySelector(".zhaoshengqudao_edit").style.display = "block";
            this.qudaoVal = name;
        },
        //确认编辑
        yubaobianjiOK: function ($event, id, type) {
            var self = this;
            if (self.qudaoVal.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "名称不能为空",
                    type: "warning"
                })
                return;
            }
            if ($event.target.tagName == 'SPAN') {
                $event.target.parentNode.parentNode.style.display = "none";
            }
            if ($event.target.tagName == 'BUTTON') {
                $event.target.parentNode.style.display = "none";
            }
            var formData = new FormData();
            formData.append("name", self.qudaoVal);

            var url = type == 1 ? "/api/intention/subject/" : "/api/intention/source/";

            self.$http.post(url + id, formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg
                        });
                        if (type == 1) {
                            self.getYBsubject();
                        } else {
                            slef.getSource();
                        }
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
        },
        //添加招生渠道
        addZhaoshengqudao: function () {
            var self = this;
            self.$prompt('请输招生渠道名称', '添加招生渠道', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(function (value) {
                if ((value.value + "").trim() == '') {
                    self.$message({
                        showClose: true,
                        message: "名称不能为空",
                        type: "warning"
                    })
                    return;
                }
                var formData = new FormData();
                formData.append("name", value.value);

                var url = "/api/intention/source";

                self.$http.post(url, formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getSource();
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
        //添加预报科目
        addYubaokemu: function () {
            var self = this;
            self.$prompt('请输预报科目名称', '添加预报科目', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
            }).then(function (value) {
                if ((value.value + "").trim() == '') {
                    self.$message({
                        showClose: true,
                        message: "名称不能为空",
                        type: "warning"
                    })
                    return;
                }
                var formData = new FormData();
                formData.append("name", value.value);

                var url = "/api/intention/subject";

                self.$http.post(url, formData)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.getYBsubject();
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
        //删除招生渠道 删除预报科目
        delZhaoshengqudao: function (id, type) {
            var self = this;
            var title = type == 1 ? "是否确认删除该预报科目?" : "是否确认删除该招生渠道?";
            self.$confirm(title, type == "1" ? "删除预报科目" : "删除招生渠道", {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                var url = type == 1 ? ("/api/intention/subject/" + id + "/del") : ("/api/intention/source/" + id + "/del");
                self.$http.post(url)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            if (type == 1) {
                                self.getYBsubject();
                            } else {
                                self.getSource();
                            }
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
        //预报科目
        getYBsubject: function () {
            var self = this;
            self.$http.get("/api/intention/subject")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.YBsubjectList = data.data.data;
                        window.sessionStorage.setItem("YBsubjectList", JSON.stringify(self.YBsubjectList));
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
        //获取招生渠道
        getSource: function () {
            var self = this;
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
        }

    },
    created: function () {
        this.getYBsubject();
        this.getSource();

        //权限设置
        var yx_zszs_sz = this.pdqx(["yx_zszz", "yx_zszs_sz", "yx"]);
        if (!yx_zszs_sz) {
            this.$router.push({ name: 'workyx_zszs_sztody' });
        }
    }
}

</script>