<template>
  <footer class="footer">
    <div class="footer-content">
      <!-- Sección de Enlaces Legales -->
      <div class="footer-section">
        <h4>{{ $t('footer.legalTitle') }}</h4>
        <ul>
          <li><RouterLink to="/legal">{{ $t('footer.privacyPolicy') }}</RouterLink></li>
          <li><a href="#">{{ $t('footer.termsOfUse') }}</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h4>{{ $t('footer.contactTitle') }}</h4>
        <p><i class="fas fa-envelope"></i> info@stayin.com</p>
        <p><i class="fas fa-phone"></i> +34 937 123 456</p>
        <p><i class="fas fa-map-marker-alt"></i> Carrer del Turisme, 45, Barcelona</p>
      </div>
      <!-- Sección de Redes Sociales -->
      <div class="footer-section">
        <h4>{{ $t('footer.followUs') }}</h4>
        <ul>
          <li><a href="#"><i class="fab fa-facebook-f"></i> Facebook</a></li>
          <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
          <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
        </ul>
      </div>

      <!-- Sección de Integrantes -->
      <div class="footer-section">
        <h4>{{ $t('footer.teamTitle') }}</h4>
        <ul>
          <li><a href="#">{{ $t('footer.member1') }}</a></li>
          <li><a href="#">{{ $t('footer.member2') }}</a></li>
          <li><a href="#">{{ $t('footer.member3') }}</a></li>
        </ul>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <p>&copy; {{ currentYear }} {{ $t('footer.hotelChain') }}. {{ $t('footer.allRightsReserved') }}</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
  data() {
    return {
      currentYear: new Date().getFullYear()
    };
  }
};
</script>

<style scoped>
.footer {
  background-color: var(--background-light);
  color: var(--text-primary);
  padding: var(--spacing-xl) 0 var(--spacing-lg) 0;
  text-align: center;
}

.footer-content {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  margin-bottom: var(--spacing-lg);
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 var(--spacing-md);
}

.footer-section {
  margin: var(--spacing-lg);
  max-width: 300px;
  text-align: left;
}

.footer-section h4 {
  margin-bottom: var(--spacing-md);
  font-size: var(--font-size-large);
  color: var(--background-main);
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: var(--spacing-sm);
}

.footer-section ul li a {
  color: var(--text-primary);
  text-decoration: none;
  transition: color 0.3s ease;
}

/* Eliminar el efecto hover */
.footer-section ul li a:hover {
  color: var(--text-primary);
}

.footer-section p {
  margin: var(--spacing-xs) 0;
  color: var(--text-secondary);
}

.footer-bottom {
  border-top: 1px solid var(--border-color);
  padding-top: var(--spacing-sm);
  margin-top: var(--spacing-lg);
}

.footer-bottom p {
  margin: 0;
  font-size: var(--font-size-small);
  color: var(--text-secondary);
}
</style>