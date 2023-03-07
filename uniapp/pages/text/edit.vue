<template>
	<view class="content">
		<view class="form">
			<u--form labelPosition="left">
				<u-form-item label="输入文本">
					<textarea class="textarea" v-model="form.input" rows="4"></textarea>
				</u-form-item>
				<u-form-item label="您的指令">
					<textarea class="textarea" v-model="form.instruction"></textarea>
				</u-form-item>
				<u-form-item label="数量" prop="form.n">
					<u-slider @touchmove.stop v-model="form.n" :min="1" :max="10" :step="1" style="width:100%;" :show-value="true">
					</u-slider>
				</u-form-item>
				<u-form-item label="模型">
						<u-radio-group v-model="form.model" placement="column"
							:style="{flexDirection: 'row'}">
						 <u-radio :customStyle="{marginBottom: '8px', marginRight: '15px'}" v-for="(item, index) in modelList" :key="index"
								:label="item.label" :name="item.name">
							</u-radio>
						</u-radio-group>
				</u-form-item>
				<u-form-item>
					<u-button @click="submit" type="primary">提交</u-button>
				</u-form-item>
			</u--form>
			<view class="show-more" @click="show = true" v-if="list.length">
				已生成 {{list.length}} 个文本，点击查看
			</view>
			<preview v-model="show" :list="list"></preview>
		</view>
	</view>
</template>

<script>
	import preview from './preview'
	export default {
		components: {preview},
		data() {
			return {
				CHECK_AUTH: true,
				form: {
					input: '',
					instruction: '',
					n: 1,
					model: 'text-davinci-edit-001'
				},
				modelList: [
					{ name: 'text-davinci-edit-001', label: '文本' }, 
					{ name: 'code-davinci-edit-001', label: '代码' }, 
				],
				show: false,
				list: []
			}
		},
		onLoad(options) {
			this.onLoadStart(options);
		},
		methods: {
			submit() {
				uni.showLoading({
					title: '正在生成……'
				})
				this.$http.post('/chatgpt/api/index/textedit', this.form).then(data => {
					console.log(data)
					this.list = data;
					uni.hideLoading()
				}).catch(e => {
					uni.hideLoading()
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.content {
		.form {
			padding: 40rpx;
			
			.textarea {
				border: 1px solid #ddd;
				border-radius: 10rpx;
				padding: 20rpx;
				width: 100%;
				height: 120rpx;
			}
		}
		.show-more {
			margin-top: 30rpx;
			text-align: center;
			color: #3538ff;
		}
	}
</style>
