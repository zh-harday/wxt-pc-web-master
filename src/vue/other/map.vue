<template>
    <div id="baidumapbox"></div>
</template>
<script>
    module.exports = {
        props: {	//里面存放的也是数据，与data里面的数据不同的是，这里的数据是从其他地方得到的数据
            height: {
                type: Number,
                default: 300
            },
            longitude: 0,	//定义经度
            latitude: 0,	//定义纬度
            canclick: false
        },
        mounted: function () {
            var self = this;
            this.$nextTick(function () {

                var map = new BMap.Map("baidumapbox");
                var p = false;
                map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
                map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
                var marker;
                if (self.longitude == 0 && self.latitude == 0) {
                    // 百度地图API功能
                    var point = new BMap.Point(108.95309828, 34.2777999);
                    map.centerAndZoom(point, 12);
                    var geolocation = new BMap.Geolocation();
                    geolocation.getCurrentPosition(function (r) {
                        if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                            p = true;
                            marker = new BMap.Marker(r.point);
                            map.addOverlay(marker);
                            map.panTo(r.point);
                        }
                        else {
                            alert('failed' + this.getStatus());
                        }
                    }, { enableHighAccuracy: true })
                } else {
                    var point = new BMap.Point(this.longitude, this.latitude);
                    marker = new BMap.Marker(point);
                    map.addOverlay(marker);
                    // 将标注添加到地图中
                    map.centerAndZoom(point, 12);
                    p = true;
                }



                map.addEventListener("click", function (e) {
                    
                    if (!self.canclick) {
                        return;
                    }

                    if (!p) {
                        self.$message({
                            message: '定位中，请稍后！',
                            type: 'warning'
                        });
                        return;
                    }
                    map.removeOverlay(marker);

                    self.longitude = e.point.lng;
                    self.latitude = e.point.lat;

                    var point = new BMap.Point(self.longitude, self.latitude);
                    marker = new BMap.Marker(point);

                    map.addOverlay(marker);

                    self.$emit('getmappoint', {
                        lng: e.point.lng,
                        lat: e.point.lat
                    });

                });

            })

        }
    }

</script>