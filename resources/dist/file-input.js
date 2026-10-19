//#region resources/js/file-input.js
document.addEventListener("alpine:init", () => {
	Alpine.data("fileInput", (e) => ({
		currentUrl: e.currentUrl,
		currentName: e.currentName,
		currentSize: e.currentSize,
		currentIsImage: e.currentIsImage,
		previewUrl: null,
		fileName: null,
		fileSize: null,
		isImage: !1,
		fileSelected: !1,
		get displayName() {
			return this.fileName || this.currentName;
		},
		get displaySize() {
			return this.fileSize || this.currentSize;
		},
		get showImage() {
			return this.isImage || !this.fileSelected && this.currentIsImage;
		},
		get imageSrc() {
			return this.previewUrl || this.currentUrl;
		},
		onFileSelected(e) {
			let t = e.target.files[0];
			this.previewUrl &&= (URL.revokeObjectURL(this.previewUrl), null), t ? (this.fileSelected = !0, this.fileName = t.name, this.fileSize = this.formatBytes(t.size), this.isImage = t.type.startsWith("image/"), this.isImage && (this.previewUrl = URL.createObjectURL(t))) : (this.fileSelected = !1, this.fileName = null, this.fileSize = null, this.isImage = !1);
		},
		formatBytes(e) {
			if (!e) return "0 B";
			let t = [
				"B",
				"KB",
				"MB",
				"GB"
			], n = Math.min(Math.floor(Math.log(e) / Math.log(1024)), t.length - 1);
			return `${(e / 1024 ** n).toFixed(+!!n)} ${t[n]}`;
		}
	}));
});
//#endregion
