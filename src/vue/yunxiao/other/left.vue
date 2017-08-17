<template>
    <div class="leftMeau">
        <ul>
            <!--报表权限-->
            <router-link v-if="yx_bb_zssj" tag="li" :to="{name:'zhaoshengshuju'}" :class="{ active:yx_leftMeauId ==1 }">
                <img src="../../../img/yx_data_a.png" v-show="yx_leftMeauId ==1">
                <img src="../../../img/yx_data.png" v-show="yx_leftMeauId !=1">
                <span>报表</span>
            </router-link>
            <router-link v-if="!yx_bb_zssj && !yx_xytj && !yx_cwfx" tag="li" :to="{name:'zhaoshengshuju'}" :class="{ active:yx_leftMeauId ==1 }">
                <img src="../../../img/yx_data_a.png" v-show="yx_leftMeauId ==1">
                <img src="../../../img/yx_data.png" v-show="yx_leftMeauId !=1">
                <span>报表</span>
            </router-link> 
            <router-link v-if="!yx_bb_zssj && yx_xytj" tag="li" :to="{name:'xueyuantongji'}" :class="{ active:yx_leftMeauId ==1 }">
                <img src="../../../img/yx_data_a.png" v-show="yx_leftMeauId ==1">
                <img src="../../../img/yx_data.png" v-show="yx_leftMeauId !=1">
                <span>报表</span>
            </router-link>
            <router-link v-if="!yx_bb_zssj && !yx_xytj && yx_cwfx" tag="li" :to="{name:'caiwufenxi'}" :class="{ active:yx_leftMeauId ==1 }">
                <img src="../../../img/yx_data_a.png" v-show="yx_leftMeauId ==1">
                <img src="../../../img/yx_data.png" v-show="yx_leftMeauId !=1">
                <span>报表</span>
            </router-link>
            
            <!--招生权限 start-->
            <router-link tag="li" :to="{name:'recruit_list',params:{ type:'Student' }}" :class="{ active:yx_leftMeauId ==2 }" v-if="yx_zszz_syxygl">
                <img src="../../../img/yx_zhaosheng_a.png" v-show="yx_leftMeauId ==2">
                <img src="../../../img/yx_zhaosheng.png" v-show="yx_leftMeauId !=2">
                <span>招生</span>
            </router-link>
            <router-link tag="li" :to="{name:'recruit_my',params:{ type:'all' }}" :class="{ active:yx_leftMeauId ==2 }" v-if="!yx_zszz_syxygl && yx_zszz_glxygl">
                <img src="../../../img/yx_zhaosheng_a.png" v-show="yx_leftMeauId ==2">
                <img src="../../../img/yx_zhaosheng.png" v-show="yx_leftMeauId !=2">
                <span>招生</span>
            </router-link>
            <router-link tag="li" :to="{name:'Audition'}" :class="{ active:yx_leftMeauId ==2 }" v-if="!yx_zszz_syxygl && !yx_zszz_glxygl && yx_zszz_yyst">
                <img src="../../../img/yx_zhaosheng_a.png" v-show="yx_leftMeauId ==2">
                <img src="../../../img/yx_zhaosheng.png" v-show="yx_leftMeauId !=2">
                <span>招生</span>
            </router-link>
            <router-link tag="li" :to="{name:'wxtable'}" :class="{ active:yx_leftMeauId ==2 }" v-if="!yx_zszz_syxygl && !yx_zszz_glxygl && !yx_zszz_yyst && yx_zszz_wxbd">
                <img src="../../../img/yx_zhaosheng_a.png" v-show="yx_leftMeauId ==2">
                <img src="../../../img/yx_zhaosheng.png" v-show="yx_leftMeauId !=2">
                <span>招生</span>
            </router-link>
            <router-link tag="li" :to="{name:'EnrollmentTracking'}" :class="{ active:yx_leftMeauId ==2 }" v-if="!yx_zszz_syxygl && !yx_zszz_glxygl && !yx_zszz_yyst && !yx_zszz_wxbd && yx_zszs_sz">
                <img src="../../../img/yx_zhaosheng_a.png" v-show="yx_leftMeauId ==2">
                <img src="../../../img/yx_zhaosheng.png" v-show="yx_leftMeauId !=2">
                <span>招生</span>
            </router-link>
            <!--招生权限 end-->
            <!--学员权限 start-->
            <router-link tag="li" v-if="yx_xygl_all" :to="{name:'student_list',params:{ type:'All'}}" :class="{ active:yx_leftMeauId ==3 }">
                <img src="../../../img/yx_student_a.png" v-show="yx_leftMeauId ==3">
                <img src="../../../img/yx_student.png" v-show="yx_leftMeauId !=3">
                <span>学员</span>
            </router-link>
            <router-link tag="li" v-if="!yx_xygl_all && yx_xygl_my" :to="{name:'student_list1',params:{ type:'All'}}" :class="{ active:yx_leftMeauId ==3 }">
                <img src="../../../img/yx_student_a.png" v-show="yx_leftMeauId ==3">
                <img src="../../../img/yx_student.png" v-show="yx_leftMeauId !=3">
                <span>学员</span>
            </router-link>
            <!--学员权限 end-->
            <!--班级权限 start-->
            <router-link v-if="yx_bjgl_all" tag="li" :to="{name:'class',query:{staus:'',index:1}}" :class="{ active:yx_leftMeauId ==4 }">
                <img src="../../../img/yx_class_a.png" v-show="yx_leftMeauId ==4">
                <img src="../../../img/yx_class.png" v-show="yx_leftMeauId !=4">
                <span>班级</span>
            </router-link>
            <router-link v-if="!yx_bjgl_all && yx_bjgl_my" tag="li" :to="{name:'class1',query:{staus:'',index:5}}" :class="{ active:yx_leftMeauId ==4 }">
                <img src="../../../img/yx_class_a.png" v-show="yx_leftMeauId ==4">
                <img src="../../../img/yx_class.png" v-show="yx_leftMeauId !=4">
                <span>班级</span>
            </router-link>
            <!--班级权限 end-->
            <!--课程权限 start-->
            <router-link tag="li" :to="{name:'curriculum_list' , params:{ id:'All' },query:{index:-1}}" :class="{ active:yx_leftMeauId ==5 }">
                <img src="../../../img/yx_kecheng_a.png" v-show="yx_leftMeauId ==5">
                <img src="../../../img/yx_kecheng.png" v-show="yx_leftMeauId !=5">
                <span>课程</span>
            </router-link>
            <!--课程权限 end-->
            <router-link tag="li" :to="{name:'staffList',params:{id:'All'},query:{index:-1}}" :class="{ active:yx_leftMeauId ==6 }">
                <img src="../../../img/yx_yuangong_a.png" v-show="yx_leftMeauId ==6">
                <img src="../../../img/yx_yuangong.png" v-show="yx_leftMeauId !=6">
                <span>员工</span>
            </router-link>
            <router-link v-if="yx_cwgl_log" tag="li" :to="{name:'Finance_mx'}" :class="{ active:yx_leftMeauId ==7 }">
                <img src="../../../img/yx_caiwu_a.png" v-show="yx_leftMeauId ==7">
                <img src="../../../img/yx_caiwu.png" v-show="yx_leftMeauId !=7">
                <span>财务</span>
            </router-link>
        </ul>
        <ul class="bottom_list" v-if="yx_xtsz">
            <router-link tag="li" :to="{name:'campus'}" :class="{ active:yx_leftMeauId == 9 }">
                <img src="../../../img/yx_setting_a.png" v-show="yx_leftMeauId == 9">
                <img src="../../../img/yx_setting.png" v-show="yx_leftMeauId != 9">
                <span>设置</span>
            </router-link>
        </ul>
    </div>
