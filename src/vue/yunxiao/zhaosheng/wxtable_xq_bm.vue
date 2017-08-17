<template>
    <div>
        <!--<div class="change_box">
                <el-date-picker v-model="value1" type="date" placeholder="选择日期">
                </el-date-picker>
            </div>-->
        <el-table :data="studentList.student" style="width: 100%">
            <el-table-column prop="" label="序号">
                <template scope="scope">
                    {{ scope.$index +1 }}
                </template>
            </el-table-column>
            <el-table-column prop="add_time" label="提交时间">
                <template scope="scope">
                    {{ scope.row.add_time | yyyy_mm_dd_M_S }}
                </template>
            </el-table-column>
            <el-table-column prop="name" label="姓名">
            </el-table-column>
            <el-table-column prop="age" label="年龄">
            </el-table-column>
            <el-table-column prop="phone" label="联系电话">
            </el-table-column>
            <el-table-column prop="" label="操作" width="250">
                <template scope="scope">
                    <el-button type="info" size="mini" @click="fenpeiFuns(scope.row)">分配</el-button>
                    <el-button type="primary" size="mini" @click="openyuyuexq(scope.row)">预约详情</el-button>
                    <el-button type="warning" size="mini" @click="$router.push({name:'student_server',params:{id:scope.row.intention_id}})">跟进</el-button>
                    <el-button type="danger" size="mini" @click="delstudent(scope.row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="fenye" v-if="studentList.count > studentList.student.length">
            <el-pagination @current-change="handleCurrentChange" :current-page="option.page" :page-size="option.limit" layout="total, prev, pager, next" :total="studentList.count">
            </el-pagination>
        </div>
        <!--分配-->
        <fenpei :nm="student_name" :changeid="change_id"></fenpei>
        <!--预约详情-->
        <el-dialog title="预约详情" v-model="yuyueshow">
            <p class="fengexian"></p>
            <el-form ref="formsss" label-width="70px">
                <el-row :gutter="20">
                    <el-col :span="8" v-if="xq.name != null && xq.name.trim() != ''">
                        <el-form-item label="学员姓名">
                            {{ xq.name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.age != null && xq.age.trim() != ''">
                        <el-form-item label="学员年龄">
                            {{ xq.age }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.phone != null && xq.phone.trim() != ''">
                        <el-form-item label="联系电话">
                            {{ xq.phone }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.sex != null && xq.sex.trim() != ''">
                        <el-form-item label="性别">
                            {{ xq.sex }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.birthday != null && xq.birthday.trim() != ''">
                        <el-form-item label="出生日期">
                            {{ xq.birthday }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.school != null && xq.school.trim() != ''">
                        <el-form-item label="学校">
                            {{ xq.school }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8" v-if="xq.grade != null && xq.grade.trim() != ''">
                        <el-form-item label="年级">
                            {{ xq.grade }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="家庭地址" v-if="xq.address != null && xq.address.trim() != ''">
                    {{ xq.address }}
                </el-form-item>
                <el-form-item label="备注" v-if="xq.remark != null && xq.remark.trim() != ''">
                    {{ xq.remark }}
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            value1: "",
            fenpeishow: false,
            yuyueshow: false,
            student_name: "",
            change_id: "",
            xq: {},
            studentList: {
                student: []
            },
            option: {
                page: 1,
                limit: 10
            },

            campus_id: null
        }
    },
    methods: {
        fenpeiFuns: function (name) {
            var self = this;
            if(!self.campus_id){
                self.$message({
                        showClose: true,
                        message: "数据加载中...",
                        type: "warning"
                    })
                return;
            }
            var obj = name;
            obj.id = obj.intention_id;
            obj.campus_id = self.campus_id;
            this.student_name = [obj];
            this.change_id = new Date().getTime();
        },
        //获取表单详情
        getTableMsg: function () {
            var self = this;
            self.$http.get("/api/free_form/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.campus_id = data.data.data.form.campus_id;
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
        //打开预约详情
        openyuyuexq: function (data) {
            this.xq = data;
            this.yuyueshow = true;
        },
        //删除报名记录数据
        delstudent: function (id) {
            var self = this;
            self.$confirm("是否确定删除这条报名记录？", '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(function () {
                self.$http.get("/api/free_form/student_del/" + id)
                    .then(function (data) {
                        if (data.data.status == 'ok') {
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg
                            });
                            self.option.page = 1;
                            self.getWXtableBM();
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
        //获取报名学员
        getWXtableBM: function () {
            var self = this;
            self.$http.get("/api/free_form/" + self.$route.params.id + "/student?limit=" + self.option.limit + "&page=" + self.option.page)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.studentList = data.data.data;
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
        //点击页码
        handleCurrentChange: function (val) {
            this.option.page = val;
            this.getWXtableBM();
        },
        //分配学员
        fenpeiFun: function () {
            this.fenpeishow = true;
        }
    },
    created: function () {
        this.getWXtableBM();
        this.getTableMsg();
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.change_box {
    width: 160px;
    margin-bottom: 20px;
}
</style>