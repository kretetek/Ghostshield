var plusMinus = document.getElementsByClassName("expand-menu-item");

for (var i = 0; i < plusMinus.length; i++) {
    
    var accordions = plusMinus[i].previousElementSibling;

    accordions.addEventListener("click", function(e) {

        if (window.matchMedia("(max-width: 830px)").matches) {
            e.preventDefault();

            if (this.parentElement.nextElementSibling.classList.contains("collapsed")) {
                this.nextElementSibling.innerHTML = '-';
                this.parentElement.nextElementSibling.classList.remove("collapsed");
            } else {
                this.nextElementSibling.innerHTML = '+';
                this.parentElement.nextElementSibling.classList.add("collapsed");
            }
        }
    });

    plusMinus[i].addEventListener("click", function(e) {
        if (this.parentElement.nextElementSibling.classList.contains("collapsed")) {
            this.innerHTML = '-';
            this.parentElement.nextElementSibling.classList.remove("collapsed");
        } else {
            this.innerHTML = '+';
            this.parentElement.nextElementSibling.classList.add("collapsed");
        }
    });
}