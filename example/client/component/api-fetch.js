import './key-val.js';
import {API, APIProxy} from '../API.js';
import {w3CodeColor} from '../w3CodeColor.js';

class APIFetch extends HTMLElement {
	static __tag = 'api-fetch';
	static __template = document.createElement('template');
	#root;
	#pcntr;
	#ccntr;
	#rcntr;
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
		this.#ccntr = this.#root.querySelector('#code_cntr');
		this.#rcntr = this.#root.querySelector('#resp_cntr');

		this.#ccntr.style.display = 'none';
		this.#rcntr.style.display = 'none';
		this.#root.querySelector('#fetch').onclick = async () => {
			if(!this.#fname.reportValidity()) {
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
			this.#rcntr.querySelector('pre').innerHTML = JSON.stringify(await this.#api.fetchRaw(fname, param), null, 4);
			
			let paramjson = JSON.stringify(param);
			let hasparam = Object.entries(param).length === 0;
			let paramstr1 = hasparam ? '' : `, ${paramjson}`;
			let paramstr2 = hasparam ? '' : paramjson;
			let jsonfname = JSON.stringify(fname);

			this.#ccntr.querySelector('pre').innerText = `<script type="module">
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
			w3CodeColor(this.#ccntr.querySelector('pre', 'html'));
			this.#ccntr.style.display = 'block';
			this.#rcntr.style.display = 'block';
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
}
#cntr {
	display: grid;
	gap: 0.5em;
	grid-template-areas:
		"heading heading"
		"func func"
		"pcntr pcntr"
		"add fetch"
		"ccntr rcntr";
}
#para_cntr {
	display: grid;
	gap: 0.5em;
}
</style><div id="cntr">
<header style="grid-area: heading;">API Caller</header>
<input pattern="[_a-zA-Z][_a-zA-Z0-9-.]*" style="grid-area: func;" type="text" id="func" placeholder="Function Name" required />
<section style="grid-area: pcntr;" id="para_cntr">
	<header>Parameters:</header>
</section>
<button style="grid-area: add;" id="add_para">Add parameter</button>
<button style="grid-area: fetch;" id="fetch">Fetch Result</button>
<section style="grid-area: ccntr;" id="code_cntr">
	<header>Code Example:</header>
	<pre></pre>
</section>
<section style="grid-area: rcntr;" id="resp_cntr">
	<header>Raw Response:</header>
	<pre></pre>
</section>
</div>`;
customElements.define(APIFetch.__tag, APIFetch);