<template>
  <div class="legal-page" data-view-transition>
    <div class="legal-content">
      <!-- Sección Condiciones de Uso -->
      <section class="legal-section">
        <h2 id="condiciones-uso" class="text-primary">{{ $t('legal.termsTitle') }}</h2>
        <div class="card">
          <p class="text-secondary">{{ $t('legal.termsIntro') }}</p>
          <p class="text-secondary">{{ $t('legal.termsContent') }}</p>
          <p class="text-secondary">
            A continuació, es descriuen detalladament les condicions d'ús d'aquest lloc web. L'accés i utilització d'aquest lloc implica l'acceptació dels termes i condicions aquí exposats. Si no està d'acord amb aquests termes, li preguem que no faci ús d'aquest lloc web.
          </p>
          <ul class="text-secondary">
            <li>{{ $t('legal.termsPoint1') }}</li>
            <li>{{ $t('legal.termsPoint2') }}</li>
            <li>{{ $t('legal.termsPoint3') }}</li>
            <li>{{ $t('legal.termsPoint4') }}</li>
          </ul>
        </div>
      </section>

      <!-- Sección Política de Privacidad -->
      <section class="legal-section">
        <h2 id="politica-privacidad" class="text-primary">{{ $t('legal.privacyTitle') }}</h2>
        <div class="card">
          <p class="text-secondary">{{ $t('legal.privacyIntro') }}</p>
          <p class="text-secondary">
            En aquesta secció s'explica com recopilem, utilitzem i protegim les dades personals dels nostres usuaris. La privacitat dels usuaris és una prioritat per a nosaltres, i ens comprometem a complir amb totes les normatives de privacitat i protecció de dades personals aplicables.
          </p>
          <p class="text-secondary">{{ $t('legal.privacyContent') }}</p>
          <p class="text-secondary">
            Recopilem dades de diverses maneres, incloent, però no limitant-nos a, formularis en línia, cookies i registres de navegació. La informació que recopilem s'utilitza per a millorar l'experiència de l'usuari i oferir un servei més personalitzat.
          </p>
          <ul class="text-secondary">
            <li>{{ $t('legal.privacyPoint1') }}</li>
            <li>{{ $t('legal.privacyPoint2') }}</li>
            <li>{{ $t('legal.privacyPoint3') }}</li>
            <li>{{ $t('legal.privacyPoint4') }}</li>
          </ul>
          <p class="text-secondary">{{ $t('legal.privacyOutro') }}</p>
        </div>
      </section>

      <!-- Información de la Empresa -->
      <section class="company-info">
        <h2 class="text-primary">{{ $t('legal.aboutUsTitle') }}</h2>
        <div class="card">
          <p class="text-secondary">
            Som una empresa compromesa amb oferir serveis de qualitat als nostres usuaris, garantint sempre la millor experiència possible. La nostra missió és proporcionar solucions innovadores i segures als nostres clients, alineats amb els més alts estàndards de responsabilitat i ètica.
          </p>
          <p class="text-secondary">
            A continuació, li proporcionem informació rellevant sobre la nostra empresa:
          </p>
          <p><strong>{{ $t('legal.companyName') }}</strong></p>
          <p><strong>{{ $t('legal.addressLabel') }}</strong> {{ $t('legal.address') }}</p>
          <p><strong>{{ $t('legal.cifLabel') }}</strong> {{ $t('legal.cif') }}</p>
          <p><strong>{{ $t('legal.contactLabel') }}</strong> {{ $t('legal.contact') }}</p>  
          <p><strong>{{ $t('legal.phoneLabel') }}</strong> {{ $t('legal.phone') }}</p>
        </div>
      </section>

      <!-- Sección Exención de Responsabilidad -->
      <section class="legal-section">
        <h2 id="exencion-responsabilidad" class="text-primary">{{ $t('legal.disclaimerTitle') }}</h2>
        <div class="card">
          <p class="text-secondary">{{ $t('legal.disclaimerIntro') }}</p>
          <p class="text-secondary">
            Aquest lloc web i el seu contingut es proporcionen "tal qual" i sense garanties de cap mena. No ens fem responsables de qualsevol mal directe, indirecte, incidental, especial o conseqüent que pugui sorgir de l'ús del lloc o de la impossibilitat d'accedir a aquest.
          </p>
          <p class="text-secondary">
            A pesar que fem tot el possible per garantir l'exactitud de la informació presentada en aquest lloc, no podem garantir que no existeixin errors, omissions o interrupcions en el servei.
          </p>
        </div>
      </section>

      <!-- Sección Modificaciones -->
      <section class="legal-section">
        <h2 id="modificaciones" class="text-primary">{{ $t('legal.modificationsTitle') }}</h2>
        <div class="card">
          <p class="text-secondary">{{ $t('legal.modificationsIntro') }}</p>
          <p class="text-secondary">
            Ens reservem el dret de modificar en qualsevol moment els termes i condicions, així com la política de privacitat i qualsevol altra secció d'aquest avís legal. Li recomanem revisar regularment aquesta pàgina per a estar al corrent de qualsevol canvi.
          </p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  // Forzar el scroll al inicio de la página
  window.scrollTo(0, 0);
});
</script>

<style scoped>
/* Mantenemos tus estilos existentes y añadimos modificaciones */
[data-view-transition] {
  contain: layout style;
  animation: fadeIn 2s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Legal Page Styles */
.legal-page {
  background-color: var(--background-main);
  padding: var(--spacing-xl) var(--spacing-md);
  min-height: 100vh;
  margin-top: 60px; /* Ajustar según la altura del header */
}

.legal-content {
  max-width: 1200px;
  margin: 0 auto;
}

.legal-section {
  margin-bottom: var(--spacing-xl);
}

.company-info {
  margin-top: var(--spacing-xl);
}

.card {
  background-color: var(--background-main);
  padding: var(--spacing-md);
  border-radius: var(--border-radius-md);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Sección de Responsabilidad */
.legal-section ul {
  list-style-type: disc;
  padding-left: 20px;
}

a {
  color: var(--link-color);
  text-decoration: none;
}
</style>