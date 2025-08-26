<?php

if (!isset($_SESSION['usuario'])) {
    die('Acceso no autorizado. Debes iniciar sesión.');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>

    <!-- Chatbot -->
    <div id="chatbot">
        <button id="chatbot-toggle">💬</button>
        <div id="chat-window" class="hidden">
            <div class="chat-header">
                <div>
                    <h6>
                        MU - TH - UR 6000
                    </h6>
                    <span class="info-icon">⚠
                        <span class="info-tooltip">
                            <strong>AVISO DEL DEPARTAMENTO TÉCNICO</strong><br><br>
                            Ingenieros de <strong>Weyland-Yutani</strong> están solucionando ciertos problemas con la IA a bordo.<br><br>
                            Disculpe las molestias.<br>
                            <em class="text-muted">Ref: WY/IA-MUTHUR/6000/STATUS</em>
                        </span>
                    </span>
                </div>
                <p>
                    Bienvenido a la USCSS Paladio, ¿en qué puedo ayudarte <strong><?php echo htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'tripulante'); ?></strong>?

                </p>
            </div>
            <div id="chat-messages"></div>
            <input type="text" id="chat-input" placeholder="Realiza tu consulta..." />
            <button id="send-btn">Enviar</button>
        </div>
    </div>

    <script>
        // Inicializar el chatbot
        document.addEventListener("DOMContentLoaded", () => {
            const chatWindow = document.getElementById("chat-window");

            // Alternar la visibilidad del chat al hacer clic en el botón
            document.getElementById("chatbot-toggle").addEventListener("click", () => {
                chatWindow.classList.toggle("hidden");
            });
        });

        // Función para enviar el mensaje
        document.getElementById("send-btn").addEventListener("click", () => {
            const inputField = document.getElementById("chat-input");
            const message = inputField.value.trim();
            if (message) {
                addMessage("user", message); // Agregar mensaje del usuario
                inputField.value = ""; // Limpiar campo de entrada
                getResponse(message); // Obtener respuesta del bot
            }
        });

        // Función para agregar mensajes al chat
        function addMessage(sender, text) {
            const chatMessages = document.getElementById("chat-messages");
            const messageDiv = document.createElement("div");
            messageDiv.className = sender === "user" ? "user-message" : "bot-message";
            messageDiv.textContent = text;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Función para obtener la respuesta del chatbot usando la API
        function getResponse(question) {
            fetch("../API/chatbotAPI.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        question
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Respuesta del bot:", data);
                    const answer = data.answer || "Lamentablemente, el canal de comunicación libre con la inteligencia artificial central no está operativo en este momento.";
                    addMessage("bot", answer); // Agregar respuesta del bot
                })
                .catch(error => {
                    console.error("Error al obtener respuesta:", error);
                    addMessage("bot", "Error al contactar con MU-TH-UR 6000."); // Manejo de error
                });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const chatbot = document.getElementById("chatbot");
            const toggleBtn = document.getElementById("chatbot-toggle");

            // Alternar la visibilidad del chat al hacer clic en el botón
            toggleBtn.addEventListener("click", () => {
                chatbot.classList.toggle("active");

                // Cambiar el icono según el estado
                if (chatbot.classList.contains("active")) {
                    toggleBtn.textContent = "✕"; // Icono de cerrar
                } else {
                    toggleBtn.textContent = "💬"; // Icono de chat
                }
            });
        });
    </script>

    <!-- Sonido teclas -->
    <script src="../includes/sonidoTeclas.js"></script>


</body>

</html>