//#region resources/js/submit.js
document.addEventListener("submit", (e) => {
	if (e.defaultPrevented) return;
	let t = e.target;
	if ("submitting" in t.dataset) {
		e.preventDefault();
		return;
	}
	if (t.target === "_blank") return;
	let n = e.submitter;
	if (!n || "allowResubmit" in n.dataset || n.disabled) return;
	t.dataset.submitting = "";
	let { width: r, height: i } = n.getBoundingClientRect(), a = {
		position: n.style.position,
		width: n.style.width,
		height: n.style.height,
		pointerEvents: n.style.pointerEvents
	}, o = n.innerHTML;
	n.style.position = "relative", n.style.width = `${r}px`, n.style.height = `${i}px`, n.style.pointerEvents = "none", n.setAttribute("aria-busy", "true"), n.setAttribute("aria-disabled", "true"), n.setAttribute("tabindex", "-1"), n.innerHTML = "\n        <span class=\"absolute inset-0 flex items-center justify-center\">\n            <svg class=\"animate-spin h-4 w-4\" viewBox=\"0 0 24 24\">\n                <circle class=\"opacity-25\" cx=\"12\" cy=\"12\" r=\"10\" stroke=\"currentColor\" stroke-width=\"4\" fill=\"none\"></circle>\n                <path class=\"opacity-75\" fill=\"currentColor\" d=\"M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z\"></path>\n            </svg>\n        </span>\n    ";
	let s = () => {
		delete t.dataset.submitting, n.removeAttribute("aria-busy"), n.removeAttribute("aria-disabled"), n.removeAttribute("tabindex"), n.style.position = a.position, n.style.width = a.width, n.style.height = a.height, n.style.pointerEvents = a.pointerEvents, n.innerHTML = o;
	};
	t.addEventListener("invalid", s, {
		capture: !0,
		once: !0
	}), t.addEventListener("reset-loading", s, { once: !0 }), window.addEventListener("pageshow", (e) => {
		e.persisted && s();
	}, { once: !0 });
});
//#endregion
