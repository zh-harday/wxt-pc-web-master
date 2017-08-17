//主文件引入
var vue = require("vue");
var vueRouter = require("vue-router");
var vueResource = require("vue-resource");
var store = require("./store.js");

//引入页面样式
require("../less/app.less");
var base64 = require("./base64.js");
var VueHtml5Editor = require("./vue-html5-editor.js");

// 引入 ECharts 主模块
var echarts = require('echarts/lib/echarts');
//载入主题
require("./theme.js")
    // 引入柱状图
require('echarts/lib/chart/bar');
//折线图
require('echarts/lib/chart/line');
//引入雷达图
require('echarts/lib/chart/radar');
//引入饼图
require('echarts/lib/chart/pie');

//引入标题
require('echarts/lib/component/title');
//引入提示框
require('echarts/lib/component/tooltip');
//右上角提示
require('echarts/lib/component/toolbox');
//条形图计算数据平均值
require('echarts/lib/component/markLine');

vue.prototype.echarts = echarts;

var ElementUI = require('element-ui/lib/index.js');
vue.use(vueRouter);
vue.use(vueResource);
vue.use(ElementUI);

//h5编辑器
var vuehtmlOption = require("./vuehtmlOption");
vue.use(VueHtml5Editor, vuehtmlOption)


var heads = require("../vue/other/head.vue");
var kaoqin = require("../vue/yunxiao/class/kaoqin.vue");
var tuifei = require("../vue/yunxiao/xueyuan/tuifei.vue");
var daoru = require("../vue/yunxiao/other/daoru.vue");
var addyxxy = require("../vue/yunxiao/other/addyxxy.vue");
var addnewstud = require("../vue/yunxiao/other/addnewstud.vue");
var zhuanban = require("../vue/yunxiao/other/zhuanban.vue");
vue.component("heads", heads);
vue.component("kaoqin", kaoqin);
vue.component("tuifei", tuifei);
vue.component("daoru", daoru);
vue.component("addyxxy", addyxxy);
vue.component("addnewstud", addnewstud);
vue.component("zhuanban", zhuanban);

var yxLeftMeau = require("../vue/yunxiao/other/left.vue");
var wxLeftMeau = require("../vue/weixin/other/left.vue");
var tabs = require("../vue/yunxiao/other/tabs.vue");
var map = require("../vue/other/map.vue");
var fenpei = require("../vue/yunxiao/other/fenpei.vue");
var dianp = require("../vue/yunxiao/class/dianp.vue");
var Album = require("../vue/other/Album.vue");
var datetmp = require("../vue/yunxiao/other/datetmp.vue");

vue.component("yxleftmeau", yxLeftMeau);
vue.component("wxleftmeau", wxLeftMeau);
vue.component("tabs", tabs);
vue.component("maps", map);
vue.component("fenpei", fenpei);
vue.component("dianp", dianp);
vue.component("album", Album);
vue.component("datetmp", datetmp);

//引入路由 
var routes = require("./router.js");
//引入过滤器
var filter = require("./filter");
filter();

//判断权限
vue.prototype.pdqx = function(arr) {
    var arr1 = store.state.myMessage.authority;
    for (var i = 0; i < arr1.length; i++) {
        for (var k = 0; k < arr.length; k++) {
            if (arr1[i] == arr[k]) {
                return true;
            }
        }
    }
    return false;
}

//实例化VueRouter
var router = new vueRouter({
    routes: routes // （缩写）相当于 routes: routes
})



router.beforeEach(function(to, from, next) {

    if (to.name == "login") {
        next();
        return;
    }

    var self = vue;

    if (!store.state.myMessage.id) {
        getSchoolMessage(self, store, next);
    } else {
        next();
    }


})

var app = new vue({
    store: store,
    router: router
}).$mount('#app')
var intval;



function getMymessage(self, store, next) {
    self.http.get("/api/user")
        .then(function(data) {
            if (data.data.status == 'ok') {
                data.data.data.face = "http://wx.eduwxt.com/api/face/teacher/" + data.data.data.id;
                store.state.myMessage = data.data.data;
                //设置微信营销权限
                for (var i = 0; i < data.data.data.authority.length; i++) {
                    if (data.data.data.authority[i] == 'wx') {
                        store.state.wx_xuanchuan = true;
                        store.state.wx_hudong = true;
                        store.state.wx_huodong = true;
                        store.state.wx_shangcheng = true;
                        store.state.wx_shezhi = true;
                        break;
                    }
                    if (data.data.data.authority[i] == 'wx_jcxc') {
                        store.state.wx_xuanchuan = true;
                    }
                    if (data.data.data.authority[i] == 'wx_hdyx') {
                        store.state.wx_hudong = true;
                    }
                    if (data.data.data.authority[i] == 'wx_zshd') {
                        store.state.wx_huodong = true;
                    }
                    if (data.data.data.authority[i] == 'wx_xyds') {
                        store.state.wx_shangcheng = true;
                    }
                    if (data.data.data.authority[i] == 'wx_wxsz') {
                        store.state.wx_shezhi = true;
                    }
                }
                next();
            } else {
                if (data.data.code === 1000) {
                    next({ name: 'login' });
                }
            }
        }, function(err) {

        })
}

function getSchoolMessage(self, store, next) {
    self.http.get("/api/school")
        .then(function(data) {
            if (data.data.status == 'ok') {
                data.data.data.logo = "http://wx.eduwxt.com/api/face/school/" + data.data.data.id;
                store.state.schoolMessage = data.data.data;
                document.title = data.data.data.name;
                getcampus(self, store, next);
            } else {
                if (data.data.code === 1000) {
                    next({ name: 'login' });
                }
            }
        }, function(err) {

        })
}

function getcampus(self, store, next) {
    self.http.get("/api/campus")
        .then(function(data) {
            if (data.data.status == 'ok') {
                store.state.campus = data.data.data;
                getMymessage(self, store, next);
            } else {
                if (data.data.code === 1000) {
                    next({ name: 'login' });
                }
            }
        }, function(err) {

        })
}