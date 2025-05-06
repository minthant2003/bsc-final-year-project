window.checkIsImg = (file) => {
    const validImgTypes = ["image/jpeg", "image/png", "image/jpg"];
    if (!validImgTypes.includes(file.type)) {
        return false;
    }
    return true;
}

window.checkIsOneGb = (file) => {
    const maxSize = 1 * 1024 * 1024 * 1024;
    if (file.size > maxSize) {
        return false;
    }
    return true;
}