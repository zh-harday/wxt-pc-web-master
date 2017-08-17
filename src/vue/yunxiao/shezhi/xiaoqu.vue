<template>
    <div>
        <h1 class="right_title">
            校区设置
            <article>学校详细信息及各分校信息的设置</article>
        </h1>
        <h1 class="right_title_1">
            学校信息
        </h1>
        <div class="box">
            <div class="left_logo">
                <span v-loading="loading2" element-loading-text="加载中">
                    <img :src="schoolMessage.logo" alt="">
                </span>
                <div>
                    <el-button type="success" size="small">上传学校logo</el-button>
                    <form id="logo" >
                        <input type="file" @change="updagePhoto" name="nlogo">
                        
                    </form>
                </div>
            </div>
            <div class="right_con">
                <el-input placeholder="请输入学校名称" v-model="schoolMessage.name" maxlength="50">
                    <template slot="prepend" id="ed">学校名称</template>
                </el-input>
                <el-input placeholder="请输入学校地址" v-model="schoolMessage.address" maxlength="50">
                    <template slot="prepend">学校地址</template>
                </el-input>
                <div class="two">
                    <el-input placeholder="请输入联系人" v-model="schoolMessage.leader" maxlength="20">
                        <template slot="prepend">联系人</template>
                    </el-input>
                    <el-input placeholder="请输入联系电话" v-model="schoolMessage.phone" maxlength="20">
                        <template slot="prepend">联系电话</template>
                    </el-input>
                </div>
                <div class="danhang">
                    <span>学校介绍</span>
                    <div>
                        <el-input placeholder="请输入内容" v-model="schoolMessage.description" type="textarea" :rows="2" maxlength="500"></el-input>
                    </div>
                </div>
                <div class="update_message">
                    <el-button type="success" size="small" @click="updateSchool" :loading="tijiao">更新信息</el-button>
                </div>
            </div>
        </div>
        <h1 class="right_title_1">
            校区信息
            <br>
            <el-button type="success" size="small" @click="openAddCampus" v-if="campus.length < schoolMessage.campus_quota">
                添加校区
            </el-button>
        </h1>
        <div class="box">
            <el-table :data="campus" style="width: 100%">
                <el-table-column label="编号" width="120">
                    <template scope="scope">
                        {{ scope.$index+1 }}
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="校区名称">
                </el-table-column>
                <el-table-column prop="contacts" label="联系人" width="100">
                </el-table-column>
                <el-table-column prop="phone" label="联系电话" width="135">
                </el-table-column>
                <el-table-column prop="qtdh" label="前台电话" width="135">
                </el-table-column>
                <el-table-column prop="address" label="地址">
                </el-table-column>
                <el-table-column prop="remark" label="备注">
                </el-table-column>
                <el-table-column label="操作" width="170">
                    <template scope="scope">
                        <el-button type="text" class="blue" @click="updateCampus(scope)">
                            修改
                        </el-button>
                        <el-button type="text" class="wan">
                            停用该校区
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <!--修改校区信息弹框-->
        <el-dialog :title="title" v-model="dialogFormVisible" size="tiny">
            <p class="fengexian"></p>
            <el-form :model="form">
                <el-form-item label="校区名称" :label-width="formLabelWidth">
                    <el-input v-model="form.name" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="联系人" :label-width="formLabelWidth">
                    <el-input v-model="form.contacts"></el-input>
                </el-form-item>
                <el-form-item label="联系电话" :label-width="formLabelWidth">
                    <el-input v-model="form.phone"></el-input>
                </el-form-item>
                <el-form-item label="前台电话" :label-width="formLabelWidth">
                    <el-input v-model="form.qtdh"></el-input>
                </el-form-item>
                <el-form-item label="校区地址" :label-width="formLabelWidth">
                    <el-input v-model="form.address"></el-input>
                </el-form-item>
                <el-form-item label="备注" :label-width="formLabelWidth">
                    <el-input v-model="form.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="updateCampusOK">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {

    computed: {
        schoolMessage: function () {
            return this.$store.state.schoolMessage;
        },
        campus: function () {
            return this.$store.state.campus;
        }
    },
    data: function () {
        return {
            title: "修改校区信息",
            loading2: false,
            nlogo: null,
            tijiao:false,

            dialogFormVisible: false,
            form: {
                id: 0,
                name: '',
                contacts: '',
                phone: '',
                qtdh: '',
                address: '',
                remark: ''
            },
            formLabelWidth: '90px'
        }
    },
    methods: {
        //上传头像
        updagePhoto: function () {
            var self = this;
            var val = document.getElementById("logo");
            var formData = new FormData(val);
            self.loading2 = true;
            self.$http.post("/api/school/logo", formData)
                .then(function (data) {
                    self.loading2 = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "上传成功",
                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading2 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //更新学校信息
        updateSchool: function () {
            var self = this;
            var formData = new FormData();

            formData.append("name", self.schoolMessage.name);
            formData.append("address", self.schoolMessage.address);
            formData.append("leader", self.schoolMessage.leader);
            formData.append("phone", self.schoolMessage.phone);
            formData.append("description", self.schoolMessage.description);

            self.tijiao = true;
            self.$http.post("/api/school/info", formData)
                .then(function (data) {
                    self.tijiao = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: '更新成功',
                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.tijiao = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //更新校区信息
        updateCampus: function (data) {
            var self = this;
            self.title = "修改校区信息";
            self.dialogFormVisible = true;
            self.form = data.row;
        },
        //更新校区信息 或者 添加校区
        updateCampusOK: function () {
            var self = this;
            var formData = new FormData();
            formData.append("name", self.form.name);
            formData.append("contacts", self.form.contacts);
            formData.append("phone", self.form.phone);
            formData.append("qtdh", self.form.qtdh);
            formData.append("address", self.form.address);
            formData.append("remark", self.form.remark);
            var url = self.form.id == 0 ? "/api/campus" : ("/api/campus/" + self.form.id);
            self.$http.post(url, formData)
                .then(function (data) {
                    self.loading2 = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.dialogFormVisible = false;
                        self.$store.commit("getcampus",self);
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading2 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //打开添加校区
        openAddCampus: function () {
            this.title = "增加校区";
            this.dialogFormVisible = true;
            this.form = {
                id: 0,
                name: '',
                contacts: '',
                phone: '',
                qtdh: '',
                address: '',
                remark: ''
            }
        }
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 1);
    }
}

</script>
<style lang="less" scoped>
.box {
    overflow: hidden;
    background-color: #fff;
    border-radius: 5px;
    display: flex;
    width: 100%;
    margin-bottom: 30px;
}

.left_logo {
    width: 220px;
    height: 250px;
    span {
        display: block;
        width: 220px;
        height: 220px;
        position: relative;
    }
    div {
        width: 100px;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        button {
            display: block;
            margin: 0 auto;
        }
        form{
            cursor: pointer;
            display: block;
            overflow: hidden;
            position: absolute;
            top:0;
            left:0;
            width: 100%;
            height: 100%;
        }
        input {
            display: block;
            position: absolute;
            left: -150%;
            top: -150%;
            width: 400%;
            height: 400%;

            opacity: 0;
            cursor: pointer;
        }
    }
    img {
        display: block;
        width: 180px;
        max-height: 180px;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        margin: auto;
    }
}

.right_con {
    flex: 1;
    padding: 20px;
    >div {
        margin-bottom: 20px;
        &.two {
            overflow: hidden;
            >div {
                width: 50%;
                float: left;
            }
        }
    }
    .danhang {
        overflow: hidden;
        display: flex;
        span {
            display: block;
            width: 56px;
            background-color: #fff;
            border: none;
            color: #828282;
            float: left;
            padding: 0 10px;
        }
        >div {
            flex: 1;
        }
    }
}

.update_message {
    overflow: hidden;
    button {
        display: block;
        float: right;
    }
}
</style>