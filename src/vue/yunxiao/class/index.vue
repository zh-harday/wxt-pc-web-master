<template>
    <div class="yx_class">
        <div class="left">
            <h1>班级管理
                <article>对已开设的班级进行日常教学管理工作</article>
            </h1>
            <div v-if="yx_bjgl_all">
                <router-link :to="{name:'class',query:{staus:'',index:1}}" :class="{ active : $route.query.index==1 }">所有班级({{ allClass.all }})</router-link>
                <router-link :to="{name:'class',query:{staus:0,index:2}}" :class="{ active : $route.query.index==2 }">已开课({{ allClass.ykk }})</router-link>
                <router-link :to="{name:'class',query:{staus:2,index:3}}" :class="{ active : $route.query.index==3 }">预报名({{ allClass.ybm }})</router-link>
                <router-link :to="{name:'class',query:{staus:-1,index:4}}" :class="{ active : $route.query.index==4 }">已停课({{ allClass.ytk }})</router-link>
            </div>
            <div v-if="yx_bjgl_my">
                <router-link :to="{name:'class1',query:{staus:'',index:5}}" :class="{ active : $route.query.index==5 }">我的班级({{ myClass.all }})</router-link>
                <router-link :to="{name:'class1',query:{staus:0,index:6}}" :class="{ active : $route.query.index==6 }">已开课({{ myClass.ykk }})</router-link>
                <router-link :to="{name:'class1',query:{staus:2,index:7}}" :class="{ active : $route.query.index==7 }">预报名({{ myClass.ybm }})</router-link>
                <router-link :to="{name:'class1',query:{staus:-1,index:8}}" :class="{ active : $route.query.index==8 }">已停课({{ myClass.ytk }})</router-link>
            </div>
        </div>
        <div class="right">
            <h1 class="right_title">
                班级管理
                <br>
                <el-button type="primary" size="small" @click="openNewClass">添加新班级</el-button>
                <el-input placeholder="请输入班级名称" icon="search" v-model="option.search" :on-icon-click="serachFun_sub" class="btn" @keyup.enter.native="serachFun_sub"></el-input>
            </h1>
            <div class="shaixuan">
                <el-select v-model="option.campus_id" placeholder="请选择校区" @change="changecampus" filterable v-if="myMessage.campus_id== 1">
                    <el-option label="全部" value="0"></el-option>
                    <el-option v-for="item in campus" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <el-select v-model="option.fee_method" placeholder="请选择计费方式" @change="changeFangshi">
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="'按'+list.label" :value="list.method" v-for="list in class_method"></el-option>
                </el-select>
                <el-select v-model="option.teacher_id" placeholder="请选择班主任" filterable @change="changeBZR">
                    <el-option label="全部" value="0"></el-option>
                    <el-option :label="item.name" :value="item.id" v-for="item in ygSimple"></el-option>
                </el-select>
                <el-date-picker v-model="option.time" type="daterange" align="right" placeholder="选择日期范围" :picker-options="pickerOptions" @change="changTime">
                </el-date-picker>
            </div>
            <el-table :data="classList.class" style="width: 100%" v-loading="loading" element-loading-text="加载中">
                <el-table-column prop="class_name" label="班级名称"></el-table-column>
                <el-table-column prop="campus_name" label="所在校区"></el-table-column>
                <el-table-column prop="teacher" label="班主任"></el-table-column>
                <el-table-column prop="price" label="学费">
                    <template scope="scope">
                        {{ scope.row.price }}元/{{ scope.row.fee_method |  fee_method }}
                    </template>
                </el-table-column>
                <el-table-column prop="start_time" label="开班时间">
                    <template scope="scope">
                        {{ scope.row.start_time | yyyy_mm_dd }}
                    </template>
                </el-table-column>
                <el-table-column prop="student.count" label="学员人数"></el-table-column>
                <el-table-column prop="curriculum" label="排课数量">
                    <template scope="scope">
                        <span v-if="scope.row.class_type == 0">{{scope.row.curriculum.end}}/{{ scope.row.curriculum.count }}</span>
                        <span v-if="scope.row.class_type == 1">--</span>
                    </template>
                </el-table-column>
                <el-table-column prop="staus" label="班级类型">
                    <template scope="scope">
                        <span v-if="scope.row.class_type==0">普通班级</span>
                        <span v-if="scope.row.class_type==1" class="tuoguan">托管班级</span>
                    </template>
                </el-table-column>
                <el-table-column prop="staus" label="班级状态">
                    <template scope="scope">
                        <el-tag type="success" v-if="scope.row.staus==0">正常班级</el-tag>
                        <el-tag type="success" v-if="scope.row.staus==1">已开课</el-tag>
                        <el-tag type="warning" v-if="scope.row.staus==2">预报名</el-tag>
                        <el-tag type="danger" v-if="scope.row.staus==-1">已停用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" min-width="200">
                    <template scope="scope">
                        <el-button type="primary" size="mini" @click="$router.push({name:'class_keqian',params:{id:scope.row.id}})" v-if="scope.row.class_type == 0">
                            查看详情
                        </el-button>
                        <el-button type="primary" size="mini" @click="$router.push({name:'ttpToday',params:{id:scope.row.id}})" v-if="scope.row.class_type == 1">
                            查看详情
                        </el-button>
                        <el-button type="info" size="mini" @click="bianjiClass(scope.row)" v-if="yx_bjgl_my_qtxx">
                            编辑
                        </el-button>
                        <el-button type="danger" size="mini" @click="delClass(scope.row.id,scope.row.class_name)" v-if="yx_bjgl_my_qtxx">
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="fenye" v-show="classList.count > classList.class.length">
                <el-pagination @current-change="handleCurrentChange" :current-page="classindex" :page-size="option.limit" layout="total, prev, pager, next" :total="classList.count">
                </el-pagination>
            </div>
        </div>
        <!--添加班级  编辑班级-->
        <el-dialog :title="classTitle" v-model="addclassshow">
            <p class="fengexian"></p>
            <el-form :model="newClass">
                <el-row :gutter="35">
                    <el-col :span="8">
                        <el-form-item label="班级名称" :label-width="formLabelWidth">
                            <el-input v-model="newClass.name" placeholder="请输入班级名称"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="所属校区" :label-width="formLabelWidth">
                            <el-select v-model="newClass.campus_id" placeholder="请选择校区" @change="changeCampusxiugai" v-if="myMessage.campus_id == 1" filterable>
                                <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                            </el-select>
                            <el-select v-model="newClass.campus_id" placeholder="请选择校区" v-if="myMessage.campus_id != 1" disabled>
                                <el-option :label="item.name" :value="item.id" v-for="item in campus"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="班主任" :label-width="formLabelWidth">
                            <el-select v-model="newClass.teacher" placeholder="请选择班主任" filterable>
                                <el-option :label="item.name" :value="item.id" v-for="item in ygSimple"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
    
                <el-row :gutter="35">
                    <el-col :span="8">
                        <el-form-item label="开课日期" :label-width="formLabelWidth">
                            <el-date-picker v-model="newClass.starttime" type="date" placeholder="选择开课日期" @change="changeTime">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="班级状态" :label-width="formLabelWidth">
                            <el-select v-model="newClass.status" placeholder="请选择班级状态" v-if="type == 1">
                                <el-option label="直接开班" value="0"></el-option>
                                <el-option label="预报名" value="2"></el-option>
                            </el-select>
                            <el-select v-model="newClass.status" placeholder="请选择班级状态" v-if="type == 2">
                                <el-option label="正常班级" value="0"></el-option>
                                <el-option label="预报名" value="2"></el-option>
                                <el-option label="已停用" value="-1"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="额定人数" :label-width="formLabelWidth">
                            <el-input v-model="newClass.maxnum" placeholder="请输入班级人数" type="number"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="35">
                    <el-col :span="8">
                        <el-form-item label="班级类型" :label-width="formLabelWidth">
                            <el-select v-model="newClass.class_type" placeholder="请选择类型" v-if="type == 1" @change="changeClassType">
                                <el-option label="普通班级" value="0"></el-option>
                                <el-option label="托管班级" value="1"></el-option>
                            </el-select>
                            <el-select v-model="newClass.class_type" placeholder="请选择类型" disabled v-if="type == 2">
                                <el-option label="普通班级" value="0"></el-option>
                                <el-option label="托管班级" value="1"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="计费方式" :label-width="formLabelWidth" v-show="newClass.class_type == 0">
                            <el-select v-model="newClass.fee_method" placeholder="请选择计费方式" v-if="type == 1">
                                <el-option :label="'按'+list.label" :value="list.method" v-for="list in class_method" v-if="list.method != 'month' "></el-option>
                            </el-select>
                            <el-select v-model="newClass.fee_method" placeholder="请选择计费方式" disabled v-if="type == 2">
                                <el-option :label="'按'+list.label" :value="list.method" v-for="list in class_method"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="计费方式" :label-width="formLabelWidth" v-show="newClass.class_type == 1">
                            <el-select v-model="newClass.fee_method" placeholder="请选择计费方式" disabled>
                                <el-option :label="'按'+list.label" :value="list.method" v-for="list in class_method" v-if="list.method == 'month' "></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="学费" :label-width="formLabelWidth">
                            <el-input v-model="newClass.xuefei" placeholder="请输入学费" type="number"></el-input>
                        </el-form-item>
                    </el-col>
                    
                </el-row>
                <el-row :gutter="35">
                    <el-col :span="16">
                        <el-form-item label="可选课程" :label-width="formLabelWidth">
                            <el-select v-model="newClass.subject" placeholder="请选择课程" multiple>
                                <el-option :label="item.subject" :value="item.id" v-for="item in subjectList.subject"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="允许请假" :label-width="formLabelWidth">
                            <el-select v-model="newClass.allow_leave" placeholder="请选择是否允许请假">
                                <el-option label="允许" value="1"></el-option>
                                <el-option label="不允许" value="0"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="35">
                    <el-col :span="24">
                        <el-form-item label="班级介绍" :label-width="formLabelWidth">
                            <el-input type="textarea" :rows="2" placeholder="请输入班级介绍" v-model="newClass.remark">
                            </el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="addclassshow = false">取 消</el-button>
                <el-button :loading="loading2" type="primary" @click="addClassFun" v-if="type==1">确 定</el-button>
                <el-button :loading="loading2" type="primary" @click="xiugaiClassFun" v-if="type == 2">修 改</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            loading: false,
            loading2: false,
            option: {
                limit: 15,
                search: "",
                campus_id: "",
                staus: "",
                fee_method: "",
                time: "",
                start_time: "",
                end_time: "",
                teacher_id: ""
            },
            //默认时间
            pickerOptions: {
                shortcuts: [{
                    text: '最近一周',
                    onClick: function (picker) {
                        var end = new Date();
                        var start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近一个月',
                    onClick: function (picker) {
                        var end = new Date();
                        var start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                }, {
                    text: '最近三个月',
                    onClick: function (picker) {
                        var end = new Date();
                        var start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }]
            },
            classList: {
                class: []
            },
            // 添加班级
            addclassshow: false,
            classTitle: "添加新班级",
            newClass: {
                name: "",
                campus_id: "",
                teacher: "",
                starttime: "",
                status: "1",
                maxnum: "",
                xuefei: "",
                fee_method: "cycle",
                subject: [],
                remark: "",
                allow_leave:"1",
                class_type:"0"
            },
            formLabelWidth: "70px",
            subjectList: {
                subject: []
            },
            type: 1,
            class_id: "",

            allClass: {
                all: 0,
                ykk: 0,
                ybm: 0,
                ytk: 0
            },
            myClass: {
                all: 0,
                ykk: 0,
                ybm: 0,
                ytk: 0
            },
            //权限
            yx_bjgl_all: false,
            yx_bjgl_my: false,
            yx_bjgl_my_qtxx: false
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        class_method: function () {
            return this.$store.state.class_method;
        },
        ygSimple: function () {
            return this.$store.state.ygSimple;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        classindex: function () {
            return this.$store.state.classindex;
        }
    },
    watch: {
        $route: function (val) {
            var self = this;
            self.option.staus = self.$route.query.staus;
            self.option.search = "";
            self.$store.commit("setclassindex",1);
            if (self.yx_bjgl_all && self.yx_bjgl_my) {
                self.getClassList(self.option);
            } else if (self.yx_bjgl_all && self.$route.name == "class") {
                self.getClassList(self.option);
            } else if (self.yx_bjgl_my && self.$route.name == "class1") {
                self.getClassList(self.option);
            } else {
                self.$store.commit("checkError1", {
                    self: self,
                    msg: "暂无权限"
                });
            }
        }
    },
    methods: {
        //选择计费方式
        changeClassType:function(val){
            if(val == 1){
                this.newClass.fee_method = 'month';
            }else{
                this.newClass.fee_method = 'frequency';
            }
        },
        //计费方式筛选
        changeFangshi: function (val) {
            if (val == 0) {
                this.option.fee_method = "";
            }
            this.option.search = "";
            this.$store.commit("setclassindex",1);
            this.getClassList(this.option);
        },
        //校区筛选
        changecampus: function (val) {
            var self = this;
            if (val == 0) {
                self.option.campus_id = "";
            }
            self.option.search = "";
            self.$store.commit("setclassindex",1);
            self.option.teacher_id = "";
            var campus_id = val;

            self.$store.commit('getYGList', {
                self: self,
                campus_id: campus_id,
                is_bzr: 1
            });
            self.getClassList(this.option);
        },
        //班主任筛选
        changeBZR: function () {
            var self = this;
            self.option.search = "";
            self.$store.commit("setclassindex",1);
            self.getClassList(this.option);
        },
        //搜索
        serachFun_sub: function () {
            var self = this;
            self.$store.commit("setclassindex",1);
            self.option.campus_id = "";
            self.option.staus = "";
            self.option.fee_method = "";
            self.option.teacher_id = "";
            self.option.start_time = "";
            self.option.end_time = "";

            self.getClassList(self.option);
        },
        //选择时间
        changTime: function (val) {
            if (val == "") {
                this.$store.commit("setclassindex",1);
                this.option.search = "";
                this.option.start_time = "";
                this.option.end_time = "";
                this.getClassList(this.option);
                return;
            }
            var tims = val.split(" - ");
            var st = tims[0];
            var se = tims[1];
            st = new Date(st.split("-")[0], st.split("-")[1]-1, st.split("-")[2]).getTime() / 1000;
            se = new Date(se.split("-")[0], se.split("-")[1]-1, se.split("-")[2]).getTime() / 1000;
            this.option.start_time = st;
            this.option.end_time = se;
            this.$store.commit("setclassindex",1);
            this.option.search = "";
            this.getClassList(this.option);
        },
        //页码点击
        handleCurrentChange: function (val) {
            this.$store.commit("setclassindex",val);
            this.getClassList(this.option);
        },
        //获取班级列表
        getClassList: function (option) {
            var self = this;
            self.loading = true;
            var campus_id = self.myMessage.campus_id == "1" ? option.campus_id : self.myMessage.campus_id;
            var type = self.$route.name == "class1" ? "my" : "";
            self.$http.get("/api/classs?page=" + self.classindex + "&limit=" + option.limit + "&search=" + option.search + "&campus_id=" + campus_id + "&staus=" + option.staus + "&fee_method=" + option.fee_method + "&teacher_id=" + option.teacher_id + "&start_time=" + option.start_time + "&end_time=" + option.end_time + "&type=" + type)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.class.length; i++) {
                            for (var k = 0; k < self.campus.length; k++) {
                                if (data.data.data.class[i].campus_id == self.campus[k].id) {
                                    data.data.data.class[i].campus_name = self.campus[k].name;
                                    break;
                                }
                            }
                        }
                        self.classList = data.data.data;
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
        //获取班级列表
        getClassListCount: function (staus, type) {
            var self = this;
            var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
            self.$http.get("/api/classs?&limit=1&campus_id=" + campus_id + "&staus=" + staus + "&type=" + type)
                .then(function (data) {

                    if (staus == "" && type == "") {
                        self.allClass.all = data.data.data.count;
                    }
                    if (staus == "0" && type == "") {
                        self.allClass.ykk = data.data.data.count;
                    }
                    if (staus == "2" && type == "") {
                        self.allClass.ybm = data.data.data.count;
                    }
                    if (staus == "-1" && type == "") {
                        self.allClass.ytk = data.data.data.count;
                    }

                    if (staus == "" && type == "my") {
                        self.myClass.all = data.data.data.count;
                    }
                    if (staus == "0" && type == "my") {
                        self.myClass.ykk = data.data.data.count;
                    }
                    if (staus == "2" && type == "my") {
                        self.myClass.ybm = data.data.data.count;
                    }
                    if (staus == "-1" && type == "my") {
                        self.myClass.ytk = data.data.data.count;
                    }

                }, function (err) {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //删除班级
        delClass: function (id, name) {
            var self = this;
            var self = this;
            self.$confirm('删除后数据将无法恢复，请谨慎操作', '删除班级', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.post("/api/classs/" + id + "/del")
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: name + "删除成功"
                            });
                            self.getClassList(self.option)
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
        //获取课程列表
        getSubjectList: function (campus_id) {
            var self = this;
            self.$http.get("/api/subject/simple?campus_id=" + campus_id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.subjectList = data.data.data;
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
        //添加班级选择校区
        changeCampusxiugai: function (val) {
            var self = this;
            self.getSubjectList(val);
            var campus_id = val;
    
            self.$store.commit('getYGList', {
                self: self,
                campus_id: campus_id,
                is_bzr: 1
            });
        },
        //选择开课日期
        changeTime: function (val) {
            this.newClass.starttime = val;
        },
        //打开添加新班级
        openNewClass: function () {
            this.addclassshow = true;
            this.classTitle = "添加新班级";
            this.type = 1;
            this.newClass = {
                name: "",
                campus_id: this.myMessage.campus_id == "1" ? "" : this.myMessage.campus_id,
                teacher: "",
                starttime: "",
                status: "",
                maxnum: "",
                xuefei: "",
                fee_method: "",
                subject: [],
                remark: "",
                allow_leave:"1",
                class_type:"0"
            }
        },
        //班级详情
        getClassDetail: function (cid) {
            var self = this;
            self.$http.get("/api/classs/" + cid)
                .then(function (data) {
                    
                    if (data.data.status == 'ok') {
                        var obj = data.data.data;
                        self.newClass = {
                            name: obj.class_name,
                            campus_id: obj.campus_id,
                            teacher: obj.teacher_id,
                            starttime: "",
                            status: obj.staus,
                            maxnum: obj.max_student,
                            xuefei: obj.price,
                            fee_method: obj.fee_method,
                            subject: [],
                            remark: obj.remark,
                            allow_leave:obj.allow_leave,
                            class_type:obj.class_type
                        }
                        for (var i = 0; i < obj.subject.length; i++) {
                            self.newClass.subject.push(obj.subject[i].id);
                        }
                        self.newClass.starttime = new Date(obj.start_time * 1000).toLocaleDateString();

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
        //打开编辑班级
        bianjiClass: function (obj) {
            var self = this;
            self.addclassshow = true;
            self.classTitle = "修改班级信息";
            this.type = 2;
            self.class_id = obj.id;
            self.getClassDetail(obj.id)
            self.newClass = {
                name: "",
                campus_id: "",
                teacher: "",
                starttime: "",
                status: "",
                maxnum: "",
                xuefei: "",
                fee_method: "",
                subject: [],
                remark: "",
                allow_leave:"1",
                class_type:"0"
            }

        },
        //修改班级信息
        xiugaiClassFun: function () {
            var self = this;
            if (self.newClass.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级名称",
                    type: 'warning'
                });
                return;
            }
            if (!self.newClass.starttime) {
                self.$message({
                    showClose: true,
                    message: "请选择开课日期",
                    type: 'warning'
                });
                return;
            }
            if ((self.newClass.maxnum + "").trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级人数",
                    type: 'warning'
                });
                return;
            }
            if ((self.newClass.xuefei + "").trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级学费",
                    type: 'warning'
                });
                return;
            }
            if (self.newClass.subject.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请选择课程",
                    type: 'warning'
                });
                return;
            }

            self.loading2 = true;

            var starttime = new Date(self.newClass.starttime.split("-")[0], self.newClass.starttime.split("-")[1]-1, self.newClass.starttime.split("-")[2]).getTime() / 1000;

            var formData = new FormData();
            formData.append("class_name", self.newClass.name);
            formData.append("campus_id", self.newClass.campus_id);
            formData.append("teacher_id", self.newClass.teacher);
            formData.append("price", self.newClass.xuefei);
            formData.append("fee_method", self.newClass.fee_method);
            formData.append("staus", self.newClass.status);
            formData.append("subject", self.newClass.subject);
            formData.append("max_student", self.newClass.maxnum);
            formData.append("start_time", starttime);
            formData.append("remark", self.newClass.remark);
            formData.append("allow_leave", self.newClass.allow_leave);
            formData.append("class_type", self.newClass.class_type);
            self.$http.post("/api/classs/" + self.class_id, formData)
                .then(function (data) {
                    self.addclassshow = false;
                    self.loading2 = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "班级信息已更新",
                        });
                        self.getClassList(self.option)
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.addclassshow = false;
                    self.loading2 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //添加班级
        addClassFun: function () {
            var self = this;
            if (self.newClass.name.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级名称",
                    type: 'warning'
                });
                return;
            }
            if (self.newClass.campus_id.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: 'warning'
                });
                return;
            }
            if (self.newClass.teacher.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择班主任",
                    type: 'warning'
                });
                return;
            }
            if ((self.newClass.starttime + "").trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请选择开课日期",
                    type: 'warning'
                });
                return;
            }
            if ((self.newClass.maxnum + "").trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级人数",
                    type: 'warning'
                });
                return;
            }
            if ((self.newClass.xuefei + "").trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请填写班级学费",
                    type: 'warning'
                });
                return;
            }
            if (self.newClass.subject.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请选择课程",
                    type: 'warning'
                });
                return;
            }
            self.loading2 = true;
            var starttime = new Date(self.newClass.starttime.split("-")[0], self.newClass.starttime.split("-")[1]-1, self.newClass.starttime.split("-")[2]).getTime() / 1000;
            var formData = new FormData();
            formData.append("class_name", self.newClass.name);
            formData.append("campus_id", self.newClass.campus_id);
            formData.append("teacher_id", self.newClass.teacher);
            formData.append("price", self.newClass.xuefei);
            formData.append("fee_method", self.newClass.fee_method);
            formData.append("staus", self.newClass.status);
            formData.append("subject", self.newClass.subject);
            formData.append("max_student", self.newClass.maxnum);
            formData.append("start_time", starttime);
            formData.append("remark", self.newClass.remark);
            formData.append("allow_leave", self.newClass.allow_leave);
            formData.append("class_type", self.newClass.class_type);
            self.$http.post("/api/classs", formData)
                .then(function (data) {
                    self.addclassshow = false;
                    self.loading2 = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: self.newClass.name + "添加成功",
                        });
                        self.option = {
                            limit: 15,
                            search: "",
                            campus_id: "",
                            staus: "",
                            fee_method: "",
                            time: "",
                            start_time: "",
                            end_time: "",
                            teacher_id: ""
                        };
                        self.getClassList(self.option)
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.addclassshow = false;
                    self.loading2 = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        }
    },
    created: function () {
        var self = this;
        this.$store.commit('setYXLeftMeauId', 4);
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        this.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id,
            is_bzr: 1
        });
        this.getSubjectList(campus_id);






        // 权限设置
        //班级权限
        this.yx_bjgl_all = this.pdqx(["yx_bjgl_all", "yx"]);
        this.yx_bjgl_my = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_my_kczt", "yx_bjgl_my_xskq", "yx_bjgl_my_qtxx", "yx"]);
        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);

        if (self.yx_bjgl_all && self.yx_bjgl_my) {
            self.getClassList(self.option);
        } else if (self.yx_bjgl_all && self.$route.name == "class") {
            self.getClassList(self.option);
        } else if (self.yx_bjgl_my && self.$route.name == "class1") {
            self.getClassList(self.option);
        } else {
            self.$store.commit("checkError1", {
                self: self,
                msg: "暂无权限"
            });
        }

        //统计班级人数
        if (this.yx_bjgl_all) {
            this.getClassListCount("", "");
            this.getClassListCount("0", "");
            this.getClassListCount("2", "");
            this.getClassListCount("-1", "");
        }

        if (this.yx_bjgl_my) {
            this.getClassListCount("", "my");
            this.getClassListCount("0", "my");
            this.getClassListCount("2", "my");
            this.getClassListCount("-1", "my");
        }

    }

}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.btn {
    width: 200px;
}

.el-select {
    width: 100%;
}

.tuoguan{
    color: @blue;
}

.shaixuan {
    margin-bottom: 10px;
    .el-select {
        width: 150px;
        margin-bottom: 10px;
        margin-right: 10px;
    }
    .el-date-editor {
        width: 150px;
        margin-right: 10px;
    }
}
</style>