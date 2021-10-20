const createElement = function (tag, classList, innerText = '') {
    let element = document.createElement(tag);
    element.classList.add(...classList);
    if (innerText) {
        element.innerText = innerText;
    }
    return element;
}