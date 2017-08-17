<template>
    <div>
        <!--转班-->
        <el-dialog title="转班" v-model="zhuanbanshow" size="tiny" custom-class="setwith">
            <!-- 普通班级 -->
            <el-form ref="form" :model="zhuanbanform" label-position="top" v-if="classmsg.class_type == 0">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="当前班级">
                            {{ classmsg.class_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="计费方式">
                            {{ classmsg.class_fee_method | fee_method }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="剩余课次">
                            <span>{{ classmsg.class_fee_method =='frequency'?classmsg.count:'--' }}次</span>
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="转入班级">
                            <el-select v-model="zhuanbanform.class_id" filterable placeholder="请选择班级">
                                <el-option v-for="item in classList.class" :label="item.class_name" :value="item.id" v-if="classmsg.class_id == item.id" disabled>
                                </el-option>
                                <el-option v-for="item in classList.class" :label="item.class_name" :value="item.id" v-if="classmsg.class_id != item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="计费方式">
                            {{ classmsg.class_fee_method | fee_method }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="转入数量">
                            <el-input v-model="zhuanbanform.number" type="number"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <!-- 托管班级 -->
            <el-form ref="form" :model="zhuanbanform" label-position="top" v-if="classmsg.class_type == 1">
                <p class="fengexian"></p>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="当前班级">
                            {{ classmsg.class_name }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="入班时间">
                            {{ classmsg.start_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="到期时间">
                            {{ classmsg.end_time | yyyy_mm_dd }}
                        </el-form-item>
                    </el-col>
                </el-row>
                <p class="fengexian2"></p>
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="转入班级">
                            <el-select v-model="zhuanbanform.class_id" filterable placeholder="请选择班级">
                                <el-option v-for="item in classList.class" :label="item.class_name" :value="item.id" v-if="classmsg.class_id == item.id" disabled>
                                </el-option>
                                <el-option v-for="item in classList.class" :label="item.class_name" :value="item.id" v-if="classmsg.class_id != item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="到期时间">
                            <el-date-picker v-model="zhuanbanform.end_time" type="date" placeholder="选择日期" :picker-options="pickerOptions0">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="转班原因">
                            <el-input v-model="zhuanbanform.remark" type="textarea" :rows="2" placeholder="请输入转班原因"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="zhuanbanshow = false">取 消</el-button>
                <el-button type="primary" @click="tgzhuanbanfun">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
module.exports = {
    /*
        {
            class_id:"",
            student_id:"",
            class_name:"",
            class_fee_method:"",
            count:"",//剩余课次
            campus_id:"",
            start_time:"",
            end_time:"",
            class_type:"",
            student_name:""
        }
    */
    props: ["classmsg", "changetime"],
    data: function () {
        return {
            classmessage: {},
            zhuanbanshow: false,
            zhuanbanform: {
                class_id: "",
                number: 0,
                end_time: "",
                remark: ""
            },
            classList: {
                class: []
            },

            pickerOptions0: {
                disabledDate: function (time) {
                    return time.getTime() < new Date().getTime();
                }
            }
        }
    },
    watch: {
        changetime: function () {
            this.zhuanbanform.class_id = "";
            this.zhuanbanform.number = 0;
            this.zhuanbanshow = true;
            this.getClassList();
        }
    },
    methods: {
        //获取班级列表
        getClassList: function () {
            var self = this;
            self.$http.get("/api/classs/simple?campus_id=" + self.classmsg.campus_id + "&fee_method=" + self.classmsg.class_fee_method)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.classList = data.data.data;
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
        // 普通转班
        zhuanbanfun: function () {
            var self = this;

            if (self.zhuanbanform.class_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择要转入的班级",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();
            formData.append("class_id", self.zhuanbanform.class_id);
            formData.append("quantity", self.zhuanbanform.number);

            self.$http.post("/api/classs/" + self.classmsg.class_id + "/student/" + self.classmsg.student_id + "/change", formData)
                .then(function (data) {
                    self.zhuanbanshow = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "转班成功",
                        });
                        self.$emit("change");
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
        //托管转班
        tgzhuanbanfun: function () {
            var self = this;

            if (self.zhuanbanform.class_id == '') {
                self.$message({
                    showClose: true,
                    message: "请选择要转入的班级",
                    type: "warning"
                })
                return;
            }

            if (self.zhuanbanform.end_time == '') {
                self.$message({
                    showClose: true,
                    message: "请选择到期时间",
                    type: "warning"
                })
                return;
            }

            if (self.zhuanbanform.remark.trim() == '') {
                self.$message({
                    showClose: true,
                    message: "请输入转班原因",
                    type: "warning"
                })
                return;
            }

            var formData = new FormData();
            formData.append("new_class_id", self.zhuanbanform.class_id);
            formData.append("remark", self.zhuanbanform.remark);
            formData.append("end_time", new Date(self.zhuanbanform.end_time).getTime() / 1000);

            self.$http.post("/api/classs/" + self.classmsg.class_id + "/" + self.classmsg.student_id + "/tuoguan/change", formData)
                .then(function (data) {
                    self.zhuanbanshow = false;
                    if (data.data.status == 'ok') {
                        self.$store.commit("success", {
                            self: self,
                            msg: "转班成功",
                        });
                        self.$emit("change");
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
    }
}
</script>
