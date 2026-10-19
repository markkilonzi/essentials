//#region resources/js/shell.js
var e = (e = null) => {
	document.querySelectorAll("details[data-menu][open]").forEach((t) => {
		e && t.contains(e) || t.removeAttribute("open");
	});
};
document.addEventListener("click", (t) => {
	let n = document.querySelector("[data-shell]");
	if (!n) return;
	let r = window.matchMedia("(min-width: 1024px)").matches;
	t.target.closest("[data-shell-toggle]") && n.toggleAttribute(r ? "data-collapsed" : "data-open"), t.target.closest("[data-shell-close]") && n.removeAttribute("data-open"), !r && t.target.closest("aside nav a") && n.removeAttribute("data-open"), r && t.target.closest("summary[data-nav-group]") && n.removeAttribute("data-collapsed"), e(t.target);
}), document.addEventListener("keydown", (t) => {
	t.key === "Escape" && (document.querySelector("[data-shell]")?.removeAttribute("data-open"), e());
});
//#endregion
