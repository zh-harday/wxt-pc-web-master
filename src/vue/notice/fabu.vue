<template>
    <div>
        <h1 class="title">
            发布新员工通知
        </h1>
        <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="通知标题">
                <el-input v-model="form.title" placeholder="请输入通知标题"></el-input>
            </el-form-item>
            <el-form-item label="接收校区">
                <el-select v-model="checkList" placeholder="请选择" filterable v-if="myMessage.campus_id == 1" @change="changeCampud">
                    <el-option label="所有校区" value="1"></el-option>
                    <el-option v-for="item in campus" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <el-select v-model="checkList" placeholder="请选择" filterable v-if="myMessage.campus_id != 1" disabled>
                    <el-option v-for="item in campus" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="发布正文">
                <vue-html5-editor :content="form.content" :height="400" @change="updateData"></vue-html5-editor>
            </el-form-item>
    
            <el-form-item>
                <el-button type="primary" size="small" class="btom_btn" @click="sendFun" :loading="fabubtn">发布通知</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            checkList: "",
            canchange: true,
            form: {
                title: "",
                content: ""
            },
            fabubtn: false
        }
    },
    computed: {
        campus: function () {
            return this.$store.state.campus;
        },
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    methods: {
        //选择校区
        changeCampud: function (val) {
            this.checkList = val;
        },
        //发布通知
        sendFun: function () {
            var self = this;

            if (self.form.title.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入标题",
                    type: 'warning'
                });
                return;
            }
            if (self.form.checkList == "") {
                self.$message({
                    showClose: true,
                    message: "请选择校区",
                    type: 'warning'
                });
                return;
            }
            if (self.form.content.trim() == "") {
                self.$message({
                    showClose: true,
                    message: "请输入内容",
                    type: 'warning'
                });
                return;
            }
            self.fabubtn = true;
            var formData = new FormData();
            formData.append("campus_id", self.checkList);
            formData.append("title", self.form.title);
            formData.append("message", self.form.content);
            self.$http.post("/api/message", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$router.push({ name: 'AllMessage' })
                        self.$store.commit("success", {
                            self: self,
                            msg: "消息发送成功"
                        });
                        self.fabubtn = false;
                        self.$store.commit("getNewMsgCount", self);
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
        updateData: function (value) {
            this.form.content = value;
        },
        getContent: function () {
            console.log(this.content)
        },
        yulan: function () {

        }
    },
    created: function () {

        var self = this;
        var campus = self.myMessage.campus_id == "1" ? "1" : self.myMessage.campus_id;
        self.checkList = campus;

        var yx_yggl_msg = self.pdqx(["yx_yggl", "yx_yggl_msg", "yx"]);
        if (!yx_yggl_msg) {
            this.$router.push({ name: 'worktody' })
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
.el-select {
    width: 100%;
}

.title {
    font-size: @h2;
    color: @c1;
    border-bottom: 1px solid #9d9d9d;
    padding-bottom: 14px;
    position: relative;
    margin-bottom: 20px;
}

.btom_btn {
    display: block;
    float: right;
}

.el-form-item {
    margin-bottom: 10px;
}
</style>