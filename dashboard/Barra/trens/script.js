document.addEventListener("DOMContentLoaded", () => {

    const typeButtons = document.querySelectorAll(".type-btn");

    typeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            typeButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
        });
    });

    const searchForm = document.getElementById("search-form");

    if (searchForm) {
        searchForm.addEventListener("submit", (event) => {
            event.preventDefault();

            const origem = document.getElementById("origem").value;
            const destino = document.getElementById("destino").value;

            if (origem && destino) {
                window.location.href = "../viajar/viajar.php";
            }
        });
    }

    const couponLink = document.getElementById("add-coupon");
    if (couponLink) {
        couponLink.addEventListener("click", (e) => {
            e.preventDefault();
            const cupom = prompt("Digite o seu cupom de desconto:");
            if (cupom) {
                alert(`Cupom "${cupom}" aplicado com sucesso!`);
            }
        });
    }

});