class TestReport extends HTMLElement {
	static _meta = {
		tag: 'test-report',
		template: document.createElement('template')
	};
	#root;
	#data = null;
	#data_set = false;

	constructor() {
		super();
		this.#root = this.attachShadow({mode: 'closed'});
		this.#root.appendChild(TestReport._meta.template.content.cloneNode(true));
	}
	#qs(query, all = false) {
		return all ? this.#root.querySelectorAll(query) : this.#root.querySelector(query);
	}
	static #dataQuery = function(prop) {
		prop = prop.split('.');
		let val = this;
		for(let p of prop) {
			if(typeof val[p] === 'undefined') {
				return null;
			} else {
				val = val[p];
			}
		}
		return val;
	};
	static #genSection = function(heading, value) {
		const s = document.createElement('section');
		switch(typeof value) {
			case 'bigint':
			case 'number':
			case 'string': break;
			case 'boolean':
				value = value ? 'true' : 'false';
				break;
			case 'object':
				value = value === null ? 'null' : JSON.stringify(value, null, 4);
				break;
			default:
				value = 'Unknown';
		}
		s.innerHTML = `<header>${heading}</header><pre>${value}</pre>`;
		return s;
	};
	set data(value) {
		if(this.#data_set || value === null || typeof value !== 'object') {
			return;
		}
		this.#data = value;
		const boundQueryData = TestReport.#dataQuery.bind(this.#data);
		Object.freeze(this.#data);
		this.#data_set = true;

		const summ = {
			nodes: this.#qs('summary dd', true),
			setVal: (idx, dataQuery, def, suffix = '') => {
				let val = boundQueryData(dataQuery);
				summ.nodes[idx].innerText = (val === null) ? def : `${val}${suffix}`;
			}
		};
		summ.setVal(2, 'meta.api_time', 'Unknown', 'ms');
		summ.setVal(3, 'meta.net_time', 'Unknown', 'ms');
		summ.setVal(4, 'meta.api_mem_peak', 'Unknown', 'KB');

		{
			summ.nodes[0].innerHTML = (boundQueryData('title') ?? def).replaceAll(/'([^']+)'/g, '<span class="code">$1</span>');
		}
		
		
		{
			let result = Object.values(this.#data.results);
			result = {total: result.length, passed: result.reduce((c, v) => v ? c + 1 : c, 0)};
			result.all = result.total === result.passed;

			summ.nodes[1].innerText = `[${result.passed}/${result.total}]`;
			summ.nodes[1].classList.add(result.all ? 'passed' : 'failed');
		}

		const main = this.#qs('main');
		main.appendChild(TestReport.#genSection('Request:', this.#data.request));
		main.appendChild(TestReport.#genSection('Test Result:', this.#data.results));
		main.appendChild(TestReport.#genSection('Response:', this.#data.response));
	}
	get data() {
		this.#data;
	}
	// static get observedAttributes() {
	// 	return [];
	// }
	// connectedCallback() {}
	// disconnectedCallback() {}
	// attributeChangedCallback(attrName, oldVal, newVal) {}
	// adoptedCallback() {}
}
TestReport._meta.template.innerHTML =
`<style>
	dl {
		list-style-type: none;
		display: grid;
		grid-template-columns: max-content 1fr;
		gap: 0.5em;
		margin: 0;
		padding: 0.5em 0;
	}
	dt {
		font-weight: bold;
	}
	dd {
		margin: 0;
	}
	dd.passed {
		color: #2196f3;
	}
	dd.failed {
		color: red;
	}
	details {
		box-shadow: 0 0 5px black;
		font-family: Consolas, 'Courier New', monospace;
		display: grid;
	}
	details > summary {
		padding: 0.5em 0.75em;
		cursor: pointer;
		list-style-type: none;
		display: grid;
		gap: 0.5em;
		grid-template-columns: minmax(max-content, 1fr) repeat(4, 1fr);
		background: #212121;
		color: white;
	}
	details[open] > summary {
		border-bottom: 1px solid black;
	}
	details > main {
		padding: 0.5em 0.75em;
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 0.5em;
	}
	details > main > section:nth-of-type(2) {
		grid-row: 2;
	}
	details > main > section:nth-of-type(3) {
		grid-row: 1 / span 2;
	}
	details section > header {
		font-weight: bold;
	}
	pre {
		font-family: Consolas, 'Courier New', monospace;
		white-space: pre-wrap;
		padding: 0;
		margin: 0;
	}
	span.code {
		font-style: italic;
		text-decoration: underline;
	}
</style>
<details>
	<summary>
		<dl><dt>Title:</dt><dd>Test</dd></dl>
		<dl><dt>Passed:</dt><dd>Unknown</dd></dl>
		<dl><dt>API-Time:</dt><dd>Unknown</dd></dl>
		<dl><dt>Net-Time:</dt><dd>Unknown</dd></dl>
		<dl><dt>Peek-Mem:</dt><dd>Unknown</dd></dl>
	</summary>
	<main></main>
</details>`;
Object.freeze(TestReport);
customElements.define(TestReport._meta.tag, TestReport);