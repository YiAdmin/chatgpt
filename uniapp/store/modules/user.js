import Tools from '@/common/tools'
const http = uni.$u.http;
const state = {
	userinfo: {}
}
const mutations = {
	SAVE_USERINFO(state, data) {
		state.userinfo = data || {}
	}
}
const actions = {
	login({ commit, dispatch }, form) {
		return new Promise((resolve, reject) => {
			http.post('cms/api/login', form).then(data => {
				if (data.code == 1 || data.code == 3) {
					uni.setStorageSync('token', data.user.token)
					dispatch('getUserinfo')
				}
				resolve(data)
			}).catch(e => {
				reject(e)
			})
		})
	},
	logout({ commit }) {
		return new Promise((resolve, reject) => {
			http.post('cms/api/logout').then(res => {
				uni.removeStorageSync('token');
				commit('SAVE_USERINFO', {})
				resolve()
			}).catch(e => { reject(e) })
		})
	},
	isLogin({ dispatch }) {
		return new Promise( async (resolve, reject) => {
			if (!Tools.isEmpty(state.userinfo)) {
				resolve(true);
			} else {
				dispatch('getUserinfo').then(() => {
					resolve(!Tools.isEmpty(state.userinfo));
				}).catch(e => {
					resolve(false)
				});
			}
		})
	},
	getUserinfo({ commit }) {
		return new Promise((resolve, reject) => {
			http.get('cms/api/get_userinfo').then(data => {
				commit('SAVE_USERINFO', data);
				resolve(data)
			}).catch(e => {
				reject(e)
			})
		})
	}
}
export default {
	state,
	mutations,
	actions
}