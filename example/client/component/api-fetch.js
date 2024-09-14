import './key-val.js';
import {w3CodeColor} from './w3CodeColor.js';
import {API, APIProxy} from '../API.js';

class APIFetch extends HTMLElement {
	static __tag = 'api-fetch';
	static __template = document.createElement('template');
	static #funcpattern = /^[_a-zA-Z][_a-zA-Z0-9-.]*$/;
	#root;
	#pcntr;
	#ccntr;
	#rcntr;
	#ocntr;
	#fname;
	#api;
	constructor() {
		super();
		
		this.#api = new API('../server/public', window.location.href);

		this.#root = this.attachShadow({mode: 'closed'});
		this.#root.appendChild(APIFetch.__template.content.cloneNode(true));
		this.#root.querySelector('#add_para').onclick = () => {
			this.#root.querySelector('#para_cntr').appendChild(document.createElement('key-val'));
		};

		this.#fname = this.#root.querySelector('#func');
		this.#pcntr = this.#root.querySelector('#para_cntr');
		this.#ccntr = this.#root.querySelector('#code_cntr pre');
		this.#rcntr = this.#root.querySelector('#resp_cntr pre');
		this.#ocntr = this.#root.querySelector('#out_cntr');
		
		this.#ocntr.style.display = 'none';

		this.#root.querySelector('#fetch').onclick = async () => {
			this.#ocntr.style.display = 'none';
			if(!APIFetch.#funcpattern.test(this.#fname.value)) {
				this.#fname.setCustomValidity(`Required 'function-name' pattern: ${APIFetch.#funcpattern}`);
				this.#fname.reportValidity();
				return;
			}
			let fname = this.#fname.value;
			let param = {};
			for(let kv of Array.from(this.#pcntr.querySelectorAll('key-val'))) {
				let d = kv.data;
				if(d === null) {
					return;
				}
				param = {...param, ...d};
			}
			const resp = await this.#api.fetchRaw(fname, param);
			if(resp.status === 5) {
				this.#rcntr.innerText = resp.value;
			} else {
				this.#rcntr.innerHTML = JSON.stringify(resp, null, 4);
			}

			this.#root.querySelector('#resp_cntr').style.background =  (resp.status === 0) ? '#4caf50' : '#f44336';
			
			let paramjson = JSON.stringify(param);
			let hasparam = Object.entries(param).length === 0;
			let paramstr1 = hasparam ? '' : `, ${paramjson}`;
			let paramstr2 = hasparam ? '' : paramjson;
			let jsonfname = JSON.stringify(fname);

			this.#ccntr.innerText = `<script type="module">
import {API, APIProxy} from './API.js';
const url = '../server/public'; /* The API URL eg: '', 'api' */
const base = window.location.href /* The API base path eg: 'https://api.example.com' */

{ /* Using the API class directly */
	const api = new API(url, base);
	
	/* Fetch raw response */
	let rawResp = await api.fetchRaw(${jsonfname}${paramstr1});
	console.log(rawResp);

	/* Fetch response value */
	try {
		let val = await api.fetch(${jsonfname}${paramstr1});
		console.log(val);
	} catch (error) {
		console.error(error);
	}
}
{ /* Using APIProxy */
	const api = new APIProxy(url, base);

	/* Fetch raw response */
	let rawResp = await api.fetchRaw.${fname}(${paramstr2});
	console.log(rawResp);

	/* Fetch response value */
	try {
		let val = await api.fetch.${fname}(${paramstr2});
		console.log(val);
	} catch (error) {
		console.error(error);
	}
}
</script>`;
			w3CodeColor(this.#ccntr, 'html');
			this.#ocntr.style.display = 'grid';
		};
	}
}
APIFetch.__template.innerHTML = `<style>
* {
	box-sizing: border-box;
}
#cntr {
	padding: 0.5em;
}
button {
	cursor: pointer;
	padding: 0.5em;
}
input {
	height: 2.5em;
	padding: 0.5em;
	vertical-align: middle;
}
header {
	font-weight: bold;
	font-size: larger;
}
pre {
	font-family: Consolas,'Courier New', monospace;
	white-space: pre-wrap;
	padding: 0;
	margin: 0;
	margin-top: 0.5em;
}
#cntr {
	display: grid;
	gap: 0.5em;
	grid-template-columns: 1fr;
	background: #1a237e;
	color: white;
}
#para_cntr {
	display: grid;
	gap: 0.5em;
}
#out_cntr {
	display: grid;
	grid-template-columns: 1fr 1fr;
	padding: 0.5em 1em;
	gap: 0.5em
}
#out_cntr > section {
	box-shadow: 1px 1px 1em black;
	padding: 0.5em 1em;
	background: #e0f2f1;
}
</style><div id="cntr">
	<header>API Function: <button id="fetch">Execute 🗘</button></header>
	<datalist id="list">
		<option value="test">test</option>
		<option value="test.lol">lol</option>
		<option value="test.ping">ping</option>
		<option value="test.auth">auth</option>
	</datalist>
	<input list="list" type="text" id="func" placeholder="Function Name" required />
	<section id="para_cntr">
		<header>Parameters: <button id="add_para"><strong>+</strong></button></header>
	</section>
</div>
<div id="out_cntr">
	<section id="code_cntr">
		<header>Code Example:</header>
		<pre></pre>
	</section>
	<section id="resp_cntr">
		<header>Raw Response:</header>
		<pre></pre>
	</section>
</div>`;
customElements.define(APIFetch.__tag, APIFetch);