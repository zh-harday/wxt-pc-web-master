<template>
    <div class="album" v-show="show">
        <div class="modelbox" @click="show = false"></div>
        <div class="albumbox" ref="albumbox">
            <img :src="imgSrc" alt="" @mousedown="mousedown" @mousemove="mousemove" ref="albumBigImg" :style="{transform:'translate3d('+x+'px,'+y+'px,0)'}">
            <span @click="show = false"></span>
        </div>
        <div class="albumbottom">
            <div ref="albumbottom">
                <span v-for="(item,index) in list" :class="{active:indexs == index}" @click="changeImg(index)">
                    <img :src="item" alt="">
                </span>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {
    props: ["list", "change"],
    watch: {
        change: function () {
            this.show = true;
        },
        list: function () {
            this.$refs.albumbottom.style.width = this.list.length * 90 + "px";
            this.imgSrc = this.list[0];
        }
    },
    data: function () {
        return {
            show: false,
            indexs: 0,
            imgSrc: "",
            canmove: false,
            lx: 0,
            ly: 0,
            x:0,
            y:0
        }
    },
    methods: {
        mousedown: function (e) {
            var self = this;
            self.canmove = true;
            self.lx = e.offsetX;
            self.ly = e.offsetY;
        },
        mousemove: function (e) {
            var self = this;
            e.stopPropagation();
            e.preventDefault();
            if (self.canmove) {
                self.x += (e.offsetX - self.lx);
                self.y += (e.offsetY - self.ly);
            }
        },
        changeImg: function (index) {
            this.indexs = index;
            this.imgSrc = this.list[index];
        }
    },
    mounted: function () {
        var self = this;
        self.$nextTick(function () {
            self.$refs.albumbottom.style.width = self.list.length * 90 + "px";
            self.$refs.albumbox.style.height = (document.documentElement.clientHeight - 150) + "px";

        })
        var box = document.getElementsByClassName("album")[0];
        box.onmouseup = function () {
            self.canmove = false;
        }
    }
}
</script>
<style lang="less" scoped>
@import "../../less/color.less";
.album {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 3000;
    .modelbox {
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
    }
    .albumbox {
        width: 90%;
        height: 90%;
        background-color: #fff;
        border-radius: 5px 5px 0 0;
        overflow: hidden;
        position: absolute;
        left: 0;
        right: 0;
        margin: 0 auto;
        top: 50px;
        img {
            display: block;
            max-width: 100%;
            .autocenter();
            cursor:move;
        }
        span{
            display: block;
            width: 50px;
            height: 50px;
            position: absolute;
            right: 0;
            top: 0;
            background-image: url(../../img/albumclose.png);
            background-size: 100% 100%;
            cursor: pointer;
        }
    }
    .albumbottom {
        height: 100px;
        width: 100%;
        overflow: hidden;
        background-color: #000;
        position: fixed;
        bottom: 0;
        left: 0;
        overflow-x: auto;
        padding: 10px;
        box-sizing: border-box;
        >div {
            height: 80px;
            width: 100%;
            span {
                display: block;
                width: 80px;
                height: 80px;
                overflow: hidden;
                margin-right: 10px;
                float: left;
                box-sizing: border-box;
                cursor: pointer;
                &.active {
                    border: 1px solid red;
                }
                img {
                    display: block;
                    width: 100%;
                    min-height: 80px;
                }
            }
        }
    }
}
</style>
