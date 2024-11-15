document.getElementById("categoriesDropDown").style.display = "none";
document.getElementById("categoriesLabel").style.display = "none";
document.getElementById("costLabel").style.display = "none";
document.getElementById("menuItemCostInput").style.display = "none";

document.getElementById("menuPostType").addEventListener("click", function () {
    document.getElementById("categoriesDropDown").style.display = "block";
    document.getElementById("categoriesLabel").style.display = "block";
    document.getElementById("costLabel").style.display = "block";
    document.getElementById("menuItemCostInput").style.display = "block";
});
