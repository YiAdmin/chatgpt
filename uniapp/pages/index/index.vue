<template>
	<view class="content">
		<view class="menu-list">
			<view v-for="(item,i) in menuList" :key="i" :style="{width: item.width || '50vw'}">
				<view class="menu-item">
					<view class="menu" @click="$u.route(item.url)" :style="{background: 'linear-gradient(' + item.background + ')', height: item.height || '100px'}">
						<view class="menu-title" :style="{fontSize: item.fontSize || '20px', color: item.fontColor || '#222' }">
							{{item.title}}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="footer">
			<u-link href="https://www.yiadmin.net" text="Powered By YiAdmin"></u-link>
		</view>
	</view>
</template>
<script>
	export default {
		data() {
			return {
				menuList: [
					{ title: '聊天', url: '/pages/chat/index', height: '150px',  width: '100vw', background: '72deg, #feac85, #f50'},
					{ title: '编辑文本', url: '/pages/text/edit', height: '150px',  width: '100vw', background: '72deg, #fe6ad2, #ff23c5'},
					{ title: '作图', url: '/pages/image/index', height: '150px', width: '100vw' , background: '72deg, #4b6cff, #1332ff'},
				]
			}
		}
	}
</script>
<style scoped lang="scss">
	.content {
		.menu-list {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			.menu-item {
				padding: 10px;
				
				.menu {
					// background: linear-gradient(72deg, #ffd4bf, #f50);
					justify-content: center;
					align-items: center;
					border-radius: 10px;
				}
			}
		}
	}
	.footer {
		position: fixed;
		bottom: 10rpx;
		right: 0;
		left: 0;
		display: flex;
		justify-content: center;
		align-items: center;
	}
</style>