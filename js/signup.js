function toggleBusinessDocument() {
    var role = document.getElementById("role").value;
    var businessDocumentField = document.getElementById("businessDocumentField");
    if (role === "business") {
        businessDocumentField.style.display = "block";
    } else {
        businessDocumentField.style.display = "none";
    }
}