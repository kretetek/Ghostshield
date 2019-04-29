let swatches = document.getElementsByClassName("sv-swatch");
const displayImg = document.getElementById("display-img");
let swatchTitle = document.getElementById("stain-title");
let imgContainer = document.getElementById("sv_image-container");

let defaultSwatch = function() {
    let defaultColor = document.getElementsByClassName("sv-swatch")[0].getAttribute("swatch-color");
    imgContainer.style.backgroundColor = defaultColor;
}

document.addEventListener("load", defaultSwatch());

for (let i = 0; i < swatches.length; i++) {
    swatches[i].addEventListener("click", function(e) {
        e.preventDefault();
        let attribute = this.getAttribute("swatch-color");
        imgContainer.style.backgroundColor = attribute;
        swatchTitle.innerHTML = this.childNodes[3].innerHTML;
    });
}