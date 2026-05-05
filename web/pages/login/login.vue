<template>
	<view class="login-page">
		<view class="login-header">
			<image class="logo" src="/static/logo.png" mode="aspectFit"></image>
			<view class="lang-switch" @tap="switchLang">
				<image src="/static/lang.png" mode="aspectFit"></image>
				<text>简体中文</text>
			</view>
		</view>
		
		<view class="login-form">
			<text class="login-title">账号登录</text>
			
			<view class="input-group">
				<view class="input-item">
					<text class="label">账号</text>
					<input class="input-box" v-model="account" placeholder="请输入账号" placeholder-class="placeholder" />
					<text class="clear-btn" @tap="account=''" v-if="account">✕</text>
				</view>
				<view class="input-item">
					<text class="label">密码</text>
					<input class="input-box" v-model="passwd" :password="!showPwd" placeholder="* * * * * *" placeholder-class="placeholder" />
					<text class="eye-btn" @tap="showPwd=!showPwd">{{showPwd ? '◎' : '◎'}}</text>
					<text class="clear-btn" @tap="passwd=''" v-if="passwd">✕</text>
				</view>
			</view>
			
			<view class="tui-submit" @tap="handleLogin">登录</view>
		</view>
		
		<view class="login-footer">
			<view class="footer-links">
				<text class="link" @tap="toRegister">立即开户</text>
				<text class="link" @tap="toKefu">在线客服</text>
			</view>
			<view class="online-info">
				<text>当前在线:</text>
				<text class="online-num">{{onlineNum}}</text>
			</view>
			<text class="agreement">登录即表示同意APP隐私政策</text>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex'
	
	export default {
		data() {
			return {
				account: '',
				passwd: '',
				showPwd: false,
				onlineNum: 25015832
			}
		},
		onLoad() {
			// 模拟在线人数变化
			setInterval(() => {
				this.onlineNum = 25015000 + Math.floor(Math.random() * 2000)
			}, 5000)
		},
		methods: {
			async handleLogin() {
				if (!this.account || !this.passwd) {
					uni.showToast({ title: '请输入账号和密码', icon: 'none' })
					return
				}
				
				uni.showLoading({ title: '登录中...' })
				
				const result = await this.$store.dispatch('login', {
					account: this.account,
					passwd: this.passwd
				})
				
				uni.hideLoading()
				
				if (result.success) {
					// 获取用户信息
					await this.$store.dispatch('getUserInfo')
					uni.switchTab({ url: '/pages/index/index' })
				} else {
					uni.showToast({ title: result.msg || '登录失败', icon: 'none' })
				}
			},
			toRegister() {
				uni.showToast({ title: '注册暂未开放', icon: 'none' })
			},
			toKefu() {
				uni.showToast({ title: '联系客服', icon: 'none' })
			},
			switchLang() {}
		}
	}
</script>

<style>
	.login-page {
		min-height: 100vh;
		background: linear-gradient(180deg, #1a1a2e 0%, #0f0f1a 100%);
		display: flex;
		flex-direction: column;
		padding: 80rpx 40rpx 40rpx;
		box-sizing: border-box;
	}
	
	.login-header {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		margin-bottom: 80rpx;
	}
	
	.logo {
		width: 120rpx;
		height: 120rpx;
	}
	
	.lang-switch {
		display: flex;
		align-items: center;
		padding: 10rpx 20rpx;
		background: rgba(255,255,255,0.05);
		border-radius: 30rpx;
	}
	.lang-switch image { width: 30rpx; height: 30rpx; margin-right: 10rpx; }
	.lang-switch text { color: #888; font-size: 24rpx; }
	
	.login-form {
		flex: 1;
	}
	
	.login-title {
		font-size: 48rpx;
		font-weight: bold;
		color: #fff;
		margin-bottom: 60rpx;
		display: block;
	}
	
	.input-group {
		background: rgba(255,255,255,0.03);
		border-radius: 24rpx;
		padding: 20rpx;
	}
	
	.input-item {
		display: flex;
		align-items: center;
		padding: 30rpx 20rpx;
		border-bottom: 2rpx solid rgba(255,255,255,0.05);
		position: relative;
	}
	.input-item:last-child { border-bottom: none; }
	
	.label {
		color: #888;
		font-size: 28rpx;
		width: 80rpx;
		flex-shrink: 0;
	}
	
	.input-box {
		flex: 1;
		color: #fff;
		font-size: 32rpx;
		height: 50rpx;
	}
	
	.placeholder { color: #555; }
	
	.clear-btn, .eye-btn {
		padding: 10rpx;
		color: #555;
		font-size: 28rpx;
	}
	
	.tui-submit {
		margin-top: 80rpx;
		background: linear-gradient(135deg, #d4a849, #b8922e);
		color: #fff;
		height: 100rpx;
		line-height: 100rpx;
		text-align: center;
		border-radius: 50rpx;
		font-size: 36rpx;
		font-weight: bold;
		box-shadow: 0 8rpx 30rpx rgba(212, 168, 73, 0.3);
	}
	
	.login-footer {
		margin-top: 60rpx;
		text-align: center;
	}
	
	.footer-links {
		display: flex;
		justify-content: center;
		gap: 60rpx;
		margin-bottom: 30rpx;
	}
	
	.link {
		color: #d4a849;
		font-size: 28rpx;
	}
	
	.online-info {
		color: #888;
		font-size: 24rpx;
		margin-bottom: 20rpx;
	}
	
	.online-num {
		color: #d4a849;
		margin-left: 10rpx;
	}
	
	.agreement {
		color: #555;
		font-size: 22rpx;
	}
</style>
