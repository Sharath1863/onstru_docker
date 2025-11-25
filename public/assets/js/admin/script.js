// Aside Icons Toggle Funtion
$(document).ready(function () {
    var collapse = $(".collapse");
    collapse.on("show.bs.collapse hide.bs.collapse", function (e) {
        var opened = e.type === "show";
        $(this)
            .prev("button")
            .find(".toggle-icon")
            .toggleClass("fa-angle-down", opened)
            .toggleClass("fa-angle-right", !opened);
        $(this).parent().find(".asidebtn").toggleClass("active", opened);
    });
});

// Contact Number / Age Validation / Pincode Validation / Year Validation / Aadhar Validation
function validate_contact(input) {
    const value = input.value.replace(/\D/g, "");
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    input.value = value;
}

function validate_age(input) {
    const value = input.value.replace(/\D/g, "");
    if (value.length > 3) {
        value = value.slice(0, 3);
    }
    input.value = value;
}

function validate_pincode(input) {
    const value = input.value.replace(/\D/g, "");
    if (value.length > 6) {
        value = value.slice(0, 6);
    }
    input.value = value;
}

function validate_year(input) {
    const value = input.value.replace(/\D/g, "");
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    input.value = value;
}

function validate_aadhar(input) {
    const value = input.value.replace(/\D/g, "");
    if (value.length > 12) {
        value = value.slice(0, 12);
    }
    input.value = value;
}

function togglePasswordVisibility(inputId, showId, hideId) {
    let input = $("#" + inputId);
    let passShow = $("#" + showId);
    let passHide = $("#" + hideId);

    if (input.attr("type") === "password") {
        input.attr("type", "text");
        passShow.hide();
        passHide.show();
    } else {
        input.attr("type", "password");
        passShow.show();
        passHide.hide();
    }
}

// Tooltip
const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);