</template>
<script>
module.exports = {
    data: function () {
        return {
            yx_xtsz: false,
            yx_zszz: false,
            yx_zszz_glxygl: false,
            yx_zszz_syxygl: false,
            yx_zszz_yyst: false,
            yx_zszz_wxbd: false,
            yx_zszs_sz: false,
            yx_bjgl_all: false,
            yx_bjgl_my: false,

            yx_cwgl_log:false,

            yx_xygl_all: false,
            yx_xygl_my: false,

            yx_bb_zssj: false,
            yx_xytj: false,
            yx_cwfx: false
        }
    },
    computed: {
        yx_leftMeauId: function () {
            return this.$store.state.yx_leftMeauId
        }
    },
    created: function () {
        //设置权限
        this.yx_xtsz = this.pdqx(["yx_xtsz", "yx"]);
        //判断招生菜单url
        this.yx_zszz_glxygl = this.pdqx(["yx_zszz_glxygl", "yx"]);
        this.yx_zszz_syxygl = this.pdqx(["yx_zszz_syxygl", "yx"]);
        this.yx_zszz_yyst = this.pdqx(["yx_zszz_yyst", "yx"]);
        this.yx_zszz_wxbd = this.pdqx(["yx_zszz_wxbd", "yx"]);
        this.yx_zszs_sz = this.pdqx(["yx_zszs_sz", "yx"]);
        //学员
        this.yx_xygl_all = this.pdqx(["yx_xygl_all", "yx"]);
        
        //财务列表统计
        this.yx_cwgl_log = this.pdqx(["yx_cwgl_log", "yx"]);
        //班级权限
        this.yx_bjgl_all = this.pdqx(["yx_bjgl_all", "yx"]);
        this.yx_bjgl_my = this.pdqx(["yx_bjgl_my", "yx_bjgl_my_view", "yx_bjgl_my_kczt", "yx_bjgl_my_xskq", "yx_bjgl_my_qtxx", "yx"]);
        //学员权限
        this.yx_xygl_all = this.pdqx(["yx_xygl_all", "yx_xygl", "yx"]);
        this.yx_xygl_my = this.pdqx(["yx_xygl_my", "yx_xygl", "yx"]);
        //报表权限
        this.yx_bb_zssj = this.pdqx(["yx_bb_zssj","yx_bb","yx"]);
        this.yx_xytj = this.pdqx(["yx_xytj","yx_bb","yx"]);
        this.yx_cwfx = this.pdqx(["yx_cwfx","yx_bb","yx"]);
    }
}

</script>
<style lang="less" scoped>
@import "../../../less/color.less";
.bottom_list {
    width: 80px;
    position: fixed;
    bottom: 0;
    left: 0;
}
</style>