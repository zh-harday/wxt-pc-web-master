<template>
	<div style="overflow:auto;height:100%">
		<yxleftmeau></yxleftmeau>
		<div class="rightbox">
			<router-view></router-view>
		</div>
	</div>
</template>
<script>
module.exports = {

	created: function () {

		this.$store.commit('setHeadID', 2);
		this.$store.commit('setHeadShow', true);
		this.$store.commit('setYXLeftMeauId', 0);

		this.$store.commit("sethelpid", "");// 帮助

		this.$store.commit("getNewMsgCount", this);//更新消息
		var self = this;
		self.$store.commit("getZQWBM", {
			self: self
		})
	}
}

</script>