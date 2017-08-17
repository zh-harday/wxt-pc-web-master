var webpack = require('webpack');
var path = require('path');
var vue = require('vue-loader');
var htmlWebpackPlugin = require("html-webpack-plugin");
var ExtractTextPlugin = require("extract-text-webpack-plugin"); //提取CSS
var CopyWebpackPlugin = require("copy-webpack-plugin");

module.exports = {
    entry: {
        commenVue: ["vue", 'vue-router', 'vue-resource', 'element-ui/lib/index.js', 'vuex'],
        main: './src/js/main.js'
    },
    output: {
        path: path.resolve(__dirname, 'app/public/v2'),
        publicPath: '/v2/', // 输出解析文件的目录，url 相对于 HTML 页面
        filename: 'js/[name].js?[chunkhash]'
    },
    module: {
        loaders: [{
                test: /\.js$/,
                loader: 'babel',
                include: path.resolve(__dirname, "src"),
                exclude: path.resolve(__dirname, "node_modules"),
                query: {
                    presets: ["latest"]
                }
            },
            {
                test: /\.vue$/, //一个匹配loaders所处理的文件的拓展名的正则表达式（必须）
                loader: 'vue', //loader的名称（必须）
                exclude: './node_modules/'
            },
            {
                test: /\.css$/,
                //从右向左处理 postcss加浏览器前缀 importLoaders 处理@import '../css/style.css' css-loader处理css文件 style把处理好的css已style标签插入到html页面
                loader: 'style-loader!css-loader',
                exclude: './node_modules/'
            },
            {
                test: /\.less$/,
                loader: ExtractTextPlugin.extract("style-loader", "css-loader!less-loader"),
                exclude: './node_modules/'
            },
            {
                test: /\.(png|jpg|gif|svg)/i,
                loaders: [
                    'file-loader?name=img/[name].[ext]?[hash:5]',
                    // 'file-loader?name=img/[name]-[hash:5].[ext]',
                    // 'url-loader?limit=8024&name=img/[name]-[hash:5].[ext]',
                    'image-webpack-loader' //图片压缩
                ],
                exclude: './node_modules/'
            },
            {
                test: /\.(eot|ttf|woff|woff2)(\?\S*)?$/,
                loader: 'file-loader?name=css/fonts/[name].[ext]'
            }
        ]

    },
    postcss: [
        require('autoprefixer')({
            //向前兼容5个版本
            borswers: ['last 5 versions']
        })
    ],
    //提取css到单独文件
    vue: {
        loaders: {
            css: ExtractTextPlugin.extract("css"),
            // you can also include <style lang="less"> or other langauges
            less: ExtractTextPlugin.extract("css!less"),
            postcss: [require('postcss-cssnext')()]
        }
    },

    plugins: [
        //提取公共JS
        new webpack.optimize.CommonsChunkPlugin('commenVue', 'js/commenVue.js?[chunkhash]'),
        new htmlWebpackPlugin({
            template: "src/index.html",
            inject: 'body',
            filename: "index.html",
            minify: { //页面压缩
                removeComments: false, //删除注释
                collapseWhitespace: false, //去空格
                minifyCSS: false, //压缩CSS
                minifyJS: false, //压缩JS
                removeEmptyAttributes: false //删除空白属性
            }

        }),
        //提取css到单独文件
        new ExtractTextPlugin("css/index.css?[contenthash]"),


        // 配置生产环境
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        // 压缩代码
        // new webpack.optimize.UglifyJsPlugin({
        //     compress: {
        //         warnings: false,
        //         drop_debugger: true,
        //         drop_console: true,
        //     }
        // }),
        new webpack.optimize.OccurenceOrderPlugin(), //为组件分配ID，通过这个插件webpack可以分析和优先考虑使用最多的模块，并为它们分配最小的ID
        new webpack.BannerPlugin("Copyright © 2014-2017 Learning Tech Co.,Ltd. 陕ICP备15001970-2号") //在css js头部插入注释

    ],
    resolve: {
        //查找module的话从这里开始查找
        // root: 'E:/github/flux-example/src', //绝对路径
        //自动扩展文件后缀名，意味着我们require模块可以省略不写后缀名
        // extensions: ['', '.js', '.json', '.scss'],
        //模块别名定义，方便后续直接引用别名，无须多写长长的地址
        alias: {
            'vue': 'vue/dist/vue.min.js', //后续直接 require('AppStore') 即可
            'vue-router': 'vue-router/dist/vue-router.min.js',
            'vue-resource': 'vue-resource/dist/vue-resource.min.js',
            'vuex': 'vuex/dist/vuex.min.js'
        }
    },
    watch: true
}