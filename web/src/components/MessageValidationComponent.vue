<template>
  <dialog ref="validationDialog" class="dialog-overlay">
    <div class="validation-container">
      <h2>{{ $t('validateMessageTitle') }}</h2>
      <div class="form-group">
        <label for="validationCode">{{ $t('validationCode') }}</label>
        <input type="text" id="validationCode" v-model="validationCode" placeholder="" />
      </div>
      <button type="submit" class="btn-primary" @click="validateMessage">{{ $t('validate') }}</button>
      <p v-if="validationError" class="error-message">{{ $t('validationError') }}</p>
    </div>
  </dialog>
</template>

<script>
export default {
  data() {
    return {
      validationCode: '',
      validationError: false
    };
  },
  mounted() {
    this.$refs.validationDialog.showModal();
    this.disableScroll(); // Deshabilitar el scroll al abrir el diálogo
  },
  beforeUnmount() {
    this.enableScroll(); // Habilitar el scroll al cerrar el diálogo
  },
  // MessageValidationComponent.vue
  methods: {
    validateMessage() {
      const storedMessage = localStorage.getItem('validationMessage'); // Obtener el código almacenado
      if (this.validationCode === storedMessage) {
        // Si el código es correcto, guardar la reserva
        this.saveReservation();
      } else {
        // Si el código es incorrecto, mostrar un error
        this.validationError = true;
        console.error('Código de validación incorrecto');
      }
    },
    saveReservation() {
      this.validationError = false;

      // Guardar los datos en sessionStorage
      const reservationData = {
        email: this.$parent.formData.email,
        hotelName: this.$parent.hotel.name,
        roomType: this.$parent.roomType,
        roomId: this.$parent.roomId,
        dateIn: this.$parent.formData.dateIn,
        dateOut: this.$parent.formData.dateOut,
        totalPrice: this.$parent.totalPrice,
      };

      sessionStorage.setItem('reservationData', JSON.stringify(reservationData));

      // Redirigir a PaymentComponent.vue
      this.$router.push({ name: 'payment' });
    },
    refreshPage() {
      window.location.reload();
    },
    disableScroll() {
      // Guardar la posición actual del scroll
      document.body.style.top = `-${window.scrollY}px`;
      // Deshabilitar el scroll
      document.body.style.position = 'fixed';
      document.body.style.width = '100%';
      document.body.style.overflow = 'hidden';
    },
    enableScroll() {
      // Restaurar la posición del scroll
      const scrollY = document.body.style.top;
      document.body.style.position = '';
      document.body.style.width = '';
      document.body.style.overflow = '';
      document.body.style.top = '';
      window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }
  }
};
</script>

<style scoped>

h2 {
  color: #2d3748;
}

.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(5px);
  display: flex;
  justify-content: center;
  align-items: center;
  border: none;
  padding: 2rem;
  z-index: 9999;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
}

.validation-container {
  padding: 2.5rem;
  background-color: var(--background-main);
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  text-align: center;
  max-width: 400px;
  width: 100%;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.form-group {
  margin-bottom: 1.5rem;
  text-align: left;
}

.form-group label {
  display: block;
  margin-bottom: 0.75rem;
  color: #4a5568;
  font-size: 0.875rem;
  font-weight: 600;
}

.form-group input {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid var(--background-main);
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.2s ease;
  box-sizing: border-box;
}

.form-group input:focus {
  border-color: var(--primary-color);
  outline: none;
}

.btn-primary {
  width: 100%;
  padding: 1rem;
  background: var(--primary-color);
  color: var(--background-main);
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.2s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-primary:hover {  
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-primary:active {
  transform: translateY(0);
}

.error-message {
  color: #e53e3e;
  margin-top: 1rem;
  font-size: 0.875rem;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 480px) {
  .validation-container {
    padding: 1.5rem;
    margin: 0 1rem;
  }
  
  .dialog-overlay {
    backdrop-filter: blur(2px);
  }
}
</style>
