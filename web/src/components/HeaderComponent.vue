<template>
  <header 
    :class="['header', { 'header-scrolled': isScrolled, 'fade-in': isScrolled }]"
    ref="header"
  >
    <nav class="nav">
      <!-- Logo -->
      <div class="logo">
        <img src="../../public/images/logo.png" alt="Logo" />
      </div>

      <!-- Navigation Links -->
      <ul class="nav-items">
        <li><a href="/" class="nav-link">{{ $t('home') }}</a></li>
        <RouterLink to="/search/hotels" class="nav-link">{{ $t('hotels') }}</RouterLink>
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
  name: 'HeaderComponent',
  data() {
    return {
      isScrolled: false,
      currentLanguage: this.$i18n.locale,
      dropdownOpen: false,
      availableLanguages: [
        { code: 'en', name: 'English' },
        { code: 'es', name: 'Español' },
        { code: 'ca', name: 'Català' }
      ]
    };
  },
  computed: {
    currentLanguageName() {
      const lang = this.availableLanguages.find(lang => lang.code === this.currentLanguage);
      return lang ? lang.name : '';
    }
  },
  methods: {
    handleScroll() {
      const scrollPosition = window.scrollY;
      this.isScrolled = scrollPosition > 50;
    },
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
    window.addEventListener('scroll', this.handleScroll);
    document.addEventListener('click', this.handleClickOutside);
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleScroll);
    document.removeEventListener('click', this.handleClickOutside);
  }
};
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.header {
  position: fixed;
  top: 0;
  width: 100%;
  padding: 1.5rem 0;
  transition: all 0.3s ease-in-out;
  z-index: 1000;
  background: transparent;
}

.header-scrolled {
  background: var(--background-main); /* Solid background when scrolled */
  padding: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo img {
  display: flex;
  width: 120px; /* Adjust logo width */
}

.fade-in {
  animation: fadeIn 1s ease-in-out; /* Fade-in animation */
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
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
  color: #333;
  transition: color 0.3s ease;
  font-family: 'Poppins', sans-serif; /* Asegurar la misma tipografía */
}

.nav-link:hover {
  color: #666;
}

/* Change text color when header is transparent */
.header:not(.header-scrolled) .nav-link,
.header:not(.header-scrolled) .logo,
.header:not(.header-scrolled) .dropdown-button,
.header:not(.header-scrolled) .login-link {
  color: white;
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
  color: white;
}

.login-link {
  text-decoration: none;
  color: #333;
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