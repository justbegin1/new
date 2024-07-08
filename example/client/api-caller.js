class APICaller {
	static #desc = [
		'OK',
		'Invalid request',
		'Authorization failed',
		'Request failed',
		'Server error',
		'Invalid server response (JSON error)',
	];
	static #isObj(value) {
		return (typeof value === 'object' && value !== null);
	}
	#config;
	logging = {};

	constructor({
		url,
		headers = null,
		logging = {
			master: true,
			collapsed: true,
			full: false,

			params: false,
			debug: true,
			meta: false,
		}
	} = {}) {
		try {
			url = (new URL(url)).href;
		} catch (error) {
			throw new Error(`Invaild API url`);
		}
		if(!url.endsWith('/')) {
			url = `${url}/`;
		}
		headers ??= {};
		if(!APICaller.#isObj(headers)) {
			throw new Error('Invalid API headers; Expected an object;');
		}
		this.logging = Object.assign({}, this.logging, logging);
		this.#config = {
			url,
			headers
		}
	}
	async coreFetch({
		func = '',
		params = null,
		headers = null,
		logging = {}
	} = {}) {
		logging = Object.assign({}, this.logging, logging);
		headers ??= {};
		if(!APICaller.#isObj(headers)) {
			throw new Error('Invalid API headers; Expected an object;');
		}

		let fetched;
		let ret = {
			status: 0,
			value: null,
			statusDesc: null,
			headers: null,
			meta: null,
			debug: null
		};

		// Create fetch config
		const config = {
			method: 'POST',
			mode: 'cors'
		};
		// Combine default headers with current headers
		config.headers = Object.assign({}, this.#config.headers, headers);

		// Add params if present
		if(APICaller.#isObj(params)) {
			config.headers['Content-Type'] = 'application/json';
			config.body = JSON.stringify(params);
		} else {
			config.method = 'GET';
		}

		const url = `${this.#config.url}${func}`;

		// Fetch data
		try {
			// console.log('API CALL', { url, params });
			fetched = await window.fetch(url, config);
			ret.headers = fetched.headers;
		} catch (error) {
			ret.status = -1;
			ret.statusDesc = 'Network failed';
			ret.value ??= error.message;
			return Promise.resolve(ret);
		}
		if(!fetched.ok) {
			ret.status = fetched.status;
			ret.statusDesc = APICaller.#desc[3];
			ret.value = fetched.statusText;
			return Promise.resolve(ret);
		}
		fetched = await fetched.text().catch(e => e.message);
		try {
			const json = JSON.parse(fetched);
			if(!APICaller.#isObj(json)) {
				throw new Error('Not object');
			}
			fetched = json;
		} catch (error) {
			ret.status = 5;
			ret.value = fetched;
			ret.statusDesc = APICaller.#desc[5];
			return Promise.resolve(ret);
		}
		ret = {...ret, ...fetched};
		
		ret.statusDesc = APICaller.#desc[ret.status] ?? 'Unknown';
		
		if(logging.master) {
			if(logging.full) {
				logging.collapsed ? console.groupCollapsed('API Call:', url) : console.group('API Call:', url);
				console.log('Request :', { url, params, headers });
				console.log('Response:', ret);
				console.groupEnd();
			} else {
				logging.collapsed ? console.groupCollapsed('API Call:', url) : console.group('API Call:', url);
				if(logging.params) {
					console.group('Request Params:', params);
				}
				if(logging.meta) {
					console.log('Response Meta:', ret.meta);
				}
				
				console.log('Response Desc:', ret.statusDesc);
				console.log('Response Value:', ret.value);
				
				if(logging.debug && ret.debug !== null) {
					console.log('Debug messages:');
					if(!Array.isArray(ret.debug)) {
						ret.debug = [ret.debug];
					}
					for(const msg of ret.debug) {
						console.log(msg);
					}
				}
				console.groupEnd();
			}
		}
		
		return Promise.resolve(ret);
	}
	
	#proxy() {
		/*
		syntax: 
		this.proxy().funcGroup.funcName(params = null, {
			headers = null,
			logging = {},
			onerror = null
		} = {});
		*/
		const list = [];
		return new Proxy(function() {}, {
			get: (dummy, name, pxy) => {
				list.push(name);
				return pxy;
			},
			apply: async (dummy, pxy, args) => {
				const params = args[0] ?? null;
				let opts = {
					headers: null,
					logging: {},
					onerror: null
				};
				if(APICaller.#isObj(args[1])) {
					opts = Object.assign({}, opts, args[1]);
				}
				const resp = await this.coreFetch({
					func: list.join('.'),
					params: params,
					headers: opts.headers,
					logging: opts.logging
				});
				if(resp.status === 0) {
					return resp.value;	
				} else {
					if(typeof opts.onerror === 'function') {
						opts.onerror(resp);
					} else {
						alert(`${resp.statusDesc}:\n${resp.value}`);
						// App.popupbox({
						// 	content: resp.value,
						// 	title: HTML`<svg-icon icon="info"></svg-icon> ${resp.statusDesc}`
						// });
					}
				}
				return null;
			}
		});
	}
	get fetch() {
		return this.#proxy();
	}
}

export default APICaller;

// Example: How to use

// const MyApp = new APICaller({
// 	url: 'https://api.example.com',
// });

// Default way to call API
// console.log('Response', await MyApp.fetch.test({
// 	'int': '3123',
// 	'bool': 0
// }));

// Core way to call API
// console.log('Response', await MyApp.coreFetch({
// 	func: 'test.lol',
// 	params: {
// 		'int': '3123',
// 		'bool': 0
// 	}
// }).catch(e => {
// 	console.log('Response Error', e);
// 	return null
// }));