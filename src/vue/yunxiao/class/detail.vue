<template>
    <div class="yx_class">
        <div class="left">
            <h1>班级管理
                <a href=""></a>
                <article>对已开设的班级进行日常教学管理工作</article>
            </h1>
            <router-link :to="{name:'class_keqian',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_keqian' }">课前管理</router-link>
            <router-link :to="{name:'class_kehou',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_kehou' }">课后管理</router-link>
            <router-link :to="{name:'class_yuxitixing',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_yuxitixing' || $route.name=='class_fabu_yuxitixing' }">预习提醒</router-link>
            <router-link :to="{name:'class_banjizuoye',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_banjizuoye' || $route.name=='class_fabu_zuoye'}">班级作业</router-link>
            <router-link :to="{name:'class_banjitongzhi',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_banjitongzhi' || $route.name == 'class_fabu_tongzhi' }">班级通知</router-link>
            <router-link :to="{name:'class_chengjiguanli',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_chengjiguanli' }">成绩管理</router-link>
            <router-link :to="{name:'class_xueyuanliebiao',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_xueyuanliebiao' }">学员列表</router-link>
            <router-link :to="{name:'class_log',params:{id:$route.params.id}}" :class="{ active : $route.name=='class_log' }">操作日志</router-link>
        </div>
        <div class="right">
            <div class="subject_right_top">
                <span>
                    <img src="../../../img/yx_class_right_logo.png" alt="">
                </span>
                <div>
                    <h1>{{ classDetail.class_name }}</h1>
                    <p>
                        <span>开课时间：{{ classDetail.reg_time | yyyy_mm_dd }}</span>
                        <span>
                            开设课程：
                            <span v-for="(list,index) in classDetail.subject">
                                <span v-if="index == classDetail.subject.length-1">{{ list.subject_name }}</span>
                                <span v-else>{{ list.subject_name }},</span>
                            </span>
                        </span>
    
                    </p>
                    <p>
                        <span>所在校区：{{ classDetail.campus_name }}</span>
                        <span>班主任：{{ classDetail.teacher_name }}</span>
                        <span>收费类型：{{ parseInt(classDetail.price) }}元/{{ classDetail.fee_method | fee_method }}</span>
                        <span>学员人数：{{ xueyuanCount.count }}/{{ classDetail.max_student}}</span>
                        <span>排课数量：{{ paikeCount.end }}/{{ paikeCount.count }}</span>
                        <el-button type="primary" class="pk_btns" size="small" @click="$router.push({name:'class_fabu_yuxitixing'})" v-if=" yx_bjgl_my_qtxx && $route.name == 'class_yuxitixing' ">发布预习提醒</el-button>
                        <el-button type="primary" class="pk_btns" size="small" @click="openPaike" v-if="yx_bjgl_my_qtxx && $route.name == 'class_keqian' ">班级排课</el-button>
                        <el-button type="primary" class="pk_btns" size="small" @click="$router.push({name:'class_fabu_zuoye'})" v-if="yx_bjgl_my_qtxx && $route.name == 'class_banjizuoye'">发布作业</el-button>
                        <el-button type="primary" class="pk_btns" size="small" @click="$router.push({name:'class_fabu_tongzhi'})" v-if="yx_bjgl_my_qtxx && $route.name == 'class_banjitongzhi'">发布班级通知</el-button>
                        <el-button type="primary" class="pk_btns" size="small" @click="openshengchengtable" v-if="yx_bjgl_my_qtxx && $route.name=='class_chengjiguanli'">导入成绩</el-button>
                        <el-button type="primary" class="pk_btns" size="small" style="margin-right:10px" @click="shengchengtable = true" v-if="yx_bjgl_my_qtxx && $route.name=='class_chengjiguanli'">生成表格</el-button>
                    </p>
                    <p v-if="classDetail.remark != ''">
                        <span>班级描述：{{ classDetail.remark }}</span>
                    </p>
                    <span @click="$router.go(-1)">
                        <i class="el-icon-arrow-left"></i>返回上一级</span>
    
                </div>
            </div>
            <router-view></router-view>
        </div>
        <!--班级排课-->
        <el-dialog title="班级排课" v-model="classpaike" class="paike_box">
            <p class="fengexian"></p>
            <el-row>
                <el-col :span="7" class="paike_left">
                    <el-form :model="paikeForm" label-width="70px" label-position="top">
                        <el-form-item label="选择课程">
                            <el-select v-model="paikeForm.subjectname" placeholder="请选择课程" @change="getSubject">
                                <el-option v-for="item in classDetail.subject" :label="item.subject_name" :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="授课老师">
                            <el-select v-model="paikeForm.teacher" placeholder="请选择授课老师" filterable>
                                <el-option v-for="item in subjectDetail.teacher" :label="item.teacher" :value="item.teacher_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="上课教室">
                            <el-select v-model="paikeForm.classroom" placeholder="请选择教室">
                                <el-option v-for="item in classroomList" :label="item.room_name" :value="item.id" v-if="classDetail.campus_id == item.campus_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-row :gutter="20">
                            <el-col :span="12">
                                <el-form-item label="开始日期">
                                    <el-date-picker v-model="paikeForm.starttime" type="date" placeholder="选择日期"></el-date-picker>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="上课时间">
                                    <el-time-picker v-model="paikeForm.sktime" placeholder="选择上课时间" format="HH:mm"></el-time-picker>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :span="12">
                                <el-form-item label="时长(分钟)">
                                    <el-input v-model="paikeForm.longtime" placeholder="请输入时长" type="number" min="1"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="排课次数">
                                    <el-input v-model="paikeForm.count" placeholder="请输入排课次数" type="number" min="1"></el-input>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col :span="24">
                                <el-form-item label="重复周期">
                                    <el-checkbox-group v-model="paikeForm.week">
                                        <el-checkbox label="星期一"></el-checkbox>
                                        <el-checkbox label="星期二"></el-checkbox>
                                        <el-checkbox label="星期三"></el-checkbox>
                                        <el-checkbox label="星期四"></el-checkbox>
                                        <el-checkbox label="星期五"></el-checkbox>
                                        <el-checkbox label="星期六"></el-checkbox>
                                        <el-checkbox label="星期天"></el-checkbox>
                                    </el-checkbox-group>
    
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                    <el-button type="primary" @click="yulanpaike">预览排课</el-button>
                </el-col>
                <el-col :span="17" class="paike_right" v-loading="loading" :element-loading-text="loadingtext">
                    <el-table :data="paikeData" height="550" border style="width: 100%">
                        <el-table-column prop="subject_id" label="课程">
                            <template scope="scope">
                                <span v-for="item in classDetail.subject">
                                    <span v-if="item.id == scope.row.subject_id">{{ item.subject_name }}</span>
                                </span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="time" label="上课时间" width="240">
                            <template scope="scope">
                                {{ scope.row.time | yyyy_mm_dd_M_S_week }}
                                <el-tooltip class="item" effect="dark" :content="scope.row.tishi" placement="top">
                                    <el-tag type="danger" v-if="scope.row.subject">冲突</el-tag>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                        <el-table-column prop="teacher_name" label="授课老师" width="100">
                            <template scope="scope">
                                <span>{{ scope.row.teacher_name }}</span>
                                <el-tooltip class="item" effect="dark" :content="scope.row.tishi" placement="top">
                                    <el-tag type="danger" v-if="scope.row.teacher">冲突</el-tag>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                        <el-table-column prop="room_id" label="教室">
                            <template scope="scope">
                                <span v-for="item in classroomList" v-if="item.id == scope.row.room_id">
                                    {{ item.room_name }}
                                </span>
                                <el-tooltip class="item" effect="dark" :content="scope.row.tishi" placement="top">
                                    <el-tag type="danger" v-if="scope.row.room">冲突</el-tag>
                                </el-tooltip>
                            </template>
                        </el-table-column>
                        <el-table-column label="操作" width="110">
                            <template scope="scope">
                                <el-button type="text" @click="xiugaidaipaike(scope)">修改</el-button>
                                <el-button type="text" @click="delDaipaike(scope)">删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div class="paike_bottom">
                        <el-button type="primary" @click="baocunpaikeBTn">确认保存</el-button>
                    </div>
                </el-col>
            </el-row>
        </el-dialog>
        <!--生成表格-->
        <el-dialog title="生成成绩表" v-model="shengchengtable" size="tiny" custom-class="daorucehngjidialog">
            <p class="fengexian"></p>
            <el-form label-width="80px">
                <el-form-item label="考试名称">
                    <el-input v-model="chengjiform.name" placeholder="请输入考试名称"></el-input>
                </el-form-item>
                <el-form-item label="考试科目" class="kaoshikemu">
                    <el-tag :key="tag" v-for="tag in chengjiTag" :closable="true" :close-transition="false" @close="handleClose(tag)" type="success">
                        {{tag}}
                    </el-tag>
                    <el-input class="input-new-tag" v-if="inputVisible" v-model="inputValue" ref="saveTagInput" size="small" @keyup.enter.native="handleInputConfirm" @blur="handleInputConfirm"></el-input>
                    <el-button v-else class="button-new-tag" size="small" @click="showInput">添加科目</el-button>
                    <p class="tishiwenzi">输入科目后按回车键确定</p>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <form :action="'/api/classs/'+$route.params.id+'/score/out_xls'" method="post" target="_blank">
                    <input type="hidden" name="title" :value="chengjiform.name">
                    <input type="hidden" name="subject" :value="chengjiform.list">
                    <el-button @click="shengchengtable = false">取消</el-button>
                    <button type="submit" class="table_btn">生成表格</button>
                </form>
    
            </span>
        </el-dialog>
        <!--导入成绩-->
        <el-dialog title="导入成绩" v-model="daoruchengji" size="tiny" custom-class="daorucehngjidialog">
            <el-upload class="upload-demo" drag :action="postUpdateURL" name="file" :on-success="updateSuccess" :on-error="updateError" ref="upload" :auto-upload="false" :file-list="fileList">
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">将文件拖到此处，或
                    <em>点击上传</em>
                </div>
                <div class="el-upload__tip" slot="tip">只能上传从该平台生成的Excel文件</div>
            </el-upload>
            <p class="daoruchengjitishi">请确认表格内的成绩信息无误后再上传</p>
            <span slot="footer" class="dialog-footer">
                <el-button @click="daoruchengji = false">取消</el-button>
                <el-button type="primary" @click="submitUpload">上传</el-button>
            </span>
        </el-dialog>
        <!--修改排课信息-->
        <el-dialog title="修改排课信息" v-model="xiugaipaikeShow" size="tiny">
            <el-form :model="daipaikeform" label-width="70px">
                <el-form-item label="课程">
                    <el-select v-model="daipaikeform.subject_id" placeholder="请选择课程" @change="xiugaipaikechange">
                        <el-option v-for="item in classDetail.subject" :label="item.subject_name" :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="上课时间">
                    <el-date-picker v-model="daipaikeform.time" type="datetime" placeholder="选择日期时间" format="yyyy-MM-dd HH:mm">
                    </el-date-picker>
                </el-form-item>
                <el-form-item label="上课时长">
                    <el-input v-model="daipaikeform.sc" placeholder="请输入课程时长" type="number" min="1"></el-input>
                </el-form-item>
    
                <el-form-item label="授课老师">
                    <el-select v-model="daipaikeform.teacher_id" placeholder="请选择授课老师" filterable>
                        <el-option v-for="item in subjectDetail.teacher" :label="item.teacher" :value="item.teacher_id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="教室">
                    <el-select v-model="daipaikeform.room_id" placeholder="请选择教室">
                        <el-option v-for="item in classroomList" :label="item.room_name" :value="item.id" v-if="classDetail.campus_id == item.campus_id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="xiugaipaikeShow = false">取消</el-button>
                <el-button type="primary" @click="xiugaiqueding">确定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    watch: {
        chengjiTag: function (val) {
            this.chengjiform.list = val.join(",");
        }
    },
    data: function () {
        return {
            loading: false,
            loadingtext: "保存中...",
            count: 0,
            xiugaipaikeShow: false,
            index: "",
            //修改排课的信息
            daipaikeform: {
                subject_id: "",
                teacher_id: "",
                teacher_name: "",
                room_id: "",
                time: "",
                endtime: "",
                taecher: false,
                room: false,
                subject: false,
                sc: ""
            },
            classDetail: window.sessionStorage.getItem("classDetail" + this.$route.params.id) ? JSON.parse(window.sessionStorage.getItem("classDetail" + this.$route.params.id)) : {
                subject: []
            },
            paikeCount: {},
            paikeData: [],
            xueyuanCount: {},
            //导入成绩
            daoruchengji: false,
            chengjiform: {
                name: "",
                list: ""
            },
            chengjiTag: [],
            inputVisible: true,
            inputValue: "",
            //生成表格
            shengchengtable: false,
            //班级排课、
            classpaike: false,
            //排课form
            paikeForm: {
                subjectname: "",
                teacher: "",
                classroom: "",
                week: [],
                weeks: [],
                starttime: "",
                count: "",
                sktime: "",
                longtime: ""
            },

            postUpdateURL: "",
            fileList: [],

            subjectDetail: {
                teacher: []
            },
            classroomList: [],

            yx_bjgl_my_qtxx: false
        }
    },
    methods: {
        //修改排课change
        xiugaipaikechange: function (id) {
            console.log(id)
            this.daipaikeform.teacher_id = "";
            this.getSubject(id);
        },
        //修改确定
        xiugaiqueding: function () {
            var self = this;
            var obj = {};

            var teacher_name = "";


            for (var k in self.daipaikeform) {
                obj[k] = self.daipaikeform[k];
            }

            for (var x = 0; x < self.subjectDetail.teacher.length; x++) {
                if (obj.teacher_id == self.subjectDetail.teacher[x].teacher_id) {
                    teacher_name = self.subjectDetail.teacher[x].teacher;
                    break;
                }
            }

            obj.teacher_name = teacher_name;
            if (typeof obj.time == 'object') {
                obj.time = obj.time.getTime() / 1000;
                obj.endtime = obj.time + (obj.sc * 60);
            } else {
                obj.time = obj.time / 1000;
            }

            var formData = new FormData();
            formData.append("subject_id", obj.subject_id);
            formData.append("teacher_id", obj.teacher_id);
            formData.append("room_id", obj.room_id);
            formData.append("start_time", obj.time);
            formData.append("end_time", obj.endtime);

            self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/is_conflict", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        obj.taecher = false;
                        obj.room = false;
                        obj.subject = false;
                        obj.tishi = false;
                        for (var ks in obj) {
                            self.paikeData[self.index][ks] = obj[ks];
                        }
                        self.xiugaipaikeShow = false;
                    } else {

                        var text = data.data.msg + "，是否保存?";
                        obj.tishi = data.data.msg;
                        obj[data.data.data.type] = true;
                        self.$confirm(text, '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(function () {
                            for (var ks in obj) {
                                self.paikeData[self.index][ks] = obj[ks];
                            }
                            self.xiugaipaikeShow = false;
                        }).catch(function () {
                            return;
                        });
                    }
                }, function () {
                    self.xiugaipaikeShow = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //修改待排课
        xiugaidaipaike: function (scope) {
            this.xiugaipaikeShow = true;
            this.index = scope.$index;
            for (var key in scope.row) {
                this.daipaikeform[key] = scope.row[key];
            }
            this.daipaikeform.time = this.daipaikeform.time * 1000;
            this.getSubject(scope.row.subject_id);
        },
        //删除待排课
        delDaipaike: function (obj) {
            var self = this;
            self.index = obj.$index;
            self.paikeData.splice(self.index, 1);
        },
        //点击保存排课按钮
        baocunpaikeBTn: function () {
            var self = this;
            self.loading = true;
            self.svaePaike(self.paikeData[self.count]);
        },
        //排课保存
        svaePaike: function (obj) {
            var self = this;
            if (self.paikeData.length == 0) {
                self.loading = false;
                self.$message({
                    showClose: true,
                    message: "请添加排课信息",
                    type: "warning"
                })
                return;
            }
            if (!obj) {
                self.$store.commit("success", {
                    self: self,
                    msg: "排课保存成功"
                });
                self.loading = false;
                self.classpaike = false;
                //清空待保存课程
                self.paikeData = [];
                //重新获取课程
                self.$store.commit("qudongpauke", new Date().getTime());
                return;
            }
            var formData = new FormData();
            formData.append("subject_id", obj.subject_id);
            formData.append("teacher_id", obj.teacher_id);
            formData.append("room_id", obj.room_id);
            formData.append("start_time", obj.time);
            formData.append("end_time", obj.endtime);

            self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.count++;
                        self.loadingtext = "已完成(" + self.count + "/" + self.paikeData.length + ")"
                        self.svaePaike(self.paikeData[self.count]);
                    } else {
                        self.loading = false;
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.loading = false;
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //预览排课
        yulanpaike: function () {

            var self = this;
            if (self.paikeForm.subjectname == "") {
                self.$message({
                    showClose: true,
                    message: "请选择课程",
                    type: "warning"
                })
                return;
            }
            if (self.paikeForm.teacher == "") {
                self.$message({
                    showClose: true,
                    message: "请选择授课老师",
                    type: "warning"
                })
                return;
            }
            if (self.paikeForm.classroom == "") {
                self.$message({
                    showClose: true,
                    message: "请选择教室",
                    type: "warning"
                })
                return;
            }
            if (self.paikeForm.starttime == "") {
                self.$message({
                    showClose: true,
                    message: "请选择开始日期",
                    type: "warning"
                })
                return;
            }
            if (typeof self.paikeForm.sktime == 'string') {
                self.$message({
                    showClose: true,
                    message: "请选择上课时间",
                    type: "warning"
                })
                return;
            }

            if (self.paikeForm.longtime == '') {
                self.$message({
                    showClose: true,
                    message: "请填写上课时长",
                    type: "warning"
                })
                return;
            }

            if (self.paikeForm.count == '') {
                self.$message({
                    showClose: true,
                    message: "请填写排课次数",
                    type: "warning"
                })
                return;
            }

            if (self.paikeForm.week.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请勾选星期",
                    type: "warning"
                })
                return;
            }

            self.paikeForm.weeks = [];

            var teacher_name = "";
            //通过课程 选择授课老师
            for (var x = 0; x < self.subjectDetail.teacher.length; x++) {
                if (self.paikeForm.teacher == self.subjectDetail.teacher[x].teacher_id) {
                    teacher_name = self.subjectDetail.teacher[x].teacher;
                    break;
                }
            }

            //初始化排课星期

            for (var i = 0; i < self.paikeForm.week.length; i++) {
                if (self.paikeForm.week[i] == "星期一") {
                    self.paikeForm.weeks.push(1);
                } else if (self.paikeForm.week[i] == "星期二") {
                    self.paikeForm.weeks.push(2);
                } else if (self.paikeForm.week[i] == "星期三") {
                    self.paikeForm.weeks.push(3);
                } else if (self.paikeForm.week[i] == "星期四") {
                    self.paikeForm.weeks.push(4);
                } else if (self.paikeForm.week[i] == "星期五") {
                    self.paikeForm.weeks.push(5);
                } else if (self.paikeForm.week[i] == "星期六") {
                    self.paikeForm.weeks.push(6);
                } else if (self.paikeForm.week[i] == "星期天") {
                    self.paikeForm.weeks.push(7);
                }
            }

            //排课开始时间
            var stime = self.paikeForm.starttime.getTime() / 1000;


            var sh = self.paikeForm.sktime.getHours();
            var sm = self.paikeForm.sktime.getMinutes();

            var ts = sh * 60 * 60 + sm * 60;

            var t = stime + ts;
            //排课开始星期
            var week = self.paikeForm.starttime.getDay() == 0 ? 7 : self.paikeForm.starttime.getDay();
            //7天的时长
            var weektime = 60 * 60 * 24 * 7;

            var startArr = [];
            var start = 0;
            var temp;

            //冒号排序
            for (var a = 0; a < self.paikeForm.weeks.length; a++) {
                for (var b = a + 1; b < self.paikeForm.weeks.length; b++) {

                    if (self.paikeForm.weeks[a] > self.paikeForm.weeks[b]) {
                        temp = self.paikeForm.weeks[a];
                        self.paikeForm.weeks[a] = self.paikeForm.weeks[b];
                        self.paikeForm.weeks[b] = temp;
                    }
                }
            }

            //思路
            /*
                计算出每天排课的开始日期
                然后根据一周循环
            */
            for (var j = 0; j < self.paikeForm.weeks.length; j++) {

                //起始时间大于当前时间
                if (self.paikeForm.weeks[j] >= week) {
                    //开始需要排课的星期 大于等于 排课开始星期
                    start = t + (self.paikeForm.weeks[j] - week) * 60 * 60 * 24;
                } else {
                    start = t - (week - self.paikeForm.weeks[j]) * 60 * 60 * 24;
                }
                startArr.push({
                    start: start,
                    count: 0,
                    week: self.paikeForm.weeks[j]
                })


            }
            //startArr 记录了需要派克的星期的开始时间

            //排课信息数组
            var arr = [];
            //循环需要排课的数量
            while (arr.length < parseInt(self.paikeForm.count)) {

                for (var g = 0; g < startArr.length; g++) {

                    if (arr.length == self.paikeForm.count) {
                        break;
                    }

                    var tday = new Date().getDay() == 0 ? 7 : new Date().getDay();

                    // 判断第一次排课开始时间 开始星期 小于 排课开始日期的星期 自动跳过 开始日期加一周
                    if (startArr[g].week <= tday && (startArr[g].start + startArr[g].count * weektime) < new Date().getTime() / 1000 && (startArr[g].start + startArr[g].count * weektime) < t) {
                        startArr[g].count++;
                        continue;
                    }

                    //判断第一次排课时间
                    if ((startArr[g].start + startArr[g].count * weektime) >= t && (startArr[g].start + startArr[g].count * weektime) > new Date().getTime() / 1000) {

                    } else {
                        startArr[g].count++;
                        continue;
                    }

                    arr.push({
                        subject_id: self.paikeForm.subjectname,
                        teacher_id: self.paikeForm.teacher,
                        teacher_name: teacher_name,
                        room_id: self.paikeForm.classroom,
                        time: (startArr[g].start + startArr[g].count * weektime),
                        endtime: (startArr[g].start + startArr[g].count * weektime + self.paikeForm.longtime * 60),
                        taecher: false,
                        room: false,
                        subject: false,
                        sc: self.paikeForm.longtime,
                        tishi: false
                    })
                    startArr[g].count++;
                }
            }

            var xp = 0;
            //检查排课冲突
            for (var c = 0; c < self.paikeData.length; c++) {

                for (var v = 0; v < arr.length; v++) {
                    if (arr[v].time > self.paikeData[c].endtime || arr[v].endtime < self.paikeData[c].time) {

                    } else {
                        xp++;
                    }

                }
            }

            if (xp > 0) {
                self.$confirm("当前排课信息与待保存排课有时间冲突,是否继续？", '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(function () {
                    for (var z = 0; z < arr.length; z++) {
                        arr[z].time = parseInt(arr[z].time);
                        arr[z].endtime = parseInt(arr[z].endtime);
                        self.jiancePaike(arr[z]);
                    }

                    self.paikeForm = {
                        subjectname: "",
                        teacher: "",
                        classroom: "",
                        week: [],
                        weeks: [],
                        starttime: "",
                        count: "",
                        sktime: "",
                        longtime: ""
                    }
                }).catch(function () {
                    return false;
                });
            } else {
                for (var z = 0; z < arr.length; z++) {
                    arr[z].time = parseInt(arr[z].time);
                    arr[z].endtime = parseInt(arr[z].endtime);
                    self.jiancePaike(arr[z]);
                }

                self.paikeForm = {
                    subjectname: "",
                    teacher: "",
                    classroom: "",
                    week: [],
                    weeks: [],
                    starttime: "",
                    count: "",
                    sktime: "",
                    longtime: ""
                }
            }
        },
        //检查排课情况
        jiancePaike: function (obj) {
            var self = this;
            var formData = new FormData();
            formData.append("subject_id", obj.subject_id);
            formData.append("teacher_id", obj.teacher_id);
            formData.append("room_id", obj.room_id);
            formData.append("start_time", obj.time);
            formData.append("end_time", obj.endtime);

            self.$http.post("/api/classs/" + self.$route.params.id + "/curriculum/is_conflict", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        obj.tishi = false;
                        self.paikeData.push(obj);
                    } else {
                        obj[data.data.data.type] = true;
                        obj.tishi = data.data.msg;
                        self.paikeData.push(obj);
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //打开排课
        openPaike: function () {
            this.classpaike = true;
            this.getClassroom();
        },
        //获取排课数量
        getPaikeshuliang: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/curriculum/count")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.paikeCount = data.data.data;
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
        //获取班级学员人数
        getxueyuanCount: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id + "/student/count")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.xueyuanCount = data.data.data;
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
        //删除标签
        handleClose: function (tag) {
            this.chengjiTag.splice(this.chengjiTag.indexOf(tag), 1);
        },
        //班级详情
        getClassDetail: function () {
            var self = this;
            self.$http.get("/api/classs/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classDetail = data.data.data;
                        window.sessionStorage.setItem("classDetail" + self.$route.params.id, JSON.stringify(self.classDetail));
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
        //输入成绩回车
        handleInputConfirm: function () {
            var inputValue = this.inputValue;
            if (inputValue) {
                this.chengjiTag.push(inputValue);
            }
            this.inputVisible = false;
            this.inputValue = '';
        },
        //显示输入tag
        showInput: function () {
            var self = this;
            this.inputVisible = true;
            this.$nextTick(function () {
                self.$refs.saveTagInput.$refs.input.focus();
            });
        },
        //上传成绩表
        submitUpload() {
            this.$refs.upload.submit();
        },
        //上传成功
        updateSuccess: function (response, file, fileList) {
            var self = this;
            if (response.status == 'error') {
                if (response.code == '1000') {
                    self.$store.commit("checkError", {
                        self: self,
                        data: data.data
                    });
                    return;
                }
                self.daoruchengji = false;
                self.fileList = [];
                self.$message({
                    showClose: true,
                    message: response.msg,
                    type: "error"
                })
            } else {
                self.$message({
                    showClose: true,
                    message: response.msg,
                    type: "success"
                })
                self.daoruchengji = false;
                self.$store.commit("changechengji", new Date().getTime());
            }
        },
        //上传失败
        updateError: function () {
            this.$message({
                showClose: true,
                message: "网络错误",
                type: "error"
            })
        },
        //打开导入成绩
        openshengchengtable: function () {
            this.daoruchengji = true;
            this.fileList = [];
        },
        //生成表格
        shengchengtableFun: function () {
            var self = this;
            var formData = new FormData();
            formData.append("title", self.chengjiform.name);
            formData.append("subject", self.chengjiTag);

            self.$http.post("/api/classs/" + self.$route.params.id + "/score/out_xls", formData)
                .then(function (data) {
                    window.open(data.data);
                    if (data.data.status == 'ok') {
                    } else {
                        self.$store.commit("checkError", {
                            self: self,
                            data: data.data
                        });
                    }
                }, function () {
                    self.$message({
                        showClose: true,
                        message: "网络错误",
                        type: "error"
                    })
                })
        },
        //获取教室列表
        getClassroom: function () {
            var self = this;
            self.$http.get("/api/room")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classroomList = data.data.data;
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
        //课程详情
        getSubject: function (id) {
            var self = this;
            if (!id) {
                return;
            }
            self.paikeForm.teacher = "";

            self.$http.get("/api/subject/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.subjectDetail = data.data.data;
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
        this.$store.commit('setYXLeftMeauId', 4);
        this.getClassDetail();
        this.getxueyuanCount();
        this.getPaikeshuliang();
        //上传成绩url
        this.postUpdateURL = window.location.origin + "/api/classs/" + this.$route.params.id + "/score";

        //其他
        this.yx_bjgl_my_qtxx = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_qtxx", "yx_bjgl_all", "yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.kaoshikemu {
    .el-input {
        width: 100px;
    }
    .el-tag {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .tishiwenzi {
        color: @wan;
        font-size: 12px;
        height: 20px;
        line-height: 20px;
    }
}

.daoruchengjitishi {
    padding: 5px 0;
    color: @wan;
    font-size: 14px;
}

.table_btn {
    display: inline-block;
    line-height: 1;
    white-space: nowrap;
    cursor: pointer;
    background: @color;
    border: 1px solid @color;
    color: #fff;
    -webkit-appearance: none;
    text-align: center;
    box-sizing: border-box;
    outline: 0;
    margin: 0;
    padding: 10px 15px;
    font-size: 14px;
    border-radius: 4px;
    &:hover {
        opacity: .8;
    }
    &:active {
        opacity: .5;
    }
}

.paike_left {
    padding-right: 20px;
    border-right: 1px solid #ddd;
    .el-select {
        width: 100%;
    }
}

.paike_right {
    padding-left: 20px;
}

.paike_bottom {
    text-align: right;
    padding-top: 14px;
}
</style>