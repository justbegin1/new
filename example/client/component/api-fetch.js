import './key-val.js';
class APIFetch extends HTMLElement {
	static __tag = 'api-fetch';
	static __template = document.createElement('template');
	#root;
	#pcntr;
	#qcntr;
	#rcntr;
	#fname;
	constructor() {
		super();
		this.#root = this.attachShadow({mode: 'closed'});
		this.#root.appendChild(APIFetch.__template.content.cloneNode(true));
		this.#root.querySelector('#add_para').onclick = () => {
			this.#root.querySelector('#para_cntr').appendChild(document.createElement('key-val'));
		};

		this.#fname = this.#root.querySelector('#func');
		this.#pcntr = this.#root.querySelector('#para_cntr');
		this.#qcntr = this.#root.querySelector('#req_cntr');
		this.#rcntr = this.#root.querySelector('#resp_cntr');

		this.#qcntr.style.display = 'none';
		this.#rcntr.style.display = 'none';
		this.#root.querySelector('#fetch').onclick = () => {
			if(!this.#fname.reportValidity()) {
				return;
			}
			const fname = this.#fname.value;
			let param = {};
			for(let kv of Array.from(this.#pcntr.querySelectorAll('key-val'))) {
				let d = kv.data;
				if(d === null) {
					return;
				}
				param = {...param, ...d};
			}
			console.log(fname, param);
		};
	}
}
APIFetch.__template.innerHTML = `<style>
* {
	box-sizing: border-box;
}
#cntr {
	max-width: 20em;
}
button {
	cursor: pointer;
}
input {
	height: 2.5em;
	padding: 0.5em;
	vertical-align: middle;
}
#cntr {
	display: grid;
	gap: 0.5em;
	grid-template-areas:
		"func func"
		"pcntr pcntr"
		"add fetch"
		"qcntr qcntr"
		"rcntr rcntr";
}
#para_cntr {
	display: grid;
	gap: 0.5em;
}
</style><div id="cntr"><input pattern="[_a-zA-Z][_a-zA-Z0-9-.]*" style="grid-area: func;" type="text" id="func" placeholder="Function Name" required />
<section style="grid-area: pcntr;" id="para_cntr">
	<header>Parameters:</header>
</section>
<button style="grid-area: add;" id="add_para">Add parameter</button>
<button style="grid-area: fetch;" id="fetch">Fetch Result</button>
<section style="grid-area: qcntr;" id="req_cntr">
	<header>Request:</header>
	<pre></pre>
</section>
<section style="grid-area: rcntr;" id="resp_cntr">
	<header>Response:</header>
	<pre></pre>
</section>
</div>`;
customElements.define(APIFetch.__tag, APIFetch);