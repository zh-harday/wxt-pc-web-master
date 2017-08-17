<template>
    <div class="head" v-if="headShow">
        <div class="text">{{ schoolMessage.name }}</div>
        <a href="http://www.eduwxt.com" class="logo">
            <img src="../../img/logo.svg" alt="">
        </a>
        <div class="meau">
            <router-link :to='{ name: "worktody" }' :class="{active:headID == 1}">
                工作看板
            </router-link>
    
            <router-link :to='{ name: "zhaoshengshuju" }' :class="{active:headID == 2}">
                万象云校
            </router-link>
            
    
            <div v-if="schoolMessage.pigcms_token != null && schoolMessage.pigcms_token !='' ">
                <router-link :to='{ name: "Propaganda" }' :class="{active:headID == 3 }" v-if="wx_xuanchuan">
                    微信营销
                </router-link>
                <router-link :to='{ name: "Interaction" }' :class="{active:headID == 3 }" v-if="!wx_xuanchuan && wx_hudong">
                    微信营销
                </router-link>
                <router-link :to='{ name: "activity" }' :class="{active:headID == 3 }" v-if="!wx_xuanchuan && !wx_hudong && wx_huodong">
                    微信营销
                </router-link>
                <router-link :to='{ name: "ShoppingMall" }' :class="{active:headID == 3 }" v-if="!wx_xuanchuan && !wx_hudong && !wx_huodong && wx_shangcheng">
                    微信营销
                </router-link>
                <router-link :to='{ name: "Setup" }' :class="{active:headID == 3 }" v-if="!wx_xuanchuan && !wx_hudong && !wx_huodong && !wx_shangcheng && wx_shezhi">
                    微信营销
                </router-link>
            </div>
            <div v-else>
                <router-link :to="{name:'wxbind'}">公众号授权</router-link>
            </div>
        </div>
        <div class="right_meau">
    
            <!--帮助-->
            <div class="help">
                <el-dropdown :menu-align="'start'" :hide-on-click="true" trigger="click">
                    <span class="el-dropdown-link"></span>
                    <el-dropdown-menu slot="dropdown" class="message_xl">
                        <!--暂无帮助-->
                        <div v-if="helpid == '' ">
                            <el-dropdown-item class="title">
                                <s>当前页面暂无帮助</s>
                            </el-dropdown-item>
                        </div>
                        <!--微网站-->
                        <div v-if="helpid == 'weiwangzhan' ">
                            <el-dropdown-item class="title">
                                <s>微网站帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178403/" target="_blank">1、什么是微网站？</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1021113/" target="_blank">2、用微网站让学员随时获取学校最新动态</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178341/" target="_blank">3、第一步，教您填写配置首页信息</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178397/" target="_blank">4、第二步，在这里学习添加一个网站模板</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178369/" target="_blank">5、第三步，添加分类版块的教程</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1029830/" target="_blank">6、第四步，添加文章内容的教程</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178373/" target="_blank">7、第五步，学会给幻灯片的首页添加图片</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178402/" target="_blank">8、第六步，学会给有背景图的模板添加图片</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178382/" target="_blank">9、最后一步，设置快捷导航</a>
                            </el-dropdown-item>
                        </div>
                        <!--微场景帮助-->
                        <div v-if="helpid == 'weichangjing' ">
                            <el-dropdown-item class="title">
                                <s>微场景帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178445/" target="_blank">1、什么是微场景?</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178419/" target="_blank">2、制作一个像易企秀的H5微场景页面</a>
                            </el-dropdown-item>
                        </div>
                        <!--微渠道帮助-->
                        <div v-if="helpid == 'weiqudao' ">
                            <el-dropdown-item class="title">
                                <s>微渠道帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178457/" target="_blank">1、了解什么是微渠道?</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178448/" target="_blank">2、在这里学习如何生成一个渠道二维码</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178454/" target="_blank">3、利用渠道二维码对活动推广做整体解析</a>
                            </el-dropdown-item>
                        </div>
                        <!--图文消息帮助-->
                        <div v-if="helpid == 'tuwenxiaoxi' ">
                            <el-dropdown-item class="title">
                                <s>图文消息帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/177912/" target="_blank">1、如何在微校通后台编辑图文消息</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1022929/" target="_blank">2、利用关注回复引导用户了解学校介绍</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/177921/" target="_blank">3、运用关键字设置文本自动回复</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/177966/" target="_blank">4、运用关键字设置语音自动回复</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178000/" target="_blank">5、运用关键字设置多条图文自动回复</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/177873/" target="_blank">6、学习用微校通系统群发图文</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178073/" target="_blank">7、公众号对话无应答？设置一条自动回复</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178204/" target="_blank">8、学习生成原文生成链接</a>
                            </el-dropdown-item>
                        </div>
                        <!--线上互动-->
                        <div v-if="helpid == 'xianshanghudong' ">
                            <el-dropdown-item class="title">
                                <s>线上互动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179080/" target="_blank">1、了解什么是线上互动</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178712/" target="_blank">2、快速学习如何建立线上报名通道</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178714/" target="_blank">3、教您给教师设置名片</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178755/" target="_blank">4、为您制作一个校园论坛，让粉丝畅所欲言</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180778/" target="_blank">5、学会给学校制作校园相册</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178788/" target="_blank">6、制作线上邀约涵，还能帮您整理客户名单</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178717/" target="_blank">7、不用发愁问卷调查，线上轻松搞定！</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178716/" target="_blank">8、制作校园留言板，接收用户对学校建议</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178780/" target="_blank">9、线上活动推广怎么样？通过后台数据去检测</a>
                            </el-dropdown-item>
                        </div>
                        <!--线下互动-->
                        <div v-if="helpid == 'xianxiahudong' ">
                            <el-dropdown-item class="title">
                                <s>线下互动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179917/" target="_blank">1、什么是微现场？</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179916/" target="_blank">2、让粉丝持续签倒，累计够数值可获得奖励</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179916/" target="_blank">3、现场用手机摇一摇，可实现大屏幕上比赛</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179982/" target="_blank">4、在现场用微信墙做游戏，保证新奇有乐趣</a>
                            </el-dropdown-item>
                        </div>
                        <!--抽奖类-->
                        <div v-if="helpid == 'choujianglei' ">
                            <el-dropdown-item class="title">
                                <s>抽奖类活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179227/" target="_blank">1、动手设置一款刮刮卡，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179129/" target="_blank">2、调动起幸运大转盘，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179246/" target="_blank">3、九格子随机派发礼品，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179143/" target="_blank">4、三个金蛋总有一个能中奖，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179370/" target="_blank">5、一款让奖品滚动游戏，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179261/" target="_blank">6、拿起手机摇一摇中奖了，还能给微现场助兴</a>
                            </el-dropdown-item>
                        </div>
                        <!--集赞类-->
                        <div v-if="helpid == 'jizanlei' ">
                            <el-dropdown-item class="title">
                                <s>集赞类活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179135/" target="_blank">1、设置一个摇钱树，挂满礼物等你用金币来换取</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179451/" target="_blank">2、送粉丝一个礼物盒子，点开就能抽奖了</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180729/" target="_blank">3、动手制作一款人气超高的投票活动</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179411/" target="_blank">4、爱心募捐用于公益，让用户有参与荣誉感</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179211/" target="_blank">5、集齐字就能召唤神龙，跟好友分享乐趣吧</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179185/" target="_blank">6、看谁获得的助力值最高，会派发奖品奖励</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179104/" target="_blank">7、需好友帮忙刷出优惠金额，常用于招生季</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179086/" target="_blank">8、比拼人气的游戏，人气值高者会有大奖励</a>
                            </el-dropdown-item>
                        </div>
                        <!--红包类-->
                        <div v-if="helpid == 'hongbaolei' ">
                            <el-dropdown-item class="title">
                                <s>红包类活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179528/" target="_blank">1、给粉丝们的纯福利，推荐土豪学校使用</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179542/" target="_blank">2、随机给粉丝发红包，还能给微现场助兴</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179521/" target="_blank">3、有朋友帮助才能领到合体红包哦</a>
                            </el-dropdown-item>
                        </div>
                        <!--趣味类-->
                        <div v-if="helpid == 'quweilei' ">
                            <el-dropdown-item class="title">
                                <s>趣味类活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1030396/" target="_blank">1、一款娱乐的答题游戏，送给粉丝欢乐</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179358/" target="_blank">2、一款知识问答的游戏，就是为了奖励学霸</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/179464/" target="_blank">3、这是一款新闻视频+语音合成恶搞程序</a>
                            </el-dropdown-item>
                        </div>
                        <!--促销类-->
                        <div v-if="helpid == 'chuxiaolei' ">
                            <el-dropdown-item class="title">
                                <s>促销类活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180305/" target="_blank">1、发起一个项目活动，让众人帮你筹钱</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180501/" target="_blank">2、学校推出低价限量课程，粉丝先抢购先得</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180265/" target="_blank">3、课程低价促销，好友帮忙能砍到最低价格</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180557/" target="_blank">4、以拍卖形式出售课程，出价高者得</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180646/" target="_blank">5、课程价格由高到低递减售卖，先付款先得</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180605/" target="_blank">6、鼓励粉丝捆绑消费，团里人越多折扣越低</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180459/" target="_blank">7、通过让用户支付一元获取抽奖资格</a>
                            </el-dropdown-item>
                        </div>
                        <!--H5活动-->
                        <div v-if="helpid == 'h5yingxiao' ">
                            <el-dropdown-item class="title">
                                <s>H5活动帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1026970/" target="_blank">1、教你设置一款可玩性强的H5游戏</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1023038/" target="_blank">2、H5游戏库就这这里了，进来挑选吧</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1023039/" target="_blank">3、H5活动需要好友帮忙才能取得好成绩</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1023040/" target="_blank">4、H5活动库就这这里了，进来挑选吧</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1023033/" target="_blank">5、设置H5前的准备工作</a>
                            </el-dropdown-item>
                        </div>
                        <!--微信商城-->
                        <div v-if="helpid == 'weixinshangcheng' ">
                            <el-dropdown-item class="title">
                                <s>微信商城帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180153/" target="_blank">1、了解什么是微信商城</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180153/" target="_blank">2、为校区打造专属商城，方便用户在线选购</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180131/" target="_blank">3、有课程要促销吗，在这里设置团购优惠</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180178/" target="_blank">4、想实现在线商城消费，先要设置微信支付</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180250/" target="_blank">5、学习获取并上传微信支付证书</a>
                            </el-dropdown-item>
                        </div>
                        <!--收款账单-->
                        <div v-if="helpid == 'shoukuanzhangdan' ">
                            <el-dropdown-item class="title">
                                <s>收款账单帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1019940/" target="_blank">1、校方自己的微信支付平台产生的消费记录</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180116/" target="_blank">2、校方借用第三方支付平台的账单详情</a>
                            </el-dropdown-item>
                        </div>
                        <!--初始化设置-->
                        <div v-if="helpid == 'chushihuashezhi' ">
                            <el-dropdown-item class="title">
                                <s>初始化设置帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1022927/" target="_blank">1、学会将校区公众号绑定同城学微校通后台</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1022925/" target="_blank">2、设置校区定位服务，方便粉丝到访校区</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178227/" target="_blank">3、想实现家校沟通，需配置模板消息功能</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178077/" target="_blank">4、快捷关注能保障粉丝关注到校区公众号</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178246/" target="_blank">5、有用户恶意刷票？将其放置黑名单里</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178070/" target="_blank">6、开启粉丝权限，让用户体验畅通无阻</a>
                            </el-dropdown-item>
                        </div>
                        <!--粉丝设置-->
                        <div v-if="helpid == 'fensishezhi' ">
                            <el-dropdown-item class="title">
                                <s>粉丝设置帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180502/" target="_blank">1、粉丝管理包含哪些内容？</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180535/" target="_blank">2、粉丝信息管理中心介绍</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180545/" target="_blank">3、想区分粉丝类型，给他们分个组吧</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180559/" target="_blank">4、通过粉丝行为分析出他们的喜好</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/180570/" target="_blank">5、会员管理帮你筛选所有参与校区活动的人</a>
                            </el-dropdown-item>
                        </div>
                        <!--客服管理-->
                        <div v-if="helpid == 'kefushezhi' ">
                            <el-dropdown-item class="title">
                                <s>客服管理帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178497/" target="_blank">1、设置微客服，让用户感受无处不在的服务</a>
                            </el-dropdown-item>
                        </div>
                        <!--自定义菜单设置-->
                        <div v-if="helpid == 'zidingyicaidan' ">
                            <el-dropdown-item class="title">
                                <s>自定义菜单设置帮助</s>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/1021114/" target="_blank">1、什么是的自定义菜单？有什么作用</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/177897/" target="_blank">2、为您的校区量身制作一个自定义菜单</a>
                            </el-dropdown-item>
                            <el-dropdown-item>
                                <a href="http://help.eduwxt.com/hc/kb/article/178266/" target="_blank">3、设置个性化展示，让用户看到不同的菜单</a>
                            </el-dropdown-item>
                        </div>
                        <!--QQ服务-->
                        <el-dropdown-item>
                            <a href="http://crm2.qq.com/page/portalpage/wpa.php?uin=4008291566&aty=0&a=0&curl=&ty=1" target="_blank" class="qq_icon" id="qqkf">
                                <img src="../../img/qq_icon.png" alt="">
                                <span>QQ在线客服</span>
                            </a>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <!--通知11-->
            <div class="message">
                <el-dropdown :menu-align="'start'" :hide-on-click="true" trigger="click">
                    <span class="el-dropdown-link" :class="{ active:newMsgCount.unread > 0}"></span>
                    <el-dropdown-menu slot="dropdown" class="message_xl">
                        <el-dropdown-item class="title">
                            <s>通知中心</s>
                            <el-badge :value="newMsgCount.unread" class="item">
                            </el-badge>
                        </el-dropdown-item>
                        <el-dropdown-item v-for="item in newMsgList.message">
                            <router-link :to="{name:'notice_detail',params:{id:item.id}}" :class="{active:item.statue != 1 }">
                                <span class="school" v-if="item.type != 0 ">【校园】</span>
                                <span class="xitong" v-else>【系统】</span> {{ item.title }}
                            </router-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <router-link :to="{name:'AllMessage'}" class="lookall">查看全部通知
                                <i class="el-icon-d-arrow-right"></i>
                            </router-link>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
            <!--用户信息-->
            <div class="usermsg">
                <el-dropdown :menu-align="'start'" :hide-on-click="true" trigger="click">
                    <span class="el-dropdown-link">
                        <s>
                            <img :src="myMessage.face+'?'+new Date().getTime()" alt="">
                        </s>
                        {{ myMessage.name }}
                        <i class="el-icon-arrow-down"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown" class="message_x2">
                        <el-dropdown-item>
                            <router-link tag="span" class="span1" :to="{name:'myMessage',params:{type:'Message'}}">个人信息</router-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <router-link tag="span" class="span3" :to="{name:'myMessage',params:{type:'Password'}}">密码设置</router-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <router-link tag="span" class="span2" :to="{name:'myMessage',params:{type:'Binding'}}">账号绑定</router-link>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <span class="span4" @click="siginUp">退出登陆</span>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </div>
    </div>
