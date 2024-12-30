// Example: validatePhotoFile()
function validatePhotoFile() {
    const fileInput = document.getElementById("foto");
    const mistakeEl = document.getElementById("fotomistake");
  
    if (!fileInput.value) {
      fileInput.style.border = "1px solid red";
      fileInput.style.boxShadow = "0 0 3px #8B0000";
      mistakeEl.innerHTML = "(Wybierz plik graficzny)";
      mistakeEl.style.display = "block";
    } else {
      fileInput.style.border = "1px solid green";
      fileInput.style.boxShadow = "0 0 1px green";
      mistakeEl.style.display = "none";
    }
  }
  
  // Example: fotonamecheck()
  function fotonamecheck() {
    const descInput = document.getElementById("opis");
    const mistakeEl2 = document.getElementById("fotomistake2");
    
    if (descInput.value.length > 255) {
      descInput.style.border = "1px solid red";
      descInput.style.boxShadow = "0 0 3px #8B0000";
      mistakeEl2.innerHTML = "(Opis zdjęcia nie może przekraczać 255 znaków)";
      mistakeEl2.style.display = "block";
    } else {
      descInput.style.border = "1px solid green";
      descInput.style.boxShadow = "0 0 1px green";
      mistakeEl2.style.display = "none";
    }
  }
  