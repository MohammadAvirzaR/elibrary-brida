<template>
  <div class="profile-container">
    <div class="profile-section">
      <h2 class="section-title">Profil Singkat</h2>
      <p class="section-content">
        Perpustakaan BRIDA merupakan pusat sumber belajar yang menyediakan berbagai koleksi buku, jurnal, dan sumber daya digital untuk mendukung kegiatan pembelajaran dan penelitian.
      </p>
    </div>

    <div class="profile-section">
      <h2 class="section-title">Visi</h2>
      <p class="section-content">
        Terwujudnya BRIDA Sebagai Poros Penelitian Dan Pengembangan Kebijakan Pembangunan Dan Inovasi Daerah Menuju Sulawesi Tenggara Yang Aman, Maju, Sejahtera Dan Bermartabat
      </p>
    </div>

    <div class="profile-section">
      <h2 class="section-title">Misi</h2>
      <ul class="misi-list">
        <li>Meningkatkan pelaksanaan dan pemanfaatan hasil-hasil penelitian dan pengembangan (Kelitbangan) untuk menunjang kebijakan dan program penyelenggaraan Pemerintahaan Daerah</li>
        <li>Meningkatkan implementasi Sistem Inovasi Daerah (SIDa) untuk mendorong efektivitas penyelenggaraan pemerintahan daerah, mendorong daya saing serta kemandirian lokal</li>
        <li>Meningkatkan sinergitas kelembagaan melalui koordinasi, fasilitasi, dan kerja sama kelitbangan dalam rangka pembangunan dan pembudayaan IPTEK di daerah</li>
      </ul>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'WebsiteProfile',
  data() {
    return {}
  }
}
</script>

<style scoped>
.profile-container {
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.profile-section {
  margin-bottom: 2rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 1rem;
  border-bottom: 2px solid #000000;
  padding-bottom: 0.5rem;
}

.section-content {
  font-size: 1rem;
  line-height: 1.6;
  color: #555;
  text-align: justify;
}

.misi-list {
  list-style-type: none;
  padding-left: 0;
}

.misi-list li {
  font-size: 1rem;
  line-height: 1.6;
  color: #555;
  margin-bottom: 1rem;
  padding-left: 1.5rem;
  position: relative;
  text-align: justify;
}

.misi-list li:before {
  content: "•";
  color: #007bff;
  font-weight: bold;
  position: absolute;
  left: 0;
}
</style>
