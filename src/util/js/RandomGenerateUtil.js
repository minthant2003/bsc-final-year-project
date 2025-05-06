window.generateRandomStr = (length) => {
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let randomStr = "";
    for (let i = 0; i < length; i++) {
        randomStr += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    return randomStr;
}