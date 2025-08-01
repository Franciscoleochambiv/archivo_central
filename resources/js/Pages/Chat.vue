<template>
    <div>
      <!-- Botón flotante para abrir el chat -->
      <div v-if="!isOpen" class="fixed bottom-5 right-5">
        <button @click="toggleChat" class="bg-red-500 text-white px-4 py-2 rounded-full shadow-lg">
          Abrir Chat 
        </button>
      </div>
  
      <!-- Ventana de chat emergente -->
      <div v-if="isOpen" class="fixed bottom-5 right-5 w-96 bg-white border border-red-300 rounded-lg shadow-lg">
        <div class="flex justify-between items-center bg-red-100 text-gray p-3 rounded-t-lg">
          <h2 class="text-lg font-semibold">Chat IA</h2>
          <button @click="toggleChat" class="text-white">X</button>
        </div>
        <div class="p-4 h-64 overflow-y-auto" ref="chatContainer">
          <!-- Spinner visible mientras se espera la respuesta del servidor -->
  
          <div v-for="(message, index) in messages" :key="index" class="mb-2">
            <div v-if="message.isUser" class="text-right">
              <span class="bg-red-300 text-white rounded-lg px-3 py-1 inline-block">{{ message.text }}</span>
            </div>
            <div v-else class="text-left">
              <span class="bg-gray-100 text-gray-700 rounded-lg px-3 py-1 inline-block">{{ message.text }}</span>
            </div>
          </div>
        </div>
        <div class="p-3 border-t">
          <div v-if="isLoading" class="flex justify-center mb-2">
            <div class="loader"></div> <!-- Spinner -->
          </div>
          <input
            v-model="newMessage"
            @keyup.enter="sendMessage"
            type="text"
            placeholder="Escribe un mensaje..."
            class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-indigo-500 p-4 pe-12 text-sm shadow-sm"
            :disabled="isLoading"
          />
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isOpen: false, // Controla la visibilidad del chat
        messages: [], // Lista de mensajes del chat
        newMessage: '', // Texto del nuevo mensaje
        isLoading: false, // Controla el estado de carga del spinner
      };
    },
    mounted() {
      // Restaurar mensajes del localStorage si existen
      const savedMessages = localStorage.getItem('chatMessages');
      if (savedMessages) {
        this.messages = JSON.parse(savedMessages);
      }
    },
    methods: {
      toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
          this.scrollToBottom(); // Asegura que se desplace al abrir el chat
        }
      },
      sendMessage() {
        if (this.newMessage.trim()) {
          // Agregar el mensaje del usuario a la lista
          this.messages.push({ text: this.newMessage, isUser: true });
  
          // Guardar los mensajes en el localStorage
          this.saveMessages();
  
          // Desplazar automáticamente hacia abajo
          this.scrollToBottom();
  
          // Enviar la solicitud al servidor para obtener la respuesta
          this.getResponseFromServer(this.newMessage);
  
          // Limpiar el input
          this.newMessage = '';
        }
      },

      async getResponseFromServer(message) {
        this.isLoading = true; // Mostrar spinner mientras se espera la respuesta
        try {
          // Hacer una solicitud POST al backend (Laravel) usando Axios

          //axios.post('/api/afp', this.form)
          //.then(response => {
          const response = await axios.post('/api/obtener_ia', {
            question: message,
          });

          console.log(response)
          
  
          // Agregar la respuesta del servidor a la lista de mensajes
          this.messages.push({ text: response.data.response, isUser: false });
  
          // Guardar los mensajes en el localStorage
          this.saveMessages();
  
          // Desplazar automáticamente hacia abajo
          this.scrollToBottom();
        } catch (error) {
          console.error('Error al obtener la respuesta del servidor:', error);
        } finally {
          this.isLoading = false; // Ocultar spinner cuando se reciba la respuesta
        }
      },
      saveMessages() {
        // Guardar los mensajes en localStorage
        localStorage.setItem('chatMessages', JSON.stringify(this.messages));
      },
      scrollToBottom() {
        // Desplazar hacia el final del contenedor del chat
        this.$nextTick(() => {
          const chatContainer = this.$refs.chatContainer;
          chatContainer.scrollTop = chatContainer.scrollHeight;
        });
      },
    },
  };
  </script>
  
  <style scoped>
  /* Spinner CSS */
  .loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
  </style>
  