<template>
	<view class="container u-flex-column">
		<view class="section" style="margin-top: 30%;">
			<view class="u-flex-row u-flex-center">
			</view>
			<u--form :model="form" label-width="70">
				<u-form-item label="类型">
					<u-radio-group v-model="form.type" placement="row">
						<u-radio :customStyle="{marginBottom: '8px', marginRight: '8px'}" v-for="(item, index) in verify.types" :key="index"
							:label="item.title" :name="item.value">
						</u-radio>
					</u-radio-group>
				</u-form-item>
				<u-form-item label="帐号" prop="form.username">
					<u--input v-model="form.username" border="bottom" placeholder="请输入帐号"></u--input>
				</u-form-item>
				<u-form-item label="邮箱" prop="form.email" v-if="form.type=='email'">
					<u--input v-model="form.email" border="bottom" placeholder="请输入邮箱"></u--input>
				</u-form-item>
				<u-form-item label="手机号" prop="form.mobile" v-if="form.type=='sms'">
					<u--input v-model="form.mobile" border="bottom" placeholder="请输入手机号码"></u--input>
				</u-form-item>
				<u-form-item label="验证码" prop="form.code">
					<u-input placeholder="验证码" v-model="form.code" border="bottom">
						<template slot="suffix">
							<u-code ref="uCode" seconds="30"></u-code>
							<u-button @tap="getCode" type="success" size="mini">{{tips}}</u-button>
						</template>
					</u-input>
				</u-form-item>
				<u-form-item label="新密码" prop="form.password">
					<u--input v-model="form.password" type="password" border="bottom" placeholder="请输入新密码"></u--input>
				</u-form-item>
			</u--form>
			<view class="u-block">
				<u-button class="mt-30 w-100" @click="submit" type="primary">提交</u-button>
			</view>
		</view>

	</view>
</template>

<script>
	import {
		mapState,
		mapActions
	} from 'vuex';
	import Account from '@/common/Account';
	import loggedOutMixin from '@/mixins/loggedOut'
	export default {
		mixins: [loggedOutMixin],
		data() {
			return {
				form: {
					type: 'sms',
					username: '',
					email: '',
					mobile: '',
					password: '',
					code: ''
				},
				verify: {
					types: [
						{title: '短信', value: 'sms'}, {title: '邮件', value: 'email'}
					],
				},
				tips: '获取验证码',
				LOGGED_OUT: true
			}
		},
		async onLoad(options) {
			options = await this.onLoadStart(options);
		},
		computed: {
			...mapState(['appInfo'])
		},
		methods: {
			submit() {
				const form = this.form;
				this.$http.post('cms/api/find', {form, action: 'user_find_account'}).then(() => {
					this.$u.toast("密码已重置");
					setTimeout(() => {
						this.$u.route({
							url: 'pages/public/login'
						})
					}, 1000)
				})
			},
			getCode() {
				if (this.$refs.uCode.canGetCode) {
					uni.showLoading({
						title: '正在获取验证码'
					})
					const {
						type,
						email,
						mobile,
						username,
					} = this.form;
					this.$http.post('user/get_find_verify_code', {
						form: {
							type,
							email,
							mobile,
							username
						},
						action: 'user_find_account'
					}).then(data => {
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
		background: #fff;
		height: 100%;
	}

	.container {
		height: 100%;
		background-color: #fff;
		align-items: center;

		.section {
			width: 80%;
		}
	}
	/* #ifdef MP-BAIDU */
	.container {
		height: 100vh;
	}
	/* #endif */
</style>
