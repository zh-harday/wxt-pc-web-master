var obj = function() {
    this.canvas = document.createElement("canvas");
    this.c = this.canvas.getContext("2d");
    this.ps = 0;
}
obj.prototype.init = function(id, width, height, n, r) {
    this.canvas.setAttribute("width", width);
    this.canvas.setAttribute("height", height);
    this.canvas.setAttribute("id", "canvas");

    this.canvas.style.display = "block";
    this.canvas.style.backgroundColor = "block";
    this.canvas.style.margin = "auto";
    this.canvas.style.width = width / 2 + "px";
    this.canvas.style.height = height / 2 + "px";
    this.ps = r / n;

    document.getElementById(id).appendChild(this.canvas);
}
obj.prototype.polygon = function(n, x, y, r, angle, num, text) {
    if (num > 4) {
        return;
    }
    this.c.beginPath();
    var angle = angle || 0;
    this.c.moveTo(x + r * Math.sin(angle), y - r * Math.cos(angle)); //确立第一个点  
    var delta = 2 * Math.PI / n; //相邻两个顶点之间的夹角  
    for (var i = 0; i < n; i++) { //其他顶点  
        angle += delta; //角度调整  
        this.c.lineTo(x + r * Math.sin(angle), y - r * Math.cos(angle));
        if (num == 0) {
            this.c.lineTo(x, y);
            this.c.lineTo(x + r * Math.sin(angle), y - r * Math.cos(angle));
            this.c.font = "26px Arial";
            this.c.fillStyle = '#3fda9e';

            if (i == 0) {
                this.c.textAlign = 'center';
                this.c.textBaseline = 'top';
                this.c.fillText(text[1], x + r * Math.sin(angle), y - r * Math.cos(angle) + 5);
            } else if (i == 1) {
                this.c.textAlign = 'center';
                this.c.textBaseline = 'top';
                this.c.fillText(text[2], x + r * Math.sin(angle), y - r * Math.cos(angle) + 5);
            } else if (i == 2) {
                this.c.textAlign = 'right';
                this.c.textBaseline = 'middle';
                this.c.fillText(text[3], x + r * Math.sin(angle), y - r * Math.cos(angle));
            } else if (i == 3) {
                this.c.textAlign = 'center';
                this.c.textBaseline = 'bottom';
                this.c.fillText(text[4], x + r * Math.sin(angle), y - r * Math.cos(angle));
            } else if (i == 4) {
                this.c.textAlign = 'left';
                this.c.textBaseline = 'middle';
                this.c.fillText(text[0], x + r * Math.sin(angle), y - r * Math.cos(angle));
            }
        }
    }
    this.c.closePath(); //首位相邻  
    this.c.lineWidth = 2; //边框样式
    this.c.strokeStyle = "rgb(221,221,221)"; //边框样式
    this.c.stroke() //绘制边框
    num++;
    r -= this.ps;
    this.polygon(n, x, y, r, angle, num)
}
obj.prototype.polygonA = function(n, x, y, arr, angle) {
    this.c.beginPath();
    var angle = angle || 0;
    this.c.moveTo(x + arr[0] * this.ps * Math.sin(angle), y - arr[0] * this.ps * Math.cos(angle)); //确立第一个点  
    var delta = 2 * Math.PI / n; //相邻两个顶点之间的夹角  
    for (var i = 1; i < n; i++) { //其他顶点  
        angle += delta; //角度调整  
        this.c.lineTo(x + arr[i] * this.ps * Math.sin(angle), y - arr[i] * this.ps * Math.cos(angle));
    }
    this.c.closePath(); //首位相邻  
    this.c.lineWidth = 4; //边框样式
    this.c.strokeStyle = "rgba(63,218,158,.5)"; //边框样式
    this.c.fillStyle = "rgba(63,218,158,.5)";
    this.c.stroke() //绘制边框
    this.c.fill();
}
module.exports = obj;