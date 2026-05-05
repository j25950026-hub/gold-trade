<template>
	<view class="withdraw-page">
		<view class="card">
			<text class="card-title">提现金额</text>
			<view class="input-box">
				<text class="currency">¥</text>
				<input class="input" v-model="amount" type="digit" placeholder="最低100" />
			</view>
			<text class="balance-hint">可用余额: ¥{{userInfo ? userInfo.money : '0.00'}}</text>
		</view>
		
		<view class="card">
			<text class="card-title">提现方式</text>
			<view class="type-list">
				<view class="type-item" :class="{active: type === 'usdt'}" @tap="type='usdt'">
					<text>USDT(TRC-20)</text>
				</view>
			</view>
		</view>
		
		<view class="card">
			<text class="card-title">提现地址</text>
			<input class="input-box" v-model="address" placeholder="请输入USDT地址" />
		</view>
		
		<view class="submit-btn" @tap="submitWithdraw">
			提交提现申请
		</view>
		
		<view class="withdraw-records" @tap="toPage('/pages/withdraw/record')">
			<text>提现记录 ></text>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex'
	
	export default {
		data() {
			return {
				amount: '',
				type: 'usdt',
				address: ''
			}
		},
		computed: mapState(['userInfo']),
		methods: {
			async submitWithdraw() {
				const num = parseFloat(this.amount)
				if (!num || num < 100) {
					uni.showToast({ title: '最低提现100元', icon: 'none' })
					return
				}
				if (!this.address) {
					uni.showToast({ title: '请输入提现地址', icon: 'none' })
					return
				}
				
				uni.showLoading({ title: '提交中...' })
				
				const res = await uni.request({
					url: '/api/finance/withdraw_submit',
					method: 'POST',
					data: { amount: num, type: this.type, address: this.address },
					header: { token: uni.getStorageSync('token') }
				})
				
				uni.hideLoading()
				
				if (res.data && res.data.status === 1) {
					uni.showToast({ title: '申请已提交', icon: 'success' })
					await this.$store.dispatch('getUserInfo')
					this.amount = ''
					this.address = ''
				} else {
					uni.showToast({ title: res.data?.massage || '提交失败', icon: 'none' })
				}
			},
			toPage(url) { uni.navigateTo({ url }) }
		}
	}
</script>

<style>
	.withdraw-page { background: #0f0f1a; min-height: 100vh; padding: 30rpx; }
	
	.card { background: #1e1e35; border-radius: 20rpx; padding: 30rpx; margin-bottom: 30rpx; }
	.card-title { font-size: 28rpx; color: #fff; font-weight: bold; margin-bottom: 24rpx; display: block; }
	
	.input-box { display: flex; align-items: center; background: #252545; border-radius: 16rpx; padding: 24rpx; }
	.currency { font-size: 32rpx; color: #d4a849; margin-right: 16rpx; }
	.input { flex: 1; color: #fff; font-size: 36rpx; height: 50rpx; }
	.balance-hint { font-size: 22rpx; color: #666; margin-top: 16rpx; display: block; }
	
	.type-list { display: flex; gap: 20rpx; }
	.type-item { padding: 20rpx 40rpx; background: #252545; border-radius: 16rpx; font-size: 26rpx; color: #888; border: 2rpx solid transparent; }
	.type-item.active { border-color: #d4a849; color: #d4a849; }
	
	.submit-btn { background: linear-gradient(135deg, #d4a849, #b8922e); color: #fff; height: 90rpx; line-height: 90rpx; text-align: center; border-radius: 50rpx; font-size: 32rpx; font-weight: bold; margin-bottom: 30rpx; }
	
	.withdraw-records { text-align: center; color: #888; font-size: 24rpx; }
</style>
