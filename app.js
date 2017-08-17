/**
 * Created by keller on 2016/12/19.
 */
var httpProxy = require('http-proxy');
var express = require("express");
var port = 801;
var http = require("http");
var querystring = require('querystring');
var path = require("path");

var app = express();
var server = http.createServer(app);


// 新建一个代理 Proxy Server 对象  
var proxy = httpProxy.createProxyServer();

// 捕获异常  
proxy.on('error', function(err, req, res) {
    res.writeHead(500, {
        'Content-Type': 'text/plain'
    });
    res.end('Something went wrong. And we are reporting a custom error message.');
});

//设置静态资源
app.use(express.static(path.resolve(__dirname,"app/public")));
//设置模板路由
// app.set("views", __dirname + "/view/");
// //设置html模板
// app.engine('html', require('ejs').__express);
// app.set('views engine', 'html');

app.all("/api/*", function(req, res) {
    delete req.headers.host;
    proxy.web(req, res, { target: 'http://v2.eduwxt.com' });
})

app.get("/upload/*", function(req, res) {
    delete req.headers.host;
    proxy.web(req, res, { target: 'http://v2.eduwxt.com' });
})

server.listen(port, function() {
    console.log("代理服务器已启动，http://127.0.0.1:" + port);
});
