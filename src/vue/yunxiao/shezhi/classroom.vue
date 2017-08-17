<template>
    <div>
        <h1 class="right_title">
            教室设置
            <article>添加及修改各校区教室信息</article>
            <el-button type="success" size="small" @click="tianjia">添加教室</el-button>
        </h1>
        <tabs :list="titleList" :datas="classroomLists" v-on:tabfun="tabCallBack" text="" v-if="myMessage.campus_id == 1"></tabs>
        <ul class="cardList" v-loading="loading1" element-loading-text="加载中">
            <li v-for="(list,index) in classroomList" :class="'cark_li'+index%4">
                <div>
                    <i class="el-icon-edit" @click="xiugai(list)"></i>
                    <i class="el-icon-delete" @click="deleteClassroom(list.id,list.room_name)"></i>
                </div>
                <h2>{{ list.campus_name }}</h2>
                <h1>{{ list.room_name }}</h1>
                <p>{{ list.remark }}</p>
            </li>
        </ul>
    
        <!--修改教室弹框-->
        <el-dialog :title="title" v-model="dialogFormVisible" size="tiny" v-loading="loading" element-loading-text="加载中">
            <p class="fengexian"></p>
            <el-form :model="classroom">
                <el-form-item label="名称" :label-width="formLabelWidth">
                    <el-input v-model="classroom.room_name" auto-complete="off" placeholder="请输入教室名称"></el-input>
                </el-form-item>
                <el-form-item label="校区" :label-width="formLabelWidth" v-show="classroom.type == 2">
                    <el-select v-model="campus_list" placeholder="请选择校区" @change="changeCampus" ref="selectClassroomCampus" v-if="myMessage.campus_id == 1">
                        <el-option :label="list.name" v-model="list.id" v-for="list in campus"></el-option>
                    </el-select>
                    <el-select v-model="campus_list" placeholder="请选择校区" ref="selectClassroomCampus" v-if="myMessage.campus_id != 1" disabled>
                        <el-option :label="list.name" v-model="list.id" v-for="list in campus"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="备注" :label-width="formLabelWidth">
                    <el-input v-model="classroom.remark" placeholder="最多可输入30个字符" maxlength="30"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="modifyClassroom">确 定</el-button>
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
            loading: false,
            loading1: false,
            title: "修改教室信息",
            dialogFormVisible: false,
            formLabelWidth: "40px",
            titleList: [],
            campus_list: 0,
            classroom: {
                id: 0,
                room_name: "",
                remark: "",
                type: 2
            },
            classroomLists: window.sessionStorage.getItem("classroomList") ? JSON.parse(window.sessionStorage.getItem("classroomList")) : [],
            classroomList: window.sessionStorage.getItem("classroomList") ? JSON.parse(window.sessionStorage.getItem("classroomList")) : [],
        }
    },
    methods: {
        //选择校区回掉
        tabCallBack: function (id) {
            var self = this;
            if (id == 0) {
                self.classroomList = self.classroomLists;
                return;
            }
            var arr = [];
            for (var i = 0; i < self.classroomLists.length; i++) {
                if (self.classroomLists[i].campus_id == id) {
                    arr.push(self.classroomLists[i]);
                }
            }
            self.classroomList = arr;
        },
        //获取教室列表
        getClassroom: function () {
            var self = this;
            if (self.classroomLists.length == 0) {
                self.loading1 = true;
            }
            self.$http.get("/api/room")
                .then(function (data) {
                    self.loading1 = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            for (var j = 0; j < self.campus.length; j++) {
                                if (self.campus[j].id == data.data.data[i].campus_id) {
                                    data.data.data[i].campus_name = self.campus[j].name;
                                }
                            }
                        }
                        self.classroomLists = data.data.data;
                        self.classroomList = data.data.data;
                        window.sessionStorage.setItem("classroomList", JSON.stringify(self.classroomLists))
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading1 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除教室
        deleteClassroom: function (id, name) {

            var self = this;
            self.$confirm('确定要删除"' + name + '"?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/room/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功",
                            });
                            self.getClassroom();
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
        //修改打开
        xiugai: function (data) {
            this.title = "修改教室";
            this.dialogFormVisible = true;
            this.classroom.id = data.id;
            this.classroom.room_name = data.room_name;
            this.classroom.remark = data.remark;
            this.classroom.type = 1;
        },
        //添加打开
        tianjia: function () {
            this.title = "添加教室";
            this.dialogFormVisible = true;
            this.classroom.type = 2;
            this.classroom = {
                id: 0,
                room_name: "",
                remark: "",
                type: 2
            };
        },
        //选择校区
        changeCampus: function () {
            this.classroom.campus_id = this.$refs.selectClassroomCampus.value;
        },
        //修改教室 或者添加
        modifyClassroom: function () {
            var self = this;
            if (self.classroom.room_name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入校区名称",
                    type: 'warning'
                });
                return;
            }
            var formData = new FormData();
            formData.append("room_name", self.classroom.room_name);
            formData.append("remark", self.classroom.remark);
            if (self.classroom.type == 2) {
                if (self.campus_list == 0) {
                    self.$message({
                        showClose: true,
                        message: "请选择校区",
                        type: 'warning'
                    });
                    return;
                }
                formData.append("campus_id", self.campus_list);
            }
            self.loading = true;
            var url = self.classroom.type == 1 ? ("/api/room/" + self.classroom.id) : "/api/room";
            self.$http.post(url, formData)
                .then(function (data) {
                    self.loading = false;
                    self.dialogFormVisible = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: data.data.msg,
                        });
                        self.getClassroom();
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function (err) {
                    self.loading = false;
                    self.dialogFormVisible = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        this.$store.commit('setSettingLeftMeau', 3);
        this.getClassroom();
        this.titleList = this.campus;
        this.campus_list = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;
        this.classroom.campus_id = this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id;

        for (var i = 0; i < this.titleList.length; i++) {
            this.titleList[i].num = 0;
        }
    }
}

</script>

<style lang="less" scoped>
.el-select {
    width: 100%;
}
</style>