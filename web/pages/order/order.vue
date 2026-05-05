<template>
	<view class="order-page">
		<!-- Tab切换 -->
		<view class="tab-bar">
			<view class="tab-item" v-for="(tab, i) in tabs" :key="i"
				:class="{active: currentTab === i}" @tap="switchTab(i)">
				<text>{{tab}}</text>
			</view>
		</view>
		
		<!-- 订单列表 -->
		<view class="order-list" v-if="orders.length > 0">
			<view class="order-item" v-for="item in orders" :key="item.id">
				<view class="order-header">
					<text class="order-title">{{item.title}}</text>
					<text class="order-status" :class="statusClass(item.status)">{{statusText(item.status)}}</text>
				</view>
				<view class="order-body">
					<view class="order-info">
						<text class="info-label">类型</text>
						<text class="info-value" :class="item.type == 1 ? 'green' : 'red'">{{item.type == 1 ? '买涨' : '买跌'}}</text>
					</view>
					<view class="order-info">
						<text class="info-label">金额</text>
						<text class="info-value">¥{{item.number}}</text>
					</view>
					<view class="order-info">
						<text class="info-label">盈亏</text>
						<text class="info-value" :class="item.ploss >= 0 ? 'green' : 'red'">{{item.ploss >= 0 ? '+' : ''}}{{item.ploss}}</text>
					</view>
					<view class="order-info">
						<text class="info-label">时间</text>
						<text class="info-value time">{{item.buy_time}}</text>
					</view>
				</view>
				<view class="order-progress" v-if="item.status === 0">
					<view class="progress-bar">
						<view class="progress-fill" :style="{width: progressPercent(item) + '%'}"></view>
					</view>
				</view>
			</view>
		</view>
		
		<view class="empty" v-else>
			<text>暂无交易记录</text>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				tabs: ['全部', '未完成', '已完成'],
				currentTab: 0,
				orders: [],
				timer: null
			}
		},
		onShow() {
			this.loadOrders()
			this.timer = setInterval(() => this.loadOrders(), 3000)
		},
		onHide() { clearInterval(this.timer) },
		methods: {
			async loadOrders() {
				const statusMap = [3, 0, 1]
				const data = await this.$store.dispatch('getOrders', statusMap[this.currentTab])
				if (data && data.lists) {
					this.orders = data.lists
				}
			},
			switchTab(i) {
				this.currentTab = i
				this.loadOrders()
			},
			statusText(s) {
				const map = {0: '进行中', 1: '已盈利', 2: '已亏损', 3: '平局'}
				return map[s] || '未知'
			},
			statusClass(s) {
				const map = {0: 'doing', 1: 'win', 2: 'lose', 3: 'draw'}
				return map[s] || ''
			},
			progressPercent(item) {
				if (item.remain_milli_seconds === 0 && item.status === 0) return 99
				if (item.endTime <= 0) return 0
				const total = item.seconds * 1000
				const elapsed = total - item.remain_milli_seconds
				return Math.min(99, (elapsed / total) * 100)
			}
		}
	}
</script>

<style>
	.order-page { background: #0f0f1a; min-height: 100vh; padding: 20rpx; }
	
	.tab-bar {
		display: flex;
		background: #1e1e35;
		border-radius: 16rpx;
		padding: 6rpx;
		margin-bottom: 20rpx;
	}
	
	.tab-item {
		flex: 1;
		text-align: center;
		padding: 16rpx;
		border-radius: 12rpx;
		font-size: 28rpx;
		color: #888;
	}
	
	.tab-item.active {
		background: #d4a849;
		color: #fff;
		font-weight: bold;
	}
	
	.order-item {
		background: #1e1e35;
		border-radius: 16rpx;
		padding: 24rpx;
		margin-bottom: 16rpx;
	}
	
	.order-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 16rpx;
	}
	
	.order-title { font-size: 28rpx; color: #fff; font-weight: bold; }
	
	.order-status { font-size: 24rpx; padding: 4rpx 16rpx; border-radius: 8rpx; }
	.order-status.doing { background: rgba(52, 152, 219, 0.2); color: #3498db; }
	.order-status.win { background: rgba(46, 204, 113, 0.2); color: #2ecc71; }
	.order-status.lose { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
	.order-status.draw { background: rgba(149, 165, 166, 0.2); color: #95a5a6; }
	
	.order-body {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 12rpx;
	}
	
	.order-info { }
	.info-label { font-size: 22rpx; color: #666; display: block; }
	.info-value { font-size: 26rpx; color: #fff; }
	.info-value.time { font-size: 22rpx; color: #666; }
	
	.green { color: #2ecc71; }
	.red { color: #e74c3c; }
	
	.order-progress {
		margin-top: 16rpx;
	}
	
	.progress-bar {
		height: 6rpx;
		background: #333;
		border-radius: 3rpx;
		overflow: hidden;
	}
	
	.progress-fill {
		height: 100%;
		background: linear-gradient(90deg, #d4a849, #f1c40f);
		border-radius: 3rpx;
		transition: width 1s;
	}
	
	.empty {
		text-align: center;
		padding: 200rpx 0;
		color: #666;
		font-size: 28rpx;
	}
</style>
