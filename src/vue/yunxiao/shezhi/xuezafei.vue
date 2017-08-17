<template>
    <div>
        <h1 class="right_title">
            学杂费用设置
            <article>除课程学费外的其他收费项目设置</article>
            <el-button type="success" size="small" @click="dialogFormVisible = true">管理杂费分类</el-button>
            <el-button type="success" size="small" @click="addzafeixiangmu">添加杂费项目</el-button>
        </h1>
        <tabs :list="xuezafei" :datas="xuezafeiLists" v-on:tabfun="tabCallBack_fenlei" text="类别" style="margin-bottom:0px"></tabs>
        <tabs :list="titleList" :datas="xuezafeiLists" v-on:tabfun="tabCallBack_campus" text="校区"></tabs>
        <ul class="cardList">
    
            <li v-for="(list,index) in xuezafeiList" :class="'cark_li'+index%4">
                <div>
                    <i class="el-icon-delete" @click="delZixiang(list.id,list.fees_name)"></i>
                </div>
                <h2>{{ list.campus_name }}</h2>
                <h1>{{ list.fees_name }}</h1>
                <p>{{ list.fees_names }}</p>
                <p>{{ list.price }}元/{{ list.unit }}</p>
            </li>
        </ul>
    
        <!--管理类别-->
        <el-dialog title="杂费分类管理" v-model="dialogFormVisible" size="tiny">
            <p class="fengexian"></p>
            <div class="add_lb" @click="addNewSubject">
                <i class="el-icon-plus"></i>
            </div>
            <div class="add_lb_xiugai_new" v-show="newAddsub">
                <el-row :gutter="18" class="">
                    <el-col :span="20">
                        <el-input v-model="lbform.name" placeholder="请输入内容" size="small"></el-input>
                    </el-col>
                    <el-col :span="4">
                        <el-button type="success" @click="addNewSubjectFun" size="small">确定</el-button>
                    </el-col>
                </el-row>
            </div>
            <ul class="guanlileibie">
                <li v-for="item in xuezafei">
                    <div class="add_lb_zanshi">
                        <el-row :gutter="18">
                            <el-col :span="20">
                                <span class="type_name">{{ item.type_name }}</span>
                            </el-col>
                            <el-col :span="4">
                                <el-button type="danger" size="small" @click="delZafeifenlei(item.id)">删除</el-button>
                            </el-col>
                        </el-row>
                    </div>
                </li>
            </ul>
        </el-dialog>
        <!--添加杂费项目-->
        <el-dialog title="添加杂费项目" v-model="zafeixiangmushow" size="tiny" class="addzafeizixiangmu">
            <p class="fengexian"></p>
            <el-form :model="zfxmform" label-width="70px">
                <el-form-item label="项目名称">
                    <el-input v-model="zfxmform.fees_name" placeholder="请输入杂费项目名称"></el-input>
                </el-form-item>
                <el-form-item label="所属校区">
                    <el-select v-model="zfxmform.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id == 1">
                        <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                    </el-select>
                    <el-select v-model="zfxmform.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1" disabled>
                        <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="杂费分类">
                    <el-select v-model="zfxmform.fees_type" placeholder="请选杂费类型">
                        <el-option :label="item.name" :value="item.id" v-for="item in xuezafei"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="单价">
                    <el-input v-model="zfxmform.price" placeholder="请输入单价" type="number" min="0"></el-input>
                </el-form-item>
                <el-form-item label="单位">
                    <el-input v-model="zfxmform.unit" placeholder="请输入单位"></el-input>
                </el-form-item>
                <el-form-item label="备注">
                    <el-input v-model="zfxmform.remark" type="textarea" :rows="2" placeholder="请输入备注信息"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="zafeixiangmushow = false">取 消</el-button>
                <el-button type="primary" @click="tianjiazafeixiangmu">确 定</el-button>
            </div>
        </el-dialog>
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
            lbform: {
                name: "",
                id: ""
            },
            newAddsub: false,
            xuezafei: window.sessionStorage.getItem("xuezafei") ? JSON.parse(window.sessionStorage.getItem("xuezafei")) : [],
            xuezafeiLists: window.sessionStorage.getItem("xuezafeiLists") ? JSON.parse(window.sessionStorage.getItem("xuezafeiLists")) : [],
            xuezafeiList: window.sessionStorage.getItem("xuezafeiLists") ? JSON.parse(window.sessionStorage.getItem("xuezafeiLists")) : [],

            titleList: [],
            id1: 0,
            id2: 0,

            //管理类别
            dialogFormVisible: false,
            zafeixiangmushow: false,
            formLabelWidth: "70px",
            zfxmform: {
                fees_name: "",
                campus_id: "",
                fees_type: "",
                price: 0,
                unit: "",
                remark: ""
            }

        }
    },
    methods: {
        //添加杂费项目
        addzafeixiangmu: function () {
            this.zafeixiangmushow = true;
        },
        //添加杂费项目
        tianjiazafeixiangmu: function () {
            var self = this;


            if (self.zfxmform.fees_name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "学杂费项目名称不能为空",
                    type: 'warning'
                });
                return;
            }
            if (self.zfxmform.campus_id == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: 'warning'
                });
                return;
            }
            if (self.zfxmform.fees_type == "") {
                self.$message({
                    showClose: true,
                    message: "请选择杂费类型",
                    type: 'warning'
                });
                return;
            }
            if (self.zfxmform.price < 0) {
                self.$message({
                    showClose: true,
                    message: "杂费项目单价必须大于等于0",
                    type: 'warning'
                });
                return;
            }
            if (self.zfxmform.unit.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写杂费项目单位",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();

            for(var k in self.zfxmform){
                formData.append(k, self.zfxmform[k]);
            }

            self.$http.post("/api/finance/fees", formData)
                .then(function (data) {
                    self.zafeixiangmushow = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "学杂费项目添加成功",
                        });
                        self.getXuezafeiList();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.zafeixiangmushow = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //打开新增
        addNewSubject: function () {
            this.newAddsub = true;
            this.lbform = {
                name: "",
                id: ""
            }
        },
        //新增杂费分类
        addNewSubjectFun: function () {
            var self = this;

            if (self.lbform.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "学杂费类型名称不能为空",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();
            formData.append("type_name", self.lbform.name);

            self.$http.post("/api/finance/fees_type", formData)
                .then(function (data) {
                    self.newAddsub = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "学杂费添加成功",
                        });
                        self.getzafeifenlei();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.newAddsub = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除杂费分类
        delZafeifenlei: function (id) {
            var self = this;

            self.$confirm('确定要删除?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/finance/fees_type/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: "杂费分类删除成功",
                            });
                            self.getzafeifenlei();
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
        tabCallBack_fenlei: function (id) {
            var self = this;
            self.id1 = id;
            if (self.id1 == 0 && self.id2 == 0) {
                self.xuezafeiList = self.xuezafeiLists;
                return;
            }

            if (self.id1 == 0 && self.id2 != 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].campus_id == self.id2) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
                return;
            }

            if (self.id1 != 0 && self.id2 == 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].fees_type == self.id1) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
                return;
            }

            if (self.id1 != 0 && self.id2 != 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].fees_type == self.id1 && self.xuezafeiLists[i].campus_id == self.id2) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
            }

            return;

        },
        //点击校区筛选
        tabCallBack_campus: function (id) {
            var self = this;
            self.id2 = id;
            if (self.id2 == 0 && self.id1 == 0) {
                self.xuezafeiList = self.xuezafeiLists;
                return;
            }

            if (self.id1 != 0 && self.id2 == 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].fees_type == self.id1) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
                return;
            }

            if (self.id1 == 0 && self.id2 != 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].campus_id == self.id2) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
                return;
            }

            if (self.id1 != 0 && self.id2 != 0) {
                var arr = [];
                for (var i = 0; i < self.xuezafeiLists.length; i++) {
                    if (self.xuezafeiLists[i].fees_type == self.id1 && self.xuezafeiLists[i].campus_id == self.id2) {
                        arr.push(self.xuezafeiLists[i]);
                    }
                }
                self.xuezafeiList = arr;
            }

            return;
        },
        //获取学杂费分类
        getzafeifenlei: function () {
            var self = this;
            self.$http.get("/api/finance/fees_type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            data.data.data[i].name = data.data.data[i].type_name;
                            data.data.data[i].num = 0;
                        }
                        self.xuezafei = data.data.data;
                        window.sessionStorage.setItem("xuezafei", JSON.stringify(self.xuezafei))
                        self.getXuezafeiList();
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
        //获取学杂费列表
        getXuezafeiList: function () {
            var self = this;
            self.$http.get("/api/finance/fees")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {

                            for (var j = 0; j < self.xuezafei.length; j++) {
                                if (self.xuezafei[j].id == data.data.data[i].fees_type) {
                                    data.data.data[i].fees_names = self.xuezafei[j].type_name;
                                }
                            }

                            for (var k = 0; k < self.titleList.length; k++) {
                                if (self.titleList[k].id == data.data.data[i].campus_id) {
                                    data.data.data[i].campus_name = self.titleList[k].name;
                                    break;
                                }
                            }
                        }

                        self.xuezafeiLists = data.data.data;
                        self.xuezafeiList = data.data.data;
                        window.sessionStorage.setItem("xuezafeiLists", JSON.stringify(self.xuezafeiLists))
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
        //删除杂项子项
        delZixiang: function (id, name) {
            var self = this;
            self.$confirm('确定要删除"' + name + '"?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/finance/fees/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功",
                            });
                            self.getXuezafeiList();
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
        //管理类别
        guanlileibie: function () {

        }
    },
    created: function () {
        this.titleList = this.campus;
        for (var i = 0; i < this.titleList.length; i++) {
            this.titleList[i].num = 0;
        }
        this.$store.commit('setSettingLeftMeau', 6);
        this.getzafeifenlei();

        this.zfxmform.campus_id = this.myMessage.campus_id == 1?"":this.myMessage.campus_id;
    }
}

</script>

<style lang="less" scoped>
@import "../../../less/color.less";
s {
    text-decoration: none;
}

.addzafeizixiangmu {
    .el-select {
        width: 100%;
    }
}

.guanlileibie {
    height: 300px;
    overflow: hidden;
    overflow-y: auto;
    li {
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
        position: relative;
        overflow: hidden;
        height: 35px;
        line-height: 35px;
        .type_name {
            display: block;
            color: @c2;
            font-size: 14px;
        }
        .add_lb_xiugai {
            display: none;
            position: absolute;
            top: 10px;
            left: 0;
            width: 100%;
            background-color: #fff;
        }
    }
}

.add_lb_xiugai_new {
    width: 100%;
    background-color: #fff;
    margin: 10px 0;
}

.add_lb {
    width: 100%;
    height: 30px;
    line-height: 30px;
    border-radius: 3px;
    text-align: center;
    font-size: 18px;
    color: @c3;
    cursor: pointer;
    border: 1px dashed #ddd;
    &:hover {
        opacity: .7;
    }
    &:active {
        opacity: .5;
    }
}
</style>