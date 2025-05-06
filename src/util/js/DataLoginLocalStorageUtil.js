window.setDataLoginToLocalStorage = (dataLoginObj) => {
    localStorage.setItem("finalProjectDataLogin", JSON.stringify(dataLoginObj));
}

window.getDataLoginFromLocalStorage = () => {
    return JSON.parse(localStorage.getItem("finalProjectDataLogin"));
}

window.removeDataLoginFromLocalStorage = () => {
    localStorage.removeItem("finalProjectDataLogin");
}
