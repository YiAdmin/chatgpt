<template>
	<view class="container u-flex-column">
		<view class="register-section" style="margin-top: 30%;" v-if="appInfo.config.cms && appInfo.config.cms.user.open_register && form.type">
			<u--form :model="form" label-width="70">
				<u-form-item label="邮箱" prop="form.email" v-if="form.type == 'email'">
					<u--input v-model="form.email" border="bottom" placeholder="请输入邮箱"></u--input>
				</u-form-item>
				<u-form-item label="手机号码" prop="form.mobile" v-if="form.type == 'sms'">
					<u--input v-model="form.mobile" border="bottom" placeholder="请输入手机号码"></u--input>
				</u-form-item>
				<u-form-item label="密码" prop="form.password">
					<u--input v-model="form.password" type="password" border="bottom" placeholder="请输入密码"></u--input>
				</u-form-item>
				<u-form-item label="验证码" prop="form.code">
					<u-input placeholder="验证码" border="bottom">
						<template slot="suffix">
							<u-code ref="uCode" @change="codeChange" seconds="30"></u-code>
							<u-button @tap="getCode" type="success" size="mini">{{tips}}</u-button>
						</template>
					</u-input>
				</u-form-item>
			</u--form>
			<view class="u-block">
				<u-button class="mt-30" @click="submit" type="primary">注册</u-button>
			</view>
			<view v-if="appInfo.config.cms && appInfo.config.cms.user.register_type.length > 1" class="u-flex text-gray mt-20 u-flex-end" @click="handleChangeRegisterType">
				<text>{{form.type == 'email' ? '手机注册' : '邮箱注册'}}</text>
			</view>
		</view>
		<view class="text-xl text-gray" style="margin-top: 50%;" v-if="appInfo.config.cms && !appInfo.config.cms.user.open_register">
			本站未开放注册!
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import loggedOutMixin from '@/mixins/loggedOut'
	export default {
		mixins: [loggedOutMixin],
		data() {
			return {
				form: {
					type: '', // email,sms
					email: '',
					mobile: '',
					password: '',
					code: ''
				},
				tips: '获取验证码',
				LOGGED_OUT: true
			}
		},
		computed: {
			...mapState({
				appInfo: state => state.appInfo
			})
		},
		watch: {
			'appInfo': {
				deep: true, handler: function(v) {
					this.getDefaultRegisterType()
				}
			}
		},
		async onLoad(options) {
			options = await this.onLoadStart(options);
			this.getDefaultRegisterType()
		},
		methods: {
			getDefaultRegisterType() {
				if (this.appInfo.config.cms && this.appInfo.config.cms.user.register_type.length) this.form.type = this.appInfo.config.cms.user.register_type[0] 
			},
			submit() {
				const { type, email, mobile, password, code} = this.form;
			},
			handleChangeRegisterType() {
				
				this.form.type = this.form.type == 'email' ? 'sms' : 'email';
			},
			hasType(type) {
				if (!this.appInfo.config.cms) return false;
				return this.appInfo.config.cms.user.register_type.indexOf(type) == -1 ? false : true;
			},
			codeChange(text) {
				this.tips = text;
			},
			getCode() {
				if (this.$refs.uCode.canGetCode) {
				  uni.showLoading({
					title: '正在获取验证码'
				  })
				  const { type, email, mobile } = this.form;
				  this.$http.post('cms/api/get_verify_code', {form: {
					  type, email, mobile
				  }, action: 'user_register'}).then(data => {
					  uni.hideLoading();
					  uni.$u.toast('验证码已发送');
					  this.$refs.uCode.start();
				  })
				} else {
				  uni.$u.toast('倒计时结束后再发送');
				}
			},
		}
	}
</script>

<style scoped lang="scss">
	page {
		background-color: #fff;
	}

	.container {
		height: 100%;
		background-color: #fff;
		align-items: center;

		.register-section {
			width: 80%;
		}
		
	}
	/* #ifdef MP-BAIDU */
	.container {
		height: 100vh;
	}
	/* #endif */
</style>