</template>
<script>
module.exports = {

    computed: {
        helpid: function () {
            return this.$store.state.helpid
        },
        headID: function () {
            return this.$store.state.headID
        },
        headShow: function () {
            return this.$store.state.headShow
        },
        myMessage: function () {
            return this.$store.state.myMessage
        },
        schoolMessage: function () {
            return this.$store.state.schoolMessage;
        },
        newMsgCount: function () {
            return this.$store.state.newMsgCount;
        },
        newMsgList: function () {
            return this.$store.state.newMsgList;
        },

        wx_xuanchuan: function () {
            return this.$store.state.wx_xuanchuan;
        },
        wx_hudong: function () {
            return this.$store.state.wx_hudong;
        },
        wx_huodong: function () {
            return this.$store.state.wx_huodong;
        },
        wx_shangcheng: function () {
            return this.$store.state.wx_shangcheng;
        },
        wx_shezhi: function () {
            return this.$store.state.wx_shezhi;
        },

    },
    data: function () {
        return {
            activeIndex: 1,
            newMsgCount: {},
            messageList: {
                message: []
            },

            bind: false,

            //报表权限
            yx_bb_zssj: false,
            yx_xytj: false,
            yx_cwfx: false
        }
    },
    methods: {
        //退出
        siginUp: function () {
            var self = this;

            self.$http.get("/api/user/logout")
                .then(function (data) {
                    if (data.data.status == 'ok') {
                        window.location.href = "/v2/"
                    } else {
                        self.$message({
                            showClose: true,
                            message: data.data.msg,
                            type: 'warning'
                        });
                        if (data.data.code === 1000) {
                            self.$router.push({ name: 'login' });
                        }
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

        if (this.schoolMessage.pigcms_token != '' || this.schoolMessage.pigcms_token != null) {
            this.bind = true;
        } else {
            this.bind = false;
        }
    }
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
s {
    text-decoration: none;
}

.head {
    width: 100%;
    height: 60px;
    background-color: #fff;
    position: fixed;
    z-index: 100;
    box-shadow: 5px 0 5px rgba(0, 0, 0, .3);
    .logo {
        display: block;
        height: 60px;
        width: 80px;
        float: left;
        position: relative;
        img {
            display: block;
            height: 50px;
            margin: 0 auto;
            margin-top: 5px;
        }
    }
    .meau {
        float: left;
        overflow: hidden;
        height: 60px;
        font-size: @h3;
        color: @c2;
        >div {
            overflow: hidden;
            float: left;
        }
        a {
            display: block;
            line-height: 60px;
            padding: 0 20px;
            float: left;
            &.active {
                background-color: #eee;
                &:hover {
                    background-color: rgba(0, 0, 0, .05)
                }
            }
            &:hover {
                background-color: rgba(0, 0, 0, .05)
            }
        }
    }
    .text {
        width: 100%;
        height: 60px;
        line-height: 60px;
        text-align: center;
        position: absolute;
        left: 0;
        top: 0;
        z-index: -1;
        font-size: @h1;
        color: @c1;
    }
    .usermsg {
        float: left;
        height: 60px;
        text-align: center;
        line-height: 60px;
        position: relative;
        cursor: pointer;
        padding: 0 10px;
        .el-dropdown-link {
            display: block;
            height: 100%;
            width: 100%;
            position: relative;
            box-sizing: border-box;
            s {
                display: block;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                overflow: hidden;
                margin-top: 10px;
                float: left;
                margin-right: 10px;
                img {
                    display: block;
                    width: 100%;
                    height: 100%;
                }
            }
        }
        &:hover {
            background-color: rgba(0, 0, 0, .05);
        }
    }
    .message {
        float: left;
        width: 50px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        position: relative;
        cursor: pointer;
        .el-dropdown-link {
            display: block;
            width: 50px;
            height: 60px;
            background-image: url(../../img/notice_icon.png);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 25px 25px;
            &.active {
                &:after {
                    content: "";
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background-color: @error;
                    position: absolute;
                    top: 15px;
                    right: 15px;
                }
            }
        }
        &:hover {
            background-color: rgba(0, 0, 0, .05);
        }
    }
    .help {
        float: left;
        width: 50px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        position: relative;
        cursor: pointer;
        .el-dropdown-link {
            display: block;
            width: 50px;
            height: 60px;
            background-image: url(../../img/help_icon.png);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 25px 25px;
        }
        &:hover {
            background-color: rgba(0, 0, 0, .05);
        }
    }
    .right_meau {
        float: right;
        >div {
            position: relative;
        }
    }
}

.message_xl {
    width: 300px;
    li {
        border-bottom: 1px solid #f2f2f2;
        &:last-child {
            border-bottom: none;
        }
    }
    a {
        display: block;
        .textOverflow();
        color: @c2;
        &.active {
            font-weight: bold;
        }
        .school {
            color: @blue;
        }
        .xitong {
            color: @color;
        }
        &.lookall {
            text-align: center;
            color: @c2;
        }
        &.qq_icon {
            color: @color;
            overflow: hidden;
            padding: 8px 0;
            img {
                margin-right: 5px;
                display: block;
                height: 20px;
                float: left;
            }
            span {
                display: block;
                height: 20px;
                line-height: 20px;
                float: left;
                font-size: 14px;
            }
        }
    }
}

.message_x2 {
    width: 105px;
    span {
        display: block;
        padding-left: 24px;
        background-repeat: no-repeat;
        background-size: auto 16px;
        background-position: left center;
        &.span1 {
            background-image: url(../../img/head_my_icon.png);
        }
        &.span2 {
            background-image: url(../../img/head_zhanghaobd_icon.png);
        }
        &.span3 {
            background-image: url(../../img/head_mima_icon.png);
        }
        &.span4 {
            background-image: url(../../img/head_tuichu_icon.png);
        }
    }
}

.title {
    cursor: default;
    border-bottom: 1px solid #ddd;
    text-align: center;
    font-size: @h3;
    color: @c1;
    &:hover {
        background-color: #fff;
    }
}
</style>