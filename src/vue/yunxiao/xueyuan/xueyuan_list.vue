<template>
    <div>
        <h1 class="right_title">
            学员管理
            <br>
            <el-button type="success" size="small" @click="addxueyuanModel">添加新学员</el-button>
            <el-button type="success" size="small" @click="daoruxueyuan">导入学员信息</el-button>
            <el-button type="success" size="small" v-if="false">导出学员信息</el-button>
            <el-input placeholder="请输入学员姓名" icon="search" v-model="option.search" @keyup.enter.native="serachFuns" :on-icon-click="serachFuns" class="btn"></el-input>
        </h1>
        <div class="xy_serach_box">
            <el-select v-model="form.campus_id" placeholder="请选择校区" @change="campus_change" filterable v-if="myMessage.campus_id == 1">
                <el-option label="全部" value="0"></el-option>
                <el-option :label="list.name" :value="list.id" v-for="list in campus"></el-option>
            </el-select>
            <el-select v-model="form.class_id" placeholder="请输入班级名称" filterable @change="changeClass">
                <el-option label="全部" value="0"></el-option>
                <el-option v-for="item in classSimple" :label="item.class_name" :value="item.id">
                </el-option>
            </el-select>
            <el-date-picker v-model="form.time" type="daterange" align="right" placeholder="选择日期范围" :picker-options="pickerOptions" @change="changTime">
            </el-date-picker>
        </div>
        <el-table :data="xueyuanList.student" style="width: 100%" v-loading="loading" element-loading-text="加载中" @sort-change="xhpaixuFun">
            <el-table-column prop="number" label="学号" sortable="custom">
            </el-table-column>
            <el-table-column prop="name" label="学员姓名">
            </el-table-column>
            <el-table-column prop="sex" label="性别" sortable="custom">
            </el-table-column>
            <el-table-column prop="campus_name" label="所在校区">
            </el-table-column>
            <el-table-column prop="phone" label="联系方式">
            </el-table-column>
            <el-table-column prop="reg_time" label="入学时间" sortable="custom">
                <template scope="scope">
                    {{ scope.row.reg_time | yyyy_mm_dd }}
                </template>
            </el-table-column>
            <el-table-column label="微校通状态">
                <template scope="scope">
                    <el-tag type="danger" v-if="scope.row.weixin_bind == 0">未绑定</el-tag>
                    <el-tag type="primary" v-else>已绑定</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="name" label="操作" min-width="250">
                <template scope="scope">
                    <el-button type="primary" size="mini" @click="$router.push({name:'student_dtl_m',params:{id:scope.row.id}})">查看详情</el-button>
                    <el-button type="info" size="mini" @click="$router.push({name:'ServiceTracking',params:{id:scope.row.id}})">服务跟踪</el-button>
                    <el-button v-if="yx_cwgl_jf" type="warning" size="mini" @click="$router.push({name:'newRegistration',params:{id:scope.row.id}})">报名缴费</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-show="xueyuanList.count > xueyuanList.student.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="xyindex" :page-sizes="[10, 20, 30, 40]" :page-size="option.limit" layout="total, prev, pager, next" :total="xueyuanList.count">
            </el-pagination>
        </div>
        <addnewstud :changetimes="changetimes"></addnewstud>
        <daoru :type="1" :changetime="changetime"></daoru>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            changetime: 0,
            changetimes: 0,
            loading: false,
            // 列表参数
            option: {
                limit: 15,
                search: "",
                campus_id: "",
                class_id: "",
                start_time: "",
                end_time: "",
                type: "All",
                field: "number",
                direction: "DESC"
            },
            // 学员列表
            xueyuanList: {
                student: []
            },

            //筛选条件参数
            form: {
                campus_id: "",
                class_id: "",
                time: ""
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



            //权限
            yx_cwgl_jf: false
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        },
        classSimple: function () {
            return this.$store.state.classSimple;
        },
        updatestudent: function () {
            return this.$store.state.updatestudent;
        },
        xyindex: function () {
            return this.$store.state.xyindex;
        }
    },
    watch: {
        $route: function () {
            this.option.type = this.$route.params.type == 'All' ? '' : this.$route.params.type;
            this.$store.commit("setxyindex", 1);
            this.getXueyuanList(this.option);
        },
        updatestudent: function () {
            this.$store.commit("setxyindex", 1);
            this.getXueyuanList(this.option);
        }
    },
    methods: {
        //学号排序
        xhpaixuFun: function (val) {
            if (val.prop == null) return;
            var self = this;
            self.option.field = val.prop;
            self.option.direction = val.order == 'descending' ? 'DESC' : 'ASC';
            self.$store.commit("setxyindex", 1);
            self.getXueyuanList(self.option);
        },
        //打开添加学员
        addxueyuanModel: function () {
            this.changetimes = new Date().getTime();
        },
        //导入学员信息
        daoruxueyuan: function () {
            this.changetime = new Date().getTime();
        },

        //校区筛选
        campus_change: function (val) {
            var self = this;
            var campus_id = val == "0" ? "" : val;
            this.option.campus_id = campus_id;
            this.$store.commit("setxyindex", 1);
            this.option.search = "";
            // 更新班级
            this.$store.commit("getClassList", {
                self: self,
                campus_id: self.option.campus_id
            })

            this.getXueyuanList(this.option);
        },

        //班级筛选
        changeClass: function (val) {
            this.option.class_id = val;
            this.$store.commit("setxyindex", 1);
            this.option.search = "";
            this.getXueyuanList(this.option);
        },
        //时间转换 toms
        theTimeToMS: function (val) {
            var arr = val.split("-");
            var result = new Date(arr[0], arr[1] - 1, arr[2]).getTime() / 1000;
            return result;
        },
        //时间筛选
        changTime: function (val) {
            if (val == "") {
                this.$store.commit("setxyindex", 1);
                this.option.search = "";
                this.option.start_time = "";
                this.option.end_time = "";
                this.getXueyuanList(this.option);
            } else {
                var arr = val.split(" - ");
                this.$store.commit("setxyindex", 1);
                this.option.search = "";
                this.option.start_time = this.theTimeToMS(arr[0]);
                this.option.end_time = this.theTimeToMS(arr[1]);
                this.getXueyuanList(this.option);
            }
        },

        //获取学员列表
        getXueyuanList: function (option) {
            var self = this;
            self.loading = true;
            var campus_id = self.myMessage.campus_id == "1" ? option.campus_id : self.myMessage.campus_id;
            var my = self.$route.name == "student_list" ? "" : "1";
            self.$http.get("/api/student?type=" + option.type + "&page=" + self.xyindex + "&limit=" + option.limit + "&field=" + option.field + "&direction=" + option.direction + "&search=" + option.search + "&campus_id=" + campus_id + "&class_id=" + option.class_id + "&start_time=" + option.start_time + "&end_time=" + option.end_time + "&my=" + my)
                .then(function (data) {
                    self.loading = false;
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.student.length; i++) {
                            for (var k = 0; k < self.campus.length; k++) {
                                if (data.data.data.student[i].campus_id == self.campus[k].id) {
                                    data.data.data.student[i].campus_name = self.campus[k].name;
                                    break;
                                }
                            }
                        }
                        self.xueyuanList = data.data.data;
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
        //搜索
        serachFuns: function () {
            this.option.campus_id = "";
            this.$store.commit("setxyindex", 1);
            this.option.class_id = "";
            this.option.start_time = "";
            this.option.end_time = "";
            this.form = {
                campus_id: "",
                class_id: "",
                time: ""
            };
            this.getXueyuanList(this.option);
        },
        // 点击页码
        handleCurrentChange: function (val) {
            this.$store.commit("setxyindex", val);
            this.getXueyuanList(this.option);
        }
    },
    created: function () {
        var self = this;
        this.option.type = this.$route.params.type == 'All' ? '' : this.$route.params.type;
        this.getXueyuanList(this.option);
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        this.$store.commit("getClassList", {
            self: self,
            campus_id: campus_id
        })

        //权限设置
        this.yx_cwgl_jf = this.pdqx(["yx_cwgl_jf", "yx"]);
    }
}

</script>
<style lang="less" scoped>
.el-input {
    width: auto;
}

.xy_serach_box {
    margin-bottom: 20px;
}

.yuangong_xiugaixq {
    padding: 0;
    .el-input {
        width: 100%;
    }
    .xyxq_title {
        font-weight: normal;
    }
}

.el-dialog__body {
    padding-top: 10px;
}



.btn {
    width: 200px;
}
</style>