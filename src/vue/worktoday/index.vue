<template>
	<div class="worktody1">
		<div>
			<div class="worktody">
				<el-row :gutter="20">
					<el-col :span="8" class="left_message">
						<el-card class="box-card">
							<h1 class="title_h1">欢迎使用同城学微校通!</h1>
							<el-row :gutter="20" style="margin-bottom:28px">
								<el-col :span="12">
									<div class="tc_box">
										<div class="tc_box_left">
											<img src="../../img/yx_data_bm1.png" alt="">
										</div>
										<div class="tc_box_right">
											<span>当前套餐</span>
											<p class="taocan_font">{{ campus_msg.pack_name }}</p>
										</div>
									</div>
									<p class="jindutiao p1">
										<s>到期时间
											<i>{{ campus_msg.exp_time | yyyy_mm_dd }}</i>
										</s>
										<span>
											<span :style="{ width: ( (new Date().getTime()/1000 - parseInt(campus_msg.re_time)  )/( parseInt(campus_msg.exp_time)-parseInt(campus_msg.re_time) ) )*140 + 'px'}"></span>
										</span>
									</p>
								</el-col>
								<el-col :span="12">
									<div class="tc_box">
										<div class="tc_box_left">
											<img src="../../img/yxxiaoxuicojn.png" alt="">
										</div>
										<div class="tc_box_right">
											<span>校区限制</span>
											<p>{{ campus_msg.campus_quota }}</p>
										</div>
									</div>
									<p class="jindutiao p2">
										<s>已开通
											<i>{{ campus_msg.campus_opened }}</i>
										</s>
										<span>
											<span :style="{ width: campus_msg.campus_opened/campus_msg.campus_quota*140 + 'px'}"></span>
										</span>
									</p>
								</el-col>
							</el-row>
							<el-row :gutter="20">
								<el-col :span="12">
									<div class="tc_box">
										<div class="tc_box_left">
											<img src="../../img/yxxueyuanicom.png" alt="">
										</div>
										<div class="tc_box_right">
											<span>学员限制</span>
											<p>{{ campus_msg.student_quota }}</p>
										</div>
									</div>
									<p class="jindutiao p3">
										<s>已注册
											<i>{{ campus_msg.student_opened }}</i>
										</s>
										<span>
											<span :style="{ width: campus_msg.student_opened/campus_msg.student_quota*140 + 'px'}"></span>
										</span>
									</p>
								</el-col>
								<el-col :span="12" class="text-right">
									<div class="tc_box">
										<div class="tc_box_left">
											<img src="../../img/yxyingxiaocishu.png" alt="">
										</div>
										<div class="tc_box_right">
											<span>周访问限制</span>
											<p>{{ campus_msg.pv_quota }}</p>
										</div>
									</div>
									<p class="jindutiao p4">
										<s>已展示
											<i>{{ campus_msg.pv_opened }}</i>
										</s>
										<span>
											<span :style="{ width: campus_msg.pv_opened/campus_msg.pv_quota*140 + 'px'}"></span>
										</span>
									</p>
								</el-col>
							</el-row>
							<div class="help">
								<h1>帮助提示：</h1>
								<p>1 点击页面右上角"
									<img src="../../img/help_icon.png" />"进入当前页面帮助说明；</p>
								<p>2
									<a href="http://crm2.qq.com/page/portalpage/wpa.php?uin=4008291566&aty=0&a=0&curl=&ty=1" target="_blank" id="qqkfs">
										<img src="../../img/qqkefu.png" alt="">
									</a>周一至周日9:00～18:00；</p>
								<p>3 拨打客服热线
									<span>400-829-1566</span>。</p>
							</div>
						</el-card>
					</el-col>
					<el-col :span="16">
						<el-card class="box-card">
							<h1 class="title_h2">Geting Things Done</h1>
							<el-row :gutter="45" class="bth">
								<el-col :span="6">
									<div class="wz_box" @click="wasskData.count>0?(wassk = true):(wassk = false)">
										<span :class="{active:wasskData.count>0}">{{ wasskData.count }}</span>
										<p>未按时上课</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="wasxkData.count>0?(wasxk = true):(wasxk = false)">
										<span :class="{active:wasxkData.count>0}">{{ wasxkData.count }}</span>
										<p>未按时下课</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="wdpxyData.count>0?(wdpxy = true):(wdpxy = false)">
										<span :class="{active:wdpxyData.count>0}">{{ wdpxyData.count }}</span>
										<p>未点评学员</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="ylkaoqinData.count>0?(ylkaoqin = true):(ylkaoqin = false)">
										<span :class="{active:ylkaoqinData.count>0}">{{ ylkaoqinData.count }}</span>
										<p>遗漏考勤</p>
									</div>
								</el-col>
							</el-row>
							<el-row :gutter="45" class="bth">
								<el-col :span="6">
									<div class="wz_box" @click="ywgjData.count>0?(ywgj = true):(ywgj = false)">
										<span :class="{active:ywgjData.count > 0}">{{ ywgjData.count }}</span>
										<p>已延误跟进</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="xyxxuyData.count>0?(xyxxuy = true):(xyxxuy = false)">
										<span :class="{active:xyxxuyData.count > 0}">{{ xyxxuyData.count }}</span>
										<p>新意向学员</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="xueyuangenjindtData.count>0?(xueyuangenjindt = true):(xueyuangenjindt = false)">
										<span :class="{active:xueyuangenjindtData.count>0}">{{ xueyuangenjindtData.count }}</span>
										<p>意向学员跟进动态</p>
									</div>
								</el-col>
								<el-col :span="6">
									<div class="wz_box" @click="xueyuanhuipingData.count>0?(xueyuanhuiping = true):(xueyuanhuiping = false)">
										<span :class="{active:xueyuanhuipingData.count > 0}">{{ xueyuanhuipingData.count }}</span>
										<p>学员回评动态</p>
									</div>
								</el-col>
							</el-row>
							<div class="kuaijierukou">
								<div class="title_h1">快捷入口</div>
								<router-link :to="{name:'class1',query:{staus:'',index:5}}" tag="div" class="kuaijie_box">
									<img src="../../img/myclass_kj.png" class="active1">
									<img src="../../img/myclass_kj_a.png" class="active2">
									<span>我的班级</span>
								</router-link>
								<router-link :to="{ name:'student_list1',params:{ type:'All'}}" tag="div" class="kuaijie_box">
									<img src="../../img/wodexueyuansss.png" class="active1">
									<img src="../../img/wodexueyuansss_a.png" class="active2">
									<span>我的学员</span>
								</router-link>
								<router-link :to="{name:'recruit_my',params:{ type:'all' }}" tag="div" class="kuaijie_box">
									<img src="../../img/yixiangxueyuanooo.png" class="active1">
									<img src="../../img/yixiangxueyuanooo_a.png" class="active2">
									<span>我的意向学员</span>
								</router-link>
								<div class="kuaijie_box" @click="addzhengshixyFun">
									<img src="../../img/tianjiazhengshixueyuan.png" class="active1">
									<img src="../../img/tianjiazhengshixueyuan_a.png" class="active2">
									<span>添加正式学员</span>
								</div>
								<div class="kuaijie_box" @click="addyixiangxyFun">
									<img src="../../img/tianjiayixiangxueyuan.png" class="active1">
									<img src="../../img/tianjiayixiangxueyuan_a.png" class="active2">
									<span>添加意向学员</span>
								</div>
								<router-link :to="{name:'Audition',query:{add:true}}" class="kuaijie_box">
									<img src="../../img/shitingkelll.png" class="active1">
									<img src="../../img/shitingkelll_a.png" class="active2">
									<span>添加新试听课</span>
								</router-link>
							</div>
						</el-card>
					</el-col>
				</el-row>
				<el-row :gutter="20">
					<el-col :span="8">
						<el-card class="list_card">
							<h1 class="box-card_title">今日课程</h1>
							<ul class="cartd">
								<li class="tishixinxi" v-if="mysubjecttoday.today_curriculum.length == 0">暂无内容</li>
								<li v-for="item in mysubjecttoday.today_curriculum">
									<h1>
										<s>{{ item.subject_name }}</s>
										<el-tag type="danger" v-if="item.status == 0 && item.start_time < new Date().getTime()/1000">已延误</el-tag>
										<el-tag type="success" v-if="item.status == 0 && item.start_time >= new Date().getTime()/1000">即将开课</el-tag>
										<el-tag type="success" v-if="item.status == 1">上课中</el-tag>
										<el-tag type="gray" v-if="item.status == 2">已下课</el-tag>
										<time v-if="item.status != 1" :class="{active:item.status == 0 && item.start_time < new Date().getTime()/1000}">{{ item.start_time | M_S }}~{{ item.end_time | M_S }}</time>
										<time v-if="item.status == 1">上课时间：{{ item.start_time | M_S }}</time>
									</h1>
									<p>
										<span>班主任：{{ item.class_teacher }}</span>
										<s>1102美术教室</s>
									</p>
									<div>
										<span>{{ item.class_name }}</span>
										<s>
											<el-button type="info" size="small" @click="kaoqinFun(item.class_id,item.curriculum_id)" v-if="yx_bjgl_my_xskq">考勤</el-button>
											<el-button type="success" size="small" v-if="yx_bjgl_my_kczt && item.status == 0" @click="shangkeFun(item.class_id,item.curriculum_id,'start')">上课</el-button>
											<el-button type="warning" size="small" v-if="yx_bjgl_my_kczt && item.status == 1" @click="shangkeFun(item.class_id,item.curriculum_id,'end')">下课</el-button>
										</s>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="mysubjecttoday.count>3">
								<el-pagination @current-change="MysubjecttodayClick" :current-page="mysubpage" :page-size="3" layout="total, prev, pager, next" :total="mysubjecttoday.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8" v-if="gzkb_jrkc">
						<el-card class="list_card">
							<h1 class="box-card_title">全校今日课程</h1>
							<ul class="cartd">
								<li class="tishixinxi" v-if="subjecttoday.today_curriculum.length == 0">暂无内容</li>
								<li v-for="item in subjecttoday.today_curriculum">
									<h1>
										<s>{{ item.subject_name }}</s>
										<el-tag type="danger" v-if="item.status == 0 && item.start_time < new Date().getTime()/1000">已延误</el-tag>
										<el-tag type="primary" v-if="item.status == 0 && item.start_time >= new Date().getTime()/1000">即将开课</el-tag>
										<el-tag type="success" v-if="item.status == 1">上课中</el-tag>
										<el-tag type="gray" v-if="item.status == 2">已下课</el-tag>
										<time v-if="item.status != 1" :class="{active:item.status == 0 && item.start_time < new Date().getTime()/1000}">{{ item.start_time | M_S }}~{{ item.end_time | M_S }}</time>
										<time v-if="item.status == 1">上课时间：{{ item.start_time | M_S }}</time>
									</h1>
									<p>
										<span class="ss">班主任：{{ item.class_teacher }}</span>
										<span class="pp">授课老师：{{ item.curriculum_teacher }}</span>
										<s class="pp">{{ item.room_name }}</s>
									</p>
									<div>
										<span>{{ item.class_name }}</span>
										<s>
											<el-button type="info" size="small" @click="kaoqinFun(item.class_id,item.curriculum_id)" v-if="yx_bjgl_all">考勤</el-button>
											<el-button type="success" size="small" v-if="yx_bjgl_all && item.status == 0" @click="shangkeFun(item.class_id,item.curriculum_id,'start')">上课</el-button>
											<el-button type="warning" size="small" v-if="yx_bjgl_all && item.status == 1" @click="shangkeFun(item.class_id,item.curriculum_id,'end')">下课</el-button>
										</s>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="subjecttoday.count>3">
								<el-pagination @current-change="subjecttodayClick" :current-page="subpage" :page-size="3" layout="total, prev, pager, next" :total="subjecttoday.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title">今日跟进</h1>
							<ul class="cartd1">
								<li class="tishixinxi" v-if="gjList.intention.length == 0">暂无内容</li>
								<li v-for="item in gjList.intention">
									<h1 class="jinrigenjin_title">
										<img src="../../img/yixiang_1.png" alt="">{{ item.name }}
										<el-button type="success" size="small" class="jinrigenjin_btn" @click="$router.push({name:'student_server',params:{id:item.id}})">跟进</el-button>
									</h1>
									<el-row :gutter="20">
										<el-col :span="12">
											<span class="jinrigenjin_tel">
												<img src="../../img/telphone_icon.png" alt="">{{ item.phone }}</span>
										</el-col>
										<el-col :span="12">
											<span class="jinrigenjin_sr">
												<img src="../../img/shengri_icon.png" alt="">{{ item.age }}岁</span>
										</el-col>
									</el-row>
								</li>
							</ul>
							<div class="fenye" v-if="gjList.count > gjList.intention.length">
								<el-pagination @current-change="genjinpageClick" :current-page="genjpage" :page-size="studlimit" layout="total, prev, pager, next" :total="gjList.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title">请假审批</h1>
							<ul class="cartd_2">
								<li class="tishixinxi" v-if="leavelist.leave.length == 0">暂无内容</li>
								<li v-for="item in leavelist.leave">
									<div class="cartd_2_title">
										<span>
											<img :src="item.face?'http://wx.eduwxt.com'+item.face:'http://img.eduwxt.com/assets/images/users/avatar-11.jpg'" alt="">
										</span>
										<s>{{ item.student_name }}
											<time>{{ item.time | times }}</time>
										</s>
										<el-dropdown trigger="click" @command="qingjiafun" v-if="yx_bjgl_my_xskq">
											<el-button type="primary" size="small">
												同意
												<i class="el-icon-caret-bottom el-icon--right"></i>
											</el-button>
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item :command="'1/'+item.class_id+'/'+item.curriculum_id+'/'+item.student_id ">扣课时</el-dropdown-item>
												<el-dropdown-item :command="'0/'+item.class_id+'/'+item.curriculum_id+'/'+item.student_id ">不扣课时</el-dropdown-item>
											</el-dropdown-menu>
										</el-dropdown>
									</div>
									<p>{{ item.info }}</p>
									<div class="bottoms" id="qingjia">
										<!--<img src="../../img/kechengiconshj.png" alt="">-->
										<span>{{ item.subject }}</span>
										<s>{{ item.class_name }}</s>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="leavelist.count > 3">
								<el-pagination @current-change="leaveClickpage" :current-page="leavepage" :page-size="limit" layout="total, prev, pager, next" :total="leavelist.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title">
								<span>试听安排</span>
								<el-select v-if="myMessage.campus_id == 1" v-model="st_campus_id" placeholder="请选择校区" size="small" @change="changecampusstk">
									<el-option label="全部校区" value=""></el-option>
									<el-option v-for="item in campus" :label="item.name" :value="item.id">
									</el-option>
								</el-select>
								<el-button type="text" @click="getNextWeek" :class="{active:!stk}">下周</el-button>
								<el-button type="text" :class="{active:stk}" @click="getThisWeek">本周</el-button>
							</h1>
							<div class="shitinganpai">
								<ul class="shiting_list">
									<li class="tishixinxi" v-if="shitingkeList.lecture.length == 0">暂无内容</li>
									<li v-for="item in shitingkeList.lecture">
										<h1>
											<s>{{ item.title }}</s>
											<el-tag type="success">已约{{ item.student_count }}人</el-tag>
										</h1>
										<p>
											<s>
												<img src="../../img/gz_room.png" alt="">{{ item.room_name }}</s>
										</p>
										<p class="last">
											<time>
												<img src="../../img/wxtable_liulan.png" alt="">{{ item.start_time | yyyy_mm_dd_M_S_week }}</time>
											<span>
												<img src="../../img/laoshi.png" alt="">{{ item.subject_name }}</span>
										</p>
									</li>
								</ul>
								<div class="fenye" v-if="shitingkeList.count > shitingkeList.lecture">
									<el-pagination @current-change="shitingkepage" :current-page="option.page" :page-size="option.limit" layout="total, prev, pager, next" :total="shitingkeList.count">
									</el-pagination>
								</div>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8" v-if="gzkb_xkbm">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title">选课报名</h1>
							<ul class="cartd_2">
								<li class="tishixinxi" v-if="xuankeBMList.reservation.length == 0">暂无内容</li>
								<li v-for="item in xuankeBMList.reservation">
									<div class="cartd_2_title">
										<span>
											<img :src="item.face" alt="">
										</span>
										<s>{{ item.student_name }}
											<i>No.{{ item.student_number }}</i>
										</s>
										<el-dropdown trigger="click" @command="clickup" v-if="item.status  == 0">
											<el-button type="info" size="small">
												<span v-if="item.status  == 0">未处理</span>
												<span v-if="item.status  == 1">已联系</span>
												<span v-if="item.status  == 2">已报名</span>
												<span v-if="item.status  == 3">已放弃</span>
												<i class="el-icon-caret-bottom el-icon--right"></i>
											</el-button>
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item :command="'1/'+item.id">已联系</el-dropdown-item>
												<el-dropdown-item :command="'2/'+item.id">已报名</el-dropdown-item>
												<el-dropdown-item :command="'3/'+item.id">已放弃</el-dropdown-item>
												<el-dropdown-item :command="'4/'+item.id" divided>删除</el-dropdown-item>
											</el-dropdown-menu>
										</el-dropdown>
	
										<el-dropdown trigger="click" @command="clickup" v-if="item.status  == 1">
											<el-button type="success" size="small">
												<span v-if="item.status  == 0">未处理</span>
												<span v-if="item.status  == 1">已联系</span>
												<span v-if="item.status  == 2">已报名</span>
												<span v-if="item.status  == 3">已放弃</span>
												<i class="el-icon-caret-bottom el-icon--right"></i>
											</el-button>
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item :command="'1/'+item.id">已联系</el-dropdown-item>
												<el-dropdown-item :command="'2/'+item.id">已报名</el-dropdown-item>
												<el-dropdown-item :command="'3/'+item.id">已放弃</el-dropdown-item>
												<el-dropdown-item :command="'4/'+item.id" divided>删除</el-dropdown-item>
											</el-dropdown-menu>
										</el-dropdown>
	
										<el-dropdown trigger="click" @command="clickup" v-if="item.status  == 3">
											<el-button type="danger" size="small">
												<span v-if="item.status  == 0">未处理</span>
												<span v-if="item.status  == 1">已联系</span>
												<span v-if="item.status  == 2">已报名</span>
												<span v-if="item.status  == 3">已放弃</span>
												<i class="el-icon-caret-bottom el-icon--right"></i>
											</el-button>
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item :command="'1/'+item.id">已联系</el-dropdown-item>
												<el-dropdown-item :command="'2/'+item.id">已报名</el-dropdown-item>
												<el-dropdown-item :command="'3/'+item.id">已放弃</el-dropdown-item>
												<el-dropdown-item :command="'4/'+item.id" divided>删除</el-dropdown-item>
											</el-dropdown-menu>
										</el-dropdown>
	
									</div>
									<p>预报班级：{{ item.class_name }}</p>
									<div class="bottoms">
										<span>
											<img src="../../img/wxtable_liulan.png" alt="">{{ item.time | yyyy_mm_dd_M_S }}</span>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="xuankeBMList.count > 3">
								<el-pagination @current-change="xuankeBMfenye" :current-page="xkbmpage" :page-size="limit" layout="total, prev, pager, next" :total="xuankeBMList.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8" v-if="gzkb_tsyj">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title">投诉意见</h1>
							<ul class="cartd_2">
								<li class="tishixinxi" v-if="suggestList.suggest.length == 0">暂无内容</li>
								<li v-for="item in suggestList.suggest">
									<div class="cartd_2_title">
										<span>
											<img :src="item.face" alt="">
										</span>
										<s>{{ item.student_name }}
											<time>{{ item.time | yyyy_mm_dd_M_S }}</time>
										</s>
										<el-button type="success" size="small" v-if="item.reply_time == null" @click="chuliTSBox(item.id)">处理</el-button>
										<el-button type="text" size="small" v-if="item.reply_time != null" @click="lookTSBox(item)" class="clearright">查看处理结果</el-button>
										<el-button type="danger" size="small" @click="delSuggest(item.id)">删除</el-button>
									</div>
									<p :title="item.body">{{ item.body }}</p>
									<div class="bottoms">
										<span class="jinrigenjin_tel">
											<img src="../../img/telphone_icon.png" alt="" v-if="item.contact != ''">{{ item.contact }}</span>
										<s>
											<img src="../../img/wxtable_liulan.png" alt="" v-if="item.reply_time">{{ item.reply_time | yyyy_mm_dd_M_S }}</s>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="suggestList.count>3">
								<el-pagination @current-change="suggestpageFUn" :current-page="sugsestpage" :page-size="limit" layout="total, prev, pager, next" :total="suggestList.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
					<el-col :span="8">
						<el-card class="box-card1 list_card">
							<h1 class="box-card_title xueyuanlist">
								学员通知
								<el-button type="success" size="small" @click="xynoticeShow = true" v-if="gzkb_xytz">发布新通知</el-button>
							</h1>
							<ul class="cartd_2">
								<li class="tishixinxi" v-if="studentNoticeList.notice.length == 0">暂无内容</li>
								<li v-for="item in studentNoticeList.notice" class="xueyuantongzhi" @click="xynoticeFun(item.id)">
									<div class="notice_title">
										{{ item.title }}
									</div>
									<div class="bottoms">
										<span class="jinrigenjin_tel">{{ item.campus_name }}</span>
										<s>
											<img src="../../img/wxtable_liulan.png" alt="" v-if="item.time">{{ item.time | yyyy_mm_dd_M_S }}</s>
									</div>
								</li>
							</ul>
							<div class="fenye" v-if="studentNoticeList.count>studentNoticeList.limit">
								<el-pagination @current-change="studnoticepageFUn" :current-page="studpage" :page-size="studlimit" layout="total, prev, pager, next" :total="studentNoticeList.count">
								</el-pagination>
							</div>
						</el-card>
					</el-col>
				</el-row>
				<!--投诉意见弹框-->
				<el-dialog :title="tousutitle" v-model="tousujianyi" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<el-input type="textarea" :autosize="{ minRows: 4, maxRows: 8}" placeholder="请输入该投诉建议的处理结果" v-model="reply" v-show="toushuShow">
					</el-input>
					<p class="chulixiangqing" v-show="!toushuShow">
						{{ suggestRe.reply }}
					</p>
					<div class="foots_tousu" v-show="!toushuShow">
						<span>负责老师：{{ suggestRe.reply_name }}</span>
						<span>处理时间：{{ suggestRe.reply_time | yyyy_mm_dd_M_S }}</span>
					</div>
					<span slot="footer" class="dialog-footer" v-show="toushuShow">
						<el-button @click="tousujianyi = false">取消</el-button>
						<el-button type="primary" @click="huifusuggest">确定</el-button>
					</span>
				</el-dialog>
				<!--未按时上课-->
				<el-dialog title="未按时上课" v-model="wassk" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style2">
						<router-link tag="li" :to="{name:'class_keqian',params:{id:item.class_id}}" v-for="item in wasskData.curriculum">
							<h1>
								<s>{{ item.subject_name }}</s>
								<el-tag type="warning">已延误{{ item.start_time | ywMint }}</el-tag>
								<i class="el-icon-arrow-right"></i>
							</h1>
							<p>
								<span>
									<img src="../../img/class_iconsss.png" alt="">{{ item.class_name }}</span>
								<span>
									<img src="../../img/wxtable_liulan.png" alt="">{{ item.start_time | yyyy_mm_dd_M_S }}</span>
								<span>
									<img src="../../img/gz_room.png" alt="">{{ item.room_name }}</span>
							</p>
						</router-link>
					</ul>
					<div class="fenye" v-if="wasskData.count>wasskData.curriculum.length">
						<el-pagination @current-change="wasskFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="wasskData.count">
						</el-pagination>
					</div>
				</el-dialog>
				<!--未按时下课-->
				<el-dialog title="未按时下课" v-model="wasxk" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style2">
						<router-link tag="li" :to="{name:'class_keqian',params:{id:item.class_id}}" v-for="item in wasxkData.curriculum">
							<h1>
								<s>{{ item.subject_name }}</s>
								<el-tag type="warning">已延误{{ item.start_time | ywMint }}</el-tag>
								<i class="el-icon-arrow-right"></i>
							</h1>
							<p>
								<span>
									<img src="../../img/class_iconsss.png" alt="">{{ item.class_name }}</span>
								<span>
									<img src="../../img/wxtable_liulan.png" alt="">{{ item.start_time | yyyy_mm_dd_M_S }}</span>
								<span>
									<img src="../../img/gz_room.png" alt="">{{ item.room_name }}</span>
							</p>
						</router-link>
					</ul>
					<div class="fenye" v-if="wasxkData.count > wasxkData.curriculum.length">
						<el-pagination @current-change="wasxkFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="wasxkData.count">
						</el-pagination>
					</div>
				</el-dialog>
				<!--未点评学员-->
				<el-dialog title="未点评学员" v-model="wdpxy" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style2">
						<li v-for="item in wdpxyData.curriculum" @click="dianpingXY(item.class_id,item.id)">
							<h1>
								<s>{{ item.subject_name }}</s>
								<el-tag type="warning">{{ item.wait_evaluation }}人未点评</el-tag>
								<i class="el-icon-arrow-right"></i>
							</h1>
							<p>
								<span>
									<img src="../../img/class_iconsss.png" alt="">{{ item.class_name }}</span>
								<span>
									<img src="../../img/wxtable_liulan.png" alt="">{{ item.start_time | yyyy_mm_dd_M_S }}</span>
								<span>
									<img src="../../img/gz_room.png" alt="">{{ item.room_name }}</span>
							</p>
						</li>
					</ul>
				</el-dialog>
				<!--遗漏考勤-->
				<el-dialog title="遗漏考勤" v-model="ylkaoqin" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style2">
						<li v-for="item in ylkaoqinData.curriculum" @click="openKaoqin(item.class_id,item.curriculum_id)">
							<h1>
								<s>{{ item.subject_name }}</s>
								<el-tag type="warning">{{ item.wait_attence }}人未考勤</el-tag>
								<i class="el-icon-arrow-right"></i>
							</h1>
							<p>
								<span>
									<img src="../../img/class_iconsss.png" alt="">{{ item.class_name }}</span>
								<span>
									<img src="../../img/wxtable_liulan.png" alt="">{{ item.start_time | yyyy_mm_dd_M_S }}</span>
								<span>
									<img src="../../img/gz_room.png" alt="">{{ item.room_name }}</span>
							</p>
						</li>
					</ul>
				</el-dialog>
				<!--已延误跟进-->
				<el-dialog title="已延误跟进" v-model="ywgj" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style1">
						<li v-for="item in ywgjData.intention">
							<h1>
								<span class="span">
									<img src="../../img/yixiang_1.png" alt="">
								</span>
								<s>{{ item.name }}</s>
								<el-tag type="warning">已延误{{ item.next_time | yyyy_mm_dd }}</el-tag>
								<el-button type="primary" size="small" @click="$router.push({name:'student_server',params:{id:item.id}})">跟进</el-button>
							</h1>
							<p>
								<span>
									<img src="../../img/telphone_icon.png" alt="">15319333285</span>
								<span>{{ item.age }}岁</span>
							</p>
						</li>
					</ul>
					<div class="fenye" v-if="ywgjData.count > ywgjData.intention.length">
						<el-pagination @current-change="yanwugenjinFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="ywgjData.count">
						</el-pagination>
					</div>
				</el-dialog>
	
				<!--新意向学员-->
				<el-dialog title="新意向学员" v-model="xyxxuy" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style1">
						<li v-for="item in xyxxuyData.intention">
							<h1>
								<span class="span">
									<img src="../../img/yixiang_1.png" alt="">
								</span>
								<s>{{ item.name }}</s>
								<el-tag type="warning">{{ item.sender }}分配</el-tag>&nbsp;&nbsp;&nbsp;&nbsp;
								<el-tag type="success" v-if="item.status_name">{{ item.status_name }}</el-tag>
								<el-button type="primary" size="small" @click="$router.push({name:'student_server',params:{id:item.id}})">跟进</el-button>
							</h1>
							<p>
								<span>
									<img src="../../img/telphone_icon.png" alt="">{{ item.phone }}
								</span>
								<span>{{ item.age }}岁</span>
							</p>
						</li>
					</ul>
					<div class="fenye" v-if="xyxxuyData.count > xyxxuyData.intention.length">
						<el-pagination @current-change="xyxxuyFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="xyxxuyData.count">
						</el-pagination>
					</div>
				</el-dialog>
	
				<!--学员跟进动态-->
				<el-dialog title="意向学员跟进动态" v-model="xueyuangenjindt" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style3 yxxueyuangenjindt">
						<li v-for="item in xueyuangenjindtData.intention" @click="$router.push({name:'student_server',params:{id:item.id}})">
							<div>
								<div class="style3_right">
									<div>{{ item.content }}</div>
								</div>
							</div>
							<p>
								<span>
									学员姓名：{{ item.name }}</span>
								<span>
									跟进时间：{{ item.time | yyyy_mm_dd }}</span>
								<span>
									跟进人：{{ item.teacher_name }}</span>
							</p>
						</li>
					</ul>
					<div class="fenye" v-if="xueyuangenjindtData.count > xueyuangenjindtData.intention.length">
						<el-pagination @current-change="xueyuangenjindtFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="xueyuangenjindtData.count">
						</el-pagination>
					</div>
				</el-dialog>
	
				<!--学员回评动态-->
				<el-dialog title="学员回评动态" v-model="xueyuanhuiping" class="tousuyijian_box">
					<p class="fengexian_t"></p>
					<ul class="style3">
						<li v-for="item in xueyuanhuipingData.intention">
							<div>
								<div class="style3_left">
									<span>
										<img :src="item.student_face?'http://wx.eduwxt.com'+item.student_face:'http://img.eduwxt.com/assets/images/users/avatar-11.jpg'" alt="">
									</span>
									<s>{{ item.student_name }}</s>
								</div>
								<div class="style3_right">
									<p>
										<el-rate v-model="item.teacher_score" disabled></el-rate>
										<el-button type="success" size="mini" @click="querenchakannew(item.evaluation_id)">确认查看</el-button>
										<time>回评时间：{{ item.reply_time | yyyy_mm_dd }}</time>
									</p>
									<div>{{ item.reply_info }}</div>
								</div>
							</div>
							<p>
								<span>
									<img src="../../img/class_iconsss.png" alt="">{{ item.class_name }}</span>
								<span>
									<img src="../../img/wxtable_liulan.png" alt="">{{ item.curriculum_start_time | yyyy_mm_dd_M_S }}</span>
								<span>
									<img src="../../img/kechengiconshj.png" alt="">{{ item.subject }}</span>
							</p>
						</li>
					</ul>
					<div class="fenye" v-if="xueyuanhuipingData.count > xueyuanhuipingData.intention.length">
						<el-pagination @current-change="xueyuanhuipingFYclick" :current-page="1" :page-size="limits" layout="total, prev, pager, next" :total="xueyuanhuipingData.count">
						</el-pagination>
					</div>
				</el-dialog>
	
				<!--查看学员通知-->
				<el-dialog title="通知详情" v-model="noticeShow" class="notice_box">
					<p class="fengexian_t"></p>
					<h1 class="notice_t">{{ studDetail.title }}</h1>
					<div class="fuzhuwenzi">
						<span>接收范围：{{ studDetail.campus_name }}</span>
						<time>{{ studDetail.time | yyyy_mm_dd_M_S }}</time>
					</div>
					<div class="notice_content" v-html="studDetail.body"></div>
				</el-dialog>
				<!--发布学员通知-->
				<el-dialog title="发布学员通知" v-model="xynoticeShow" class="notice_box">
					<p class="fengexian_t"></p>
					<el-form ref="form" :model="noticeform" label-width="80px">
						<el-row>
							<el-col :span="24">
								<el-form-item label="通知标题">
									<el-input v-model="noticeform.title" placeholder="请输入标题"></el-input>
								</el-form-item>
							</el-col>
						</el-row>
	
						<el-row>
							<el-col :span="24">
								<el-form-item label="通知摘要">
									<el-input v-model="noticeform.info" placeholder="请输入摘要信息"></el-input>
								</el-form-item>
							</el-col>
						</el-row>
	
						<el-row>
							<el-col :span="24">
								<el-form-item label="选择校区">
									<el-select v-model="noticeform.campus_id" placeholder="请选择" filterable v-if="noticeform.campus_id == '1'">
										<el-option label="所有校区" value="1"></el-option>
										<el-option v-for="item in campus" :label="item.name" :value="item.id">
										</el-option>
									</el-select>
									<el-select v-model="noticeform.campus_id" placeholder="请选择" filterable v-if="noticeform.campus_id != '1'" disabled>
										<el-option label="所有校区" value="1"></el-option>
										<el-option v-for="item in campus" :label="item.name" :value="item.id">
										</el-option>
									</el-select>
								</el-form-item>
							</el-col>
						</el-row>
	
						<el-row>
							<el-col :span="24">
								<el-form-item label="正文信息">
									<vue-html5-editor :content="noticeform.body" :height="350" @change="updateData"></vue-html5-editor>
								</el-form-item>
							</el-col>
						</el-row>
						<div class="fabuyuxu_bottom">
							<el-button type="primary" @click="postNoticeFun">发布通知</el-button>
						</div>
					</el-form>
				</el-dialog>
			</div>
		</div>
		<!--考勤管理-->
		<kaoqin :id="kaoqinid" :cid="class_id" :changeid="kaoqinid_change"></kaoqin>
		<!--添加意向学员-->
		<addyxxy :changeshow="changeshow"></addyxxy>
		<!--添加学员-->
		<addnewstud :changetimes="changetimes"></addnewstud>
		<!--课后点评-->
		<dianp :id="dpid" :cid="cid" :changedps="changdp"></dianp>
	
	</div>
