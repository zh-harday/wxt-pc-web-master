<template>
    <div class="content_two">
        <div class="c_left">
            <h1>新增缴费</h1>
            <el-tabs v-model="activeName">
                <el-tab-pane label="报班费用" name="first">
                    <el-tree :data="data" :props="defaultProps" accordion @node-click="handleNodeClick1">
                    </el-tree>
                </el-tab-pane>
                <el-tab-pane label="学杂费用" name="second">
                    <el-tree :data="zafeiList" :props="defaultProps" accordion @node-click="handleNodeClick2">
                    </el-tree>
                </el-tab-pane>
            </el-tabs>
        </div>
        <div class="c_right">
            <div class="box1">
                <el-table :data="feiyongList" style="width: 100%">
                    <el-table-column prop="name" label="收费项目"></el-table-column>
                    <el-table-column prop="shoufei_type" label="收费类型">
                        <template scope="scope">
                            <span v-if="scope.row.sftype == 0">报名费</span>
                            <span v-if="scope.row.sftype == 1">杂费</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="shoufei_type" label="收费方式">
                        <template scope="scope">
                            <span v-if="scope.row.shoufei_type == 0">正常收费</span>
                            <span v-if="scope.row.shoufei_type == 1">赠送</span>
                            <span v-if="scope.row.shoufei_type == 2">转结</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="count" label="数量">
                    </el-table-column>
                    <el-table-column prop="price" label="单价">
                        <template scope="scope">
                            <span>{{ scope.row.price }}元/{{ scope.row.type | fee_method }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="yj" label="原价">
                        <template scope="scope">
                            {{ scope.row.yj }}元
                        </template>
                    </el-table-column>
                    <el-table-column prop="zk" label="折扣">
                        <template scope="scope">
                            {{ scope.row.zk }}%
                        </template>
                    </el-table-column>
                    <el-table-column prop="zjyh" label="折上优惠">
                        <template scope="scope">
                            {{ scope.row.zjyh }}元
                        </template>
                    </el-table-column>
                    <el-table-column prop="yhjg" label="实际金额">
                        <template scope="scope">
                            {{ scope.row.yhjg }}元
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="操作" width="100">
                        <template scope="scope">
                            <el-button type="primary" size="mini" @click="delShowfeiTable(scope)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="text">
                    正常收费{{ tongji.zc }}项，赠送{{ tongji.zs }}项，应收合计
                    <s>{{ tongji.yshj }}</s>元，优惠共计
                    <s>{{ tongji.yh }}</s>元，实收
                    <span>{{ tongji.ss }}</span>元
                </div>
            </div>
            <div class="box2">
                <el-form ref="form" :model="form" label-width="70px">
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="经办人">
                                <el-input v-model="myMessage.name" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="办理时间">
                                <el-date-picker v-model="nowTime" type="date" placeholder="选择日期">
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="收款账户">
                                <el-select v-model="caiwuzh" placeholder="请选择账户">
                                    <el-option v-for="item in finance" :label="item.account_name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <p class="line"></p>
                    <el-row :gutter="30">
                        <el-col :span="8">
                            <el-form-item label="来源渠道">
                                <el-select v-model="msg.qd" placeholder="请选择来源渠道" filterable>
                                    <el-option :label="item.name" :value="item.id" v-for="item in sourceList"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="课程顾问">
                                <el-select v-model="selfMessage.student[0].gw_id" placeholder="请选择课程顾问" filterable>
                                    <el-option value="0" label="请选择课程顾问" disabled></el-option>
                                    <el-option :label="item.name" :value="item.id" v-for="item in guwenList"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="市场专员">
                                <el-select v-model="selfMessage.student[0].zy_id" placeholder="请选择市场专员" filterable>
                                    <el-option value="0" label="请选择市场专员" disabled></el-option>
                                    <el-option :label="item.name" :value="item.id" v-for="item in shichangList"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="30">
                        <el-col :span="24">
                            <el-form-item label="备注">
                                <el-input type="textarea" v-model="beizhu"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <p class="warning">如需对业绩划分做调整，请在此设置，报名成功后暂不允许修改</p>
                    <div class="bottom">
                        <el-button>取消</el-button>
                        <el-button type="primary" @click="jiaofeiFun">确认缴费</el-button>
                    </div>
                </el-form>
            </div>
            <!--添加学杂费用-->
            <el-dialog :title="feiyongtitle" v-model="bmshow">
                <el-table :data="form.fyList" v-show="type == 1">
                    <el-table-column property="name" label="报名班级"></el-table-column>
                    <el-table-column property="type" label="计费方式">
                        <template scope="scope">
                            {{ scope.row.type | fee_method }}
                        </template>
                    </el-table-column>
                    <el-table-column property="price" label="单价">
                        <template scope="scope">
                            {{ scope.row.price }}元
                        </template>
                    </el-table-column>
                </el-table>
                <el-table :data="form.fyList" v-show="type == 2">
                    <el-table-column property="name" label="费用名称"></el-table-column>
                    <el-table-column property="type" label="单位"></el-table-column>
                    <el-table-column property="price" label="单价">
                        <template scope="scope">
                            {{ scope.row.price }}元
                        </template>
                    </el-table-column>
                </el-table>
                <p class="line1"></p>
                <el-form :model="form" label-width="70px">
    
                    <el-row :gutter="widthline">
                        <el-col :span="10">
                            <el-form-item label="收费方式">
                                <el-radio-group v-model="form.shoufei_type" @change="changeShoufeifangshi">
                                    <el-radio label="0">正常收费</el-radio>
                                    <el-radio label="1">赠送</el-radio>
                                    <el-radio label="2">转结</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                    </el-row>
    
                    <el-row :gutter="widthline">
                        <el-col :span="8">
                            <el-form-item label="购买数量">
                                <el-input v-model="form.count" type="number" @input="jisuan" v-show="goumaiCountShow" min="1" @input="inputCount"></el-input>
                                <el-input v-model="form.count" type="number" @input="jisuan" v-show="!goumaiCountShow" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="折扣%">
                                <el-input v-model="form.zk" type="number" @input="jisuan" v-show="zhekouShow" min="0" max="100"></el-input>
                                <el-input v-model="form.zk" type="number" @input="jisuan" v-show="!zhekouShow" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="直接优惠" type="number">
                                <el-input v-model="form.zjyh" @input="jisuan" v-show="zhekouShow" type="number" min="0"></el-input>
                                <el-input v-model="form.zjyh" @input="jisuan" v-show="!zhekouShow" disabled></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="widthline">
                        <el-col :span="8">
                            <el-form-item label="原价(元)">
                                <el-input v-model="form.yj" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="优惠后价格(元)">
                                <el-input v-model="form.yhjg" disabled></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row :gutter="widthline">
                        <el-col :span="14" v-show="timeshow">
                            <el-form-item label="报名时间">
                                <el-date-picker v-model="starttime" type="daterange" placeholder="选择日期范围">
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <p class="warning">
                        单价*数量*折扣%-直接优惠 = 优惠后价格
                        <br> 选择赠送或转结时，默认折扣为0，实际价格为0。
                    </p>
                    <div class="bottom">
                        <el-button @click="bmshow = false">取消</el-button>
                        <el-button type="primary" @click="addShowfeileixing">添加</el-button>
                    </div>
                </el-form>
            </el-dialog>
    
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            //表单间距
            widthline: 40,
            //顾问员工
            guwenList: [],
            //市场员工
            shichangList: [],
            //财务账户
            caiwuzh: "",
            //办理时间
            nowTime: new Date().getTime(),
            //缴费备注
            beizhu: "",
            //购买数量
            goumaiCountShow: true,
            //是否显示 可选的时间
            timeshow: false,
            starttime: "",
            //选项卡默认
            activeName: "first",
            //数组结构
            defaultProps: {
                children: 'children',
                label: 'label'
            },
            //班级列表
            data: [{
                label: '报名中',
                children: []
            }, {
                label: '已开班',
                children: []
            }],
            //学杂费列表
            zafeiList: [],
            //费用列表
            feiyongList: [],

            form: {
                //收费类型
                sftype: 0,
                //报名表格中数据
                fyList: [{
                    id: "",
                    name: "",
                    type: "",
                    price: "",
                    start_time: "0",
                    end_time: "0",
                }],
                shoufei_type: "0",
                count: 1,
                zk: 100,
                zjyh: 0,
                yj: 0,
                yhjg: 0
            },
            //选择收费方式
            zhekouShow: true,
            //总计统计
            tongji: {
                zc: 0,
                zs: 0,
                yshj: 0,
                yh: 0,
                ss: 0
            },

            msg: {
                qd: ""
            },

            bmshow: false,
            type: 1,//默认表示显示班级费用
            feiyongtitle: "添加报名费用",
            finance: window.sessionStorage.getItem("finances") ? JSON.parse(window.sessionStorage.getItem("finances")) : [],
            sourceList: window.sessionStorage.getItem("sourceList") ? JSON.parse(window.sessionStorage.getItem("sourceList")) : [],
            selfMessage: {
                student: [
                    {}
                ]
            }
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
    watch: {
        feiyongList: function (val) {

            var self = this;
            self.tongji = {
                zc: 0,
                zs: 0,
                yshj: 0,
                yh: 0,
                ss: 0
            }
            for (var i = 0; i < val.length; i++) {
                if (val[i].shoufei_type == "0") {
                    self.tongji.zc++;
                }
                if (val[i].shoufei_type == "1") {
                    self.tongji.zs++;
                }
                self.tongji.yshj += Number(val[i].yj);
                self.tongji.yh += (val[i].yj - val[i].yhjg);
                self.tongji.ss += Number(val[i].yhjg);
            }

        }
    },
    methods: {

        //缴费确认
        jiaofeiFun: function () {
            var self = this;
            var nowTime;
            if (self.feiyongList.length == 0) {
                self.$message({
                    showClose: true,
                    message: "请添加收费类型",
                    type: 'warning'
                });
                return;
            }
            if (self.caiwuzh == '') {
                self.$message({
                    showClose: true,
                    message: "请选择收费账户",
                    type: 'warning'
                });
                return;
            }
            if (typeof self.nowTime == 'number') {
                nowTime = parseInt(self.nowTime / 1000);
            } else {
                nowTime = parseInt(self.nowTime.getTime() / 1000);
            }

            var arr = [];


            for (var i = 0; i < self.feiyongList.length; i++) {
                var obj = {};
                obj.fee_type = self.feiyongList[i].sftype;
                obj.fee_name = self.feiyongList[i].name;
                obj.fee_id = self.feiyongList[i].id;
                obj.fee_mode = self.feiyongList[i].shoufei_type;
                obj.quantity = self.feiyongList[i].count;

                if(self.feiyongList[i].sftype == 1){
                    obj.unit = self.feiyongList[i].type;
                }

                if(self.feiyongList[i].sftype == 0){
                    switch (self.feiyongList[i].type) {
                        case 'frequency':
                            obj.unit = "次";
                            break;
                        case 'cycle':
                            obj.unit = "期";
                            break;
                        case 'month':
                            obj.unit = "月";
                            break;
                    }
                }
                               
                obj.price = self.feiyongList[i].price;
                obj.discount = self.feiyongList[i].zk;
                obj.preferential = self.feiyongList[i].zjyh;
                obj.actual = self.feiyongList[i].yhjg;
                obj.start_time = self.feiyongList[i].start_time;
                obj.end_time = self.feiyongList[i].end_time;
                arr.push(obj);
            }
            arr = JSON.stringify(arr);
            var formData = new FormData();
            formData.append("payee_time", nowTime);
            formData.append("remark", self.beizhu);
            formData.append("account", self.caiwuzh);
            formData.append("paylist", arr);

            formData.append("zy_id", self.selfMessage.student[0].zy_id);
            formData.append("gw_id", self.selfMessage.student[0].gw_id);

            self.$http.post("/api/student/" + self.$route.params.id + "/payment", formData)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.$confirm('缴费成功, 是否查看或打印缴费详情?', '提示', {
                            confirmButtonText: '确认',
                            cancelButtonText: '取消',
                            type: 'success'
                        }).then(function () {
                            self.$router.push({ name: 'Finance_detail', params: { id: data.data.data.orders_id } })
                        }).catch(function () {

                        });
                        self.feiyongList = [];
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
        //删除收费项目
        delShowfeiTable: function (data) {
            this.feiyongList.splice(data.$index, 1);
        },
        //添加收费类型
        addShowfeileixing: function () {
            var self = this;
            if (Number(self.form.count) < 1) {
                self.$message({
                    showClose: true,
                    message: "购买数量必须大于1",
                    type: 'warning'
                });
                return;
            }
            if (Number(self.form.zk) < 0 || Number(self.form.zk) > 100) {
                self.$message({
                    showClose: true,
                    message: "折扣率必须在0-100之间",
                    type: 'warning'
                });
                return;
            }
            if (Number(self.form.zjyh) < 0) {
                self.$message({
                    showClose: true,
                    message: "直接优惠必须大于等于0",
                    type: 'warning'
                });
                return;
            }
            self.feiyongList.push({
                sftype: self.form.sftype,
                id: self.form.id,
                name: self.form.fyList[0].name,
                type: self.form.fyList[0].type,
                price: self.form.fyList[0].price,
                start_time: self.form.fyList[0].start_time,
                end_time: self.form.fyList[0].end_time,
                shoufei_type: self.form.shoufei_type,
                count: self.form.count,
                zk: self.form.zk,
                zjyh: self.form.zjyh,
                yj: self.form.yj,
                yhjg: self.form.yhjg
            });
            self.bmshow = false;
            self.form = {
                //收费类型
                sftype: 0,
                //报名表格中数据
                fyList: [{
                    id: "",
                    name: "",
                    type: "",
                    price: "",
                    start_time: "0",
                    end_time: "0"
                }],
                shoufei_type: "0",
                count: 1,
                zk: 100,
                zjyh: 0,
                yj: 0,
                yhjg: 0
            };
        },
        //改变收费方式
        changeShoufeifangshi: function () {
            var self = this;
            if (self.form.shoufei_type == "0") {
                self.form.zk = 100;
                self.form.zjyh = 0;
                self.form.yhjg = (self.form.count * Number(self.form.fyList[0].price) * (self.form.zk / 100) - self.form.zjyh).toFixed(2);
                self.zhekouShow = true;

            } else {
                self.zhekouShow = false;
                self.form.zk = 0;
                self.form.zjyh = 0;
                self.form.yhjg = (self.form.count * Number(self.form.fyList[0].price) * (self.form.zk / 100) - self.form.zjyh).toFixed(2);
            }
        },
        //计算原价
        jisuan: function () {
            var self = this;
            if (self.form.count < 1 || self.form.count == 0) {
                self.form.count = 1;
            }
            if (parseInt(self.form.count)) {
                var time1 = new Date().getTime() + self.form.count * 1000 * 60 * 60 * 24 * 30;
                this.starttime = [new Date(), new Date(time1)];
            }
            if (!parseInt(self.form.count) && !parseInt(self.form.zk) && !parseInt(self.form.zjyh)) {
                self.form.yj = 0;
                self.form.yhjg = 0;
                return;
            }
            self.form.yj = (self.form.count * Number(self.form.fyList[0].price)).toFixed(2);
            self.form.yhjg = (self.form.count * Number(self.form.fyList[0].price) * (self.form.zk / 100) - self.form.zjyh).toFixed(2);
        },
        //学费点击
        handleNodeClick1: function (data) {
            var self = this;
            self.type = 1;
            self.feiyongtitle = "添加报名费用";
            if (data.children) {
                return;
            }
            self.bmshow = true;

            if (data.class_type == 0) {
                self.timeshow = false;
            }
            if (data.class_type == 1) {
                self.timeshow = true;
                self.starttime = [new Date(), new Date(new Date().getTime() + 30 * 1000 * 60 * 60 * 24)];
            }

            self.form.count = 1;

            switch (data.fee_method) {
                case 'frequency':
                    self.goumaiCountShow = true;
                    self.form.fyList[0].start_time = 0;
                    self.form.fyList[0].end_time = 0;
                    break;
                case 'cycle':
                    self.goumaiCountShow = false;
                    self.form.fyList[0].start_time = 0;
                    self.form.fyList[0].end_time = 0;
                    break;
                case 'month':
                    self.goumaiCountShow = true;
                    self.form.fyList[0].start_time = self.starttime[0].getTime() / 1000;
                    self.form.fyList[0].end_time = self.starttime[1].getTime() / 1000;
                    break;
            }

            self.form.id = data.id;
            self.form.fyList[0].name = data.class_name;
            self.form.fyList[0].type = data.fee_method;
            self.form.fyList[0].price = data.price;
            self.form.shoufei_type = "0";

            self.form.zk = 100;
            self.form.zjyh = 0;
            self.form.yj = 0;
            self.form.yhjg = 0;
            self.form.sftype = 0;
            self.jisuan();
        },
        //杂费点击
        handleNodeClick2: function (data) {
            var self = this;
            self.type = 2;
            self.feiyongtitle = "添加学杂费用";
            if (data.children) {
                return;
            }
            self.timeshow = false;
            self.goumaiCountShow = true;
            self.bmshow = true;
            self.form.id = data.id;
            self.form.fyList[0].name = data.fees_name;
            self.form.fyList[0].type = data.unit;
            self.form.fyList[0].price = data.price;
            self.form.shoufei_type = "0";
            self.form.count = 1;
            self.form.zk = 100;
            self.form.zjyh = 0;
            self.form.yj = 0;
            self.form.yhjg = 0;
            self.form.sftype = 1;
            self.jisuan();
        },
        //获取学杂费分类
        getzafeifenlei: function () {
            var self = this;
            self.$http.get("/api/finance/fees_type")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            data.data.data[i].children = [];
                            data.data.data[i].label = data.data.data[i].type_name;
                        }
                        self.zafeiList = data.data.data;
                        self.getXuezafeiList();

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
        //获取学杂费列表
        getXuezafeiList: function () {
            var self = this;
            self.$http.get("/api/finance/fees")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.length; i++) {
                            data.data.data[i].label = data.data.data[i].fees_name;
                            for (var j = 0; j < self.zafeiList.length; j++) {
                                if (self.zafeiList[j].id == data.data.data[i].fees_type) {
                                    self.zafeiList[j].children.push(data.data.data[i]);
                                    break;
                                }
                            }

                        }
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
        //获取班级列表
        getClassList: function (campus_id) {
            var self = this;
            self.$http.get("/api/classs/simple?campus_id=" + campus_id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        for (var i = 0; i < data.data.data.class.length; i++) {
                            data.data.data.class[i].label = data.data.data.class[i].class_name;
                            if (data.data.data.class[i].staus == 2) {
                                self.data[0].children.push(data.data.data.class[i])
                            }
                            if (data.data.data.class[i].staus == 0 || data.data.data.class[i].staus == 1) {
                                self.data[1].children.push(data.data.data.class[i])
                            }
                        }
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
        //获取学员信息
        getMessage: function () {
            var self = this;
            self.$http.get("/api/student/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.selfMessage = data.data.data;
                        self.getGuwen(self.selfMessage.student[0].campus_id);
                        self.getShichang(self.selfMessage.student[0].campus_id);
                        self.getClassList(data.data.data.student[0].campus_id);
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
        //获取财务列表
        getFinance: function () {
            var self = this;
            self.$http.get("/api/finance/account")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.caiwuzh = data.data.data.length > 0 ? data.data.data[0].id : "";
                        for (var i = 0; i < data.data.data.length; i++) {
                            for (var j = 0; j < self.campus.length; j++) {
                                if (self.campus[j].id == data.data.data[i].campus_id) {
                                    data.data.data[i].campus_name = self.campus[j].name;
                                    break;
                                }
                                if (data.data.data[i].campus_id == 1) {
                                    data.data.data[i].campus_name = "所有校区";
                                }
                            }
                        }
                        self.finance = data.data.data;
                        window.sessionStorage.setItem("finances", JSON.stringify(self.finance))
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
        //获取招生渠道
        getSource: function () {
            var self = this;
            if (self.sourceList.length > 0) {
                return;
            }
            self.$http.get("/api/intention/source")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.sourceList = data.data.data;
                        window.sessionStorage.setItem("sourceList", JSON.stringify(self.sourceList));
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
        //获取顾问
        getGuwen: function (campus_id) {
            var self = this;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_gw=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.guwenList = data.data.data.staff;
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
        //获取市场
        getShichang: function (campus_id) {
            var self = this;
            self.$http.get("/api/staff/simple?campus_id=" + campus_id + "&is_sc=1")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.shichangList = data.data.data.staff;
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
        var self = this;
        self.$store.commit('setyx_xy_xq_meau_id', 3);
        self.getzafeifenlei();
        self.getMessage();
        self.getFinance();
        self.getSource();
        var campus_id = self.myMessage.campus_id == "1" ? "" : self.myMessage.campus_id;
        self.$store.commit('getYGList', {
            self: self,
            campus_id: campus_id
        });

        //权限设置
        var yx_cwgl_jf = self.pdqx(["yx_cwgl_jf", "yx"]);
        if (!yx_cwgl_jf) {
            self.$router.push({ name: 'worktody' })
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.c_left {
    padding: 10px 0;
    h1 {
        padding: 10px 20px;
    }
}

.el-select {
    width: 100%;
}

.el-tree {
    border: none;
}

.c_right {
    padding: 0;
    background-color: transparent;
}

.bottom {
    text-align: right;
}

.warning {
    color: @wan;
    margin-bottom: 20px;
}

.box1 {
    background-color: #fff;
    margin-bottom: 20px;
    color: @c2;
    .text {
        text-align: right;
        padding: 10px;
        s {
            text-decoration: none;
            font-size: @h3;
            color: @c1;
        }
        span {
            font-size: @h3;
            color: @color;
        }
    }
}

.box2 {
    background-color: #fff;
    padding: 20px;
}

.line {
    width: 100%;
    height: 1px;
    background-color: #ddd;
    margin-bottom: 20px;
}

.line1 {
    width: 100%;
    height: 1px;
    background-color: #ddd;
    margin: 20px 0;
    margin-bottom: 10px;
}
</style>