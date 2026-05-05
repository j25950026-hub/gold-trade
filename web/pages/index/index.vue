<template>
	<view class="index-page">
		<!-- 顶栏 -->
		<view class="top-bar">
			<view class="user-info" @tap="toUser">
				<image class="avatar" src="/static/avatar.png" mode="aspectFill"></image>
				<view class="user-detail">
					<text class="welcome">欢迎您</text>
					<text class="username">{{userInfo ? userInfo.username : '未登录'}}</text>
				</view>
			</view>
			<view class="top-right">
				<text class="kefu-btn" @tap="toKefu">客服</text>
			</view>
		</view>
		
		<!-- 资产卡片 -->
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
		
		<!-- Banner轮播 -->
		<swiper class="banner" indicator-dots autoplay interval="3000">
			<swiper-item v-for="(item, i) in banners" :key="i">
				<image class="banner-img" :src="item.image" mode="aspectFill"></image>
			</swiper-item>
		</swiper>
		
		<!-- 行情列表 -->
		<view class="section-header">
			<text class="section-title">行情列表</text>
			<text class="section-more" @tap="toProduct">查看全部 ></text>
		</view>
		
		<view class="goods-list">
			<view class="goods-item" v-for="item in goodsList" :key="item.id" @tap="toDeal(item.id)">
				<image class="goods-icon" :src="item.image" mode="aspectFit"></image>
				<view class="goods-info">
					<text class="goods-name">{{item.title}}</text>
					<text class="goods-code">{{item.code}}</text>
				</view>
				<view class="goods-price">
					<text class="price">{{item.price}}</text>
				</view>
				<view class="goods-change" :class="item.is_z === 1 ? 'bg-green' : 'bg-red'">
					<text>{{item.zf}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex'
	
	export default {
		data() {
			return {
				banners: [],
				goodsList: []
			}
		},
		computed: mapState(['userInfo']),
		onShow() {
			this.loadData()
		},
		methods: {
			async loadData() {
				// 加载首页
				const indexData = await this.$store.dispatch('getIndex')
				if (indexData) {
					this.banners = indexData.bannerList || []
				}
				
				// 加载行情
				const goods = await this.$store.dispatch('getGoods')
				if (goods) this.goodsList = goods
				
				// 更新用户信息
				if (this.$store.state.token) {
					await this.$store.dispatch('getUserInfo')
				}
			},
			toUser() {
				if (!this.$store.state.token) {
					uni.navigateTo({ url: '/pages/login/login' })
				} else {
					uni.switchTab({ url: '/pages/user/user' })
				}
			},
			toDeal(id) {
				if (!this.$store.state.token) {
					uni.navigateTo({ url: '/pages/login/login' })
					return
				}
				uni.navigateTo({ url: `/pages/deal/deal?goods_id=${id}` })
			},
			toProduct() { uni.switchTab({ url: '/pages/product/product' }) },
			toRecharge() { uni.navigateTo({ url: '/pages/recharge/recharge' }) },
			toWithdraw() { uni.navigateTo({ url: '/pages/withdraw/withdraw' }) },
			toKefu() {
				// 跳转客服
				uni.showToast({ title: '联系在线客服', icon: 'none' })
			}
		}
	}
</script>

<style>
	.index-page {
		min-height: 100vh;
		background: #0f0f1a;
		padding-bottom: 120rpx;
	}
	
	/* 顶栏 */
	.top-bar {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 30rpx;
		background: linear-gradient(180deg, #1a1a2e, transparent);
	}
	
	.user-info {
		display: flex;
		align-items: center;
	}
	
	.avatar {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		border: 2rpx solid #d4a849;
		margin-right: 20rpx;
	}
	
	.welcome {
		font-size: 22rpx;
		color: #888;
		display: block;
	}
	
	.username {
		font-size: 32rpx;
		color: #fff;
		font-weight: bold;
	}
	
	.kefu-btn {
		padding: 10rpx 30rpx;
		background: rgba(212, 168, 73, 0.2);
		color: #d4a849;
		border-radius: 30rpx;
		font-size: 26rpx;
	}
	
	/* 资产卡片 */
	.asset-card {
		margin: 0 30rpx 30rpx;
		background: linear-gradient(135deg, #1e1e35, #2a2a45);
		border-radius: 24rpx;
		padding: 40rpx;
	}
	
	.asset-label {
		font-size: 24rpx;
		color: #888;
	}
	
	.asset-amount {
		font-size: 56rpx;
		color: #d4a849;
		font-weight: bold;
		display: block;
		margin: 10rpx 0;
	}
	
	.asset-usdt {
		font-size: 24rpx;
		color: #666;
	}
	
	.asset-stats {
		display: flex;
		gap: 40rpx;
		margin: 30rpx 0;
	}
	
	.stat-item { flex: 1; }
	.stat-label { font-size: 22rpx; color: #888; display: block; }
	.stat-value { font-size: 32rpx; font-weight: bold; }
	
	.green { color: #2ecc71; }
	.red { color: #e74c3c; }
	
	.asset-actions {
		display: flex;
		gap: 30rpx;
	}
	
	.action-btn {
		flex: 1;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		border-radius: 40rpx;
		font-size: 30rpx;
		font-weight: bold;
	}
	
	.recharge { background: linear-gradient(135deg, #d4a849, #b8922e); color: #fff; }
	.withdraw { background: rgba(255,255,255,0.1); color: #fff; border: 2rpx solid #333; }
	
	/* Banner */
	.banner {
		height: 280rpx;
		margin: 0 30rpx 30rpx;
		border-radius: 20rpx;
		overflow: hidden;
	}
	
	.banner-img {
		width: 100%;
		height: 280rpx;
	}
	
	/* 行情列表 */
	.section-header {
		display: flex;
		justify-content: space-between;
		padding: 20rpx 30rpx;
	}
	
	.section-title {
		font-size: 32rpx;
		color: #fff;
		font-weight: bold;
	}
	
	.section-more {
		color: #d4a849;
		font-size: 24rpx;
	}
	
	.goods-list {
		margin: 0 30rpx;
	}
	
	.goods-item {
		display: flex;
		align-items: center;
		background: #1e1e35;
		border-radius: 16rpx;
		padding: 24rpx;
		margin-bottom: 16rpx;
	}
	
	.goods-icon {
		width: 60rpx;
		height: 60rpx;
		margin-right: 20rpx;
	}
	
	.goods-info { flex: 1; }
	.goods-name { font-size: 28rpx; color: #fff; display: block; }
	.goods-code { font-size: 22rpx; color: #666; }
	
	.goods-price {
		margin-right: 20rpx;
		text-align: right;
	}
	
	.price { font-size: 28rpx; color: #fff; }
	
	.goods-change {
		padding: 8rpx 20rpx;
		border-radius: 8rpx;
		font-size: 24rpx;
		color: #fff;
		min-width: 100rpx;
		text-align: center;
	}
	
	.bg-green { background: #2ecc71; }
	.bg-red { background: #e74c3c; }
</style>
