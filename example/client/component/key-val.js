class KeyVal extends HTMLElement {
	static __tag = 'key-val';
	static __template = document.createElement('template');
	static #keypattern = /^[_a-zA-Z][_a-zA-Z0-9-]*$/;
	#root;
	constructor() {
		super();
		this.#root = this.attachShadow({mode: 'closed'});
		this.#root.appendChild(KeyVal.__template.content.cloneNode(true));
		this.#root.querySelector('#rem').onclick = () => {
			this.parentNode.removeChild(this);
		}
	}
	get data() {
		let keynode = this.#root.querySelector('#key');
		let valnode = this.#root.querySelector('#val');
		if(!KeyVal.#keypattern.test(keynode.value)) {
			keynode.setCustomValidity(`Required 'key' pattern: ${KeyVal.#keypattern.toString()}`);
			keynode.reportValidity();
			return null;
		}
		try {
			let value = (valnode.value === '') ? null : JSON.parse(valnode.value);
			return {
				[keynode.value]: value
			};
		} catch (error) {
			valnode.setCustomValidity('Must be a JSON value');
			valnode.reportValidity();
			return null;
		}
	}
}
KeyVal.__template.innerHTML = `<style>
* {
	box-sizing: border-box;
}
:host {
	display: grid;
	grid-template-columns: min-content min-content 1fr min-content;
	gap: 0.5em;
	align-items: center;
}
input[type="text"] {
	width: 9em;
	text-align: right
}
input[type="text"], textarea {
	height: 2.5em;
	padding: 0.5em;
	vertical-align: middle;
}
#rem {
	cursor: pointer;
	vertical-align: middle;
	padding: 0.25em;
}
</style><datalist id="list">
	<option value="bool">bool</option>
	<option value="email">email</option>
	<option value="flag">flag</option>
	<option value="float">float</option>
	<option value="hex">hex</option>
	<option value="int">int</option>
	<option value="ipv4">ipv4</option>
	<option value="ipv6">ipv6</option>
	<option value="json">json</option>
	<option value="json64">json64</option>
	<option value="mac">mac</option>
	<option value="mixed">mixed</option>
	<option value="number">number</option>
	<option value="string">string</option>
	<option value="string64">string64</option>
	<option value="timestamp">timestamp</option>
	<option value="unsigned">unsigned</option>
	<option value="url">url</option>
	<option value="url64">url64</option>
	// Nullables
	<option value="_bool">bool (nullable)</option>
	<option value="_email">email (nullable)</option>
	<option value="_flag">flag (nullable)</option>
	<option value="_float">float (nullable)</option>
	<option value="_hex">hex (nullable)</option>
	<option value="_int">int (nullable)</option>
	<option value="_ipv4">ipv4 (nullable)</option>
	<option value="_ipv6">ipv6 (nullable)</option>
	<option value="_json">json (nullable)</option>
	<option value="_json64">json64 (nullable)</option>
	<option value="_mac">mac (nullable)</option>
	<option value="_mixed">mixed (nullable)</option>
	<option value="_number">number (nullable)</option>
	<option value="_string">string (nullable)</option>
	<option value="_string64">string64 (nullable)</option>
	<option value="_timestamp">timestamp (nullable)</option>
	<option value="_unsigned">unsigned (nullable)</option>
	<option value="_url">url (nullable)</option>
	<option value="_url64">url64 (nullable)</option>
</datalist><input list="list" type="text" name="key" id="key" placeholder="Parameter Name" required />
<strong>:</strong>
<textarea name="val" id="val" cols="30" rows="1" placeholder="Parameter Value; Must be a JSON value"></textarea>
<input type="button" id="rem" value="🗙" />`;
customElements.define(KeyVal.__tag, KeyVal);