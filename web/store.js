// Vuex Store
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: {
		token: '',
		userInfo: null,
		appInfo: {}
	},
	
	mutations: {
		setToken(state, token) {
			state.token = token
			uni.setStorageSync('token', token)
		},
		setUserInfo(state, info) {
			state.userInfo = info
			uni.setStorageSync('userInfo', info)
		},
		setAppInfo(state, info) {
			state.appInfo = info
		},
		clearLogin(state) {
			state.token = ''
			state.userInfo = null
			uni.removeStorageSync('token')
			uni.removeStorageSync('userInfo')
		}
	},
	
	actions: {
		// 登录
		async login({ commit }, { account, passwd }) {
			const res = await uni.request({
				url: '/api/login/login',
				method: 'POST',
				data: { account, passwd }
			})
			if (res.data.status === 1) {
				commit('setToken', res.data.data.token)
				return { success: true }
			}
			return { success: false, msg: res.data.massage }
		},
		
		// 获取用户信息
		async getUserInfo({ commit, state }) {
			const res = await uni.request({
				url: '/api/user/getUserInfo',
				header: { token: state.token }
			})
			if (res.data.status === 1) {
				commit('setUserInfo', res.data.data)
				return res.data.data
			}
			return null
		},
		
		// 获取首页数据
		async getIndex({ state }) {
			const res = await uni.request({
				url: '/api/index/index',
				header: { token: state.token }
			})
			return res.data.data
		},
		
		// 获取行情
		async getGoods() {
			const res = await uni.request({
				url: '/api/index/goods'
			})
			return res.data.data
		},
		
		// 下单
		async buy({ state }, { goods_id, type, number, seconds }) {
			const res = await uni.request({
				url: '/api/order/buy',
				method: 'POST',
				data: { goods_id, type, number, seconds },
				header: { token: state.token }
			})
			return res.data
		},
		
		// 获取订单
		async getOrders({ state }, status) {
			const res = await uni.request({
				url: '/api/order/orderlist',
				data: { status: status || 3 },
				header: { token: state.token }
			})
			return res.data.data
		}
	}
})

export default store
