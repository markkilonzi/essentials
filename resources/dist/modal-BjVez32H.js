//#region node_modules/@alpinejs/ui/dist/module.esm.js
function e(e, t, n, r) {
	return {
		items: [],
		activeKey: i(),
		orderedKeys: [],
		activatedByKeyPress: !1,
		activateSelectedOrFirst: e.debounce(function() {
			r(!1);
		}),
		registerItemsQueue: [],
		registerItem(e, t, n, r) {
			this.registerItemsQueue.length === 0 && queueMicrotask(() => {
				this.registerItemsQueue.length > 0 && (this.items = this.items.concat(this.registerItemsQueue), this.registerItemsQueue = [], this.reorderKeys(), this.activateSelectedOrFirst());
			});
			let i = {
				key: e,
				el: t,
				value: n,
				disabled: r
			};
			this.registerItemsQueue.push(i);
		},
		unregisterKeysQueue: [],
		unregisterItem(e) {
			this.unregisterKeysQueue.length === 0 && queueMicrotask(() => {
				this.unregisterKeysQueue.length > 0 && (this.items = this.items.filter((e) => !this.unregisterKeysQueue.includes(e.key)), this.orderedKeys = this.orderedKeys.filter((e) => !this.unregisterKeysQueue.includes(e)), this.unregisterKeysQueue = [], this.reorderKeys(), this.activateSelectedOrFirst());
			}), this.unregisterKeysQueue.push(e);
		},
		getItemByKey(e) {
			return this.items.find((t) => t.key === e);
		},
		getItemByValue(t) {
			return this.items.find((n) => e.raw(n.value) === e.raw(t));
		},
		getItemByEl(e) {
			return this.items.find((t) => t.el === e);
		},
		getItemsByValues(t) {
			let n = t.map((t) => e.raw(t)), r = this.items.filter((t) => n.includes(e.raw(t.value)));
			return r = r.slice().sort((e, t) => {
				let n = e.el.compareDocumentPosition(t.el);
				return n & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : n & Node.DOCUMENT_POSITION_PRECEDING ? 1 : 0;
			}), r;
		},
		getActiveItem() {
			if (!this.hasActive()) return null;
			let e = this.items.find((e) => e.key === this.activeKey.get());
			return e || this.deactivateKey(this.activeKey.get()), e;
		},
		activateItem(e) {
			e && this.activateKey(e.key);
		},
		reorderKeys: e.debounce(function() {
			this.orderedKeys = this.items.map((e) => e.key), this.orderedKeys = this.orderedKeys.slice().sort((e, t) => {
				if (e === null || t === null) return 0;
				let n = this.items.find((t) => t.key === e).el, r = this.items.find((e) => e.key === t).el, i = n.compareDocumentPosition(r);
				return i & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : i & Node.DOCUMENT_POSITION_PRECEDING ? 1 : 0;
			}), this.orderedKeys.includes(this.activeKey.get()) || this.deactivateKey(this.activeKey.get());
		}),
		getActiveKey() {
			return this.activeKey.get();
		},
		activeEl() {
			if (this.activeKey.get()) return this.items.find((e) => e.key === this.activeKey.get()).el;
		},
		isActiveEl(e) {
			let t = this.items.find((t) => t.el === e);
			return this.activeKey.is(t);
		},
		activateEl(e) {
			let t = this.items.find((t) => t.el === e);
			this.activateKey(t.key);
		},
		isDisabledEl(e) {
			return this.items.find((t) => t.el === e).disabled;
		},
		get isScrollingTo() {
			return this.scrollingCount > 0;
		},
		scrollingCount: 0,
		activateAndScrollToKey(e, t) {
			this.getItemByKey(e) && (this.scrollingCount++, this.activateKey(e, t), this.items.find((t) => t.key === e).el.scrollIntoView({ block: "nearest" }), setTimeout(() => {
				this.scrollingCount--;
			}, 25));
		},
		isDisabled(e) {
			let t = this.items.find((t) => t.key === e);
			return t ? t.disabled : !1;
		},
		get nonDisabledOrderedKeys() {
			return this.orderedKeys.filter((e) => !this.isDisabled(e));
		},
		hasActive() {
			return !!this.activeKey.get();
		},
		wasActivatedByKeyPress() {
			return this.activatedByKeyPress;
		},
		isActiveKey(e) {
			return this.activeKey.is(e);
		},
		activateKey(e, t = !1) {
			this.isDisabled(e) || (this.activeKey.set(e), this.activatedByKeyPress = t);
		},
		deactivateKey(e) {
			this.activeKey.get() === e && (this.activeKey.set(null), this.activatedByKeyPress = !1);
		},
		deactivate() {
			this.activeKey.get() && (this.isScrollingTo || (this.activeKey.set(null), this.activatedByKeyPress = !1));
		},
		nextKey() {
			if (!this.activeKey.get()) return;
			let e = this.nonDisabledOrderedKeys.findIndex((e) => e === this.activeKey.get());
			return this.nonDisabledOrderedKeys[e + 1];
		},
		prevKey() {
			if (!this.activeKey.get()) return;
			let e = this.nonDisabledOrderedKeys.findIndex((e) => e === this.activeKey.get());
			return this.nonDisabledOrderedKeys[e - 1];
		},
		firstKey() {
			return this.nonDisabledOrderedKeys[0];
		},
		lastKey() {
			return this.nonDisabledOrderedKeys[this.nonDisabledOrderedKeys.length - 1];
		},
		searchQuery: "",
		clearSearch: e.debounce(function() {
			this.searchQuery = "";
		}, 350),
		searchKey(e) {
			this.clearSearch(), this.searchQuery += e;
			let t;
			for (let e in this.items) if (this.items[e].el.textContent.trim().toLowerCase().startsWith(this.searchQuery)) {
				t = this.items[e].key;
				break;
			}
			if (this.nonDisabledOrderedKeys.includes(t)) return t;
		},
		activateByKeyEvent(e, t = !1, r = () => !1, i = () => {}, a) {
			let o, s;
			a(!0);
			let c = !0;
			switch (e.key) {
				case ["ArrowDown", "ArrowRight"][n === "vertical" ? 0 : 1]:
					if (e.preventDefault(), e.stopPropagation(), a(!1), !r()) {
						i();
						break;
					}
					this.reorderKeys(), s = this.hasActive(), o = s ? this.nextKey() : this.firstKey();
					break;
				case ["ArrowUp", "ArrowLeft"][n === "vertical" ? 0 : 1]:
					if (e.preventDefault(), e.stopPropagation(), a(!1), !r()) {
						i();
						break;
					}
					this.reorderKeys(), s = this.hasActive(), o = s ? this.prevKey() : this.lastKey();
					break;
				case "Home":
				case "PageUp":
					if (e.key == "Home" && e.shiftKey) return;
					e.preventDefault(), e.stopPropagation(), a(!1), this.reorderKeys(), s = this.hasActive(), o = this.firstKey();
					break;
				case "End":
				case "PageDown":
					if (e.key == "End" && e.shiftKey) return;
					e.preventDefault(), e.stopPropagation(), a(!1), this.reorderKeys(), s = this.hasActive(), o = this.lastKey();
					break;
				default: c = this.activatedByKeyPress, t && e.key.length === 1 && (o = this.searchKey(e.key));
			}
			o && this.activateAndScrollToKey(o, c);
		}
	};
}
function t(e, t, r, i) {
	let a = n(r, i);
	a.forEach((e) => e._x_hiddenInput = !0), a.forEach((e) => e._x_ignore = !0);
	let o = t.children, s = [];
	for (let e = 0; e < o.length; e++) {
		let t = o[e];
		if (t._x_hiddenInput) s.push(t);
		else break;
	}
	e.mutateDom(() => {
		s.forEach((e) => e.remove()), a.reverse().forEach((e) => t.prepend(e));
	});
}
function n(e, t, i = []) {
	if (r(t)) for (let r in t) i = i.concat(n(`${e}[${r}]`, t[r]));
	else {
		let n = document.createElement("input");
		return n.setAttribute("type", "hidden"), n.setAttribute("name", e), n.setAttribute("value", "" + t), [n];
	}
	return i;
}
function r(e) {
	return typeof e == "object" && !!e;
}
function i(e) {
	let t = {}, n, r = Alpine.reactive({ state: !1 }), i = () => (r.state, n), a = (e) => {
		e !== n && (n !== void 0 && (t[n].state = !1), n = e, t[e] === void 0 ? t[e] = Alpine.reactive({ state: !0 }) : t[e].state = !0, r.state = !r.state);
	};
	return e === void 0 || a(e), {
		get: i,
		set: a,
		is: (e) => t[e] === void 0 ? (t[e] = Alpine.reactive({ state: !1 }), t[e].state) : !!t[e].state
	};
}
function a(e) {
	e.directive("combobox", (t, n, { evaluate: r }) => {
		n.value === "input" ? s(t, e) : n.value === "button" ? c(t, e) : n.value === "label" ? l(t, e) : n.value === "options" ? u(t, e) : n.value === "option" ? d(t, e, n, r) : o(t, e);
	}).before("bind"), e.magic("combobox", (t) => {
		let n = e.$data(t);
		return {
			get value() {
				return n.__value;
			},
			get isOpen() {
				return n.__isOpen;
			},
			get isDisabled() {
				return n.__isDisabled;
			},
			get activeOption() {
				let e = n.__context?.getActiveItem();
				return e && e.value;
			},
			get activeIndex() {
				let t = n.__context?.getActiveItem();
				return t ? Object.values(e.raw(n.__context.items)).findIndex((n) => e.raw(t) == e.raw(n)) : null;
			}
		};
	}), e.magic("comboboxOption", (t) => {
		let n = e.$data(t), r = e.findClosest(t, (e) => e.hasAttribute("x-combobox:option"));
		if (!r) throw "No x-combobox:option directive found...";
		return {
			get isActive() {
				return n.__context.isActiveKey(e.$data(r).__optionKey);
			},
			get isSelected() {
				return n.__isSelected(r);
			},
			get isDisabled() {
				return n.__context.isDisabled(e.$data(r).__optionKey);
			}
		};
	});
}
function o(n, r) {
	r.bind(n, {
		"x-id"() {
			return [
				"alpine-combobox-button",
				"alpine-combobox-options",
				"alpine-combobox-label"
			];
		},
		"x-modelable": "__value",
		"x-data"() {
			return {
				__ready: !1,
				__value: null,
				__isOpen: !1,
				__context: void 0,
				__isMultiple: void 0,
				__isStatic: !1,
				__isDisabled: void 0,
				__displayValue: void 0,
				__compareBy: null,
				__inputName: null,
				__isTyping: !1,
				__hold: !1,
				init() {
					this.__isMultiple = r.extractProp(n, "multiple", !1), this.__isDisabled = r.extractProp(n, "disabled", !1), this.__inputName = r.extractProp(n, "name", null), this.__nullable = r.extractProp(n, "nullable", !1), this.__compareBy = r.extractProp(n, "by"), this.__context = e(r, this.__isMultiple, "vertical", () => this.__activateSelectedOrFirst());
					let i = r.extractProp(n, "default-value", this.__isMultiple ? [] : null);
					this.__value = i, queueMicrotask(() => {
						r.effect(() => {
							this.__inputName && t(r, this.$el, this.__inputName, this.__value);
						}), r.effect(() => !this.__isMultiple && this.__resetInput());
					});
				},
				__startTyping() {
					this.__isTyping = !0;
				},
				__stopTyping() {
					this.__isTyping = !1;
				},
				__resetInput() {
					let e = this.$refs.__input;
					e && (e.value = this.__getCurrentValue());
				},
				__getCurrentValue() {
					return !this.$refs.__input || !this.__value ? "" : this.__displayValue ? this.__displayValue(this.__value) : typeof this.__value == "string" ? this.__value : "";
				},
				__open() {
					if (this.__isOpen) return;
					this.__isOpen = !0;
					let e = this.$refs.__input;
					if (e) {
						let t = e.value, { selectionStart: n, selectionEnd: r, selectionDirection: i } = e;
						e.value = "", e.dispatchEvent(new Event("change")), e.value = t, i === null ? e.setSelectionRange(n, r) : e.setSelectionRange(n, r, i);
					}
					((e) => requestAnimationFrame(() => requestAnimationFrame(e)))(() => {
						this.$refs.__input.focus({ preventScroll: !0 }), this.__activateSelectedOrFirst();
					});
				},
				__close() {
					this.__isOpen = !1, this.__context.deactivate();
				},
				__activateSelectedOrFirst(e = !0) {
					if (!this.__isOpen || this.__context.hasActive() && this.__context.wasActivatedByKeyPress()) return;
					let t;
					if (this.__isMultiple) {
						let e = this.__context.getItemsByValues(this.__value);
						t = e.length ? e[0].value : null;
					} else t = this.__value;
					let n = null;
					e && t && (n = this.__context.getItemByValue(t)), n ? this.__context.activateAndScrollToKey(n.key) : this.__context.activateAndScrollToKey(this.__context.firstKey());
				},
				__selectActive() {
					let e = this.__context.getActiveItem();
					e && this.__toggleSelected(e.value);
				},
				__selectOption(e) {
					let t = this.__context.getItemByEl(e);
					t && this.__toggleSelected(t.value);
				},
				__isSelected(e) {
					let t = this.__context.getItemByEl(e);
					return !t || t.value === null || t.value === void 0 ? !1 : this.__hasSelected(t.value);
				},
				__toggleSelected(e) {
					if (!this.__isMultiple) {
						this.__value = e;
						return;
					}
					let t = this.__value.findIndex((t) => this.__compare(t, e));
					t === -1 ? this.__value.push(e) : this.__value.splice(t, 1);
				},
				__hasSelected(e) {
					return this.__isMultiple ? this.__value.some((t) => this.__compare(t, e)) : this.__compare(this.__value, e);
				},
				__compare(e, t) {
					let n = this.__compareBy;
					if (n ||= (e, t) => r.raw(e) === r.raw(t), typeof n == "string") {
						let e = n;
						n = (t, n) => !t || typeof t != "object" || !n || typeof n != "object" ? r.raw(t) === r.raw(n) : t[e] === n[e];
					}
					return n(e, t);
				}
			};
		},
		"@mousedown.window"(e) {
			!this.$refs.__input.contains(e.target) && !this.$refs.__button.contains(e.target) && !this.$refs.__options.contains(e.target) && (this.__close(), this.__resetInput());
		}
	});
}
function s(e, t) {
	t.bind(e, {
		"x-ref": "__input",
		":id"() {
			return this.$id("alpine-combobox-input");
		},
		role: "combobox",
		tabindex: "0",
		"aria-autocomplete": "list",
		async ":aria-controls"() {
			return await f(() => this.$refs.__options && this.$refs.__options.id);
		},
		":aria-expanded"() {
			return this.$data.__isDisabled ? void 0 : this.$data.__isOpen;
		},
		":aria-multiselectable"() {
			return this.$data.__isMultiple ? !0 : void 0;
		},
		":aria-activedescendant"() {
			if (!this.$data.__context.hasActive()) return;
			let e = this.$data.__context.getActiveItem();
			return e ? e.el.id : null;
		},
		":aria-labelledby"() {
			return this.$refs.__label ? this.$refs.__label.id : this.$refs.__button ? this.$refs.__button.id : null;
		},
		"x-init"() {
			let e = t.extractProp(this.$el, "display-value");
			e && (this.$data.__displayValue = e);
		},
		"@input.stop"(e) {
			this.$data.__isTyping && (this.$data.__open(), this.$dispatch("change"));
		},
		"@blur"() {
			this.$data.__stopTyping(!1);
		},
		"@keydown"(e) {
			queueMicrotask(() => this.$data.__context.activateByKeyEvent(e, !1, () => this.$data.__isOpen, () => this.$data.__open(), (e) => this.$data.__isTyping = e));
		},
		"@keydown.enter.prevent.stop"() {
			this.$data.__selectActive(), this.$data.__stopTyping(), this.$data.__isMultiple || (this.$data.__close(), this.$data.__resetInput());
		},
		"@keydown.escape.prevent"(e) {
			this.$data.__static || e.stopPropagation(), this.$data.__stopTyping(), this.$data.__close(), this.$data.__resetInput();
		},
		"@keydown.tab"() {
			this.$data.__stopTyping(), this.$data.__isOpen && this.$data.__close(), this.$data.__resetInput();
		},
		"@keydown.backspace"(e) {
			if (this.$data.__isMultiple || !this.$data.__nullable) return;
			let t = e.target;
			requestAnimationFrame(() => {
				if (t.value === "") {
					this.$data.__value = null;
					let e = this.$refs.__options;
					e && (e.scrollTop = 0), this.$data.__context.deactivate();
				}
			});
		}
	});
}
function c(e, t) {
	t.bind(e, {
		"x-ref": "__button",
		":id"() {
			return this.$id("alpine-combobox-button");
		},
		"aria-haspopup": "true",
		async ":aria-controls"() {
			return await f(() => this.$refs.__options && this.$refs.__options.id);
		},
		":aria-labelledby"() {
			return this.$refs.__label ? [this.$refs.__label.id, this.$el.id].join(" ") : null;
		},
		":aria-expanded"() {
			return this.$data.__isDisabled ? null : this.$data.__isOpen;
		},
		":disabled"() {
			return this.$data.__isDisabled;
		},
		tabindex: "-1",
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button");
		},
		"@click"(e) {
			this.$data.__isDisabled || (this.$data.__isOpen ? (this.$data.__close(), this.$data.__resetInput()) : (e.preventDefault(), this.$data.__open()), this.$nextTick(() => this.$refs.__input.focus({ preventScroll: !0 })));
		}
	});
}
function l(e, t) {
	t.bind(e, {
		"x-ref": "__label",
		":id"() {
			return this.$id("alpine-combobox-label");
		},
		"@click"() {
			this.$refs.__input.focus({ preventScroll: !0 });
		}
	});
}
function u(e, t) {
	t.bind(e, {
		"x-ref": "__options",
		":id"() {
			return this.$id("alpine-combobox-options");
		},
		role: "listbox",
		":aria-labelledby"() {
			return this.$refs.__label ? this.$refs.__label.id : this.$refs.__button ? this.$refs.__button.id : null;
		},
		"x-init"() {
			this.$data.__isStatic = t.bound(this.$el, "static", !1), t.bound(this.$el, "hold") && (this.$data.__hold = !0);
		},
		"x-show"() {
			return this.$data.__isStatic ? !0 : this.$data.__isOpen;
		}
	});
}
function d(e, t) {
	t.bind(e, {
		"x-id"() {
			return ["alpine-combobox-option"];
		},
		":id"() {
			return this.$id("alpine-combobox-option");
		},
		role: "option",
		":tabindex"() {
			return this.$comboboxOption.isDisabled ? void 0 : "-1";
		},
		"x-effect"() {
			this.$comboboxOption.isSelected ? e.setAttribute("aria-selected", !0) : e.setAttribute("aria-selected", !1);
		},
		":aria-disabled"() {
			return this.$comboboxOption.isDisabled;
		},
		"x-data"() {
			return {
				__optionKey: null,
				init() {
					this.__optionKey = (Math.random() + 1).toString(36).substring(7);
					let e = t.extractProp(this.$el, "value"), n = t.extractProp(this.$el, "disabled", !1, !1);
					this.__context.registerItem(this.__optionKey, this.$el, e, n);
				},
				destroy() {
					this.__context.unregisterItem(this.__optionKey);
				}
			};
		},
		"@click"() {
			this.$comboboxOption.isDisabled || (this.__selectOption(this.$el), this.__isMultiple || (this.__close(), this.__resetInput()), this.$nextTick(() => this.$refs.__input.focus({ preventScroll: !0 })));
		},
		"@mouseenter"(e) {
			this.__context.activateEl(this.$el);
		},
		"@mousemove"(e) {
			this.__context.isActiveEl(this.$el) || this.__context.activateEl(this.$el);
		},
		"@mouseleave"(e) {
			this.__hold || this.__context.deactivate();
		}
	});
}
function f(e) {
	return new Promise((t) => queueMicrotask(() => t(e())));
}
function p(e) {
	e.directive("dialog", (t, n) => {
		n.value === "overlay" ? h(t, e) : n.value === "panel" ? g(t, e) : n.value === "title" ? _(t, e) : n.value === "description" ? v(t, e) : m(t, e);
	}), e.magic("dialog", (t) => {
		let n = e.$data(t);
		return {
			get open() {
				return n.__isOpen;
			},
			get isOpen() {
				return n.__isOpen;
			},
			close() {
				n.__close();
			}
		};
	});
}
function m(e, t) {
	t.bind(e, {
		"x-data"() {
			return {
				init() {
					t.bound(e, "open") !== void 0 && t.effect(() => {
						this.__isOpenState = t.bound(e, "open");
					}), t.bound(e, "initial-focus") !== void 0 && this.$watch("__isOpenState", () => {
						this.__isOpenState && setTimeout(() => {
							t.bound(e, "initial-focus").focus();
						}, 0);
					});
				},
				__isOpenState: !1,
				__close() {
					t.bound(e, "open") ? this.$dispatch("close") : this.__isOpenState = !1;
				},
				get __isOpen() {
					return t.bound(e, "static", this.__isOpenState);
				}
			};
		},
		"x-modelable": "__isOpenState",
		"x-id"() {
			return ["alpine-dialog-title", "alpine-dialog-description"];
		},
		"x-show"() {
			return this.__isOpen;
		},
		"x-trap.inert.noscroll"() {
			return this.__isOpen;
		},
		"@keydown.escape"() {
			this.__close();
		},
		":aria-labelledby"() {
			return this.$id("alpine-dialog-title");
		},
		":aria-describedby"() {
			return this.$id("alpine-dialog-description");
		},
		role: "dialog",
		"aria-modal": "true"
	});
}
function h(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__isOpen === void 0 && console.warn("\"x-dialog:overlay\" is missing a parent element with \"x-dialog\".");
		},
		"x-show"() {
			return this.__isOpen;
		},
		"@click.prevent.stop"() {
			this.$data.__close();
		}
	});
}
function g(e, t) {
	t.bind(e, {
		"@click.outside"() {
			this.$data.__close();
		},
		"x-show"() {
			return this.$data.__isOpen;
		}
	});
}
function _(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__isOpen === void 0 && console.warn("\"x-dialog:title\" is missing a parent element with \"x-dialog\".");
		},
		":id"() {
			return this.$id("alpine-dialog-title");
		}
	});
}
function v(e, t) {
	t.bind(e, { ":id"() {
		return this.$id("alpine-dialog-description");
	} });
}
function y(e) {
	e.directive("disclosure", (t, n) => {
		n.value ? n.value === "panel" ? S(t, e) : n.value === "button" && x(t, e) : b(t, e);
	}).before("bind"), e.magic("disclosure", (t) => {
		let n = e.$data(t);
		return {
			get isOpen() {
				return n.__isOpen;
			},
			close() {
				n.__close();
			}
		};
	});
}
function b(e, t) {
	t.bind(e, {
		"x-modelable": "__isOpen",
		"x-data"() {
			return {
				__determineDefaultOpenState() {
					let e = !!t.bound(this.$el, "default-open", !1);
					e && (this.__isOpen = e);
				},
				__isOpen: !1,
				__close() {
					this.__isOpen = !1;
				},
				__toggle() {
					this.__isOpen = !this.__isOpen;
				}
			};
		},
		"x-id"() {
			return ["alpine-disclosure-panel"];
		}
	});
}
function x(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button");
		},
		"@click"() {
			this.$data.__isOpen = !this.$data.__isOpen;
		},
		":aria-expanded"() {
			return this.$data.__isOpen;
		},
		":aria-controls"() {
			return this.$data.$id("alpine-disclosure-panel");
		},
		"@keydown.space.prevent.stop"() {
			this.$data.__toggle();
		},
		"@keydown.enter.prevent.stop"() {
			this.$data.__toggle();
		},
		"@keyup.space.prevent"() {}
	});
}
function S(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__determineDefaultOpenState();
		},
		"x-show"() {
			return this.$data.__isOpen;
		},
		":id"() {
			return this.$data.$id("alpine-disclosure-panel");
		}
	});
}
function C(e) {
	e.directive("listbox", (t, n) => {
		n.value ? n.value === "label" ? T(t, e) : n.value === "button" ? E(t, e) : n.value === "options" ? D(t, e) : n.value === "option" && O(t, e) : w(t, e);
	}).before("bind"), e.magic("listbox", (t) => {
		let n = e.$data(t);
		return {
			get selected() {
				return n.__value;
			},
			get active() {
				let e = n.__context.getActiveItem();
				return e && e.value;
			},
			get value() {
				return n.__value;
			},
			get isOpen() {
				return n.__isOpen;
			},
			get isDisabled() {
				return n.__isDisabled;
			},
			get activeOption() {
				let e = n.__context.getActiveItem();
				return e && e.value;
			},
			get activeIndex() {
				let e = n.__context.getActiveItem();
				return e && e.key;
			}
		};
	}), e.magic("listboxOption", (t) => {
		let n = e.$data(t), r = e.findClosest(t, (e) => e.hasAttribute("x-listbox:option"));
		if (!r) throw "No x-listbox:option directive found...";
		return {
			get isActive() {
				return n.__context.isActiveKey(e.$data(r).__optionKey);
			},
			get isSelected() {
				return n.__isSelected(r);
			},
			get isDisabled() {
				return n.__context.isDisabled(e.$data(r).__optionKey);
			}
		};
	});
}
function w(n, r) {
	r.bind(n, {
		"x-id"() {
			return [
				"alpine-listbox-button",
				"alpine-listbox-options",
				"alpine-listbox-label"
			];
		},
		"x-modelable": "__value",
		"x-data"() {
			return {
				__ready: !1,
				__value: null,
				__isOpen: !1,
				__context: void 0,
				__isMultiple: void 0,
				__isStatic: !1,
				__isDisabled: void 0,
				__compareBy: null,
				__inputName: null,
				__orientation: "vertical",
				__hold: !1,
				init() {
					this.__isMultiple = r.extractProp(n, "multiple", !1), this.__isDisabled = r.extractProp(n, "disabled", !1), this.__inputName = r.extractProp(n, "name", null), this.__compareBy = r.extractProp(n, "by"), this.__orientation = r.extractProp(n, "horizontal", !1) ? "horizontal" : "vertical", this.__context = e(r, this.__isMultiple, this.__orientation, () => this.__activateSelectedOrFirst());
					let i = r.extractProp(n, "default-value", this.__isMultiple ? [] : null);
					this.__value = i, queueMicrotask(() => {
						r.effect(() => {
							this.__inputName && t(r, this.$el, this.__inputName, this.__value);
						}), r.effect(() => {
							this.__resetInput();
						});
					});
				},
				__resetInput() {
					let e = this.$refs.__input;
					e && (e.value = this.$data.__getCurrentValue());
				},
				__getCurrentValue() {
					return !this.$refs.__input || !this.__value ? "" : this.$data.__displayValue && this.__value !== void 0 ? this.$data.__displayValue(this.__value) : typeof this.__value == "string" ? this.__value : "";
				},
				__open() {
					this.__isOpen || (this.__isOpen = !0, this.__activateSelectedOrFirst(), ((e) => requestAnimationFrame(() => requestAnimationFrame(e)))(() => this.$refs.__options.focus({ preventScroll: !0 })));
				},
				__close() {
					this.__isOpen = !1, this.__context.deactivate(), this.$nextTick(() => this.$refs.__button.focus({ preventScroll: !0 }));
				},
				__activateSelectedOrFirst(e = !0) {
					if (!this.__isOpen) return;
					if (this.__context.getActiveKey()) {
						this.__context.activateAndScrollToKey(this.__context.getActiveKey());
						return;
					}
					let t;
					if (t = this.__isMultiple ? this.__value.find((e) => !!this.__context.getItemByValue(e)) : this.__value, e && t) {
						let e = this.__context.getItemByValue(t);
						e && this.__context.activateAndScrollToKey(e.key);
					} else this.__context.activateAndScrollToKey(this.__context.firstKey());
				},
				__selectActive() {
					let e = this.$data.__context.getActiveItem();
					e && this.__toggleSelected(e.value);
				},
				__selectOption(e) {
					let t = this.__context.getItemByEl(e);
					t && this.__toggleSelected(t.value);
				},
				__isSelected(e) {
					let t = this.__context.getItemByEl(e);
					return !t || t.value === null || t.value === void 0 ? !1 : this.__hasSelected(t.value);
				},
				__toggleSelected(e) {
					if (!this.__isMultiple) {
						this.__value = e;
						return;
					}
					let t = this.__value.findIndex((t) => this.__compare(t, e));
					t === -1 ? this.__value.push(e) : this.__value.splice(t, 1);
				},
				__hasSelected(e) {
					return this.__isMultiple ? this.__value.some((t) => this.__compare(t, e)) : this.__compare(this.__value, e);
				},
				__compare(e, t) {
					let n = this.__compareBy;
					if (n ||= (e, t) => r.raw(e) === r.raw(t), typeof n == "string") {
						let e = n;
						n = (t, n) => !t || typeof t != "object" || !n || typeof n != "object" ? r.raw(t) === r.raw(n) : t[e] === n[e];
					}
					return n(e, t);
				}
			};
		}
	});
}
function T(e, t) {
	t.bind(e, {
		"x-ref": "__label",
		":id"() {
			return this.$id("alpine-listbox-label");
		},
		"@click"() {
			this.$refs.__button.focus({ preventScroll: !0 });
		}
	});
}
function E(e, t) {
	t.bind(e, {
		"x-ref": "__button",
		":id"() {
			return this.$id("alpine-listbox-button");
		},
		"aria-haspopup": "true",
		":aria-labelledby"() {
			return this.$id("alpine-listbox-label");
		},
		":aria-expanded"() {
			return this.$data.__isOpen;
		},
		":aria-controls"() {
			return this.$data.__isOpen && this.$id("alpine-listbox-options");
		},
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button");
		},
		"@click"() {
			this.$data.__open();
		},
		"@keydown"(e) {
			[
				"ArrowDown",
				"ArrowUp",
				"ArrowLeft",
				"ArrowRight"
			].includes(e.key) && (e.stopPropagation(), e.preventDefault(), this.$data.__open());
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__open();
		},
		"@keydown.enter.stop.prevent"() {
			this.$data.__open();
		}
	});
}
function D(e, t) {
	t.bind(e, {
		"x-ref": "__options",
		":id"() {
			return this.$id("alpine-listbox-options");
		},
		role: "listbox",
		tabindex: "0",
		":aria-orientation"() {
			return this.$data.__orientation;
		},
		":aria-labelledby"() {
			return this.$id("alpine-listbox-button");
		},
		":aria-activedescendant"() {
			if (!this.$data.__context.hasActive()) return;
			let e = this.$data.__context.getActiveItem();
			return e ? e.el.id : null;
		},
		"x-init"() {
			this.$data.__isStatic = t.extractProp(this.$el, "static", !1), t.bound(this.$el, "hold") && (this.$data.__hold = !0);
		},
		"x-show"() {
			return this.$data.__isStatic ? !0 : this.$data.__isOpen;
		},
		"x-trap"() {
			return this.$data.__isOpen;
		},
		"@click.outside"() {
			this.$data.__close();
		},
		"@keydown.escape.stop.prevent"() {
			this.$data.__close();
		},
		"@focus"() {
			this.$data.__activateSelectedOrFirst();
		},
		"@keydown"(e) {
			queueMicrotask(() => this.$data.__context.activateByKeyEvent(e, !0, () => this.$data.__isOpen, () => this.$data.__open(), () => {}));
		},
		"@keydown.enter.stop.prevent"() {
			this.$data.__selectActive(), this.$data.__isMultiple || this.$data.__close();
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__selectActive(), this.$data.__isMultiple || this.$data.__close();
		}
	});
}
function O(e, t) {
	t.bind(e, () => ({
		"x-id"() {
			return ["alpine-listbox-option"];
		},
		":id"() {
			return this.$id("alpine-listbox-option");
		},
		role: "option",
		":tabindex"() {
			return !this.$listboxOption.isDisabled && "-1";
		},
		":aria-selected"() {
			return this.$listboxOption.isSelected;
		},
		"x-data"() {
			return {
				__optionKey: null,
				init() {
					this.__optionKey = (Math.random() + 1).toString(36).substring(7);
					let n = t.extractProp(e, "value"), r = t.extractProp(e, "disabled", !1, !1);
					this.$data.__context.registerItem(this.__optionKey, e, n, r);
				},
				destroy() {
					this.$data.__context.unregisterItem(this.__optionKey);
				}
			};
		},
		"@click"() {
			this.$listboxOption.isDisabled || (this.$data.__selectOption(e), this.$data.__isMultiple || this.$data.__close());
		},
		"@mouseenter"() {
			this.$data.__context.activateEl(e);
		},
		"@mouseleave"() {
			this.$data.__hold || this.$data.__context.deactivate();
		}
	}));
}
function k(e) {
	e.directive("popover", (t, n) => {
		n.value ? n.value === "overlay" ? N(t, e) : n.value === "button" ? j(t, e) : n.value === "panel" ? ee(t, e) : n.value === "group" && M(t, e) : A(t, e);
	}), e.magic("popover", (t) => {
		let n = e.$data(t);
		return {
			get isOpen() {
				return n.__isOpenState;
			},
			open() {
				n.__open();
			},
			close() {
				n.__close();
			}
		};
	});
}
function A(e, t) {
	t.bind(e, {
		"x-id"() {
			return ["alpine-popover-button", "alpine-popover-panel"];
		},
		"x-modelable": "__isOpenState",
		"x-data"() {
			return {
				init() {
					this.$data.__groupEl && this.$data.__groupEl.addEventListener("__close-others", ({ detail: e }) => {
						e.el.isSameNode(this.$el) || this.__close(!1);
					});
				},
				__buttonEl: void 0,
				__panelEl: void 0,
				__isStatic: !1,
				get __isOpen() {
					return this.__isStatic ? !0 : this.__isOpenState;
				},
				__isOpenState: !1,
				__open() {
					this.__isOpenState = !0, this.$dispatch("__close-others", { el: this.$el });
				},
				__toggle() {
					this.__isOpenState ? this.__close() : this.__open();
				},
				__close(e) {
					this.__isStatic || (this.__isOpenState = !1, e !== !1 && (e ||= this.$data.__buttonEl, !document.activeElement.isSameNode(e) && setTimeout(() => e.focus())));
				},
				__contains(e, n) {
					return !!t.findClosest(n, (t) => t.isSameNode(e));
				}
			};
		},
		"@keydown.escape.stop.prevent"() {
			this.__close();
		},
		"@focusin.window"() {
			this.$data.__groupEl ? this.$data.__contains(this.$data.__groupEl, document.activeElement) || this.$data.__close(!1) : this.$data.__contains(this.$el, document.activeElement) || this.$data.__close(!1);
		}
	});
}
function j(e, t) {
	t.bind(e, {
		"x-ref": "button",
		":id"() {
			return this.$id("alpine-popover-button");
		},
		":aria-expanded"() {
			return this.$data.__isOpen;
		},
		":aria-controls"() {
			return this.$data.__isOpen && this.$id("alpine-popover-panel");
		},
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button"), this.$data.__buttonEl = this.$el;
		},
		"@click"() {
			this.$data.__toggle();
		},
		"@keydown.tab"(e) {
			if (!e.shiftKey && this.$data.__isOpen) {
				let t = this.$focus.within(this.$data.__panelEl).getFirst();
				t && (e.preventDefault(), e.stopPropagation(), this.$focus.focus(t));
			}
		},
		"@keyup.tab"(e) {
			if (this.$data.__isOpen) {
				let t = this.$focus.previouslyFocused();
				if (!t) return;
				!this.$data.__buttonEl.contains(t) && !this.$data.__panelEl.contains(t) && t && this.$el.compareDocumentPosition(t) & Node.DOCUMENT_POSITION_FOLLOWING && (e.preventDefault(), e.stopPropagation(), this.$focus.within(this.$data.__panelEl).last());
			}
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__toggle();
		},
		"@keydown.enter.stop.prevent"() {
			this.$data.__toggle();
		},
		"@keyup.space.stop.prevent"() {}
	});
}
function ee(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__isStatic = t.bound(this.$el, "static", !1), this.$data.__panelEl = this.$el;
		},
		"x-effect"() {
			this.$data.__isOpen && t.bound(e, "focus") && this.$focus.first();
		},
		"x-ref": "panel",
		":id"() {
			return this.$id("alpine-popover-panel");
		},
		"x-show"() {
			return this.$data.__isOpen;
		},
		"@mousedown.window"(e) {
			this.$data.__isOpen && (this.$data.__contains(this.$data.__buttonEl, e.target) || this.$data.__contains(this.$el, e.target) || this.$focus.focusable(e.target) || this.$data.__close());
		},
		"@keydown.tab"(n) {
			if (n.shiftKey && this.$focus.isFirst(n.target)) n.preventDefault(), n.stopPropagation(), t.bound(e, "focus") ? this.$data.__close() : this.$data.__buttonEl.focus();
			else if (!n.shiftKey && this.$focus.isLast(n.target)) {
				n.preventDefault(), n.stopPropagation();
				let r = this.$focus.within(document).all(), i = r.indexOf(this.$data.__buttonEl);
				r.splice(i + 1).filter((e) => !this.$el.contains(e))[0].focus(), t.bound(e, "focus") && this.$data.__close(!1);
			}
		}
	});
}
function M(e, t) {
	t.bind(e, {
		"x-ref": "container",
		"x-data"() {
			return { __groupEl: this.$el };
		}
	});
}
function N(e, t) {
	t.bind(e, { "x-show"() {
		return this.$data.__isOpen;
	} });
}
function P(e) {
	e.directive("menu", (t, n) => {
		n.value ? n.value === "items" ? L(t, e) : n.value === "item" ? R(t, e) : n.value === "button" && I(t, e) : F(t, e);
	}).before("bind"), e.magic("menuItem", (t) => {
		let n = e.$data(t);
		return {
			get isActive() {
				return n.__activeEl == n.__itemEl;
			},
			get isDisabled() {
				return n.__itemEl.__isDisabled.value;
			}
		};
	});
}
function F(e, t) {
	t.bind(e, {
		"x-id"() {
			return ["alpine-menu-button", "alpine-menu-items"];
		},
		"x-modelable": "__isOpen",
		"x-data"() {
			return {
				__itemEls: [],
				__activeEl: null,
				__isOpen: !1,
				__open(e) {
					this.__isOpen = !0, ((e) => requestAnimationFrame(() => requestAnimationFrame(e)))(() => {
						this.$refs.__items.focus({ preventScroll: !0 }), e && e(t, this.$refs.__items, (e) => e.__activate());
					});
				},
				__close(e = !0) {
					this.__isOpen = !1, e && this.$nextTick(() => this.$refs.__button.focus({ preventScroll: !0 }));
				},
				__contains(e, n) {
					return !!t.findClosest(n, (t) => t.isSameNode(e));
				}
			};
		},
		"@focusin.window"() {
			this.$data.__contains(this.$el, document.activeElement) || this.$data.__close(!1);
		}
	});
}
function I(e, t) {
	t.bind(e, {
		"x-ref": "__button",
		"aria-haspopup": "true",
		":aria-labelledby"() {
			return this.$id("alpine-menu-label");
		},
		":id"() {
			return this.$id("alpine-menu-button");
		},
		":aria-expanded"() {
			return this.$data.__isOpen;
		},
		":aria-controls"() {
			return this.$data.__isOpen && this.$id("alpine-menu-items");
		},
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button");
		},
		"@click"() {
			this.$data.__open();
		},
		"@keydown.down.stop.prevent"() {
			this.$data.__open();
		},
		"@keydown.up.stop.prevent"() {
			this.$data.__open(z.last);
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__open();
		},
		"@keydown.enter.stop.prevent"() {
			this.$data.__open();
		}
	});
}
function L(e, t) {
	t.bind(e, {
		"x-ref": "__items",
		"aria-orientation": "vertical",
		role: "menu",
		":id"() {
			return this.$id("alpine-menu-items");
		},
		":aria-labelledby"() {
			return this.$id("alpine-menu-button");
		},
		":aria-activedescendant"() {
			return this.$data.__activeEl && this.$data.__activeEl.id;
		},
		"x-show"() {
			return this.$data.__isOpen;
		},
		tabindex: "0",
		"@click.outside"() {
			this.$data.__close();
		},
		"@keydown"(e) {
			z.search(t, this.$refs.__items, e.key, (e) => e.__activate());
		},
		"@keydown.down.stop.prevent"() {
			this.$data.__activeEl ? z.next(t, this.$data.__activeEl, (e) => e.__activate()) : z.first(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.up.stop.prevent"() {
			this.$data.__activeEl ? z.previous(t, this.$data.__activeEl, (e) => e.__activate()) : z.last(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.home.stop.prevent"() {
			z.first(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.end.stop.prevent"() {
			z.last(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.page-up.stop.prevent"() {
			z.first(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.page-down.stop.prevent"() {
			z.last(t, this.$refs.__items, (e) => e.__activate());
		},
		"@keydown.escape.stop.prevent"() {
			this.$data.__close();
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__activeEl && this.$data.__activeEl.click();
		},
		"@keydown.enter.stop.prevent"() {
			this.$data.__activeEl && this.$data.__activeEl.click();
		},
		"@keyup.space.prevent"() {}
	});
}
function R(e, t) {
	t.bind(e, () => ({
		"x-data"() {
			return {
				__itemEl: this.$el,
				init() {
					let e = t.raw(this.$data.__itemEls), n = !1;
					for (let t = 0; t < e.length; t++) if (e[t].compareDocumentPosition(this.$el) & Node.DOCUMENT_POSITION_PRECEDING) {
						e.splice(t, 0, this.$el), n = !0;
						break;
					}
					n || e.push(this.$el), this.$el.__activate = () => {
						this.$data.__activeEl = this.$el, this.$el.scrollIntoView({ block: "nearest" });
					}, this.$el.__deactivate = () => {
						this.$data.__activeEl = null;
					}, this.$el.__isDisabled = t.reactive({ value: !1 }), queueMicrotask(() => {
						this.$el.__isDisabled.value = t.bound(this.$el, "disabled", !1);
					});
				},
				destroy() {
					let e = this.$data.__itemEls;
					e.splice(e.indexOf(this.$el), 1);
				}
			};
		},
		"x-id"() {
			return ["alpine-menu-item"];
		},
		":id"() {
			return this.$id("alpine-menu-item");
		},
		":tabindex"() {
			return !this.__itemEl.__isDisabled.value && "-1";
		},
		role: "menuitem",
		"@mousemove"() {
			this.__itemEl.__isDisabled.value || this.$menuItem.isActive || this.__itemEl.__activate();
		},
		"@mouseleave"() {
			this.__itemEl.__isDisabled.value || !this.$menuItem.isActive || this.__itemEl.__deactivate();
		}
	}));
}
var z = {
	first(e, t, n = (e) => e, r = () => {}) {
		let i = e.$data(t).__itemEls[0];
		return i ? i.tagName.toLowerCase() === "template" || i.__isDisabled.value ? this.next(e, i, n) : n(i) : r();
	},
	last(e, t, n = (e) => e, r = () => {}) {
		let i = e.$data(t).__itemEls.slice(-1)[0];
		return i ? i.__isDisabled.value ? this.previous(e, i, n) : n(i) : r();
	},
	next(e, t, n = (e) => e, r = () => {}) {
		if (!t) return r();
		let i = e.$data(t).__itemEls, a = i[i.indexOf(t) + 1];
		return a ? a.__isDisabled.value || a.tagName.toLowerCase() === "template" ? this.next(e, a, n, r) : n(a) : r();
	},
	previous(e, t, n = (e) => e, r = () => {}) {
		if (!t) return r();
		let i = e.$data(t).__itemEls, a = i[i.indexOf(t) - 1];
		return a ? a.__isDisabled.value || a.tagName.toLowerCase() === "template" ? this.previous(e, a, n, r) : n(a) : r();
	},
	searchQuery: "",
	debouncedClearSearch: void 0,
	clearSearch(e) {
		this.debouncedClearSearch ||= e.debounce(function() {
			this.searchQuery = "";
		}, 350), this.debouncedClearSearch();
	},
	search(e, t, n, r) {
		if (n.length > 1) return;
		this.searchQuery += n;
		let i = e.raw(e.$data(t).__itemEls).find((e) => e.textContent.trim().toLowerCase().startsWith(this.searchQuery));
		i && !i.__isDisabled.value && r(i), this.clearSearch(e);
	}
};
function B(e) {
	e.directive("switch", (t, n) => {
		n.value === "group" ? V(t, e) : n.value === "label" ? U(t, e) : n.value === "description" ? W(t, e) : H(t, e);
	}).before("bind"), e.magic("switch", (t) => {
		let n = e.$data(t);
		return { get isChecked() {
			return n.__value === !0;
		} };
	});
}
function V(e, t) {
	t.bind(e, {
		"x-id"() {
			return ["alpine-switch-label", "alpine-switch-description"];
		},
		"x-data"() {
			return {
				__hasLabel: !1,
				__hasDescription: !1,
				__switchEl: void 0
			};
		}
	});
}
function H(e, t) {
	t.bind(e, {
		"x-modelable": "__value",
		"x-data"() {
			return {
				init() {
					queueMicrotask(() => {
						this.__value = t.bound(this.$el, "default-checked", !1), this.__inputName = t.bound(this.$el, "name", !1), this.__inputValue = t.bound(this.$el, "value", "on"), this.__inputId = "alpine-switch-" + Date.now();
					});
				},
				__value: void 0,
				__inputName: void 0,
				__inputValue: void 0,
				__inputId: void 0,
				__toggle() {
					this.__value = !this.__value;
				}
			};
		},
		"x-effect"() {
			let e = this.__value;
			if (!this.__inputName) return;
			let t = this.$el.nextElementSibling;
			if (t && String(t.id) === String(this.__inputId) && t.remove(), e) {
				let e = document.createElement("input");
				e.type = "hidden", e.value = this.__inputValue, e.name = this.__inputName, e.id = this.__inputId, this.$el.after(e);
			}
		},
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button"), this.$data.__switchEl = this.$el;
		},
		role: "switch",
		tabindex: "0",
		":aria-checked"() {
			return !!this.__value;
		},
		":aria-labelledby"() {
			return this.$data.__hasLabel && this.$id("alpine-switch-label");
		},
		":aria-describedby"() {
			return this.$data.__hasDescription && this.$id("alpine-switch-description");
		},
		"@click.prevent"() {
			this.__toggle();
		},
		"@keyup"(e) {
			e.key !== "Tab" && e.preventDefault(), e.key === " " && this.__toggle();
		},
		"@keypress.prevent"() {}
	});
}
function U(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__hasLabel = !0;
		},
		":id"() {
			return this.$id("alpine-switch-label");
		},
		"@click"() {
			this.$data.__switchEl.click(), this.$data.__switchEl.focus({ preventScroll: !0 });
		}
	});
}
function W(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__hasDescription = !0;
		},
		":id"() {
			return this.$id("alpine-switch-description");
		}
	});
}
function G(e) {
	e.directive("radio", (t, n) => {
		n.value ? n.value === "option" ? q(t, e) : n.value === "label" ? J(t, e) : n.value === "description" && Y(t, e) : K(t, e);
	}).before("bind"), e.magic("radioOption", (t) => {
		let n = e.$data(t);
		return {
			get isActive() {
				return n.__option === n.__active;
			},
			get isChecked() {
				return n.__option === n.__value;
			},
			get isDisabled() {
				let e = n.__disabled;
				return n.__rootDisabled ? !0 : e;
			}
		};
	});
}
function K(e, t) {
	t.bind(e, {
		"x-modelable": "__value",
		"x-data"() {
			return {
				init() {
					queueMicrotask(() => {
						this.__rootDisabled = t.bound(e, "disabled", !1), this.__value = t.bound(this.$el, "default-value", !1), this.__inputName = t.bound(this.$el, "name", !1), this.__inputId = "alpine-radio-" + Date.now();
					}), this.$nextTick(() => {
						let e = document.createTreeWalker(this.$el, NodeFilter.SHOW_ELEMENT, { acceptNode: (e) => e.getAttribute("role") === "radio" ? NodeFilter.FILTER_REJECT : e.hasAttribute("role") ? NodeFilter.FILTER_SKIP : NodeFilter.FILTER_ACCEPT }, !1);
						for (; e.nextNode();) e.currentNode.setAttribute("role", "none");
					});
				},
				__value: void 0,
				__active: void 0,
				__rootEl: this.$el,
				__optionValues: [],
				__disabledOptions: /* @__PURE__ */ new Set(),
				__optionElsByValue: /* @__PURE__ */ new Map(),
				__hasLabel: !1,
				__hasDescription: !1,
				__rootDisabled: !1,
				__inputName: void 0,
				__inputId: void 0,
				__change(e) {
					this.__rootDisabled || (this.__value = e);
				},
				__addOption(e, n, r) {
					let i = t.raw(this.__optionValues), a = i.map((e) => this.__optionElsByValue.get(e)), o = !1;
					for (let t = 0; t < a.length; t++) if (a[t].compareDocumentPosition(n) & Node.DOCUMENT_POSITION_PRECEDING) {
						i.splice(t, 0, e), this.__optionElsByValue.set(e, n), o = !0;
						break;
					}
					o || (i.push(e), this.__optionElsByValue.set(e, n)), r && this.__disabledOptions.add(e);
				},
				__isFirstOption(e) {
					return this.__optionValues.indexOf(e) === 0;
				},
				__setActive(e) {
					this.__active = e;
				},
				__focusOptionNext() {
					let e = this.__active, t = this.__optionValues.filter((e) => !this.__disabledOptions.has(e)), n = t[(t.indexOf(e) + 1 + t.length) % t.length];
					this.__optionElsByValue.get(n).focus(), this.__change(n);
				},
				__focusOptionPrev() {
					let e = this.__active, t = this.__optionValues.filter((e) => !this.__disabledOptions.has(e)), n = t[(t.indexOf(e) - 1 + t.length) % t.length];
					this.__optionElsByValue.get(n).focus(), this.__change(n);
				}
			};
		},
		"x-effect"() {
			let e = this.__value;
			if (!this.__inputName) return;
			let t = this.$el.nextElementSibling;
			if (t && String(t.id) === String(this.__inputId) && t.remove(), e) {
				let t = document.createElement("input");
				t.type = "hidden", t.value = e, t.name = this.__inputName, t.id = this.__inputId, this.$el.after(t);
			}
		},
		role: "radiogroup",
		"x-id"() {
			return ["alpine-radio-label", "alpine-radio-description"];
		},
		":aria-labelledby"() {
			return this.__hasLabel && this.$id("alpine-radio-label");
		},
		":aria-describedby"() {
			return this.__hasDescription && this.$id("alpine-radio-description");
		},
		"@keydown.up.prevent.stop"() {
			this.__focusOptionPrev();
		},
		"@keydown.left.prevent.stop"() {
			this.__focusOptionPrev();
		},
		"@keydown.down.prevent.stop"() {
			this.__focusOptionNext();
		},
		"@keydown.right.prevent.stop"() {
			this.__focusOptionNext();
		}
	});
}
function q(e, t) {
	t.bind(e, {
		"x-data"() {
			return {
				init() {
					queueMicrotask(() => {
						this.__disabled = t.bound(e, "disabled", !1), this.__option = t.bound(e, "value"), this.$data.__addOption(this.__option, this.$el, this.__disabled);
					});
				},
				__option: void 0,
				__disabled: !1,
				__hasLabel: !1,
				__hasDescription: !1
			};
		},
		"x-id"() {
			return ["alpine-radio-label", "alpine-radio-description"];
		},
		role: "radio",
		":aria-checked"() {
			return this.$radioOption.isChecked;
		},
		":aria-disabled"() {
			return this.$radioOption.isDisabled;
		},
		":aria-labelledby"() {
			return this.__hasLabel && this.$id("alpine-radio-label");
		},
		":aria-describedby"() {
			return this.__hasDescription && this.$id("alpine-radio-description");
		},
		":tabindex"() {
			return this.$radioOption.isDisabled ? -1 : this.$radioOption.isChecked || !this.$data.__value && this.$data.__isFirstOption(this.$data.__option) ? 0 : -1;
		},
		"@click"() {
			this.$radioOption.isDisabled || (this.$data.__change(this.$data.__option), this.$el.focus());
		},
		"@focus"() {
			this.$radioOption.isDisabled || this.$data.__setActive(this.$data.__option);
		},
		"@blur"() {
			this.$data.__active === this.$data.__option && this.$data.__setActive(void 0);
		},
		"@keydown.space.stop.prevent"() {
			this.$data.__change(this.$data.__option);
		}
	});
}
function J(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__hasLabel = !0;
		},
		":id"() {
			return this.$id("alpine-radio-label");
		}
	});
}
function Y(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$data.__hasDescription = !0;
		},
		":id"() {
			return this.$id("alpine-radio-description");
		}
	});
}
function X(e) {
	e.directive("tabs", (t, n) => {
		n.value ? n.value === "list" ? Q(t, e) : n.value === "tab" ? $(t, e) : n.value === "panels" ? te(t, e) : n.value === "panel" && ne(t, e) : Z(t, e);
	}).before("bind"), e.magic("tab", (t) => {
		let n = e.$data(t);
		return {
			get isSelected() {
				return n.__selectedIndex === n.__tabs.indexOf(n.__tabEl);
			},
			get isDisabled() {
				return n.__isDisabled;
			}
		};
	}), e.magic("panel", (t) => {
		let n = e.$data(t);
		return { get isSelected() {
			return n.__selectedIndex === n.__panels.indexOf(n.__panelEl);
		} };
	});
}
function Z(e, t) {
	t.bind(e, {
		"x-modelable": "__selectedIndex",
		"x-data"() {
			return {
				init() {
					queueMicrotask(() => {
						let e = this.__selectedIndex || Number(t.bound(this.$el, "default-index", 0)), n = this.__activeTabs(), r = (e, t, n) => Math.min(Math.max(e, t), n);
						this.__selectedIndex = r(e, 0, n.length - 1), t.effect(() => {
							this.__manualActivation = t.bound(this.$el, "manual", !1);
						});
					});
				},
				__tabs: [],
				__panels: [],
				__selectedIndex: null,
				__tabGroupEl: void 0,
				__manualActivation: !1,
				__addTab(e) {
					this.__tabs.push(e);
				},
				__addPanel(e) {
					this.__panels.push(e);
				},
				__selectTab(e) {
					this.__selectedIndex = this.__tabs.indexOf(e);
				},
				__activeTabs() {
					return this.__tabs.filter((e) => !e.__disabled);
				}
			};
		}
	});
}
function Q(e, t) {
	t.bind(e, { "x-init"() {
		this.$data.__tabGroupEl = this.$el;
	} });
}
function $(e, t) {
	t.bind(e, {
		"x-init"() {
			this.$el.tagName.toLowerCase() === "button" && !this.$el.hasAttribute("type") && (this.$el.type = "button");
		},
		"x-data"() {
			return {
				init() {
					this.__tabEl = this.$el, this.$data.__addTab(this.$el), this.__tabEl.__disabled = t.bound(this.$el, "disabled", !1), this.__isDisabled = this.__tabEl.__disabled;
				},
				__tabEl: void 0,
				__isDisabled: !1
			};
		},
		"@click"() {
			this.$el.__disabled || (this.$data.__selectTab(this.$el), this.$el.focus());
		},
		"@keydown.enter.prevent.stop"() {
			this.__selectTab(this.$el);
		},
		"@keydown.space.prevent.stop"() {
			this.__selectTab(this.$el);
		},
		"@keydown.home.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).first();
		},
		"@keydown.page-up.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).first();
		},
		"@keydown.end.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).last();
		},
		"@keydown.page-down.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).last();
		},
		"@keydown.down.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).withWrapAround().next();
		},
		"@keydown.right.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).withWrapAround().next();
		},
		"@keydown.up.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).withWrapAround().prev();
		},
		"@keydown.left.prevent.stop"() {
			this.$focus.within(this.$data.__activeTabs()).withWrapAround().prev();
		},
		":tabindex"() {
			return this.$tab.isSelected ? 0 : -1;
		},
		"@mousedown"(e) {
			e.preventDefault();
		},
		"@focus"() {
			if (this.$data.__manualActivation) this.$el.focus();
			else {
				if (this.$el.__disabled) return;
				this.$data.__selectTab(this.$el), this.$el.focus();
			}
		}
	});
}
function te(e, t) {
	t.bind(e, {});
}
function ne(e, t) {
	t.bind(e, {
		":tabindex"() {
			return this.$panel.isSelected ? 0 : -1;
		},
		"x-data"() {
			return {
				init() {
					this.__panelEl = this.$el, this.$data.__addPanel(this.$el);
				},
				__panelEl: void 0
			};
		},
		"x-show"() {
			return this.$panel.isSelected;
		}
	});
}
function re(e) {
	a(e), p(e), y(e), C(e), P(e), B(e), k(e), G(e), X(e);
}
var ie = re;
//#endregion
//#region resources/js/modal.js
document.addEventListener("alpine:init", () => {
	window.Alpine.__essentialsUi || (window.Alpine.plugin(ie), window.Alpine.__essentialsUi = !0), window.Alpine.data("essentialsModal", (e) => ({
		open: !1,
		loaded: !1,
		loading: !1,
		failed: !1,
		confirming: !1,
		data: {},
		init() {
			this.$watch("open", (t) => {
				t && e.src && this.load(), this.back();
			}), e.reopen && this.$nextTick(() => this.open = !0);
		},
		advance() {
			let e = this.$refs.body.querySelector("form");
			e && e.reportValidity() && (this.data = Object.fromEntries(new FormData(e)), this.fillReview(), this.confirming = !0);
		},
		fillReview() {
			let e = this.$refs.body.querySelector("[data-review]"), t = this.$refs.review;
			e && t && t.dataset.slotted !== "true" && (t.innerHTML = e.innerHTML);
		},
		back() {
			let e = this.confirming;
			this.confirming = !1, this.data = {}, e && this.$refs.review && this.$refs.review.dataset.slotted !== "true" && (this.$refs.review.innerHTML = "");
		},
		submitted(t) {
			e.confirm && !this.confirming && (t.preventDefault(), this.advance());
		},
		show(t) {
			e.name && t === e.name && (this.open = !0);
		},
		hide(t) {
			e.name && t === e.name && (this.open = !1);
		},
		async load() {
			if (!(this.loaded || this.loading)) {
				this.loading = !0, this.failed = !1;
				try {
					let t = await fetch(e.src, { headers: {
						"X-Requested-With": "XMLHttpRequest",
						Accept: "text/html"
					} });
					if (!t.ok) throw Error(t.status);
					this.$refs.remote.innerHTML = await t.text(), this.loaded = !0;
				} catch {
					this.failed = !0;
				}
				this.loading = !1;
			}
		}
	}));
});
//#endregion
