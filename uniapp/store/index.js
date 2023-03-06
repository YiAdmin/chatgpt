import Vue from 'vue'
import Vuex from 'vuex'
const http = uni.$u.http;
const gql = uni.$u.gql;
Vue.use(Vuex)

import user from './modules/user';

const modules = {
	user
};
const store = new Vuex.Store({
	modules,
	state: {
		appInfo: {
			config: {}
		},
		areas: null
	},
	mutations: {
		SAVE_APP_INFO(state, data) {
			state.appInfo = Object.assign(state.appInfo, data);
			uni.$emit('AppInfoChange', state.appInfo)
		},
		SAVE_PAGE_SETTING(state, data) {
			state.appInfo.page_setting = data;
		},
	},
	actions: {
		getAppInfo({ commit }) {
			return new Promise((resolve, reject) => {
				http.post('cms/api/index/appInfo').then(data => {
					uni.setStorageSync('appInfo', data);
					commit('SAVE_APP_INFO', data);
					resolve(data);
				})
			})
		}
	}
})

export default store
