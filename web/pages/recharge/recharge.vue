<template>
	<view class="recharge-page">
		<view class="card">
			<text class="card-title">选择充值金额</text>
			<view class="amount-grid">
				<view class="amount-item" v-for="(item, i) in amountList" :key="i"
					:class="{active: selectedAmount === item.amount}" @tap="selectedAmount = item.amount">
					<text class="amount-num">¥{{item.amount}}</text>
					<text class="amount-gift" v-if="item.gift > 0">赠送{{item.gift}}</text>
				</view>
			</view>
		</view>
		
		<view class="card">
			<text class="card-title">支付方式</text>
			<view class="pay-options">
				<view class="pay-item" :class="{active: payType === 'usdt'}" @tap="payType='usdt'">
					<text>USDT(TRC-20)</text>
				</view>
			</view>
		</view>
		
		<view class="submit-btn" @tap="submitRecharge">
			确认充值 ¥{{selectedAmount || 0}}
		</view>
		
		<view class="recharge-records" @tap="toRechargeLog">
			<text>充值记录 ></text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				amountList: [
					{amount: 100, gift: 0}, {amount: 200, gift: 0},
					{amount: 500, gift: 0}, {amount: 1000, gift: 0},
					{amount: 2000, gift: 0}, {amount: 5000, gift: 0},
					{amount: 10000, gift: 0}, {amount: 50000, gift: 0}
				],
				selectedAmount: 100,
				payType: 'usdt'
			}
		},
		methods: {
			async submitRecharge() {
				if (!this.selectedAmount) {
					uni.showToast({ title: '请选择金额', icon: 'none' })
					return
				}
				
				uni.showLoading({ title: '提交中...' })
				
				const res = await uni.request({
					url: '/api/finance/recharge_submit',
					method: 'POST',
					data: { amount: this.selectedAmount, type: this.payType },
					header: { token: uni.getStorageSync('token') }
				})
				
				uni.hideLoading()
				
				if (res.data && res.data.data) {
					// 显示收款信息
					uni.navigateTo({
						url: `/pages/recharge/usdt?amount=${this.selectedAmount}&order_sn=${res.data.data.order_sn}`
					})
				} else {
					uni.showToast({ title: res.data?.massage || '提交失败', icon: 'none' })
				}
			},
			toRechargeLog() {
				uni.showToast({ title: '查看充值记录', icon: 'none' })
			}
		}
	}
</script>

<style>
	.recharge-page { background: #0f0f1a; min-height: 100vh; padding: 30rpx; }
	
	.card {
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-bottom: 30rpx;
	}
	
	.card-title {
		font-size: 28rpx;
		color: #fff;
		font-weight: bold;
		margin-bottom: 24rpx;
		display: block;
	}
	
	.amount-grid {
		display: grid;
		grid-template-columns: 1fr 1fr 1fr 1fr;
		gap: 16rpx;
	}
	
	.amount-item {
		background: #252545;
		border-radius: 16rpx;
		padding: 20rpx 10rpx;
		text-align: center;
		border: 2rpx solid transparent;
	}
	
	.amount-item.active {
		border-color: #d4a849;
		background: rgba(212, 168, 73, 0.1);
	}
	
	.amount-num { font-size: 28rpx; color: #fff; display: block; }
	.amount-gift { font-size: 20rpx; color: #d4a849; }
	
	.pay-options { display: flex; gap: 20rpx; }
	
	.pay-item {
		padding: 20rpx 40rpx;
		background: #252545;
		border-radius: 16rpx;
		font-size: 26rpx;
		color: #888;
		border: 2rpx solid transparent;
	}
	
	.pay-item.active {
		border-color: #d4a849;
		color: #d4a849;
	}
	
	.submit-btn {
		background: linear-gradient(135deg, #d4a849, #b8922e);
		color: #fff;
		height: 90rpx;
		line-height: 90rpx;
		text-align: center;
		border-radius: 50rpx;
		font-size: 32rpx;
		font-weight: bold;
		margin-bottom: 30rpx;
	}
	
	.recharge-records {
		text-align: center;
		color: #888;
		font-size: 24rpx;
	}
</style>
