<template>
    <!--分配-->
    <el-dialog title="分配学员" v-model="fenpeishow" class="fenpeistudent">
        <p class="fengexian"></p>
        <div class="fenpei_title">
            将
            <span v-for="(item,index) in nm"> {{ item.name }}
                <i v-if="index < nm.length-1">,</i>
            </span>同学分配给：
            <el-input placeholder="请输入老师姓名" icon="search" v-model="input2" :on-icon-click="handleIconClick" @input="handleIconClick">
            </el-input>
        </div>
        <ul>
            <li :class="{active:sid == item.id}" v-for="item in fpList" @click="fenpeiclick(item.id)">
                <!--<span><img :src="item.face" alt=""></span>-->
                <i>{{ item.name }}</i>
                <s class="el-icon-check"></s>
            </li>
        </ul>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="queren" :loading="loading">确认</el-button>
        </div>
    </el-dialog>
</template>
<script>
module.exports = {
    props: ["nm", "changeid"],
    data: function () {
        return {
            fenpeishow: false,
            sid: "",
            fpList: [],
            fpLists: [],
            input2: "",
            loading: false
        }
    },
    computed: {
        myMessage: function () {
            return this.$store.state.myMessage;
        }
    },
    methods: {
        //获取员工
        getGWTeachList: function (id) {
            var self = this;
            if(self.fpList.length>0){
                return;
            }
            self.$http.get("/api/staff/simple?campus_id=" + id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.fpList = data.data.data.staff;
                        self.fpLists = data.data.data.staff;
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
        handleIconClick: function () {
            var self = this;
            self.fpList = [];
            for (var i = 0; i < self.fpLists.length; i++) {
                if (self.input2 == "") {
                    self.fpList = self.fpLists;
                    return;
                }
                if (self.fpLists[i].name.indexOf(self.input2) != -1) {
                    self.fpList.push(self.fpLists[i]);
                }
            }
        },
        queren: function () {
            var self = this;
            if (self.sid == '') {
                self.$message({
                    showClose: true,
                    message: "请选择课程顾问",
                    type: "success"
                })
                return;
            }
            self.loading = true;
            for (var i = 0; i < self.nm.length; i++) {
                self.PostServerFenpei(self.nm[i].id, i);
            }
        },
        PostServerFenpei: function (id, index) {
            var self = this;
            var formData = new FormData();
            formData.append("gw_id", self.sid);
            self.$http.post("/api/intention/" + id + "/gw", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        if (index == self.nm.length - 1) {
                            self.fenpeishow = false;
                            self.loading = false;
                            self.$store.commit("success", {
                                self: self,
                                msg: data.data.msg,
                            });
                            self.$emit("change");
                            self.sid = "";
                        }
                    } else {
                        self.loading = false;
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
        fenpeiclick: function (id) {
            this.sid = id;
        }
    },
    watch: {
        changeid: function () {
            var self = this;
            if (self.id != '') {
                self.fenpeishow = true;
            }
        },
        nm: function (val) {
            if(val.length == 0){
                return;
            }
            var campus_id = val[0].campus_id;
            this.getGWTeachList(campus_id);
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.fenpei_title {
    margin-bottom: 10px;
    span {
        color: @color;
    }
}

.el-input {
    margin-top: 5px;
}
</style>