<template>
	<view class="user-page">
		<view class="user-header">
			<image class="avatar" src="/static/avatar.png" mode="aspectFill"></image>
			<view class="user-meta">
				<text class="user-name">{{userInfo ? userInfo.real_name || userInfo.username : '未登录'}}</text>
				<text class="user-credit">信用分:{{userInfo ? userInfo.credit_score : 0}}</text>
			</view>
			<text class="logout-btn" @tap="logout" v-if="token">退出登陆</text>
		</view>
		
		<view class="asset-card">
			<text class="asset-label">总资产(CNY)</text>
			<text class="asset-amount">¥ {{userInfo ? userInfo.money : '0.00'}}</text>
			<text class="asset-usdt">≈ {{userInfo ? (userInfo.money / 7.24).toFixed(2) : '0.00'}} USDT</text>
			<view class="asset-stats">
				<view class="stat-item">
					<text class="stat-label">账户盈亏</text>
					<text class="stat-value" :class="(userInfo ? userInfo.yk : 0) >= 0 ? 'green' : 'red'">{{userInfo ? userInfo.yk : 0}}</text>
				</view>
				<view class="stat-item">
					<text class="stat-label">今日盈亏</text>
					<text class="stat-value" :class="(userInfo ? userInfo.yk_today : 0) >= 0 ? 'green' : 'red'">{{userInfo ? userInfo.yk_today : 0}}</text>
				</view>
			</view>
			<view class="asset-actions">
				<view class="action-btn recharge" @tap="toRecharge">入市</view>
				<view class="action-btn withdraw" @tap="toWithdraw">提现</view>
			</view>
		</view>
		
		<view class="menu-list">
			<view class="menu-item" @tap="toPage('/pages/user/auth')">
				<text>实名认证</text>
				<text class="arrow">></text>
			</view>
			<view class="menu-item" @tap="toPage('/pages/user/moneylog')">
				<text>资金明细</text>
				<text class="arrow">></text>
			</view>
			<view class="menu-item" @tap="toPage('/pages/withdraw/record')">
				<text>提现明细</text>
				<text class="arrow">></text>
			</view>
			<view class="menu-item" @tap="toPage('/pages/user/changepwd')">
				<text>修改密码</text>
				<text class="arrow">></text>
			</view>
		</view>
		
		<view class="invite-section" v-if="userInfo">
			<text class="invite-label">我的邀请码</text>
			<text class="invite-code">{{userInfo.code}}</text>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex'
	
	export default {
		computed: mapState(['userInfo', 'token']),
		onShow() {
			if (this.token) this.$store.dispatch('getUserInfo')
		},
		methods: {
			toPage(url) {
				if (!this.token) { uni.navigateTo({ url: '/pages/login/login' }); return }
				uni.navigateTo({ url })
			},
			toRecharge() { uni.navigateTo({ url: '/pages/recharge/recharge' }) },
			toWithdraw() { uni.navigateTo({ url: '/pages/withdraw/withdraw' }) },
			logout() {
				uni.showModal({
					title: '提示',
					content: '确定退出登录？',
					success: (res) => {
						if (res.confirm) {
							this.$store.commit('clearLogin')
							uni.switchTab({ url: '/pages/index/index' })
						}
					}
				})
			}
		}
	}
</script>

<style>
	.user-page { background: #0f0f1a; min-height: 100vh; padding: 20rpx 30rpx; }
	
	.user-header {
		display: flex;
		align-items: center;
		margin-bottom: 30rpx;
	}
	
	.avatar {
		width: 100rpx; height: 100rpx; border-radius: 50%;
		border: 2rpx solid #d4a849; margin-right: 20rpx;
	}
	
	.user-meta { flex: 1; }
	.user-name { font-size: 36rpx; color: #fff; font-weight: bold; display: block; }
	.user-credit { font-size: 22rpx; color: #888; }
	
	.logout-btn {
		padding: 10rpx 24rpx;
		background: rgba(231, 76, 60, 0.2);
		color: #e74c3c;
		border-radius: 30rpx;
		font-size: 24rpx;
	}
	
	.asset-card {
		background: linear-gradient(135deg, #1e1e35, #2a2a45);
		border-radius: 24rpx;
		padding: 40rpx;
		margin-bottom: 30rpx;
	}
	
	.asset-label { font-size: 24rpx; color: #888; }
	.asset-amount { font-size: 56rpx; color: #d4a849; font-weight: bold; display: block; margin: 10rpx 0; }
	.asset-usdt { font-size: 24rpx; color: #666; }
	.asset-stats { display: flex; gap: 40rpx; margin: 30rpx 0; }
	.stat-item { flex: 1; }
	.stat-label { font-size: 22rpx; color: #888; display: block; }
	.stat-value { font-size: 32rpx; font-weight: bold; }
	.green { color: #2ecc71; }
	.red { color: #e74c3c; }
	.asset-actions { display: flex; gap: 30rpx; }
	.action-btn { flex: 1; height: 80rpx; line-height: 80rpx; text-align: center; border-radius: 40rpx; font-size: 30rpx; font-weight: bold; }
	.recharge { background: linear-gradient(135deg, #d4a849, #b8922e); color: #fff; }
	.withdraw { background: rgba(255,255,255,0.1); color: #fff; border: 2rpx solid #333; }
	
	.menu-list {
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 0 30rpx;
		margin-bottom: 30rpx;
	}
	
	.menu-item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 30rpx 0;
		border-bottom: 2rpx solid rgba(255,255,255,0.05);
		font-size: 28rpx;
		color: #fff;
	}
	.menu-item:last-child { border-bottom: none; }
	
	.arrow { color: #555; font-size: 28rpx; }
	
	.invite-section {
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 30rpx;
		text-align: center;
	}
	
	.invite-label { font-size: 24rpx; color: #888; display: block; margin-bottom: 10rpx; }
	.invite-code { font-size: 40rpx; color: #d4a849; font-weight: bold; letter-spacing: 4rpx; }
</style>
