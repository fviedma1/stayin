<template>
  <div class="container">
      <!-- Bloque izquierdo - Formulario de autenticación -->
      <div class="auth-container">
        <h2 class="title">Qui fa la reserva?</h2>
        <div class="form-section">
          <div class="form-group">
            <label for="email">Correu electronic</label>
            <input
              type="email"
              id="email"
              v-model="formData.email"
              class="custom-input"
              placeholder="exemple@correu.com"
            />
          </div>
          <button class="auth-button" @click.prevent="sendValidationEmail">
            Autentificar
          </button>
        </div>
      </div>
  
      <!-- Bloque derecho (70%) - Ahora es el bloque de resumen -->
      <div class="hotel-container">
        <carousel :images="images" />
        <div class="info-hotel">
          <p>
            <strong>{{ hotel.name }}</strong>
          </p>
          <p>
            <strong>{{ roomType }}</strong>
          </p>
          <div class="label_date">
            <p><strong>Fecha de entrada:</strong> {{ formattedDateIn }}</p>
          </div>
          <div class="label_date">
            <p><strong>Fecha de salida:</strong> {{ formattedDateOut }}</p>
          </div>
        </div>
      </div>
    </div>
  
    <!-- Modal de validación -->
    <div v-if="showValidationModal" class="modal-overlay">
      <div class="modal-content">
        <message-validation-component @close="showValidationModal = false" />
      </div>
    </div>
  </template>
  
  <script>
  import emailjs from 'emailjs-com'
  import Carousel from '@/components/Carousel.vue'
  import MessageValidationComponent from '@/components/MessageValidationComponent.vue'
  
  export default {
    components: {
      Carousel,
      MessageValidationComponent,
    },
    props: ['room'], // Ya no necesitas pasar la información a través de props
    data() {
      // Recuperar la información de sessionStorage
      const roomData = JSON.parse(sessionStorage.getItem('selectedRoom')) || {};
      const startDate = sessionStorage.getItem('startDate') || '';
      const endDate = sessionStorage.getItem('endDate') || '';
  
      return {
        formData: {
          email: '',
          dateIn: startDate,
          dateOut: endDate,
        },
        
        hotel: roomData.hotels || {},
        roomId: roomData.id || '',
        roomType: roomData.type_room?.name || '',
        images: this.getImages(roomData.type_room?.images || '[]'),
        totalPrice: `${roomData.type_room?.price || 0} €`,
        showValidationModal: false,
      };
    },
    computed: {
      // Formatear las fechas para mostrarlas en un formato legible
      formattedDateIn() {
        return this.formatDate(this.formData.dateIn)
      },
      formattedDateOut() {
        return this.formatDate(this.formData.dateOut)
      },
    },
    methods: {
      getImages(images) {
        try {
          return JSON.parse(images) || ['https://via.placeholder.com/150']
        } catch (error) {
          return ['https://via.placeholder.com/150']
        }
      },
      formatDate(date) {
        if (!date) return 'No seleccionada'
        const options = { day: '2-digit', month: 'long', year: 'numeric' }
        return new Date(date).toLocaleDateString('es-ES', options)
      },
      sendValidationEmail() {
        const serviceID = 'service_385o0oo'
        const templateID = 'template_m57alfl'
        const userID = '_SrEjcLbdfNK42NtT'
        const randomMessage = Math.floor(100000 + Math.random() * 900000)
  
        const templateParams = {
          to_name: this.username,
          message: randomMessage,
          to_email: this.formData.email,
          room_type: this.roomType,
          date_in: this.formData.dateIn,
          date_out: this.formData.dateOut,
          total_price: this.totalPrice,
        }
  
        emailjs.send(serviceID, templateID, templateParams, userID).then(
          (response) => {
            localStorage.setItem('validationMessage', randomMessage)
            this.showValidationModal = true
          },
          (error) => {
            console.error('Failed to send email.', error)
          },
        )
      },
    },
  }
  </script>
  
  <style scoped>
  
  /* Estilos generales combinados */
  .container {
    margin: 0 auto;
    display: flex;
    gap: 20px;
    padding: 100px;
    justify-content: center;
    max-width: 1200px; /* Nueva propiedad */
  }
  
  /* Bloque izquierdo - Autenticación (nuevo diseño) */
  .auth-container {
    flex: 1;
    max-width: 400px;
    background: var(--background-main);
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    align-self: flex-start; /* Mantiene alineación superior */
  }
  
  .title {
    font-size: 1.8em;
    color: #2d3748;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  
  .subtitle {
    font-size: 1.2em;
    color: #4a5568;
    margin-bottom: 1.5rem;
    font-weight: 500;
  }
  
  .form-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .custom-input {
    padding: 10px; /* Añadir padding */
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    width: 100%;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  
  .custom-input:focus {
    border-color: var(--primary-color);
    outline: none;
  }
  
  .auth-button {
    background-color: var(--primary-color);
    color: var(--background-main);
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease;
    width: 100%;
  }
  
  
  
  /* Bloque derecho - Mantenido de original */
  .hotel-container {
    max-width: 400px;
    background: var(--background-main);
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }
  
  .hotel-container h2 {
    font-size: 1.4em;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
  }
  
  .info-hotel {
    padding: 20px;
  }
  
  .label_date p {
    margin: 5px 0;
  }
  
  /* Modal - Mantenido de original */
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .modal-content {
    background: var(--background-main);
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  }
  
  /* Responsive - Combinado */
  @media (max-width: 768px) {
    .container {
      flex-direction: column;
      padding: 20px;
    }
  
    .auth-container,
    .hotel-container {
      max-width: 100%;
    }
  
    .hotel-container {
      margin-top: 20px;
    }
  }
  
  /* Mantenido de original con ajustes */
  button:not(.auth-button) { /* Selector específico para otros botones */
    background-color: var(--primary-color);
    color: var(--background-main);
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
  }
  
  button:not(.auth-button):hover {
    background-color: var(--secundary-color);
  }
  
  input:not(.custom-input) { /* Selector para inputs no personalizados */
    width: 100%;
    padding: 12px 16px;
    margin-bottom: 15px;
    border: 2px solid var(--background-main);
    border-radius: 8px;
    font-size: 16px;
  }
  
  input:not(.custom-input):focus {
    border-color: var(--primary-color);
    outline: none;
  }
  </style>
  