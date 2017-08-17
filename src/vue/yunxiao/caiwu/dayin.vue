<template>
    <div class="prints_box">
        <div class="title">
            <img :src="schoolMessage.logo" alt=""> {{ schoolMessage.name }}
            <br>
            <span>订单编号：#{{ caiwuDetail.info.id }}</span>
        </div>
        <div class="xaingqingbox">
            <h1>收费详情</h1>
            <h2>学员姓名：{{ selfMessage.name }}</h2>
            <p>
                <span>学员学号：{{ selfMessage.number }}</span>
                <i>经办人：{{ caiwuDetail.info.payee }}</i>
            </p>
            <p>
                <span>交易时间：{{ caiwuDetail.info.reg_time | yyyy_mm_dd_M_S }}</span>
                <i>收款账户：{{ caiwuDetail.info.payee_account }}</i>
            </p>
        </div>
        <ul class="table_print">
            <li class="table_title">
                <span class="row1">收费类型</span>
                <span class="row2">收费项目</span>
                <span class="row3">单价</span>
                <span class="row4">数量</span>
                <span class="row5">金额</span>
            </li>
            <li v-for="item in caiwuDetail.recordss">
                <span class="row1">
                    {{ item.fee_type == "1"?"杂费":"报名费" }}
                </span>
                <span class="row2">{{ item.fee_name }}</span>
                <span class="row3">{{ item.price }}元</span>
                <span class="row4">{{ item.quantity }}{{ item.unit }}</span>
                <span class="row5">{{ (item.price*item.quantity).toFixed(2) }}元</span>
                <p>折扣：{{ item.discount }}%&nbsp;&nbsp;&nbsp;&nbsp;折上优惠：{{ item.preferential }}元&nbsp;&nbsp;&nbsp;&nbsp;优惠合计：{{ (item.price*item.quantity*(100-item.discount)/100+Number(item.preferential)).toFixed(2) }}元</p>
            </li>
        </ul>
        <div class="heji">
            <div>应收合计{{ caiwuDetail.yshj }}元，优惠合计{{ caiwuDetail.yhhj }}元，
                <span>实收{{ caiwuDetail.sshj }}元</span>
            </div>
            <p v-show="$route.query.xz =='true'">报名须知：{{ bmxz }}</p>
        </div>
        <div class="bottom">
            <h1>{{ schoolMessage.name }}</h1>
            <p>联系电话：{{ schoolMessage.phone }}</p>
            <p>交易日期：{{ caiwuDetail.info.payee_time | yyyy_mm_dd }}</p>
            <canvas id="canvas" width="300" height="300" v-show="$route.query.gz =='true'"></canvas>
        </div>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            caiwuDetail: {
                info: {},
                recordss: []
            },
            selfMessage: {},
            bmxz: ""
        }
    },
    computed: {
        schoolMessage: function () {
            return this.$store.state.schoolMessage;
        }
    },
    methods: {
        //获取报名须知
        getBMXZ: function () {
            var self = this;
            if (self.$route.query.xz != 'true') {
                return;
            }
            self.$http.get("/api/config/bmxz")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.bmxz = data.data.data;
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
        drawYZ: function (text1, src) {
            var canvas = document.getElementById("canvas");
            var context = canvas.getContext('2d');

            // 绘制印章边框   
            var width = canvas.width / 2;
            var height = canvas.height / 2;
            context.lineWidth = 7;
            context.strokeStyle = "#f00";
            context.beginPath();
            context.arc(width, height, 110, 0, Math.PI * 2);
            context.stroke();
            //画五角星
            this.create5star(context, width, height, 20, "#f00", 0);
            // 绘制印章名称   
            // context.font = '14px Helvetica';
            context.textBaseline = 'middle'; //设置文本的垂直对齐方式
            context.textAlign = 'center'; //设置文本的水平对对齐方式
            context.lineWidth = 1;
            context.fillStyle = '#f00';
            // 绘制印章单位   
            context.translate(width, height); // 平移到此位置,
            context.font = '22px 微软雅黑';
            var count = text1.length; // 字数   
            var angle = 4 * Math.PI / (3 * (count - 1)); // 字间角度   
            var chars = text1.split("");
            var c;
            for (var i = 0; i < count; i++) {
                c = chars[i]; // 需要绘制的字符   
                if (i == 0) context.rotate(5 * Math.PI / 6);
                else
                    context.rotate(angle); // 
                context.save();
                context.translate(90, 0); // 平移到此位置,此时字和x轴垂直   
                context.rotate(Math.PI / 2); // 旋转90度,让字平行于x轴   
                context.fillText(c, 0, 0); // 此点为字的中心点   
                context.restore();
            }

            var imgs = new Image();
            imgs.src = src;
            imgs.onload = function () {
                window.print()
            }
        },
        create5star: function (context, sx, sy, radius, color, rotato) {
            //绘制五角星  
            /** 
             * 创建一个五角星形状. 该五角星的中心坐标为(sx,sy),中心到顶点的距离为radius,rotate=0时一个顶点在对称轴上 
             * rotate:绕对称轴旋转rotate弧度 
             */
            context.save();
            context.fillStyle = color;
            context.translate(sx, sy); //移动坐标原点  
            context.rotate(Math.PI + rotato); //旋转  
            context.beginPath(); //创建路径  
            var x = Math.sin(0);
            var y = Math.cos(0);
            var dig = Math.PI / 5 * 4;
            for (var i = 0; i < 5; i++) { //画五角星的五条边  
                var x = Math.sin(i * dig);
                var y = Math.cos(i * dig);
                context.lineTo(x * radius, y * radius);
            }
            context.closePath();
            context.stroke();
            context.fill();
            context.restore();
        },
        //获取财务列表
        getFinanceDetail: function () {
            var self = this;
            self.$http.get("/api/finance/records_view/" + self.$route.params.id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        data.data.data.yshj = "";
                        data.data.data.yhhj = "";
                        data.data.data.sshj = "";
                        var sh = 0;
                        var yh = 0;
                        var ys = 0;
                        for (var i = 0; i < data.data.data.recordss.length; i++) {
                            if (data.data.data.recordss[i].type == "0") {
                                data.data.data.recordss[i].actual = data.data.data.recordss[i].actual * -1;
                            }
                            sh += Number(data.data.data.recordss[i].actual);
                            ys += (data.data.data.recordss[i].quantity * data.data.data.recordss[i].price);
                            yh += (ys * (100 - data.data.data.recordss[i].discount) / 100 + Number(data.data.data.recordss[i].preferential))
                        }
                        data.data.data.sshj = sh.toFixed(2);
                        data.data.data.yshj = ys.toFixed(2);
                        data.data.data.yhhj = yh.toFixed(2);
                        self.caiwuDetail = data.data.data;
                        self.getStudentDetail(data.data.data.info.student_id);
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
        //获取学生信息
        getStudentDetail: function (id) {
            var self = this;
            self.$http.get("/api/student/" + id)
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        self.selfMessage = data.data.data.student[0];
                        self.drawYZ(self.schoolMessage.name, self.schoolMessage.logo);
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
        this.$store.commit('setHeadShow', false);
        this.getBMXZ();
    },
    mounted: function () {
        this.$nextTick(function () {
            this.getFinanceDetail();
        })
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.prints_box {
    width: 100%;
    height: auto;
    background-color: #fff;
    padding: 0 30px;
    box-sizing: border-box;
    position: relative;
    padding-bottom: 200px;
    overflow: hidden;
}

.title {
    height: 60px;
    border-bottom: 1px solid #333;
    text-align: right;
    position: relative;
    box-sizing: border-box;
    padding-top: 10px;
    img {
        display: block;
        height: 50px;
        position: absolute;
        top: 5px;
        left: 0;
    }
    span {
        font-size: 12px;
    }
}

.xaingqingbox {
    margin-bottom: 10px;
    padding: 10px 0;
    h1 {
        font-size: @h3;
        color: @c1;
        font-weight: normal;
        margin-bottom: 10px;
    }
    h2 {
        font-size: 12px;
        color: @c1;
        font-weight: normal;
        margin-bottom: 5px;
    }
    p {
        overflow: hidden;
        padding: 5px 0;
        font-size: 12px;
        span {
            display: block;
            width: 50%;
            float: left;
        }
        i {
            display: block;
            width: 50%;
            float: left;
            text-align: right;
            font-style: normal;
        }
    }
}

.table_print {
    width: 100%;
    color: @c2;
    overflow: hidden;
    border-top: 1px solid #666;
    li {
        display: block;
        width: 100%;
        overflow: hidden;
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
        span {
            display: block;
            width: 20%;
            float: left;
            font-size: 10px;
            color: @c2;
            box-sizing: border-box;
            padding-right: 20px;
            &.row1 {
                width: 10%;
            }
            &.row2 {
                width: 40%;
            }
            &.row3 {
                width: 20%;
            }
            &.row4 {
                width: 10%;
            }
            &.row5 {
                width: 20%;
            }
        }
        p {
            font-size: 10px;
            color: @c3;
            clear: both;
        }
    }
}

.heji {
    padding: 20px 0 10px 0;
    >div {
        font-size: 12px;
        color: @c2;
        text-align: right;
        margin-bottom: 10px;
        span {
            font-size: 14px;
            color: @c1;
        }
    }
    p {
        font-size: 10px;
        color: @c3;
        border: 1px solid #ddd;
        padding: 10px;
    }
}

.bottom {
    height: 220px;
    text-align: right;
    padding: 20px;
    box-sizing: border-box;
    padding-top: 100px;
    position: relative;
    h1 {
        font-size: 12px;
        color: @c1;
        font-weight: normal;
        text-align: right;
        margin-bottom: 20px;
    }
    p {
        text-align: right;
        font-size: 10px;
        color: @c2;
        margin-bottom: 5px;
    }
}

#canvas {
    width: 200px;
    height: 200px;
    position: absolute;
    right: 0;
    top: 0;
    font-weight: normal;
    transform: rotate(45deg);
}
</style>