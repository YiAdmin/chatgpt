<script>
	import {
		mapActions,
		mapMutations,
		mapState
	} from 'vuex'
	import Config from '@/common/config';
	export default {
		onLaunch: async function() {
			await this.getUserinfo()
			await this.getAppInfo()
		},
		methods: {
			...mapActions(['getAppInfo', 'getUserinfo']),
		},
		onShow: function() {},
		onHide: function() {}
	}
</script>

<style lang="scss">
	@import '@/static/css/flex.scss';
	@import '@/static/css/main.scss';
	@import "@/uni_modules/uview-ui/index.scss";
	@import '@/static/css/iconfont.css';

	view {
		display: flex;
		flex-direction: column;
	}

	.clamp {
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
	}

	.clamp-2 {
		-webkit-line-clamp: 2;
	}

	.clear::after {
		content: '';
		display: block;
		clear: both;
	}

	.flex-column {
		display: flex;
		flex-direction: column;
	}

	.flex-row {
		display: flex;
		flex-direction: row;
	}

	.flex-1 {
		flex: 1;
	}

	.text-center {
		text-align: center;
	}

	.u-upload,
	.u-upload__wrap {
		flex-wrap: wrap;
	}

	.u-upload__wrap {
		max-width: 100%;
	}
</style>
