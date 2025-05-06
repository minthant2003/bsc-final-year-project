// promise method to support asynchronous
window.convertToBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            resolve(e.target.result); // return base 64 string
        };
        reader.onerror = (error) => {
            reject("");
        };
        reader.readAsDataURL(file);
    })
}