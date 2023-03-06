function parseResult(data, errors) {
	return {
		get: function(key) {
			if (key) return data[key];
			return data;
		},
		getError: function(path) {
			for (var i = 0 ; i < errors.length; i ++) {
				let error = errors[i];
				if (!error.path) {
					return {
						code: 1000,
						message: error.message,
						category: null
					}
				}
				let _path = error.path.join('.');
				if (path == _path) {
					return {
						code: error.extensions.code || 10000,
						message: error.message,
						category: error.extensions.category
					}
				} 
			}
			return false;
		},
		error: function(paths) {
			paths = typeof paths == 'string' ? [paths] : paths;
			let errors = [];
			for (var i = 0; i < paths.length; i ++) {
				let path = paths[i];
				let err = this.getError(path);
				if (err) {
					uni.showToast({
						icon: 'none',
						title: err.message
					})
					errors.push(err);
				}
			}
			return errors.length ? errors : false;
		},
		show: function(error) {
			uni.showToast({
				icon: 'none',
				title: error.message
			})
		}
	}
}

 const gql = {
	option: {
		url: ''
	},
	mode: '',
	setConfig: function(callback) {
		this.option = callback(this.option);
		return this;
	},
	fetchApi: async function(api, variables = null, operationName = null, headers = {}) {
		this.mode = 'ApiName';
		return this.fetch(api, variables, operationName, headers)
	},
	fetch: async function(query, variables = null, operationName = null, headers = {}) {
		let url = this.option.url;
		const form = {
			variables, operationName
		}
		if (this.mode == 'ApiName') url += '?api=' + query;
		else form.query = query;
		this.mode = '';
		const data = await uni.$u.http.post(url, form);
		return Promise.resolve(parseResult(data.data, data.errors || []));
	},
	setUrl: function(url) {
		this.option.url = url;
		return this;
	}
}
uni.$u.gql = gql;
export default gql;