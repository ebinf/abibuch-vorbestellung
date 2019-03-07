document.getElementById("sel_abi_qty").onchange = function() {
    var abi_qty = document.getElementById("sel_abi_qty").value;
    if (abi_qty >= 1 && abi_qty <= 5) {
        document.getElementById("lbl_abi_qty").innerHTML = abi_qty;
        if (abi_qty == 1) {
            document.getElementById("lbl_abi_plr").innerHTML = "Abibuch";
        } else {
            document.getElementById("lbl_abi_plr").innerHTML = "Abibücher";
        }
        document.getElementById("lbl_abi_pre").innerHTML = (16 * value) + ",00 €";
    }
};
