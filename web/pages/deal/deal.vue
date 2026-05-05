<template>
	<view class="deal-page">
		<!-- 产品信息 -->
		<view class="product-header">
			<image class="product-icon" :src="good.image" mode="aspectFit"></image>
			<view class="product-info">
				<text class="product-name">{{good.title}}</text>
				<text class="product-code">{{good.code}}</text>
			</view>
			<view class="product-price">
				<text class="price-num">{{currentPrice}}</text>
				<text class="price-change" :class="good.is_z === 1 ? 'green' : 'red'">{{good.zf}}</text>
			</view>
		</view>
		
		<!-- K线容器 -->
		<view class="kline-container">
			<canvas canvas-id="klineCanvas" class="kline-canvas"></canvas>
		</view>
		
		<!-- 时间选择 -->
		<view class="time-selector">
			<view class="time-item" v-for="(t, i) in timeOptions" :key="i"
				:class="{active: selectedTime === t.value}" @tap="selectTime(t.value)">
				<text>{{t.label}}</text>
				<text class="time-rate">{{t.rate}}%</text>
			</view>
		</view>
		
		<!-- 金额选择 -->
		<view class="amount-section">
			<text class="section-label">投入金额</text>
			<view class="amount-input-box">
				<text class="currency">¥</text>
				<input class="amount-input" v-model="amount" type="digit" placeholder="100" />
			</view>
			<view class="amount-presets">
				<view class="preset-item" v-for="(a, i) in amountOptions" :key="i"
					:class="{active: amount == a}" @tap="amount = String(a)">
					<text>{{a}}</text>
				</view>
			</view>
		</view>
		
		<!-- 交易按钮 -->
		<view class="deal-buttons">
			<view class="deal-btn rise" @tap="submitDeal(1)">
				<text class="btn-label">买涨</text>
				<text class="btn-profit">收益+{{profitRatio}}%</text>
			</view>
			<view class="deal-btn fall" @tap="submitDeal(2)">
				<text class="btn-label">买跌</text>
				<text class="btn-profit">收益+{{profitRatio}}%</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				goodsId: 0,
				good: {},
				currentPrice: '0.00',
				selectedTime: 180,
				amount: '100',
				profitRatio: 3,
				timeOptions: [
					{label: '60秒', value: 60, rate: 1},
					{label: '180秒', value: 180, rate: 3},
					{label: '300秒', value: 300, rate: 5}
				],
				amountOptions: [100, 200, 500, 1000, 2000]
			}
		},
		onLoad(options) {
			this.goodsId = options.goods_id || 349
			this.loadGood()
		},
		methods: {
			async loadGood() {
				const goods = await this.$store.dispatch('getGoods')
				if (goods) {
					this.good = goods.find(g => g.id == this.goodsId) || goods[0] || {}
					this.currentPrice = this.good.price || '0.00'
				}
				
				// 模拟价格更新
				setInterval(() => {
					const change = (Math.random() - 0.5) * 10
					this.currentPrice = (parseFloat(this.currentPrice) + change).toFixed(2)
				}, 3000)
				
				// 绘制K线
				this.drawKline()
			},
			
			async drawKline() {
				const ctx = uni.createCanvasContext('klineCanvas')
				const width = 690
				const height = 400
				const data = Array.from({length: 60}, (_, i) => 4500 + Math.sin(i * 0.3) * 200 + (Math.random() - 0.5) * 50)
				
				ctx.setFillStyle('#0f0f1a')
				ctx.fillRect(0, 0, width, height)
				
				const min = Math.min(...data)
				const max = Math.max(...data)
				const range = max - min || 1
				
				// 画K线蜡烛
				const candleWidth = (width - 40) / data.length
				data.forEach((val, i) => {
					const x = 20 + i * candleWidth
					const y = height - 40 - ((val - min) / range) * (height - 80)
					const isUp = i > 0 && val >= data[i - 1]
					
					ctx.setStrokeStyle(isUp ? '#2ecc71' : '#e74c3c')
					ctx.setFillStyle(isUp ? '#2ecc71' : '#e74c3c')
					
					// 阴影线
					ctx.setLineWidth(2)
					ctx.beginPath()
					ctx.moveTo(x + candleWidth/2, y - 5)
					ctx.lineTo(x + candleWidth/2, y + 5)
					ctx.stroke()
					
					// 实体
					ctx.fillRect(x, y - (isUp ? 6 : 0), candleWidth, 12)
				})
				
				// 价格标签
				ctx.setFontSize(20)
				ctx.setFillStyle('#888')
				ctx.fillText(max.toFixed(2), 5, 30)
				ctx.fillText(min.toFixed(2), 5, height - 15)
				
				ctx.draw()
			},
			
			selectTime(val) {
				this.selectedTime = val
				const map = {60: 1, 180: 3, 300: 5}
				this.profitRatio = map[val] || 3
			},
			
			async submitDeal(type) {
				if (!this.$store.state.token) {
					uni.navigateTo({ url: '/pages/login/login' })
					return
				}
				
				const num = parseInt(this.amount)
				if (!num || num < 100) {
					uni.showToast({ title: '最低100元', icon: 'none' })
					return
				}
				
				uni.showLoading({ title: '提交中...' })
				
				const res = await this.$store.dispatch('buy', {
					goods_id: this.goodsId,
					type: type,
					number: num,
					seconds: this.selectedTime
				})
				
				uni.hideLoading()
				
				if (res.status === 1) {
					uni.showToast({ title: type === 1 ? '已买涨' : '已买跌', icon: 'success' })
					// 更新余额
					await this.$store.dispatch('getUserInfo')
					// 跳转到订单页
					setTimeout(() => {
						uni.switchTab({ url: '/pages/order/order' })
					}, 1000)
				} else {
					uni.showToast({ title: res.massage || '提交失败', icon: 'none' })
				}
			}
		}
	}
