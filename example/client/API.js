export class API {
	#baseURL;
	#logDebugMessages = false;
	constructor(url, base) {
		this.#baseURL = new URL(url, base);
		this.#baseURL = this.#baseURL.href;
	}
	fetchRaw(name = '', params = null, headers = null) {
		return new Promise((Resolve, Reject) => {
			const config = {
				method: 'POST'
			}
			config.headers = (typeof headers === 'object' && headers !== null) ? headers : {};
			if(params !== null) {
				config.headers['Content-Type'] = 'application/json';
				config.body = JSON.stringify(params);
			}
			fetch(`${this.#baseURL}/${name}`, config).then(fetched => {
				const HEADERS = {};
				for(let h of fetched.headers.entries()) {
					HEADERS[h[0]] = h[1];
				}
				if(fetched.status === 200) {
					fetched.text().then(txt => {
						try {
							txt = JSON.parse(txt);
							txt.headers = HEADERS;
							Resolve(txt);
						} catch (error) {
							Resolve({
								headers: HEADERS,
								status: 5,
								value: txt
							});
						}
					}).catch(txtError => {
						Resolve({
							headers: HEADERS,
							status: 5,
							value: 'No response found'
						});
					});
				} else {
					Resolve({
						headers: HEADERS,
						status: fetched.status,
						value: fetched.statusText
					});
				}
			}).catch(fetchError => {
				Resolve({
					headers: {},
					status: -1,
					value: fetchError.message
				});
			});
		});
	}
	fetch(name = '', params = null, headers = null) {
		return new Promise((Resolve, Reject) => {
			this.fetchRaw(name, params, headers).then(ret => {
				if(ret.status === 0) {
					Resolve(ret.value);
				} else {
					Reject(ret);
				}
			});
		});
	}
}

export class APIProxy {
	#api;
	constructor(url, base) {
		this.#api = new API(url, base);
	}
	get fetch() {
		const cntr = {
			api: this.#api,
			func: []
		};
		return new Proxy(function(){}, {
			get: (dummy, prop, pxy) => {
				cntr.func.push(prop);
				return pxy;
			},
			apply: (dummy, thisArg, args = [params = null, headers = null]) => {
				return this.#api.fetch(cntr.func.join('.'), ...args);
			}
		});
	}
	get fetchRaw() {
		const cntr = {
			api: this.#api,
			func: []
		};
		return new Proxy(function(){}, {
			get: (dummy, prop, pxy) => {
				cntr.func.push(prop);
				return pxy;
			},
			apply: (dummy, thisArg, args = [params = null, headers = null]) => {
				return this.#api.fetchRaw(cntr.func.join('.'), ...args);
			}
		});
	}
}