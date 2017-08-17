<template>
    <div>
        <h1 class="right_title">
            课程管理
            <br>
            <el-button type="success" size="small" @click="openAddnewSubject" v-if="yx_kcgl">添加新课程</el-button>
            <el-button type="success" size="small" @click="$router.push({name:'curriculums'})" v-if="yx_xtsz">类别管理</el-button>
            <el-input placeholder="请输入课程名称" icon="search" v-model="search" :on-icon-click="handleIconClick" class="btn" @keyup.enter.native="handleIconClick">
            </el-input>
        </h1>
        <ul class="campus_list">
            <li :class="{ active : campus_id == -2 }" @click="clickCampus(-2,'')">全部</li>
            <li :class="{ active : campus_id == -1 }" @click="clickCampus(-1,1)">所有校区</li>
            <li v-for="(list,index) in campus" :class="{ active : campus_id == index }" @click="clickCampus(index,list.id)">{{ list.name }}</li>
        </ul>
    
        <el-table :data="SubjectList.subject" style="width: 100%" class="table_moban" v-loading="loading" element-loading-text="加载中">
            <el-table-column prop="subject" label="课程名称">
                <template scope="scope">
                    <el-popover trigger="hover" placement="right-end">
                        <p>描述: {{ scope.row.remark }}</p>
                        <div slot="reference" class="name-wrapper">
                            {{ scope.row.subject }}
                        </div>
                    </el-popover>
                </template>
            </el-table-column>
            </el-table-column>
            <el-table-column prop="campus_name" label="所属校区">
            </el-table-column>
            <el-table-column prop="type_name" label="课程类别">
            </el-table-column>
            <el-table-column prop="time" label="开设时间">
            </el-table-column>
            <!--<el-table-column prop="" label="授课老师" width="100">
                                    </el-table-column>
                                    <el-table-column prop="" label="开课班级">
                                    </el-table-column>-->
            <!--<el-table-column prop="" label="累计已上课时" width="130">
                                    </el-table-column>-->
            <!--<el-table-column prop="remark" label="备注">
                                    </el-table-column>-->
            <el-table-column label="操作" width="230" v-if="myMessage.campus_id == 1">
                <template scope="scope">
                    <el-button type="success" size="mini" @click="$router.push({name:'curriculum_detail_class',params:{id:scope.row.id}})">查看详情</el-button>
                    <el-button type="info" size="mini" @click="$router.push({name:'curriculum_detail_info',params:{id:scope.row.id}})" v-if="yx_kcgl">编辑</el-button>
                    <el-button type="danger" size="mini" @click="delSubject(scope.row.id,scope.row.subject)" v-if="yx_kcgl">删除</el-button>
                </template>
            </el-table-column>
            <el-table-column label="操作" width="230" v-if="myMessage.campus_id != 1">
                <template scope="scope">
                    <el-button type="success" size="mini" @click="$router.push({name:'curriculum_detail_class',params:{id:scope.row.id}})">查看详情</el-button>
                    <el-button type="info" size="mini" @click="$router.push({name:'curriculum_detail_info',params:{id:scope.row.id}})" v-if="yx_kcgl && myMessage.campus_id==scope.row.campus_id">编辑</el-button>
                    <el-button type="danger" size="mini" @click="delSubject(scope.row.id,scope.row.subject)" v-if="yx_kcgl && myMessage.campus_id==scope.row.campus_id">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-show="SubjectList.count>SubjectList.limit">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="subindex" :page-sizes="[10, 20, 30, 40]" :page-size="pageSize" layout="total, prev, pager, next" :total="SubjectList.count">
            </el-pagination>
        </div>
    
        <!--添加新课程-->
        <el-dialog title="添加新课程" v-model="dialogFormVisible" size="small">
            <p class="fengexian"></p>
            <el-form :model="newSubject">
                <el-form-item label="课程名称" :label-width="formLabelWidth">
                    <el-input v-model="newSubject.subject" auto-complete="off" placeholder="请输入课程名称"></el-input>
                </el-form-item>
                <div class="row_form">
                    <div class="left_form">
                        <el-form-item label="课程类别" :label-width="formLabelWidth">
                            <el-select v-model="newSubject.type_id" placeholder="请选择课程类别">
                                <el-option :label="list.type_name" v-model="list.id" v-for="list in SubjectLBList"></el-option>
                            </el-select>
                        </el-form-item>
                    </div>
                    <div class="right_form">
                        <el-form-item label="校区" :label-width="formLabelWidth">
                            <el-select v-model="newSubject.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id == 1" @change="changeCampuss">
                                <el-option :label="allCampus_name" :value="allCampus_id"></el-option>
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                            <el-select v-model="newSubject.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1" disabled>
                                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </div>
                </div>
    
                <el-form-item label="授课老师" :label-width="formLabelWidth">
                    <div class="subject_teacher">
                        <div class="sub_left">
                            <el-input placeholder="输入老师姓名" icon="search" :on-icon-click="serachTeach" v-model="searchVal" @input="serachTeach" class="serch_btn">
                            </el-input>
                            <el-table :data="ygList" style="width: 100%" height="235" @row-click="changeTeach">
                                <el-table-column prop="name" label="姓名">
                                </el-table-column>
                                <!--<el-table-column prop="" label="工号">
                                                    </el-table-column>-->
                                <el-table-column prop="campus_id" label="校区">
                                    <template scope="scope">
                                        <span v-if="scope.row.campus_id == 1">所有校区</span>
                                        <div v-if="scope.row.campus_id != 1">
                                            <span v-for="item in campus" v-if="item.id == scope.row.campus_id">{{ item.name }}</span>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="department_id" label="部门">
                                    <template scope="scope">
                                        <span v-for="item in department" v-if="item.id == scope.row.department_id">{{ item.name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="group_id" label="职位">
                                    <template scope="scope">
                                        <span v-for="item in department" v-if="item.id == scope.row.department_id">
                                            <span v-for="list in item.group" v-if="list.id == scope.row.group_id">{{ list.name }}</span>
                                        </span>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                        <div class="sub_right">
                            <h1>已选老师</h1>
                            <el-tag v-for="tag in tags" :closable="true" type="success" @close="delTeacher(tag.id)">
                                {{tag.name}}
                            </el-tag>
                            <p v-show="tags.length == 0">请在左边点击选择授课老师</p>
                        </div>
                    </div>
                </el-form-item>
    
                <el-form-item label="课程介绍" :label-width="formLabelWidth">
                    <el-input v-model="newSubject.remark" maxlength="100" placeholder="最多可输入50个字符" type="textarea" :rows="2"></el-input>
                </el-form-item>
                <div class="button">
                    <el-button type="primary" class="btns" @click="dialogFormVisible = false">取消</el-button>
                    <el-button type="primary" @click="addNewSubject">保存</el-button>
                </div>
    
        </el-dialog>
    </div>
</template>
<script>
module.exports = {

    computed: {
        yx_class_meau_id: function () {
            return this.$store.state.yx_class_meau_id;
        },
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        department: function () {
            return this.$store.state.department;
        },
        subindex: function () {
            return this.$store.state.subindex;
        }
    },
    data: function () {
        return {
            yx_xtsz: false,//判断是否可以管理类别

            allCampus_name: "所有校区",
            allCampus_id: "1",

            left_id: -1,//左侧分类下标
            search: "",
            campus_id: -2,
            campus_ids: '',
            type_id: '',

            dialogFormVisible: false,
            loading: false,
            formLabelWidth: "70px",
            newSubject: {
                id: "",
                subject: "",
                campus_id: "",
                type_id: "",
                teacher: [],
                remark: ""
            },
            // teacher_lists: [],
            // teacher_list: [],
            searchVal: "",
            tags: [],
            obj: {//用于搜索员工
                pageSize: "",
                campus_id: "",
                department_id: "",
                search: ""
            },

            pageSize: 15,

            isFocus: false,//判断搜索框焦点

            isEnd: true,

            SubjectLBList: window.sessionStorage.getItem("SubjectLBList") ? JSON.parse(window.sessionStorage.getItem("SubjectLBList")) : [],
            SubjectList: {
                subject: []
            },

            tc_campus_id: "",

            ygList: [],

            yx_kcgl:false
        }
    },
    watch: {
        $route: function (val) {
            var self = this;
            self.type_id = val.params.id == 'All' ? '' : val.params.id;
            self.left_id = val.query.index;
            self.$store.commit('setsubject_meau_id', self.left_id);
            self.$store.commit('setsubindex', 1);
            self.getSubjectList({
                limit: self.pageSize,
                type_id: self.type_id,
                campus_id: self.campus_ids,
                search: ""
            })
        },
        ygSimple: function (val) {
            this.ygList = val;
        }
    },
    methods: {
        //选择校区
        changeCampuss: function (val) {
            var self = this;
            self.tc_campus_id = val;
            self.$store.commit('getYGList', {
                self: self,
                campus_id: val,
                is_dk: 1
            });
        },
        //搜索老师
        serachTeach: function (val) {
            var self = this;
            if (val == "") {
                self.$store.commit('getYGList', {
                    self: self,
                    campus_id: self.tc_campus_id,
                    is_dk: 1
                });
                return;
            }
            var arr = [];
            for (var i = 0; i < self.ygSimple.length; i++) {
                if (self.ygSimple[i].name.indexOf(val) != -1) {
                    arr.push(self.ygSimple[i]);
                }
            }
            if (arr.length == 0) {
                return;
            }
            self.ygList = arr;

        },
        //取消选择老师
        delTeacher: function (id) {
            for (var i = 0; i < this.tags.length; i++) {
                if (this.tags[i].id == id) {
                    this.tags.splice(i, 1);
                    break;
                }
            }
        },
        //点击行选择老师
        changeTeach: function (row, event, column) {
            for (var i = 0; i < this.tags.length; i++) {
                if (this.tags[i].id == row.id) {
                    this.$message({
                        showClose: true,
                        message: "已经选择了该老师，请不要重选择",
                        type: 'warning'
                    });
                    return;
                }
            }
            this.tags.push(row);
        },

        //点击校区筛选
        clickCampus: function (id, id2) {
            var self = this;
            if (!self.isEnd) {
                self.$message({
                    showClose: true,
                    message: "数据加载中，请稍后",
                    type: 'warning'
                });
                return;
            }
            self.isEnd = false;
            self.campus_id = id;
            self.campus_ids = id2;
            self.$store.commit('setsubindex', 1);
            self.getSubjectList({
                limit: self.pageSize,
                type_id: self.type_id,
                campus_id: self.campus_ids,
                search: ""
            })
        },
        //时间格式化
        getTime: function (val) {
            var date = new Date(val * 1000);
            return date.getFullYear() + "年" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '月' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate()) + "日";
        },
        //重组课程对象
        Recombination: function (objs) {
            var self = this;
            var obj = objs;
            for (var i = 0; i < obj.subject.length; i++) {
                obj.subject[i].time = self.getTime(obj.subject[i].time);
                for (var j = 0; j < self.campus.length; j++) {
                    if (obj.subject[i].campus_id == 1) {
                        obj.subject[i].campus_name = "所有校区";
                        break;
                    }
                    if (obj.subject[i].campus_id == self.campus[j].id) {
                        obj.subject[i].campus_name = self.campus[j].name;
                        break;
                    }
                }
                for (var k = 0; k < self.SubjectLBList.length; k++) {
                    if (obj.subject[i].type_id == self.SubjectLBList[k].id) {
                        obj.subject[i].type_name = self.SubjectLBList[k].type_name;
                        break;
                    }
                }
            }
            self.SubjectList = obj;
            self.loading = false;
            self.isEnd = true;
        },
        //获取课程分类
        getSubjectfenlei: function (obj) {
            var self = this;
            self.$http.get("/api/subject/type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.SubjectLBList = data.data.data;
                        window.sessionStorage.setItem("SubjectLBList", JSON.stringify(self.SubjectLBList));
                        self.Recombination(obj);
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
        //获取课程列表
        getSubjectList: function (obj) {
            var self = this;
            self.loading = true;
            var limit = obj.limit;
            var type_id = obj.type_id;
            var campus_id = obj.campus_id;
            var search = obj.search;
            self.$http.get("/api/subject?page=" + self.subindex + "&limit=" + limit + "&type_id=" + type_id + "&campus_id=" + campus_id + "&search=" + search)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (self.SubjectLBList.length == 0) {
                            self.getSubjectfenlei(data.data.data);
                            return;
                        }
                        self.Recombination(data.data.data);
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
        //点击页码
        handleCurrentChange: function (val) {
            var self = this;
            self.$store.commit('setsubindex', val);
            self.getSubjectList({
                limit: self.pageSize,
                type_id: self.type_id,
                campus_id: self.campus_ids,
                search: ""
            })
        },
        //选择每页显示条数
        handleSizeChange: function (val) {
            var self = this;
            self.pageSize = val;
            self.getSubjectList({
                limit: self.pageSize,
                type_id: self.type_id,
                campus_id: self.campus_ids,
                search: ""
            })
        },
        //搜索
        handleIconClick: function () {
            var self = this;
            self.campus_id = -2;
            self.left_id = -1;
            self.$store.commit('setsubject_meau_id', self.left_id);
            self.campus_ids = '';
            self.type_id = '';
            self.$store.commit('setsubindex', 1);
            self.getSubjectList({
                limit: 10,
                type_id: "",
                campus_id: "",
                search: self.search.trim()
            })
        },
        //打开新增课程
        openAddnewSubject: function () {
            var self = this;
            self.dialogFormVisible = true;
            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.newSubject = {
                id: "",
                subject: "",
                campus_id: campus_id,
                type_id: "",
                teacher: [],
                remark: ""
            }
            //获取老师列表
            // this.getyuangongList(this.obj);
        },
        //添加新课程 编辑课程
        addNewSubject: function () {
            var self = this;
            if (self.newSubject.subject.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写课程名称",
                    type: 'warning'
                });
                return;
            }
            if (self.newSubject.type_id == "") {
                self.$message({
                    showClose: true,
                    message: "请选择课程类别",
                    type: 'warning'
                });
                return;
            }
            if (self.tags.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请选择授课老师",
                    type: 'warning'
                });
                return;
            }

            var formData = new FormData();

            formData.append("remark", self.newSubject.remark);

            var arr = [];
            for (var i = 0; i < self.tags.length; i++) {
                arr.push(self.tags[i].id)
            }

            formData.append("teacher", arr);


            formData.append("subject", self.newSubject.subject);
            formData.append("campus_id", self.newSubject.campus_id);
            formData.append("type_id", self.newSubject.type_id);

            self.$http.post("/api/subject", formData)
                .then(function (data) {
                    self.dialogFormVisible = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit('setsubindex', 1);
                        self.getSubjectList({
                            limit: self.pageSize,
                            type_id: "",
                            campus_id: "",
                            search: ""
                        });
                        self.$router.push({ name: 'curriculum_list', params: { id: 'All' }, query: { index: -1 } })
                        self.campus_id = -2;
                        self.$store.commit("success", {
                            self: self,
                            msg: "添加成功",
                        });
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.dialogFormVisible = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除课程
        delSubject: function (id, name) {

            var self = this;
            self.$confirm('确定要删除"' + name + '"?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/subject/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功",
                            });
                            self.$store.commit('setsubindex', 1);
                            self.getSubjectList({
                                limit: self.pageSize,
                                type_id: self.type_id,
                                campus_id: self.campus_ids,
                                search: ""
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
            }).catch(function () {

            });


        }
    },
    created: function () {
        var self = this;
        self.$store.commit('setsubindex', 1);
        self.getSubjectList({
            limit: self.pageSize,
            type_id: "",
            campus_id: "",
            search: ""
        });

        self.ygList = self.ygSimple;

        self.tc_campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$store.commit('getYGList', {
            self: self,
            campus_id: self.tc_campus_id,
            is_dk: 1
        });

        //课程设置
        this.yx_kcgl = this.pdqx(["yx_kcgl", "yx"]);
        //系统设置权限
        this.yx_xtsz = this.pdqx(["yx_xtsz", "yx"]);
    }
}

</script>
<style lang="less" scoped>
.serch_btn {
    width: 100%;
    margin-bottom: 10px;
}

.button {
    padding: 10px 0;
    text-align: right;
}

.btn {
    width: 200px;
}

.el-select {
    width: 100%;
}

.right_form {
    text-align: right;
    .el-select {
        text-align: right;
    }
}
</style>