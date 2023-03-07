<template>
	<view class="content">
		<view>
			<u-swiper :list="imageList" height="300px" @click="handleImageClick"></u-swiper>
		</view>
		<view class="toolbar">
			<u--form labelPosition="left">
				<u-form-item label="数量" prop="form.n">
					<u-slider @touchmove.stop v-model="form.n" :min="1" :max="10" :step="1" style="width:100%;" :show-value="true">
					</u-slider>
				</u-form-item>
				<u-form-item label="大小">
					<u-radio-group v-model="form.size" placement="column"
						:style="{flexDirection: 'row', justifyContent: 'space-between'}">
					 <u-radio :customStyle="{marginBottom: '8px'}" v-for="(item, index) in sizeList" :key="index"
							:label="item.name" :name="item.name">
						</u-radio>
					</u-radio-group>
				</u-form-item>
				<u-form-item label="描述">
					<textarea class="textarea" v-model="form.prompt" placeholder="请输入一段描述"></textarea>
				</u-form-item>
				<u-form-item>
					<u-button @click="submit" type="primary">生成图片</u-button>
				</u-form-item>
			</u--form>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				CHECK_AUTH: true,
				imageList: [],
				form: {
					prompt: '',
					n: 1,
					size: '256x256',
				},
				sizeList: [{
						name: '256x256'
					},
					{
						name: '512x512'
					},
					{
						name: '1024x1024'
					},
				]
			}
		},
		onLoad(options) {
			this.onLoadStart(options);
		},
		methods: {
			handleImageClick(index) {
				uni.previewImage({
					current:index,
					urls: this.imageList
				})
			},
			submit() {
				if (!this.form.prompt) return;
				uni.showLoading({
					title: '正在生成图片……'
				});
				this.$http.post('chatgpt/api/index/image', this.form).then(data => {
					let list = [];
					data.forEach(item => {
						list.push('data:image/png;base64,' + item.b64_json)
					})
					this.imageList = list;
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
		.preview-image {
			justify-content: center;
			align-items: center;
		}

		.toolbar {
			padding: 30rpx;
		}

		.textarea {
			border: 1px solid #ddd;
			padding: 10rpx;
			border-radius: 10rpx;
		}
	}
</style>
