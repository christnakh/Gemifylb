function toggleShapeInput(value) {
    var fancyShapesDiv = document.getElementById('fancy_shapes');

    if (value === 'fancy') {
        fancyShapesDiv.style.display = 'block';
    } else {
        fancyShapesDiv.style.display = 'none';
    }
}

function toggleCertificateInput(value) {
    var otherCertificateDiv = document.getElementById('other_certificate');

    if (value === 'other') {
        otherCertificateDiv.style.display = 'block';
    } else {
        otherCertificateDiv.style.display = 'none';
    }
}

function toggleColorInput(value) {
    var colorWhiteDiv = document.getElementById('color_white');
    var colorFancyDiv = document.getElementById('color_fancy');
    
    if (value === 'white') {
        colorWhiteDiv.style.display = 'block';
        colorFancyDiv.style.display = 'none';
    } else if (value === 'fancy') {
        colorWhiteDiv.style.display = 'none';
        colorFancyDiv.style.display = 'block';
    }
}