</template>
<script>
module.exports = {
	data: function () {
		return {
			limits: 5,
			//学员点评
			dpid: "",
			changdp: 0,
			cid: "",

			st_campus_id: "",
			changeshow: 0,
			changetimes: 0,

			tousujianyi: false,
			toushuShow: true,
			tousutitle: "处理投诉意见",
			//未按时上课
			wassk: false,
			wasskData: {
				curriculum: []
			},
			wasxk: false,
			wasxkData: {
				curriculum: []
			},
			wdpxy: false,
			wdpxyData: {
				curriculum: []
			},
			ylkaoqin: false,
			ylkaoqinData: {
				curriculum: []
			},
			ywgj: false,
			ywgjData: {
				intention: []
			},
			ywgjData: {
				intention: []
			},
			xyxxuy: false,
			xyxxuyData: {
				intention: []
			},
			xueyuangenjindt: false,
			xueyuangenjindtData: {
				intention: []
			},
			xueyuanhuiping: false,
			xueyuanhuipingData: {
				intention: []
			},

			limit: 3,
			//校区信息
			campus_msg: {},
			//选课报名
			xkbmpage: 1,
			xuankeBMList: {
				reservation: []
			},
			//投诉建议
			sugsestpage: 1,
			reply: "",
			suggest_id: "",
			suggestRe: {},
			suggestList: {
				suggest: []
			},
			//学员通知
			studentNoticeList: {
				notice: []
			},
			studlimit: 4,
			studpage: 1,
			//通知详情
			studDetail: {},
			//今日课程
			subpage: 1,
			subjecttoday: {
				today_curriculum: []
			},
			//我的今日课程
			mysubpage: 1,
			mysubjecttoday: {
				today_curriculum: []
			},
			//考勤管理
			kaoqinid: "",
			class_id: "",
			kaoqinid_change: "",

			//请假记录
			leavepage: 1,
			leavelist: {
				leave: []
			},
			//今日跟进jin
			genjpage: 1,
			gjList: {
				intention: []
			},

			//试听课
			stk: true,
			option: {
				limit: 3,
				page: 1,
				campus_id: "",
				start_time: "",
				end_time: ""
			},
			shitingkeList: {
				lecture: []
			},

			noticeShow: false,
			xynoticeShow: false,

			noticeform: {
				title: "",
				info: "",
				campus_id: "",
				body: ""
			},

			gzkb_jrkc: false,//今日课程
			gzkb_xkbm: false,//选课报名
			gzkb_tsyj: false,//投诉意见
			gzkb_xytz: false,
			yx_bjgl_my_xskq: false,
			yx_bjgl_my_kczt: false
		}
	},
	computed: {
		campus: function () {
			return this.$store.state.campus;
		},
		myMessage: function () {
			return this.$store.state.myMessage;
		}
	},
	methods: {
		//发送学员通知
		postNoticeFun: function () {
			var self = this;

			if (self.noticeform.title.trim() == "") {
				self.$message({
					showClose: true,
					message: "请输入标题",
					type: "warning"
				})
				return;
			}

			if (self.noticeform.body.trim() == "") {
				self.$message({
					showClose: true,
					message: "请输入内容",
					type: "warning"
				})
				return;
			}

			var formData = new FormData();
			formData.append("title", self.noticeform.title);
			formData.append("campus_id", self.noticeform.campus_id);
			formData.append("info", self.noticeform.info);
			formData.append("body", self.noticeform.body);

			self.$http.post("/api/campus_notice", formData)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.getXueyuanNoticeFun();
						self.xynoticeShow = false;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function () {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//查看学员详情
		xynoticeFun: function (id) {
			var self = this;
			self.noticeShow = true;
			self.$http.get("/api/campus_notice/" + id)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.studDetail = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//学员通知分页点击
		studnoticepageFUn: function (val) {
			this.studpage = val;
			this.getXueyuanNoticeFun();
		},
		//获取学员通知
		getXueyuanNoticeFun: function () {
			var self = this;
			self.$http.get("/api/Campus_notice?limit=" + self.studlimit + "&page=" + self.studpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.studentNoticeList = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//发布通知编辑器change
		updateData: function (val) {
			this.noticeform.body = val;
		},
		//遗漏考勤
		openKaoqin: function (cid, id) {
			this.class_id = cid;
			this.kaoqinid = id;
			this.kaoqinid_change = +new Date();
		},
		//打开课后点评
		dianpingXY: function (cid, id) {
			this.dpid = id;
			this.cid = cid;
			this.changdp = +new Date();
		},
		//添加正式学员
		addzhengshixyFun: function () {
			this.changetimes = new Date().getTime();
		},
		//打开添加意向学员
		addyixiangxyFun: function () {
			this.changeshow = new Date().getTime();
		},
		//试听课选择
		changecampusstk: function (val) {
			this.option.campus_id = val;
			this.getShitingList();
		},
		//试听课页码点击ji
		shitingkepage: function (val) {
			this.option.page = val;
			this.getShitingList();
		},
		//下周试听课
		getNextWeek: function () {

			this.stk = false;

			var n = new Date();
			var now = new Date(n.getFullYear(), n.getMonth(), n.getDate());
			var nowTime = now.getTime();
			var day = now.getDay();
			var oneDayTime = 24 * 60 * 60 * 1000;

			//显示周一
			var MondayTime = (nowTime + (8 - day) * oneDayTime) / 1000;
			//显示周日
			var SundayTime = (nowTime + (8 - day + 7) * oneDayTime) / 1000;

			this.option.start_time = MondayTime;
			this.option.end_time = SundayTime;

			this.getShitingList();
		},
		//本周试听课
		getThisWeek: function () {

			this.stk = true;

			var n = new Date();
			var now = new Date(n.getFullYear(), n.getMonth(), n.getDate());
			var nowTime = now.getTime();
			var day = now.getDay();
			var oneDayTime = 24 * 60 * 60 * 1000;


			//显示周一
			var MondayTime = (nowTime - (day - 1) * oneDayTime) / 1000;
			//显示周日
			var SundayTime = (nowTime + (8 - day) * oneDayTime) / 1000;

			this.option.start_time = MondayTime;
			this.option.end_time = SundayTime;
			this.getShitingList();
		},
		//获取试听课列表
		getShitingList: function () {
			var self = this;
			self.option.campus_id = self.myMessage.campus_id == "1" ? self.option.campus_id : self.myMessage.campus_id;
			var url = "/api/lecture?limit=" + self.option.limit + "&page=" + self.option.page + "&campus_id=" + self.option.campus_id + "&start_time=" + self.option.start_time + "&end_time=" + self.option.end_time;
			self.$http.get(url)
				.then(function (data) {
					self.loading = false;
					if (data.data.status == 'ok') {
						self.shitingkeList = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//跟进分页
		genjinpageClick: function (val) {
			this.genjpage = val;
			this.genjinList();
		},
		//今日跟进
		genjinList: function () {
			var self = this;
			self.$http.get("/api/workbench/tody_intention?limit=4&page=" + self.genjpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.gjList = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//批准请假
		qingjiafun: function (val) {
			var self = this;
			var student_id = val.split("/")[3];
			var paikeid = val.split("/")[2];
			var class_id = val.split("/")[1];
			var type = val.split("/")[0];
			var formData = new FormData();
			formData.append("student_id", student_id);
			formData.append("type", "2");
			formData.append("xk", type);
			self.$http.post("/api/classs/" + class_id + "/curriculum/" + paikeid + "/attence", formData)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.$store.commit("success", {
							self: self,
							msg: "学生已请假"
						});
						self.getleavelist();
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function () {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//请假分页
		leaveClickpage: function (val) {
			this.leavepage = val;
			this.getleavelist();
		},
		//请假记录
		getleavelist: function () {
			var self = this;
			self.$http.get("/api/workbench/leave?limit=" + self.limit + "&page=" + self.leavepage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.leavelist = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//kaoqin
		kaoqinFun: function (cid, sid) {
			this.class_id = cid;
			this.kaoqinid = sid;
			this.kaoqinid_change = new Date().getTime();
		},
		//上课下课
		shangkeFun: function (cid, sid, staus) {
			var self = this;
			var st = staus == 'start' ? '该课程已上课' : '该课程已下课,请到课后管理查看';
			var title = staus == 'start' ? '是否进行要上课操作?' : '是否进行要下课操作?'
			self.$confirm(title, '提示', {
				confirmButtonText: '确定',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(function () {
				var formData = new FormData();
				formData.append("status", staus);
				self.$http.post("/api/classs/" + cid + "/curriculum/" + sid + "/status", formData)
					.then(function (data) {
						if (data.data.status == 'ok') {
							self.$store.commit("success", {
								self: self,
								msg: st
							});
							self.getMysubjecttoday();
							self.getsubjecttoday();
						} else {
							self.$store.commit("checkError", {
								self: self,
								data: data.data
							});
						}
					}, function (err) {
						self.$message({
							showClose: true,
							message: "网络错误",
							type: "error"
						})
					})
			}).catch(function () {

			});

		},
		//我的今日课程分页点击
		MysubjecttodayClick: function (val) {
			this.mysubpage = val;
			this.getMysubjecttoday();
		},
		//我的今日课程
		getMysubjecttoday: function () {
			var self = this;
			self.$http.get("/api/workbench/my_today_curriculum?limit=" + self.limit + "&page=" + self.mysubpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.mysubjecttoday = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//今日课程分页点击
		subjecttodayClick: function (val) {
			this.subpage = val;
			this.getsubjecttoday();
		},
		//今日课程
		getsubjecttoday: function () {
			var self = this;
			self.$http.get("/api/workbench/today_curriculum?limit=" + self.limit + "&page=" + self.subpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.subjecttoday = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//删除投诉建议
		delSuggest: function (id) {
			var self = this;
			self.$confirm('确定要删除该条投诉意见信息？', '提示', {
				confirmButtonText: '删除',
				cancelButtonText: '取消',
				type: 'warning'
			}).then(function () {
				self.$http.post("/api/workbench/suggest/" + id + "/del")
					.then(function (data) {
						if (data.data.status == 'ok') {
							self.$store.commit("success", {
								self: self,
								msg: "删除成功"
							});
							self.getSuggestList();
						} else {
							self.$store.commit("checkError", {
								self: self,
								data: data.data
							});
						}
					}, function (err) {
						self.$message({
							showClose: true,
							message: "网络错误",
							type: "error"
						})
					})
			}).catch(function () {

			});
		},
		//回复投诉建议
		huifusuggest: function () {
			var self = this;
			if (self.reply.trim() == "") {
				self.$message({
					showClose: true,
					message: "请输入反馈内容",
					type: "warning"
				})
				return;
			}
			var formData = new FormData();
			formData.append("reply", self.reply);
			self.$http.post("/api/workbench/suggest/" + self.suggest_id + "/reply", formData)
				.then(function (data) {
					self.tousujianyi = false;
					if (data.data.status == 'ok') {
						self.$store.commit("success", {
							self: self,
							msg: "投诉意见处理成功"
						});
						self.getSuggestList();
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.tousujianyi = false;
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//设置投诉建议回复老师名称
		getTeachername: function (id) {
			var self = this;
			self.$http.get("/api/staff/" + id)
				.then(function (data) {
					if (data.data.status == 'ok') {
						var names = data.data.data.staff[0].name;
						for (var i = 0; i < self.suggestList.suggest.length; i++) {
							if (self.suggestList.suggest[i].reply_user == id) {
								self.suggestList.suggest[i].reply_name = names;
							}
						}
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//投诉建议
		getSuggestList: function () {
			var self = this;
			self.$http.get("/api/workbench/suggest?limit=" + self.limit + "&page=" + self.sugsestpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						for (var i = 0; i < data.data.data.suggest.length; i++) {
							data.data.data.suggest[i].face = data.data.data.suggest[i].face ? "http://wx.eduwxt.com" + data.data.data.suggest[i].face : "http://img.eduwxt.com/assets/images/users/avatar-11.jpg";
							data.data.data.suggest.reply_name = "";
							if (data.data.data.suggest[i].reply_user != null) {
								self.getTeachername(data.data.data.suggest[i].reply_user);
							}
						}
						self.suggestList = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//投诉建议分页点击
		suggestpageFUn: function (val) {
			this.sugsestpage = val;
			this.getSuggestList();
		},
		//选课报名
		xuankebaomingFun: function () {
			var self = this;
			self.$http.get("/api/workbench/reservation?limit=" + self.limit + "&page=" + self.xkbmpage)
				.then(function (data) {
					if (data.data.status == 'ok') {
						for (var i = 0; i < data.data.data.reservation.length; i++) {
							data.data.data.reservation[i].face = data.data.data.reservation[i].face ? "http://wx.eduwxt.com" + data.data.data.reservation[i].face : "http://img.eduwxt.com/assets/images/users/avatar-11.jpg";
						}
						self.xuankeBMList = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//获取校区限制
		getCampusMessage: function () {
			var self = this;
			self.$http.get("/api/workbench/pack_info")
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.campus_msg = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//未按时上课
		weianshiFun: function (index) {
			var self = this;
			self.$http.get("/api/workbench/late_curriculum?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.wasskData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		wasskFYclick: function (index) {
			this.weianshiFun(index);
		},
		//未按时下课
		weianshixiakeFun: function (index) {
			var self = this;
			self.$http.get("/api/workbench/timeout_curriculum?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.wasxkData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		wasxkFYclick: function (index) {
			this.weianshixiakeFun(index);
		},
		//未点评学员
		weidpxyfun: function () {
			var self = this;
			self.$http.get("/api/workbench/wait_evaluation")
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.wdpxyData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//未考勤
		weikaoqinFun: function () {
			var self = this;
			self.$http.get("/api/workbench/wait_attence")
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.ylkaoqinData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//已延误跟进
		yanwugenjin: function (index) {
			var self = this;
			self.$http.get("/api/workbench/wait_track?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						for (var i = 0; i < data.data.data.intention.length; i++) {
							data.data.data.intention[i].next_time = +new Date() / 1000;
						}
						self.ywgjData = data.data.data;

						for (var i = 0; i < data.data.data.intention.length; i++) {
							self.getStudentMsg(data.data.data.intention[i].id, i);
						}

					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		getStudentMsg: function (id, i) {
			var self = this;
			self.$http.get("/api/intention/" + id)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.ywgjData.intention[i].next_time = data.data.data.next_time;
					} else {
						store.checkError({
							self: self,
							data: data.data
						});
					}
				}, function (err) {

				})
		},
		yanwugenjinFYclick: function (index) {
			this.yanwugenjin(index);
		},
		//新意向学员
		newyixiangxueyuan: function (index) {
			var self = this;
			self.$http.get("/api/workbench/new_intention?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						self.xyxxuyData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		xyxxuyFYclick: function (val) {
			this.newyixiangxueyuan(val);
		},
		//学员跟进动态
		xueyuangenjinFun: function (index) {
			var self = this;
			self.$http.get("/api/workbench/new_intention_log?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						if (data.data.data.intention.length == 0) {
							self.xueyuangenjindt = false;
						}
						self.xueyuangenjindtData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		xueyuangenjindtFYclick: function (val) {
			this.xueyuangenjinFun(val);
		},
		//学员回评动态
		xueyuanHPfun: function (index) {
			var self = this;
			self.$http.get("/api/workbench/new_evaluation?limit=" + self.limits + "&page=" + index)
				.then(function (data) {
					if (data.data.status == 'ok') {
						if (data.data.data.intention.length == 0) {
							self.xueyuanhuiping = false;
						}
						self.xueyuanhuipingData = data.data.data;
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		xueyuanhuipingFYclick: function (index) {
			this.xueyuanHPfun(index);
		},
		querenchakannew: function (id) {
			var self = this;
			self.$http.get("/api/workbench/evaluation_viwe/" + id)
				.then(function (data) {
					if (data.data.status == 'ok') {
						this.xueyuanHPfun(1);
					} else {
						self.$store.commit("checkError", {
							self: self,
							data: data.data
						});
					}
				}, function (err) {
					self.$message({
						showClose: true,
						message: "网络错误",
						type: "error"
					})
				})
		},
		//处理投诉建议
		chuliTSBox: function (id) {
			this.tousujianyi = true;
			this.reply = "";
			this.suggest_id = id;
			this.toushuShow = true;
		},
		//查看投诉详情
		lookTSBox: function id(obj) {
			this.tousujianyi = true;
			this.reply = "";
			this.suggest_id = id;
			this.toushuShow = false;
			this.suggestRe = obj;
		},
		//选课报名分页
		xuankeBMfenye: function (val) {
			this.xkbmpage = val;
			this.xuankebaomingFun();
		},
		//选课报名状态修改信息
		clickup: function (val) {
			var self = this;
			var index = val.split("/")[0];
			var id = val.split("/")[1];
			if (index == "4") {

				self.$confirm('确定要删除该选课报名信息？', '提示', {
					confirmButtonText: '删除',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(function () {
					self.$http.post("/api/workbench/reservation/" + id + "/del")
						.then(function (data) {
							if (data.data.status == 'ok') {
								self.$store.commit("success", {
									self: self,
									msg: "删除成功"
								});
								self.xuankebaomingFun();
							} else {
								self.$store.commit("checkError", {
									self: self,
									data: data.data
								});
							}
						}, function (err) {
							self.$message({
								showClose: true,
								message: "网络错误",
								type: "error"
							})
						})
				}).catch(function () {

				});

			} else {
				var formData = new FormData();
				formData.append("status", index);
				self.$http.post("/api/workbench/reservation/" + id, formData)
					.then(function (data) {
						if (data.data.status == 'ok') {
							self.$store.commit("success", {
								self: self,
								msg: "状态修改成功"
							});
							self.xuankebaomingFun();
						} else {
							self.$store.commit("checkError", {
								self: self,
								data: data.data
							});
						}
					}, function (err) {
						self.$message({
							showClose: true,
							message: "网络错误",
							type: "error"
						})
					})
			}

		}
	},
	created: function () {

		this.$store.commit('setHeadID', 1);
		this.$store.commit('setHeadShow', true);

		var self = this;
		self.$store.commit("getZQWBM", {
			self: self
		})

		this.$store.commit("sethelpid", "");// 帮助
		//获取校区信息
		this.getCampusMessage();
		//获取选课报名
		this.xuankebaomingFun();
		//投诉建议
		this.getSuggestList();
		//今日课程
		this.getsubjecttoday();
		//我的今日课程
		this.getMysubjecttoday();
		//请假记录
		this.getleavelist();
		//今日跟进
		this.genjinList();
		//获取本周试听课
		this.getThisWeek();
		//未按时上课
		this.weianshiFun(1);
		this.weianshixiakeFun(1);
		this.weidpxyfun();
		this.weikaoqinFun();
		this.newyixiangxueyuan(1);
		this.xueyuanHPfun(1);
		this.xueyuangenjinFun(1);
		this.yanwugenjin(1);

		//获取学员通知
		this.getXueyuanNoticeFun();

		this.noticeform.campus_id = this.myMessage.campus_id;

		//权限判断
		this.gzkb_jrkc = this.pdqx(["gzkb_jrkc", "gzkb"]);
		this.gzkb_xkbm = this.pdqx(["gzkb_xkbm", "gzkb"]);
		this.gzkb_tsyj = this.pdqx(["gzkb_tsyj", "gzkb"]);
		this.gzkb_xytz = this.pdqx(["gzkb_xytz", "gzkb"]);

		this.$store.commit("getNewMsgCount", this);//更新消息

		//全校班级权限
		this.yx_bjgl_all = this.pdqx(["yx_bjgl_all", "yx"]);
		//上下课，调课
		this.yx_bjgl_my_kczt = this.pdqx(["yx_bjgl","yx_bjgl_all","yx_bjgl_my", "yx_bjgl_my_kczt", "yx"]);
		//考勤
		this.yx_bjgl_my_xskq = this.pdqx(["yx_bjgl","yx_bjgl_all","yx_bjgl_my", "yx_bjgl_my_xskq", "yx"]);

	}
}

</script>
<style lang="less" scoped>
@import "../../less/color.less";
.bth {
	margin-bottom: 20px;
}

.tishixinxi {
	width: 100%;
	line-height: 320px;
	text-align: center;
	font-size: 14px;
	color: @c3;
}

.notice_box {
	.el-dialog--small {
		width: 600px;
	}
	.notice_t {
		font-size: 16px;
		color: @c1;
		font-weight: normal;
		margin-bottom: 6px;
	}
	.fuzhuwenzi {
		overflow: hidden;
		font-size: 14px;
		color: @c3;
		margin-bottom: 5px;
		span {
			display: block;
			float: left;
			width: 60%;
		}
		time {
			display: block;
			width: 40%;
			float: right;
			text-align: right;
		}
	}
	
	.fabuyuxu_bottom {
		text-align: right;
	}
	.el-select {
		width: 100%;
	}
}

.xueyuantongzhi {
	cursor: pointer;
}

.notice_title {
	height: 22px;
	line-height: 22px;
	font-size: 16px;
	color: @c1;
	.textOverflow();
	margin-bottom: 15px;
	box-sizing: border-box;
	padding-right: 10px;
	background-image: url(../../img/more.png);
	background-size: auto 80%;
	background-repeat: no-repeat;
	background-position: right center;
}

.jindutiao {
	width: 140px;
	overflow: hidden;
	&.p1 {
		>span {
			>span {}
		}
	}
	&.p2 {
		>span {
			>span {
				background-color: #ffbfa7;
			}
		}
	}
	&.p3 {
		>span {
			>span {
				background-color: #bbafff;
			}
		}
	}
	&.p4 {
		>span {
			>span {
				background-color: #9eafff;
			}
		}
	}
	s {
		font-size: 14px;
		color: @c3;
		text-decoration: none;
		padding-top: 7px;
		display: block;
		overflow: hidden;
		line-height: 22px;
		i {
			float: right;
			font-style: normal;
		}
	}
	>span {
		display: block;
		width: 100%;
		height: 5px;
		box-sizing: border-box;
		overflow: hidden;
		background-color: #ddd;
		border-radius: 3px;
		>span {
			display: block;
			height: 5px;
			width: 50%;
			background-color: @color;
			position: relative;
			top: -1px;
		}
	}
}

.worktody1 {
	width: 100%;
	height: 100%;
	padding-top: 60px;
	box-sizing: border-box;
	overflow: hidden;
	>div {
		height: 100%;
		width: 100%;
		overflow: auto;
	}
}

.worktody {
	padding-top: 20px;
	width: 1180px;
	margin: 0 auto;
}

.fenye {
	margin-top: 10px;
}

.box-card {
	margin-bottom: 20px;
	height: 377px;
}

.tc_box {
	overflow: hidden;
	.tc_box_left {
		height: 18px;
		width: 18px;
		float: left;
		margin-right: 10px;
		padding-top: 5px;
		img {
			display: block;
			width: 100%;
			height: 100%;
		}
	}
	.tc_box_right {
		float: left;
		span {
			display: block;
			font-size: 12px;
			color: #c0c0c0;
			margin-bottom: 2px;
		}
		p {
			font-size: 18px;
			color: @c1;
			&.taocan_font {
				font-size: 16px;
			}
		}
	}
}

.help {
	border-top: 1px dashed #ddd;
	margin-top: 15px;
	padding-top: 10px;
	h1 {
		font-size: 14px;
		color: @c2;
		font-weight: normal;
		margin-bottom: 10px;
	}
	h2 {
		font-size: 12px;
		color: #c0c0c0;
		font-weight: normal;
		margin-bottom: 10px;
	}
	p {
		font-size: 12px;
		color: #a6a6a6;
		margin-bottom: 5px;
		span {
			color: @color;
		}
		img {
			height: 15px;
		}
		a {
			display: inline-block;
			height: 20px;
			width: 78px;
			position: relative;
			img {
				display: block;
				height: 20px;
				position: absolute;
				top: 4px;
			}
		}
	}
}

.wz_box {
	width: 100%;
	height: 70px;
	border: 1px solid #E5e5e5;
	border-radius: 4px;
	cursor: pointer;
	&:hover {
		background-color: #FFAD89;
		span {
			color: #fff;
			&.active {
				color: #fff;
			}
		}
		p {
			color: #fff;
		}
	}
	span {
		display: block;
		text-align: center;
		padding-top: 13px;
		font-size: 20px;
		color: #c7c7c7;
		&.active {
			color: #FF822E;
		}
	}
	p {
		text-align: center;
		color: #bababa;
		font-size: 14px;
	}
}

.kuaijierukou {
	width: 100%;
	border-top: 1px dashed #ddd;
	padding-top: 14px;
	clear: both;
	overflow: hidden;
}

.kuaijie_box {
	height: 74px;
	float: left;
	margin-right: 50px;
	cursor: pointer;
	.active2 {
		display: none;
	}
	&:hover {
		.active1 {
			display: none;
		}
		.active2 {
			display: block;
		}
	}
	&:last-child {
		margin-right: 0;
	}
	img {
		display: block;
		width: 37px;
		height: 37px;
		margin: 0 auto;
	}
	span {
		display: block;
		text-align: center;
		color: @c2;
		padding-top: 5px;
	}
}

.title_h1 {
	font-size: @h3;
	color: @c2;
	margin-bottom: 15px;
	font-weight: normal;
}

.title_h2 {
	font-size: @h3;
	color: @c2;
	margin-bottom: 22px;
	font-weight: normal;
}

.box-card_title {
	font-size: 16px;
	color: @c2;
	border-bottom: 1px solid #dedede;
	padding-bottom: 5px;
	font-weight: normal;
	position: relative;
	height: 27px;
	&.xueyuanlist {
		button {
			color: #fff;
		}
	}
	.el-button+.el-button {
		margin-left: 0;
	}
	button {
		color: @c3;
		float: right;
		margin-left: 5px;
		&.active {
			color: @color;
		}
	}
	span {
		margin-right: 10px;
	}
	.el-select {
		width: 120px;
		float: right;
		margin-left: 10px;
	}
}

.list_card {
	height: 430px;
	box-sizing: border-box;
	overflow: hidden;
	margin-bottom: 20px;
}

.cartd {
	height: 320px;
	>li {
		border-bottom: 1px dashed #dedede;
		padding: 10px 0;
		&:last-child {
			border-bottom: none;
			margin-bottom: 0;
		}
		h1 {
			overflow: hidden;
			height: 24px;
			line-height: 24px;
			margin-bottom: 10px;
			>s {
				display: block;
				float: left;
				max-width: 202px;
				font-size: 14px;
				color: @c2;
				font-weight: normal;
				text-decoration: none;
				margin-right: 4px;
				.textOverflow();
			}
			>span {
				float: left;
				font-weight: normal;
			}
			>time {
				display: block;
				float: right;
				font-size: 12px;
				color: @color;
				font-weight: normal;
				&.active {
					color: @error;
				}
			}
		}
		p {
			font-size: 14px;
			color: @c3;
			overflow: hidden;
			margin-bottom: 5px;
			span {
				display: block;
				float: left;
				margin-right: 10px;
				max-width: 107px;
				.textOverflow();
				&.pp {
					max-width: 117px;
					margin-right: 0;
				}
				&.ss {
					max-width: 103px;
				}
			}
			s {
				display: block;
				float: right;
				text-decoration: none;
				max-width: 220px;
				.textOverflow();
				text-align: right;
				&.pp {
					max-width: 107px;
				}
			}
		}
		div {
			height: 28px;
			line-height: 28px;
			span {
				display: block;
				height: 28px;
				line-height: 40px;
				font-size: 14px;
				color: @c3;
				float: left;
				width: 218px;
				.textOverflow();
			}
			s {
				display: block;
				height: 28px;
				float: right;
				width: 120px;
				text-decoration: none;
				text-align: right;
			}
		}
	}
}

.cartd1 {
	height: 320px;
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		&:last-child {
			border: none;
		}
	}
}

.jinrigenjin_title {
	font-size: 16px;
	color: @c2;
	height: 30px;
	line-height: 30px;
	font-weight: normal;
	margin-bottom: 8px;
	img {
		display: block;
		float: left;
		height: 26px;
		position: relative;
		top: 2px;
		margin-right: 5px;
	}
}

.jinrigenjin_tel {
	display: block;
	font-size: 14px;
	color: @c3;
	img {
		display: block;
		float: left;
		position: relative;
		height: 14px;
		top: 1px;
	}
}

.jinrigenjin_sr {
	text-align: right;
	width: 100%;
	display: block;
	img {
		position: relative;
		height: 16px;
		top: 2px;
		margin-right: 2px;
	}
}

.jinrigenjin_btn {
	float: right;
}

.cartd_2 {
	height: 320px;
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		&:last-child {
			border: none;
		}
		p {
			padding: 9px 0;
			font-size: 15px;
			color: @c2;
			.textOverflow();
			clear: both;
		}
		.bottoms {
			overflow: hidden;
			font-size: 14px;
			color: @c3;
			span {
				width: 197px;
				display: block;
				float: left;
				box-sizing: border-box;
				.textOverflow();
				img {
					height: 14px;
					position: relative;
					top: 2px;
					margin-right: 2px;
				}
			}
			s {
				width: 140px;
				display: block;
				float: right;
				text-align: right;
				text-decoration: none;
				box-sizing: border-box;
				img {
					height: 14px;
					position: relative;
					top: 2px;
					margin-right: 2px;
				}
			}
		}
	}
}

#qingjia {
	span {
		width: 50%;
		.textOverflow();
	}
	s {
		width: 50%;
		.textOverflow();
	}
}

.cartd_2_title {
	height: 30px;
	line-height: 30px;
	clear: both;
	.el-dropdown {
		float: right;
	}
	>span {
		display: block;
		width: 30px;
		height: 30px;
		overflow: hidden;
		float: left;
		margin-right: 5px;
		border-radius: 50%;
		img {
			display: block;
			width: 100%;
			min-height: 30px;
		}
	}
	s {
		font-size: 16px;
		color: @c1;
		font-weight: bold;
		text-decoration: none;
		float: left;
		time {
			margin-left: 5px;
			border: 1px solid #eee;
			padding: 2px;
			font-size: 14px;
			color: @c3;
			font-weight: normal;
			border-radius: 3px;
		}
		i {
			font-weight: normal;
			font-size: 12px;
			font-style: normal;
		}
	}
	button {
		float: right;
		margin-left: 10px;
	}
}

.el-dropdown-menu {
	width: 100px;
}

.shiting_list {
	width: 100%;
	height: 320px;
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		&:last-child {
			border: none;
		}
		h1 {
			font-weight: normal;
			font-size: 14px;
			color: @c2;
			height: 24px;
			line-height: 24px;
			margin-bottom: 10px;
			s {
				text-decoration: none;
				max-width: 240px;
				display: block;
				float: left;
				.textOverflow();
			}
			span {
				margin-left: 5px;
			}
		}
		p {
			font-size: 14px;
			color: @c3;
			overflow: hidden;
			margin-bottom: 10px;
			&.last {
				margin-bottom: 0;
			}
			img {
				height: 14px;
				position: relative;
				top: 2px;
				margin-right: 2px;
			}
			span {
				display: block;
				float: left;
				width: 30%;
			}
			time {
				display: block;
				float: right;
				width: 60%;
				text-align: right;
			}
			s {
				text-decoration: none;
				.textOverflow();
			}
		}
	}
}

.chulixiangqing {
	font-size: 14px;
	color: @c2;
	line-height: 20px;
}

.foots_tousu {
	font-size: 14px;
	color: @c3;
	padding-top: 10px;
	span {
		width: 50%;
		float: left;
		&:last-child {
			text-align: right;
		}
	}
}

.style1 {
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		&:last-child {
			border: none;
		}
		>h1 {
			overflow: hidden;
			line-height: 26px;
			font-weight: normal;
			margin-bottom: 10px;
			.span {
				display: block;
				width: 26px;
				height: 26px;
				margin-right: 5px;
				float: left;
				img {
					display: block;
					width: 26px;
					height: 26px;
				}
			}
			s {
				font-size: 15px;
				color: @c1;
				display: block;
				float: left;
				margin-right: 5px;
				text-decoration: none;
			}
			span {
				float: left;
				margin-right: 5px;
			}
			button {
				float: right;
			}
		}
		>p {
			overflow: hidden;
			span {
				display: block;
				width: 50%;
				float: left;
				font-size: 14px;
				color: @c2;
				img {
					height: 15px;
					position: relative;
					top: 2px;
					margin-right: 2px;
				}
				&:last-child {
					text-align: right;
				}
			}
		}
	}
}

.style2 {
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		cursor: pointer;
		&:last-child {
			border: none;
		}
		>h1 {
			overflow: hidden;
			line-height: 26px;
			font-weight: normal;
			margin-bottom: 10px;
			s {
				font-size: 15px;
				color: @c1;
				display: block;
				float: left;
				margin-right: 5px;
				text-decoration: none;
			}
			span {
				float: left;
			}
			i {
				float: right;
				font-weight: normal;
				font-size: 14px;
				color: @c1;
			}
		}
		>p {
			overflow: hidden;
			span {
				display: block;
				max-width: 32%;
				float: right;
				font-size: 14px;
				color: @c2;
				text-align: right;
				padding-left: 10px;
				.textOverflow();
				img {
					height: 15px;
					position: relative;
					top: 2px;
					margin-right: 2px;
				}
				&:first-child {
					float: left;
					text-align: left;
					padding-left: 0;
				}
			}
		}
	}
}

.style3 {
	li {
		padding: 10px 0;
		border-bottom: 1px dashed #dedede;
		cursor: pointer;
		&:last-child {
			border: none;
		}
		>div {
			font-size: 15px;
			color: @c1;
			overflow: hidden;
			clear: both;
			display: flex;
			>.style3_left {
				width: 60px;
				overflow: hidden;
				span {
					display: block;
					width: 38px;
					height: 38px;
					overflow: hidden;
					border-radius: 50%;
					img {
						display: block;
						width: 100%;
						min-height: 38px;
					}
				}
				s {
					display: block;
					font-size: 14px;
					color: @c1;
					text-decoration: none;
					height: 30px;
					width: 100%;
					line-height: 30px;
				}
			}
			>.style3_right {
				flex: 1;
				p {
					overflow: hidden;
					margin-bottom: 5px;
					.el-rate {
						float: left;
					}
					button {
						float: right;
					}
					time {
						float: right;
						font-size: 12px;
						color: @c3;
						margin-right: 5px;
					}
				}
				div {
					font-size: 14px;
					color: @c1;
					margin-bottom: 10px;
				}
			}
		}
		>p {
			overflow: hidden;
			clear: both;
			span {
				display: block;
				max-width: 32%;
				float: right;
				font-size: 14px;
				color: @c2;
				text-align: right;
				padding-left: 10px;
				.textOverflow();
				img {
					height: 15px;
					position: relative;
					top: 2px;
					margin-right: 2px;
				}
				&:first-child {
					float: left;
					text-align: left;
					padding-left: 0;
				}
			}
		}
	}
}

.yxxueyuangenjindt {
	>li {
		p {
			span {
				color: @c3;
			}
		}
	}
}
</style>