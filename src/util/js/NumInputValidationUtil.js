window.checkValidForPositiveDecimalNum = (e) => {
    if (["e", "E", "+", "-"].includes(e.key)) {
        e.preventDefault();
        return false;
    }
}

window.checkValidForPositiveWholeNum = (e) => {
    if (["e", "E", "+", "-", "."].includes(e.key)) {
        e.preventDefault();
        return false;
    }
}