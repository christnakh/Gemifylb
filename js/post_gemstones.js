document.addEventListener("DOMContentLoaded", function () {
  // Function to toggle visibility of "Other" certificate input field
  function toggleCertificateInput(selectedValue) {
    var otherCertificateDiv = document.getElementById("other_certificate");
    var otherCertificateInput = document.getElementById(
      "other_certificate_input"
    );

    if (selectedValue === "Other") {
      otherCertificateDiv.style.display = "block";
      otherCertificateInput.setAttribute("required", "true");
    } else {
      otherCertificateDiv.style.display = "none";
      otherCertificateInput.removeAttribute("required");
    }
  }

  // Event listener for certificate type select change
  var certificateTypeSelect = document.getElementById("certificate_type");
  if (certificateTypeSelect) {
    certificateTypeSelect.addEventListener("change", function () {
      toggleCertificateInput(this.value);
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Function to toggle visibility of "Other" shape input field
  function toggleShapeInput(selectedValue) {
    var otherShapeDiv = document.getElementById("other_shape");

    if (selectedValue === "Other") {
      otherShapeDiv.style.display = "block";
      document
        .getElementById("other_shape_input")
        .setAttribute("required", "true");
    } else {
      otherShapeDiv.style.display = "none";
      document.getElementById("other_shape_input").removeAttribute("required");
    }
  }

  // Event listener for shape select change
  var shapeSelect = document.getElementById("shape");
  if (shapeSelect) {
    shapeSelect.addEventListener("change", function () {
      toggleShapeInput(this.value);
    });
  }
});
