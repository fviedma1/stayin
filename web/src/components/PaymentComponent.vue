<template>
  <div class="container">
    <!-- Bloque de confirmación -->
    <div v-if="showConfirmation" class="confirmation-container">
      <h2>GRÀCIES PER LA TEVA RESERVA!</h2>
      <div class="confirmation-details">
        <p><strong>Correu electrònic:</strong> {{ email }}</p>
        <p><strong>Data d'entrada:</strong> {{ formattedDateIn }}</p>
        <p><strong>Data de sortida:</strong> {{ formattedDateOut }}</p>
        <p><strong>Preu total:</strong> {{ totalPrice }}</p>
      </div>
      <p class="additional-info">Rebut de confirmació enviat al teu correu electrònic</p>
    </div>

    <!-- Contenido original (visible si no hay confirmación) -->
    <template v-else>
      <div class="user-info-container">
        <h2 class="title">Confirmació de Reserva</h2>
        <div class="info-section">
          <p><strong>Correu electrònic:</strong> {{ email }}</p>
          <p><strong>Data d'entrada:</strong> {{ formattedDateIn }}</p>
          <p><strong>Data de sortida:</strong> {{ formattedDateOut }}</p>
          <p><strong>Preu total:</strong> {{ totalPrice }}</p>
        </div>
        <button class="confirm-button" @click="createReservation">Confirmar Reserva</button>
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      </div>

      <div class="hotel-info-container">
        <carousel :images="images" />
        <div class="info-hotel">
          <p><strong>{{ hotelName }}</strong></p>
          <p><strong>{{ roomType }}</strong></p>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import Carousel from '@/components/Carousel.vue';
const API_URL = import.meta.env.VITE_API_URL;

export default {
  components: {
    Carousel,
  },
  data() {
    return {
      email: '',
      hotelName: '',
      roomType: '',
      roomId: '',
      dateIn: '',
      dateOut: '',
      totalPrice: '',
      images: [],
      showConfirmation: false,
      errorMessage: '',
    };
  },
  computed: {
    formattedDateIn() {
      return this.formatDate(this.dateIn);
    },
    formattedDateOut() {
      return this.formatDate(this.dateOut);
    },
  },
  methods: {
    formatDate(date) {
      if (!date) return 'No seleccionada';
      const options = { day: '2-digit', month: 'long', year: 'numeric' };
      return new Date(date).toLocaleDateString('ca-ES', options);
    },
    async createReservation() {
      this.errorMessage = '';
      
      if (!this.roomId) {
        this.errorMessage = 'No s\'ha seleccionat una habitació vàlida.';
        return;
      }

      const reservationData = {
        email: this.email,
        room_id: this.roomId,
        date_in: this.dateIn,
        date_out: this.dateOut,
        price: parseFloat(this.totalPrice.replace('€', '').trim()),
      };

      try {
        const response = await fetch(`${API_URL}/v1/reservacions`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(reservationData),
        });

        if (!response.ok) throw new Error('Error en la sol·licitud');

        const data = await response.json();

        if (data.success) {
          this.showConfirmation = true;
          setTimeout(() => {
            this.$router.push({ name: 'home' });
            sessionStorage.removeItem('reservationData');
          }, 5000);
        } else {
          this.errorMessage = 'Error en la reserva: ' + 
            (data.errors ? JSON.stringify(data.errors) : data.message);
        }
      } catch (error) {
        this.errorMessage = 'Error en el servidor: ' + error.message;
        console.error('Error al crear la reserva:', error);
      }
    },
    getImages(images) {
      try {
        return JSON.parse(images) || ['https://via.placeholder.com/150'];
      } catch (error) {
        return ['https://via.placeholder.com/150'];
      }
    },
  },
  mounted() {
    const reservationData = JSON.parse(sessionStorage.getItem('reservationData'));

    if (reservationData) {
      this.email = reservationData.email;
      this.hotelName = reservationData.hotelName;
      this.roomType = reservationData.roomType;
      this.dateIn = reservationData.dateIn;
      this.dateOut = reservationData.dateOut;
      this.totalPrice = reservationData.totalPrice;
      this.images = this.getImages(reservationData.images || '[]');
      this.roomId = reservationData.roomId;
    } else {
      this.$router.push({ name: 'home' });
    }
  },
};
</script>

<style scoped>
.container {
  margin: 0 auto;
  display: flex;
  gap: 20px;
  padding: 100px;
  justify-content: center;
  max-width: 1200px;
}

/* Estilos para la confirmación */
.confirmation-container {
  background: white;
  padding: 3rem;
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  text-align: center;
  max-width: 600px;
  margin: 2rem auto;
}

.confirmation-container h2 {
  color: #2d3748;
  font-size: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--primary-color);
}

.confirmation-details {
  text-align: left;
  margin: 2rem 0;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 10px;
}

.confirmation-details p {
  margin: 0.8rem 0;
  font-size: 1.1rem;
}

.additional-info {
  color: #4a5568;
  font-style: italic;
  margin-top: 1.5rem;
}

/* Estilos originales modificados */
.user-info-container {
  flex: 1;
  max-width: 400px;
  background: var(--background-main);
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  text-align: center;
}

.hotel-info-container {
  flex: 1;
  max-width: 400px;
  background: var(--background-main);
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  padding: 1rem;
  text-align: center;
}

.confirm-button {
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
  margin-top: 1.5rem;
}

.error-message {
  color: #e53e3e;
  margin-top: 1rem;
  font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    flex-direction: column;
    padding: 20px;
  }

  .user-info-container,
  .hotel-info-container {
    max-width: 100%;
  }

  .hotel-info-container {
    margin-top: 20px;
  }
}
</style>