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
</style><input type="text" name="key" id="key" placeholder="Parameter Name" required />
<strong>:</strong>
<textarea name="val" id="val" cols="30" rows="1" placeholder="Parameter Value; Must be a JSON value"></textarea>
<input type="button" id="rem" value="🗙" />`;
customElements.define(KeyVal.__tag, KeyVal);