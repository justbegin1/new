import {API} from './API.js';
const isObject = o => o !== null && typeof o === 'object';

function deepEqual(o1, o2) {
	const k1 = Object.keys(o1);
	const k2 = Object.keys(o2);
	if (k1.length !== k2.length) {
		return false;
	}
	for (const key of k1) {
		const v1 = o1[key];
		const v2 = o2[key];
		const areObjects = isObject(v1) && isObject(v2);
		if (areObjects && !deepEqual(v1, v2) || !areObjects && v1 !== v2) {
			return false;
		}
	}
	return true;
}
export class ResultTest {
	static store = {};
	#handler;
	#shouldPass;
	constructor(title, handler, shouldPass = true) {
		this.title = String(title);
		if(typeof handler !== 'function') {
			throw new Error('ResultTest handler must be a function');
		}
		this.#handler = handler;
		this.#shouldPass = !!shouldPass;
	}
	test(request, response) {
		return {
			title: this.title,
			passed: this.#shouldPass === this.#handler(request, response)
		}
	}
}
ResultTest.store.statusOK = (shouldPass = true) => new ResultTest('statusOk', (q, r) => r.status === 0, shouldPass);
ResultTest.store.cmpValue = (value, shouldPass = true) => new ResultTest('cmpValue', (q, r) => deepEqual({val: value}, {val: r.value}), shouldPass);


export class APITester {
	#api;
	constructor(url, base) {
		this.#api = new API(url, base);
	}
	// async checkDataStruct(struct, data) {
	// 	const r = await this.#api.fetchRaw('data_validator', {
	// 		struct: JSON.stringify(struct),
	// 		data: data
	// 	});
	// 	if(r.status === 0 && r.value === true) {
	// 		return true;
	// 	}
	// 	// console.error('API data structure validation', {struct: struct, data: data, result: r});
	// 	return false;
	// }
	async test(
		title,
		{func = '', param = null, headers = null} = {/* request */},
		...tests
	) {
		const request = {func:func, param:param, headers:headers};
		const meta = {
			net_time: performance.now()
		};
		const response = await this.#api.fetchRaw(func, param, headers);
		meta.net_time = performance.now() - meta.net_time;
		meta.api_time = (typeof response.meta?.exe_time === 'number')
			? Number((new Number(response.meta.exe_time * 1000)).toPrecision(4))
			: 'Unknown';
		meta.api_mem_peak = (typeof response.meta?.mem_peak === 'number')
			? Number((new Number(response.meta.mem_peak / 1024)).toPrecision(3))
			: 'Unknown';

		let results = {};
		for(const t of tests) {
			if(t instanceof ResultTest) {
				const {title, passed} = t.test(request, response);
				results[title] = passed;
			}
		}
		return {
			title: title,
			results: results,
			request: request,
			response: response,
			meta: meta
		};
	}
}