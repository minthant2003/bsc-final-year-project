window.getBarcodeImg64Str = (itemCode) => {
    const canvas = document.createElement("canvas");
    JsBarcode(canvas, itemCode, {
        format: "CODE128",
        displayValue: true
    });
    return canvas.toDataURL("image/png"); // barcode base64 string
}