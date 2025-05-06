window.setCheckoutObjToLocalStorage = (checkoutObj) => {
    localStorage.setItem("checkoutObj", JSON.stringify(checkoutObj));
}

window.getCheckoutObjFromLocalStorage = () => {
    return JSON.parse(localStorage.getItem("checkoutObj"));
}

window.removeCheckoutObjFromLocalStorage = () => {
    localStorage.removeItem("checkoutObj");
}

window.initialiseCheckoutObj = () => {
    const checkoutObj = {
        items: [],
        paymentMethod: "",
        remark: "",
        userId: "",
    };
    window.setCheckoutObjToLocalStorage(checkoutObj);
}