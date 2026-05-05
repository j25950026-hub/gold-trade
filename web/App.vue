<template>
	<view class="app">
		<page-meta>
			<navigation-bar front-color="#ffffff" background-color="#1a1a2e" title="中国黄金" />
		</page-meta>
		<view class="status-bar"></view>
		<view class="content">
			<router-view />
		</view>
	</view>
</template>

<script>
	export default {
		onLaunch: function() {
			console.log('App Launch');
			// 检查登录状态
			const token = uni.getStorageSync('token');
			if (token) {
				this.$store.commit('setToken', token);
			}
		},
		onShow: function() {
			console.log('App Show');
		},
		onHide: function() {
			console.log('App Hide');
		}
	};
</script>

<style>
	/* 全局样式 */
	page {
		background-color: #0f0f1a;
		color: #ffffff;
		font-size: 28rpx;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
	}
	
	.status-bar {
		height: var(--status-bar-height);
		background-color: #1a1a2e;
	}
	
	/* 通用按钮 */
	.btn-primary {
		background: linear-gradient(135deg, #d4a849, #b8922e);
		color: #fff;
		border: none;
		border-radius: 50rpx;
		padding: 20rpx 60rpx;
		font-size: 32rpx;
		font-weight: bold;
		text-align: center;
		box-shadow: 0 8rpx 24rpx rgba(212, 168, 73, 0.3);
	}
	
	.btn-primary:active {
		opacity: 0.85;
	}
	
	/* 卡片 */
	.card {
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 30rpx;
		margin: 20rpx 30rpx;
		box-shadow: 0 4rpx 20rpx rgba(0,0,0,0.3);
	}
	
	/* 输入框 */
	.input-box {
		background: #252545;
		border-radius: 16rpx;
		padding: 24rpx 30rpx;
		color: #fff;
		font-size: 28rpx;
		border: 2rpx solid #333366;
	}
	
	.input-box:focus {
		border-color: #d4a849;
	}
	
	/* flex工具 */
	.flex-center {
		display: flex;
		justify-content: center;
		align-items: center;
	}
	
	.flex-between {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	
	/* 文字色 */
	.color-gold {
		color: #d4a849;
	}
	
	.color-red {
		color: #e74c3c;
	}
	
	.color-green {
		color: #2ecc71;
	}
	
	.color-gray {
		color: #888;
	}
</style>
