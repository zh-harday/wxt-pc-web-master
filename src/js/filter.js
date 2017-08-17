module.exports = function() {
    var vue = require("vue");
    var store = require("./store.js");
    vue.filter("times", function(val) { //今天 9:00
        if (!val || val == 0 || val == '') return "--";
        var result = "";
        var nowtime = new Date();
        var year = nowtime.getFullYear();
        var month = nowtime.getMonth() + 1;
        if (month > 11) {
            month = 1;
            year++;
        }
        month = month < 10 ? ("0" + month) : month;
        var day = nowtime.getDate();

        var ltime = new Date(val * 1000);
        var lyear = ltime.getFullYear();
        var lmonth = ltime.getMonth() + 1;
        if (lmonth > 11) {
            lmonth = 1;
            lyear++;
        }
        lmonth = lmonth < 10 ? ("0" + lmonth) : lmonth;
        var lday = ltime.getDate() < 10 ? ("0" + ltime.getDate()) : ltime.getDate();
        var lhours = ltime.getHours() < 10 ? ("0" + ltime.getHours()) : ltime.getHours();
        var lmin = ltime.getMinutes() < 10 ? ("0" + ltime.getMinutes()) : ltime.getMinutes();

        if (nowtime.getTime() - val * 1000 <= 86400000 && day == lday) {
            result = "今天 " + lhours + ":" + lmin;
        } else if (nowtime.getTime() - val * 1000 <= 86400000 * 2 && new Date((nowtime.getTime() - 86400000)).getDate() == lday) {
            result = "昨天 " + lhours + ":" + lmin;
        } else if (nowtime.getTime() - val * 1000 <= 86400000 * 3 && new Date((nowtime.getTime() - 86400000 * 2)).getDate() == lday) {
            result = "前天 " + lhours + ":" + lmin;
        } else {
            result = lyear + "-" + lmonth + "-" + lday + " " + lhours + ":" + lmin;
        }

        return result;
    })

    vue.filter("yyyy_mm_dd", function(val) { //1989-01-11
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate());
    })

    vue.filter("mm_dd", function(val) { //07月20
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        return ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '月' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate()) + '日';
    })

    vue.filter("yyyy_mm_dd1", function(val) { //1989-01-11
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        return date.getFullYear() + "/" + (date.getMonth() + 1) + '/' + date.getDate();
    })
    vue.filter("yyyy_mm_dd_M_S", function(val) { //1989-01-11 11:05
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        return date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate()) + " " + (date.getHours() < 10 ? ("0" + date.getHours()) : date.getHours()) + ":" + (date.getMinutes() < 10 ? ("0" + date.getMinutes()) : date.getMinutes());
    })

    vue.filter("yyyy_mm_dd_M_S_week", function(val) { //[周五]1989-01-11 11:05
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        var week = date.getDay();
        if (week == 0) {
            week = "[周天]";
        } else if (week == 1) {
            week = "[周一]";
        } else if (week == 2) {
            week = "[周二]";
        } else if (week == 3) {
            week = "[周三]";
        } else if (week == 4) {
            week = "[周四]";
        } else if (week == 5) {
            week = "[周五]";
        } else if (week == 6) {
            week = "[周六]";
        }
        return week + " " + date.getFullYear() + "-" + ((date.getMonth() + 1) < 10 ? ("0" + (date.getMonth() + 1)) : (date.getMonth() + 1)) + '-' + (date.getDate() < 10 ? ("0" + date.getDate()) : date.getDate()) + " " + (date.getHours() < 10 ? ("0" + date.getHours()) : date.getHours()) + ":" + (date.getMinutes() < 10 ? ("0" + date.getMinutes()) : date.getMinutes());
    })

    vue.filter("week", function(val) { //星期三
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        var week = date.getDay();
        if (week == 0) {
            week = "星期天";
        } else if (week == 1) {
            week = "星期一";
        } else if (week == 2) {
            week = "星期二";
        } else if (week == 3) {
            week = "星期三";
        } else if (week == 4) {
            week = "星期四";
        } else if (week == 5) {
            week = "星期五";
        } else if (week == 6) {
            week = "星期六";
        }
        return week;
    })

    vue.filter("M_S", function(val) { //11:05
        if (!val || val == 0 || val == '') return "--";
        var date = new Date(val * 1000);
        return (date.getHours() < 10 ? ("0" + date.getHours()) : date.getHours()) + ":" + (date.getMinutes() < 10 ? ("0" + date.getMinutes()) : date.getMinutes());
    })

    vue.filter("month", function(val) { //3个月
        if (!val || val == 0 || val == '') return "--";

        var month = (+new Date() - val * 1000) / (1000 * 60 * 60 * 24 * 30);
        month = month < 1 ? 1 : parseInt(month);
        return month;
    })

    vue.filter("ywMint", function(val) { //1天2小时
        if (!val || val == 0 || val == '') return "--";
        var result = "";
        if ((+new Date() - val * 1000) < 1000 * 60 * 60) {
            result = parseInt((+new Date() - val * 1000) / (1000 * 60)) + "分钟"
        } else if ((+new Date() - val * 1000) < 1000 * 60 * 60 * 24) {
            result = parseInt((+new Date() - val * 1000) / (1000 * 60 * 60)) + "小时"
        } else {
            result = parseInt((+new Date() - val * 1000) / (1000 * 60 * 60 * 24)) + "天"
        }

        return result;
    })

    vue.filter("fee_method", function(val) { //1天2小时
        if (!val || val == '') return "";
        var result = "";
        if (val == 'frequency') {
            result = "次"
        }
        if (val == 'cycle') {
            result = "期"
        }

        if (val == 'month') {
            result = "月"
        }
        return result;
    })

};