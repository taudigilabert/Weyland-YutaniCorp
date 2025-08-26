// Definir sonidos
const sonidoBorrar = new Audio("../sounds/sonidoTeclaIn.mp3");
const sonidoEscribir = new Audio("../sounds/sonidoTeclaOut.mp3");

// Función para reproducir sonido de escribir
function playEscribir() {
  // Reproduce el sonido sin esperar que termine el anterior
  sonidoEscribir.currentTime = 0;
  sonidoEscribir.play(); 
}

// Función para reproducir sonido de borrar
function playBorrar() {
  // Reproduce el sonido sin esperar que termine el anterior
  sonidoBorrar.currentTime = 0;
  sonidoBorrar.play();
}

// Selecciona todos los elementos de entrada (input) y textarea en la página
const inputsYTextareas = document.querySelectorAll("input, textarea");

// Evento para detectar cuando se escribe
inputsYTextareas.forEach((input) => {
  input.addEventListener("input", function (event) {
    if (event.inputType === "deleteContentBackward") {
      playBorrar();
    } else {
      playEscribir();
    }
  });

  // Evento para detectar cuando se presiona una tecla
  input.addEventListener("keydown", function () {
    playEscribir();
  });
});
