<template>
    <div>
        <h1 class="right_title">
            <span>组织结构</span>
            <article>对教职工人员的职位设置及权限添加</article>
            <el-button type="success" size="mini" class="zw_btn" @click="openAddzhiwu">添加职务</el-button>
        </h1>
        <div class="content_two">
            <div class="c_left">
                <h1>职务列表</h1>
                <el-tree class="lefttree" accordion :data="department" :props="defaultProps" @node-click="handleNodeClick" node-key="id" :default-expanded-keys="leftTree"></el-tree>
            </div>
            <div class="c_right" v-loading="loading" element-loading-text="加载中">
                <div class="quanxian_title">
                    <span>职务权限设置</span>
                    <s v-if="forms.id != 1">
                        <el-button type="danger" size="small" @click="delZhiwei" :loading="delqx">&nbsp;&nbsp;&nbsp;&nbsp;删除&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                        <el-button type="success" size="small" @click="panduanxiugaiqx" :loading="saveqx">&nbsp;&nbsp;&nbsp;&nbsp;保存&nbsp;&nbsp;&nbsp;&nbsp;</el-button>
                    </s>
                </div>
                <p class="tishi">提示：职务权限设置完毕后，您依然可以对员工进行个人权限设置，个人权限的优先级高于职务权限。</p>
                <div class="zhiwuname">
                    <el-form :model="form" :label-width="formLabelWidth">
                        <el-row :gutter="30">
                            <el-col :span="12">
                                <el-form-item label="职务名称">
                                    <el-input v-model="forms.name" auto-complete="off" placeholder="请输入职位名称" size="small" v-if="forms.id !=1"></el-input>
                                    <el-input v-model="forms.name" auto-complete="off" placeholder="请输入职位名称" size="small" v-if="forms.id ==1" disabled></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="所属部门">
                                    <el-select v-model="forms.bmid" placeholder="请选择做属部门" size="small" v-if="forms.id != 1">
                                        <el-option :label="item.name" :value="item.id" v-for="item in department"></el-option>
                                    </el-select>
                                    <el-select v-model="forms.bmid" placeholder="请选择做属部门" size="small" v-if="forms.id == 1" disabled>
                                        <el-option :label="item.name" :value="item.id" v-for="item in department"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </div>
                <div class="quanxin_box">
                    <el-tree :data="qxpeizhi" show-checkbox node-key="id" :default-checked-keys="quanxianList" :default-expanded-keys="quanxianList" :props="defaultProps1" ref="tree">
                    </el-tree>
                </div>
            </div>
        </div>
        <!--添加职务-->
        <el-dialog title="添加职务" v-model="zhiwushow" size="tiny" class="zhiwu">
            <p class="fengexian"></p>
            <el-form :model="form" :label-width="formLabelWidth">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="职务名称">
                            <el-input v-model="form.name" auto-complete="off" placeholder="请输入职位名称" size="small"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="所属部门">
                            <el-select v-model="form.bmid" placeholder="请选择做属部门" size="small">
                                <el-option :label="item.name" :value="item.id" v-for="item in department"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <div class="add_zhiwubox">
                    <el-tree :data="qxpeizhi" show-checkbox node-key="id" :default-checked-keys="[]" :default-expanded-keys="[]" :props="defaultProps1" ref="addtree">
                    </el-tree>
                </div>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="zhiwushow = false">取消</el-button>
                <el-button type="primary" @click="addzhiwuFun" :loading="addquanxian">添加</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            zhiwushow: false,
            formLabelWidth: "70px",
            addquanxian: false,
            saveqx: false,
            delqx: false,
            loading: false,
            leftTree: [],
            //添加权限
            form: {
                name: "",
                bmid: "",
                authority: []
            },
            //修改权限
            forms: {
                id: "",
                name: "",
                bmid: "",
                authority: []
            },
            department: [],
            defaultProps: {
                children: 'group',
                label: 'name'
            },
            defaultProps1: {
                children: 'children',
                label: 'label'
            },

            quanxianList: []
        };
    },
    computed: {
        qxpeizhi: function () {
            return this.$store.state.qxpeizhi;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    methods: {
        //打开添加职务
        openAddzhiwu: function () {
            this.zhiwushow = true;
            this.form = {
                name: "",
                bmid: "",
                authority: []
            };
        },
        //添加职务
        addzhiwuFun: function () {
            var self = this;

            if (self.form.name.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入职务名称",
                    type: "warning"
                })
                return;
            }

            if (self.form.bmid == '') {
                self.$message({
                    showClose: true,
                    message: "请选择所属部门",
                    type: "warning"
                })
                return;
            }

            self.form.authority = self.$refs.addtree.getCheckedKeys();
            if (self.form.authority.length == 0) {
                this.$confirm('您没有给该职位选择任何权限，是否继续保存?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.addQxFunction();
                }).catch(function () {
                    return false;
                });
                return;
            }

            self.addQxFunction();

        },
        //删除职务
        delZhiwei: function () {
            var self = this;
            if (self.forms.id == "") {
                self.$message({
                    showClose: true,
                    message: "请先在左边选择职务",
                    type: "warning"
                })
                return;
            }
            self.$confirm('是否确定删除‘' + self.forms.name + '’这个职务?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/user_group/group_del/" + self.forms.id)
                    .then(function (data) {
                        self.delqx = false;
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: "职务删除成功",
                            });
                            self.forms = {
                                id: "",
                                name: "",
                                bmid: "",
                                authority: []
                            };
                            self.$refs.tree.setCheckedKeys(self.forms.authority);
                            self.$store.commit("getZQWBM", {
                                self: self,
                                successFun: function (obj) {
                                    for (var i = 0; i < obj.data.length; i++) {
                                        for (var k = 0; k < obj.data[i].group.length; k++) {
                                            obj.data[i].group[k].department_id = obj.data[i].id;
                                        }
                                    }
                                    self.department = obj.data;
                                    self.leftTree = [self.forms.id];
                                }
                            });
                        } else {
                            self.$store.commit("checkError", {
                                self: self,
                                data: data.data
                            });
                        }
                    }, function (err) {
                        self.delqx = false;
                        self.$message({
                            showClose: true,
                            message: "网络错误",
                            type: "error"
                        })
                    })
            }).catch(function () {

            });


        },
        //添加权限函数
        addQxFunction: function () {
            var self = this;
            self.addquanxian = true;
            var formData = new FormData();
            var quanxian = JSON.stringify(self.form.authority);
            formData.append("department_id", self.form.bmid);
            formData.append("name", self.form.name);
            formData.append("authority", quanxian);

            self.$http.post("/api/user_group/group_add", formData)
                .then(function (data) {
                    self.addquanxian = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "职务添加成功",
                        });
                        self.form = {
                            name: "",
                            bmid: "",
                            authority: []
                        };
                        self.$refs.addtree.setCheckedKeys(self.form.authority);
                        self.zhiwushow = false;
                        self.$store.commit("getZQWBM", {
                            self: self,
                            successFun: function (obj) {
                                for (var i = 0; i < obj.data.length; i++) {
                                    for (var k = 0; k < obj.data[i].group.length; k++) {
                                        obj.data[i].group[k].department_id = obj.data[i].id;
                                    }
                                }
                                self.department = obj.data;
                                self.leftTree = [self.forms.id];
                            }
                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.addquanxian = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //判断修改权限条件
        panduanxiugaiqx: function () {
            var self = this;

            if (self.forms.name.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "职务名称不能为空",
                    type: "warning"
                })
                return;
            }

            if (self.forms.bmid == '') {
                self.$message({
                    showClose: true,
                    message: "请选择所属部门",
                    type: "warning"
                })
                return;
            }

            self.forms.authority = self.$refs.tree.getCheckedKeys();
            if (self.forms.authority.length == 0) {
                self.$confirm('您没有给该职位选择任何权限，是否继续保存?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    self.saveQuanxian();
                }).catch(function () {
                    return false;
                });
                return;
            }

            self.saveQuanxian();
        },
        //修改权限
        saveQuanxian: function () {
            var self = this;
            self.saveqx = true;
            var formData = new FormData();
            var quanxian = JSON.stringify(self.forms.authority);
            formData.append("department_id", self.forms.bmid);
            formData.append("name", self.forms.name);
            formData.append("authority", quanxian);

            self.$http.post("/api/user_group/group_edit/" + self.forms.id, formData)
                .then(function (data) {
                    self.saveqx = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "职务修改成功",
                        });
                        self.$refs.tree.setCheckedKeys(self.forms.authority);
                        self.$store.commit("getZQWBM", {
                            self: self,
                            successFun: function (obj) {
                                for (var i = 0; i < obj.data.length; i++) {
                                    for (var k = 0; k < obj.data[i].group.length; k++) {
                                        obj.data[i].group[k].department_id = obj.data[i].id;
                                    }
                                }
                                self.department = obj.data;
                                self.leftTree = [self.forms.id];
                            }
                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.saveqx = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },

        //获取职务权限
        getZWquanxian: function (id) {
            var self = this;
            if (self.loading) {
                self.$message({
                    showClose: true,
                    message: "请稍后再试",
                    type: "warning"
                })
                return;
            }
            self.loading = true;
            self.$http.get("/api/user_group/group_info/" + id)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        self.quanxianList = data.data.data.authority;
                        if (self.quanxianList.length == 0) {
                            self.$message({
                                showClose: true,
                                message: "此职务暂无权限",
                                type: "warning"
                            })
                        }
                        self.$refs.tree.setCheckedKeys(self.quanxianList);
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
        },
        //职务点击
        handleNodeClick: function (data) {
            if (data.group) {
                return;
            }
            this.forms.id = data.id;
            this.forms.name = data.name;
            this.forms.bmid = data.department_id;
            this.getZWquanxian(data.id);
        }
    },
    created: function () {
        var self = this;
        this.$store.commit('setSettingLeftMeau', 7);
        if (this.qxpeizhi.length == 0) {
            this.$store.commit('getPeizhi');
        }
        self.$store.commit("getZQWBM", {
            self: self,
            successFun: function (obj) {
                for (var i = 0; i < obj.data.length; i++) {
                    for (var k = 0; k < obj.data[i].group.length; k++) {
                        obj.data[i].group[k].department_id = obj.data[i].id;
                    }
                }
                self.department = obj.data;
                self.leftTree = [self.forms.id];
            }
        });
        // this.getZWquanxian("428");

    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.zw_btn {
    display: block;
}

.zhiwu {
    .el-select {
        width: 100%;
    }
}

.quanxin_box {
    border: 1px solid #ddd;
}

.quanxian_title {
    overflow: hidden;
    padding-top: 20px;
    span {
        display: block;
        float: left;
        font-size: 16px;
        color: @c1;
    }
    s {
        display: block;
        float: right;
        text-decoration: none;
    }
}

.tishi {
    font-weight: normal;
    color: @c3;
    font-size: 14px;
    margin-bottom: 20px;
}

.el-tree {
    border: none;
    margin-top: 10px;
}

.add_zhiwubox {
    border: 1px solid #ddd;
    padding: 10px;
    padding-bottom: 20px;
}
</style>