export class Remote {
	constructor(server) {
		this._server = server;
	}
	async exec(name, params = null, {method = 'POST', headers = null, jsonp = null} = {}) {
		method = method.toUpperCase();
		if(jsonp === null) {
			jsonp = '';
		} else {
			jsonp = `_jsonp_/${encodeURIComponent(jsonp)}/`;
		}
		let resp = {
			headers: {},
			content: null
		}, req = {
			method: 'POST',
			url: `${this._server}${jsonp}${encodeURIComponent(name)}`,
			params: params
		}

		let config = {
			method: 'POST',
		}
		config.headers = (headers === null) ? {} : headers;

		if(method === 'GET') {
			req.method = config.method =  'GET';
			
			if(params !== null) {
				const stringify = v => {
					if(typeof v === 'object') {
						return btoa(JSON.stringify(v)).replace(/=+$/, '');
					} else {
						return encodeURIComponent(new String(v));
					}
				};
				for(let k of Object.keys(params)) {
					req.url += `/${encodeURIComponent(k)}/${(stringify(params[k]))}`
				}
			}
			resp.content = await fetch(req.url, config);
		} else {
			if(params !== null) {
				config.headers['Content-Type'] = 'application/json';
				config.body = JSON.stringify(params);
			}

			resp.content = await fetch(req.url, config);
		}

		req.headers = config.headers;
		for(let p of resp.content.headers.entries()) {
			resp.headers[p[0]] = p[1];
		}

		resp.content = await resp.content.text();
		try {resp.content = JSON.parse(resp.content);} catch (error) {}

		return {req, resp};
	}
}