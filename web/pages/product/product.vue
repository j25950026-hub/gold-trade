<template>
	<view class="product-page">
		<view class="product-list">
			<view class="goods-item" v-for="item in goodsList" :key="item.id" @tap="toDeal(item.id)">
				<image class="goods-icon" :src="item.image" mode="aspectFit"></image>
				<view class="goods-info">
					<text class="goods-name">{{item.title}}</text>
					<text class="goods-code">{{item.code}}</text>
				</view>
				<view class="goods-price-box">
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
	export default {
		data() {
			return { goodsList: [] }
		},
		onShow() { this.load() },
		methods: {
			async load() {
				const data = await this.$store.dispatch('getGoods')
				if (data) this.goodsList = data
			},
			toDeal(id) {
				if (!this.$store.state.token) {
					uni.navigateTo({ url: '/pages/login/login' })
					return
				}
				uni.navigateTo({ url: `/pages/deal/deal?goods_id=${id}` })
			}
		}
	}
</script>

<style>
	.product-page { background: #0f0f1a; min-height: 100vh; padding: 20rpx; }
	.goods-item {
		display: flex; align-items: center;
		background: #1e1e35; border-radius: 16rpx;
		padding: 24rpx; margin-bottom: 16rpx;
	}
	.goods-icon { width: 60rpx; height: 60rpx; margin-right: 20rpx; }
	.goods-info { flex: 1; }
	.goods-name { font-size: 28rpx; color: #fff; display: block; }
	.goods-code { font-size: 22rpx; color: #666; }
	.goods-price-box { margin-right: 20rpx; text-align: right; }
	.price { font-size: 28rpx; color: #fff; }
	.goods-change { padding: 8rpx 20rpx; border-radius: 8rpx; font-size: 24rpx; color: #fff; min-width: 100rpx; text-align: center; }
	.bg-green { background: #2ecc71; }
	.bg-red { background: #e74c3c; }
</style>