</script>

<style>
	.deal-page {
		min-height: 100vh;
		background: #0f0f1a;
		padding: 30rpx;
	}
	
	.product-header {
		display: flex;
		align-items: center;
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-bottom: 20rpx;
	}
	
	.product-icon {
		width: 80rpx;
		height: 80rpx;
		margin-right: 20rpx;
	}
	
	.product-info { flex: 1; }
	.product-name { font-size: 32rpx; color: #fff; font-weight: bold; display: block; }
	.product-code { font-size: 22rpx; color: #666; }
	
	.product-price { text-align: right; }
	.price-num { font-size: 40rpx; color: #d4a849; font-weight: bold; display: block; }
	.price-change { font-size: 24rpx; }
	
	.green { color: #2ecc71; }
	.red { color: #e74c3c; }
	
	/* K线 */
	.kline-container {
		background: #0f0f1a;
		border-radius: 20rpx;
		margin-bottom: 20rpx;
		border: 2rpx solid #1e1e35;
	}
	
	.kline-canvas {
		width: 690rpx;
		height: 400rpx;
	}
	
	/* 时间选择 */
	.time-selector {
		display: flex;
		gap: 20rpx;
		margin-bottom: 30rpx;
	}
	
	.time-item {
		flex: 1;
		background: #1e1e35;
		border-radius: 16rpx;
		padding: 20rpx;
		text-align: center;
		border: 2rpx solid transparent;
	}
	
	.time-item.active {
		border-color: #d4a849;
		background: rgba(212, 168, 73, 0.1);
	}
	
	.time-item text { display: block; }
	.time-item text:first-child { font-size: 28rpx; color: #fff; }
	.time-rate { font-size: 22rpx; color: #d4a849; margin-top: 6rpx; }
	
	/* 金额 */
	.amount-section {
		background: #1e1e35;
		border-radius: 20rpx;
		padding: 30rpx;
		margin-bottom: 30rpx;
	}
	
	.section-label {
		font-size: 24rpx;
		color: #888;
		margin-bottom: 20rpx;
		display: block;
	}
	
	.amount-input-box {
		display: flex;
		align-items: center;
		background: #252545;
		border-radius: 16rpx;
		padding: 20rpx;
		margin-bottom: 20rpx;
	}
	
	.currency {
		font-size: 32rpx;
		color: #d4a849;
		margin-right: 20rpx;
	}
	
	.amount-input {
		flex: 1;
		color: #fff;
		font-size: 36rpx;
		height: 50rpx;
	}
	
	.amount-presets {
		display: flex;
		gap: 16rpx;
	}
	
	.preset-item {
		padding: 10rpx 24rpx;
		background: #252545;
		border-radius: 30rpx;
		font-size: 24rpx;
		color: #888;
		border: 2rpx solid transparent;
	}
	
	.preset-item.active {
		border-color: #d4a849;
		color: #d4a849;
	}
	
	/* 交易按钮 */
	.deal-buttons {
		display: flex;
		gap: 30rpx;
	}
	
	.deal-btn {
		flex: 1;
		height: 120rpx;
		border-radius: 20rpx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}
	
	.deal-btn.rise {
		background: linear-gradient(135deg, #2ecc71, #27ae60);
	}
	
	.deal-btn.fall {
		background: linear-gradient(135deg, #e74c3c, #c0392b);
	}
	
	.btn-label {
		font-size: 36rpx;
		color: #fff;
		font-weight: bold;
	}
	
	.btn-profit {
		font-size: 22rpx;
		color: rgba(255,255,255,0.8);
		margin-top: 6rpx;
	}
</style>
