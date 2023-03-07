<template>
	<yi-modal v-model="show" :title="title" :showOk="false" @cancel="show = false" :footer="false">
		<swiper :indicator-dots="false" :autoplay="false" :interval="3000" :duration="1000" style="height: 350px;" @change="handleChange">
			<swiper-item v-for="(item, i) in list" :key="i" style="overflow-y: scroll;">
				<view class="swiper-item"><md-parser :resource="item.text"></md-parser></view>
			</swiper-item>
		</swiper>
		<view class="footer">
			<view class="btn danger" @click="show = false">取消</view>
			<view class="btn primary" @click="handleCopy">复制内容</view>
		</view>
	</yi-modal>
</template>

<script>
	export default {
		data() {
			return {
				show: false,
				index: 0,
			}
		},
		props: {
			list: {
				default: []
			},
			value: {
				default: false
			}
		},
		mounted() {
			this.show = this.value;
		},
		watch: {
			value(v) {
				this.show = v;
			},
			show(v) {
				this.$emit('input', v);
			},
		},
		computed: {
			title() {
				return '查看 ' + (this.index + 1) + '/' + this.list.length
			}
		},
		methods: {
			handleChange(e) {
				this.index = e.detail.current
			},
			handleCopy() {
				uni.setClipboardData({
					data: this.list[this.index].text
				})
			}
		}
	}
</script>

<style scoped lang="scss">
	.list {
		.item {}
	}
	
	.footer {
		display: flex;
		flex-direction: row;
		
		.btn {
			padding: 30rpx 0;
			font-size: 30rpx;
		}
		.primary {
			flex: 1;
			text-align: center;
			color: red;
		}
		
		.danger {
			flex: 1;
			text-align: center;
			color: #888;
		}
	}
</style>
