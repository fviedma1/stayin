<template>
  <header class="header" ref="header">
    <nav class="nav">
      <!-- Logo -->
      <div class="logo">
        <img src="../../public/images/logo.png" alt="Logo" />
      </div>

      <!-- Navigation Links -->
      <ul class="nav-items">
        <li><a href="/" class="nav-link">{{ $t('home') }}</a></li>
        <li><RouterLink to="/search/hotels" class="nav-link">{{ $t('hotels') }}</RouterLink></li>
        <li><a href="#contact" class="nav-link">{{ $t('contact') }}</a></li>
      </ul>

      <!-- Right Section -->
      <div class="header-right">
        <!-- Language Switcher -->
        <div class="language-switcher">
          <button @click="toggleDropdown" class="dropdown-button">
            {{ currentLanguageName }}
          </button>
          <div v-if="dropdownOpen" class="dropdown-menu">
            <button 
              v-for="lang in availableLanguages"
              :key="lang.code"
              @click="changeLanguage(lang.code)"
              :class="{ 'active': currentLanguage === lang.code }"
              :title="lang.name"
            >
              {{ lang.name }}
            </button>
          </div>
        </div>

        <!-- Login Link -->
        <a href="/" class="login-link">
          <i class="fas fa-user"></i>
        </a>
      </div>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'HeaderFixComponent',
  data() {
    return {
      currentLanguage: this.$i18n.locale,
      dropdownOpen: false,
      availableLanguages: [
        { code: 'en', name: 'English' },
        { code: 'es', name: 'Español' },
        { code: 'ca', name: 'Català' }
      ]
    }
  },
  computed: {
    currentLanguageName() {
      const lang = this.availableLanguages.find(lang => lang.code === this.currentLanguage);
      return lang ? lang.name : '';
    }
  },
  methods: {
    changeLanguage(langCode) {
      this.$i18n.locale = langCode;
      this.currentLanguage = langCode;
      this.dropdownOpen = false;
      try {
        localStorage.setItem('locale', langCode);
      } catch (error) {
        console.error('Error guardando idioma:', error);
      }
    },
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    handleClickOutside(event) {
      if (!this.$refs.header.contains(event.target)) {
        this.dropdownOpen = false;
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  }
}

</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  width: 100%;
  background-color: var(--background-main);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  transition: background-color 0.3s ease;
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
}

.logo img {
  height: 40px;
}

.nav-items {
  display: flex;
  gap: 2rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-link {
  text-decoration: none;
  color: var(--text-primary);
  font-weight: 500;
  transition: color 0.3s ease;
  font-family: 'Poppins', sans-serif; /* Asegurar la misma tipografía */
}

.nav-link:hover {
  color: var(--primary-color);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.language-switcher {
  position: relative;
}

.dropdown-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem; /* Ajustar el tamaño de la fuente para que coincida con el resto del texto */
  font-family: 'Poppins', sans-serif; /* Asegurar la misma tipografía */
  padding: 0.3rem;
  transition: all 0.2s ease;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: var(--background-main);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  overflow: hidden;
  z-index: 1000;
}

.dropdown-menu button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  font-family: 'Poppins', sans-serif; /* Asegurar la misma tipografía */
  padding: 0.5rem 1rem;
  text-align: left;
  width: 100%;
  transition: background-color 0.2s ease;
}

.dropdown-menu button:hover {
  background-color: var(--background-dark);
}

.dropdown-menu button.active {
  background-color: var(--primary-color);
  color: var(--text-primary);
}

.login-link {
  text-decoration: none;
  color: var(--text-color);
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: 
    background-color 0.3s ease,
    color 0.3s ease;
  font-family: 'Poppins', sans-serif; /* Asegurar la misma tipografía */
}

.login-link:hover {
  background-color: var(--secundary-color);
  color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
  .nav-items {
    gap: 1rem;
  }
  
  .header-right {
    gap: 1rem;
  }
  
  .dropdown-button {
    font-size: 1rem; /* Ajustar el tamaño de la fuente para que coincida con el resto del texto */
    width: 32px;
    height: 32px;
  }
  
  .login-link {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }
}
</style>