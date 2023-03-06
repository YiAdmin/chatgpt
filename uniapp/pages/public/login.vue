<template>
	<view class="container u-flex-column">
		<view class="register-section" style="margin-top: 30%;">
			<u--form :model="form" label-width="70">
				<u-form-item label="帐号" prop="form.username">
					<u--input v-model="form.username" border="bottom" placeholder="请输入帐号/手机号码/邮箱"></u--input>
				</u-form-item>
				<u-form-item label="密码" prop="form.password">
					<u--input v-model="form.password" type="password" border="bottom" placeholder="请输入密码"></u--input>
				</u-form-item>
				<u-form-item label="验证码" prop="form.code" v-if="showCode">
					<u-input placeholder="验证码" border="bottom"></u-input>
				</u-form-item>
			</u--form>
			<view class="u-block">
				<u-button class="mt-30 w-100" @click="toLogin" type="primary">登录</u-button>
			</view>
			<view class="u-flex text-gray mt-20 u-flex-between">
				<text @click="$u.route({url: 'pages/public/find_account'})">忘记密码</text>
				<text @click="toRegister">注册帐号</text>
			</view>
		</view>
		
		
		<view class="third-party w-100">
			<view class="txt-otherlogin">其他登录方式</view>
			<view class="third-type">
				<view class="loginType">
						<!-- #ifdef H5 -->
						<button v-if="$wxApi.isweixin()" class="wechat item btn" @click="vendorLogin('WechatH5')">
							<u-icon size="40" name="weixin-fill" color="rgb(83,194,64)"></u-icon>
							<view class="txt">微信</view>
						</button>
						<!-- #endif -->
						
						<!-- #ifdef MP-WEIXIN -->
						<button v-if="canIUseGetUserProfile" class="wechat item btn" @click="getUserProfile">
							<u-icon size="40" name="weixin-fill" color="rgb(83,194,64)"></u-icon>
							<view class="txt">微信</view>
						</button>
						<button v-else class="wechat item btn" open-type="getUserInfo" @getuserinfo="onGotUserInfo">
							<u-icon size="40" name="weixin-fill" color="rgb(83,194,64)"></u-icon>
							<view class="txt">微信</view>
						</button>
						<!-- #endif -->
						
						<!-- #ifdef APP-PLUS -->
						<button class="wechat item btn" @click="vendorLogin('WechatApp')">
							<u-icon size="40" name="weixin-fill" color="rgb(83,194,64)"></u-icon>
							<view class="txt">微信</view>
						</button>
						<!-- #endif -->
					</view>
			</view>
		
			<view class="hint" v-if="appInfo && appInfo.config && appInfo.config.user_agreement && appInfo.config.privacy_agreement">
				登录即代表您同意{{appInfo && appInfo.config && appInfo.config.app_name}}
				<text class="link" @click="go(1)" v-if="appInfo.config.user_agreement">《用户协议》</text>
				<text class="link" @click="go(2)" v-if="appInfo.config.privacy_agreement">《隐私政策》</text>，并授权 {{appInfo && appInfo.config && appInfo.config.app_name}} 使用您的账号信息（如昵称、头像等）以便您统一管理
			</view>
		</view>
	</view>
</template>

