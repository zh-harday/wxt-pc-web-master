<template>
    <div class="tabs">
        <span @click="clickFun(-2,0)" :class="{ active: x == -2 }">全部({{ datas.length }})</span>
        <span @click="clickFun(-1,1)" :class="{ active: x == -1 }" v-if="all">所有{{ text }}({{ allcampus }})</span>
        <span v-for="(n,index) in list" @click="clickFun(index,n.id)" :class="{ active: x == index }">{{ n.name }}({{ n.num }})</span>
    </div>
</template>
<script>
    module.exports = {
        props: ["list", "datas", "text", "all"],
        data: function () {
            return {
                x: -2,
                allcampus: 0,
            }
        },
        watch: {
            list: function (val) {
                self.list = val;
            },
            datas: function (val) {
                var self = this;
                var obj = self.list;
                self.allcampus = 0;
                for (var j = 0; j < obj.length; j++) {
                    obj[j].num = 0;
                }
                for (var i = 0; i < val.length; i++) {
                    if (val[i].campus_id == 1) {
                        self.allcampus++;
                    }
                    for (var j = 0; j < obj.length; j++) {
                        if (val[i].campus_id == obj[j].id) {
                            obj[j].num++;
                        }
                        if (obj[j].id == val[i].fees_type) {
                            obj[j].num++;
                        }
                    }

                }
                self.list = obj;
            }
        },
        methods: {
            clickFun: function (index, id) {
                this.x = index;
                this.$emit("tabfun", id);
            }
        }
    }

</script>
<style lang="less" scoped>
    @import "../../../less/color.less";
    .tabs {
        width: 100%;
        overflow: hidden;
        margin-bottom: 20px;
        span {
            display: block;
            float: left;
            margin-right: 15px;
            font-size: 14px;
            color: @c2;
            cursor: pointer;
            margin-bottom: 10px;
            &.active {
                color: @c1;
                font-weight: bold;
            }
        }
    }
</style>