<script>
	import { mapState, mapActions } from 'vuex';
	import Account from '@/common/Account';
	import loggedOutMixin from '@/mixins/loggedOut'
	export default {
		mixins: [loggedOutMixin],
		data() {
			return {
				form: {
					username: '',
					password: '',
					code: ''
				},
				logining: false,
				referer: null,
				canIUseGetUserProfile: false,
				showCode: false,
				// #ifdef H5
				loginCode: '',
				// #endif
				LOGGED_OUT: true
			}
		},
		
		async onLoad(options) {
			options = await this.onLoadStart(options);
			this.referer = options.referer ? options.referer : null
			// #ifdef H5
			if (options.code) {
				this.getToken(options.code)
			}
			// #endif
			
			// #ifdef MP-WEIXIN
			if (wx.getUserProfile) {
				this.canIUseGetUserProfile = true
			}
			// #endif
		},
		computed: {
			...mapState(['appInfo'])
		},
		methods: {
			...mapActions(['login', 'getUserinfo']),
			inputChange(e) {
				const key = e.currentTarget.dataset.key;
				this[key] = e.detail.value;
			},
			getToken(code) {
				this.$u.post('third/api/vendor/code2Token', {code}).then(data => {					
					uni.setStorageSync('token', data)
					this.getUserinfo()
					this.goReferer()
				})
			},
			navBack() {
				if (getCurrentPages().length == 1) uni.switchTab({
					url: '/pages/index/index'
				});
				else uni.navigateBack();
			},
			toRegister() {
				uni.navigateTo({
					url: "/pages/public/register"
				})
			},
			go(type) {
				switch (type) {
					case 1:
						this.$jump({
							type: 'url',
							target: this.appInfo.config.user_agreement
						})
						break;
					case 2:
						this.$jump({
							type: 'url',
							target: this.appInfo.config.privacy_agreement
						})
						break;
				}
			},
			toHome() {
				uni.reLaunch({
					url: '/pages/index/index'
				})
			},
			async toLogin() {
				this.logining = true;
				const form = this.form
				if (this.code) form.code = this.code;
				this.login(form).then(data => {
					if (data.code == 2) {
						this.showCode = true;
						this.$u.toast(data.message)
					} else this.goReferer()
					this.logining = false;
				}).catch(e => {
					this.logining = false
				})
			},
			async vendorLogin(code, data) {
				let account = Account.init(code)
				switch (code) {
					case 'WechatH5' : {
						account.login(this.referer);
						break;
					}
					case 'WechatMiniApp' : {
						let res = await account.login(data)
						uni.setStorageSync('token', res.token)
						this.getUserinfo()
						this.goReferer()
						break;
					}
					case 'WechatApp' : {
						account.login(data, res => {
							uni.setStorageSync('token', res.token)
							this.getUserinfo()
							this.goReferer()
						}, e => {})
						break;
					}
				}
			},
			goReferer() {
				if (this.referer) {
					uni.reLaunch({
						url: decodeURIComponent(this.referer),
						fail: this.toHome(),
					})
				} else {
					this.toHome()
				}
			},
			getUserProfile: function(e) {
				wx.getUserProfile({
					desc: '用于完善会员资料',
					success: res => {
						let data = {							
							encryptedData:res.encryptedData,
							iv: res.iv,
							rawData: res.rawData
						}
						this.vendorLogin('WechatMiniApp', data)
					}
				})
			},
			onGotUserInfo: function(e) {
				let detail = e.detail
				let data = {
					encryptedData:detail.encryptedData,
					iv: detail.iv,
					rawData: detail.rawData
				}
				this.vendorLogin('WechatMiniApp', data)
			}
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

		.register-section {
			width: 80%;
		}
		
		.third-party {
			display: flex;
			flex-direction: column;
			justify-content: center;
			margin-top: 20upx;
			
			.txt-otherlogin {
				margin-top: 20rpx;
				margin-bottom: 20rpx;
				color: #00000033;
				text-align: center;
				display: flex;
				flex-direction: row;
				justify-content: center;
				align-items: center;
				
				&::before {
					display: inline-block;
					content: "";
					width: 25%;
					height: 5px;
					border-top: 1px solid #00000033;
					margin-right: 10px;
				}
				&::after {
					display: inline-block;
					content: "";
					width: 25%;
					height: 5px;
					border-top: 1px solid #00000033;
					margin-left: 10px;
				}
				
			}
			
			
			.loginType {
				display: flex;
				padding: 0 100rpx;
				justify-content: center;
				.item {
					display: flex;
					flex-direction: column;
					align-items: center;
					color: $u-content-color;
					font-size: 28rpx;
					
					.txt {
						height: 100%;
						line-height: 100%;
					}
				}
				
				
				button, button:hover {
					background: #fff;
					color: #222;
					border: none;
				}
				button:after {
					border: none;
				}
			}
			
		}
		
		
		.hint {
			padding: 20rpx 40rpx;
			font-size: 20rpx;
			color: $u-tips-color;
			
			.link {
				
			}
		}
	}
	
	
	/* #ifdef MP-BAIDU */
	.container {
		height: 100vh;
	}
	/* #endif */
</style>
