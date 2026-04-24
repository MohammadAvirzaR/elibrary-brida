<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md border-b border-neutral-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3 py-3 sm:h-16 sm:py-0">
          <!-- Logo & Brand -->
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i-lucide-book-open class="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 class="text-base sm:text-lg font-bold text-neutral-900">E-Library BRIDA</h1>
              <p class="text-[11px] sm:text-xs text-neutral-500">Dashboard Pengguna</p>
            </div>
          </div>

          <!-- Navigation Items -->
          <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-4">
            <!-- Tombol Kembali ke Landing Page -->
            <button
              @click="goToLandingPage"
              class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 transition-colors"
            >
              <i-lucide-home class="w-4 h-4 sm:mr-2" />
              <span class="hidden sm:inline">Beranda</span>
            </button>

            <!-- Contributor Request Button / Dashboard Link -->
            <button
              v-if="userRole === 'contributor'"
              @click="goToContributorDashboard"
              class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-sm"
            >
              <i-lucide-upload class="w-4 h-4 sm:mr-2" />
              <span class="hidden sm:inline">Dashboard Kontributor</span>
              <span class="sm:hidden">Kontributor</span>
            </button>

            <button
              v-else-if="userRole === 'guest' && !hasPendingRequest"
              @click="openContributorWizard"
              class="inline-flex items-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 rounded-lg hover:from-green-700 hover:to-green-800 transition-all shadow-sm"
            >
              <i-lucide-file-text class="w-4 h-4 sm:mr-2" />
              <span class="hidden sm:inline">Jadi Kontributor</span>
              <span class="sm:hidden">Kontributor</span>
            </button>

            <div v-else-if="userRole === 'guest' && hasPendingRequest" class="flex items-center gap-2 px-3 sm:px-4 py-2 text-xs sm:text-sm bg-yellow-50 border border-yellow-200 rounded-lg">
              <i-lucide-clock class="w-4 h-4 text-yellow-600" />
              <span class="text-yellow-800 font-medium">Menunggu Persetujuan</span>
            </div>

            <!-- User Profile Dropdown -->
            <div class="relative">
              <button
                @click="toggleProfileMenu"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-neutral-100 transition-colors"
              >
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                  <span class="text-sm font-semibold text-white">{{ userInitials }}</span>
                </div>
                <i-lucide-chevron-down class="w-4 h-4 text-neutral-600" />
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showProfileMenu"
                class="absolute right-0 mt-2 w-max min-w-56 max-w-[85vw] bg-white rounded-lg shadow-lg border border-neutral-200 py-2"
              >
                <div class="px-4 py-3 border-b border-neutral-200">
                  <p class="text-sm font-semibold text-neutral-900">{{ userName }}</p>
                  <p class="text-xs text-neutral-500 break-all">{{ userEmail }}</p>
                </div>
                <button
                  @click="logout"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center"
                >
                  <i-lucide-log-out class="w-4 h-4 mr-2" />
                  Keluar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Welcome Header -->
    <div class="bg-white shadow-sm border-b border-neutral-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900">
              Selamat Datang, {{ userName }}
            </h1>
            <p class="text-sm text-neutral-600 mt-1">Kelola dokumen yang Anda unduh</p>
          </div>
          <div class="text-left sm:text-right">
            <p class="text-sm text-neutral-500">Status Akun</p>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
              Aktif
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Total Unduhan</p>
              <p class="text-3xl font-bold text-neutral-900 mt-2">{{ stats.totalDownloads }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <i-lucide-download class="w-6 h-6 text-blue-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">
            <span class="text-green-600 font-semibold">+{{ stats.thisMonth }}</span> bulan ini
          </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Favorit</p>
              <p class="text-3xl font-bold text-amber-600 mt-2">{{ stats.favorites }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
              <i-lucide-star class="w-6 h-6 text-amber-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">Dokumen tersimpan</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Kategori</p>
              <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.categories }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <i-lucide-folder class="w-6 h-6 text-green-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">Jenis dokumen berbeda</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-neutral-600">Terakhir Diunduh</p>
              <p class="text-3xl font-bold text-purple-600 mt-2">{{ stats.recent }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <i-lucide-clock class="w-6 h-6 text-purple-600" />
            </div>
          </div>
          <p class="text-xs text-neutral-500 mt-4">7 hari terakhir</p>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Download Section -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Quick Browse Card -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 sm:p-8 text-white">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1">
                <h2 class="text-xl sm:text-2xl font-bold mb-2">Telusuri Koleksi Dokumen</h2>
                <p class="text-blue-100 mb-6">
                  Temukan dan unduh dokumen yang Anda butuhkan
                </p>
                <button
                  @click="goToCatalog"
                  class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold inline-flex items-center gap-2 transition-colors shadow-md"
                >
                  <i-lucide-search class="w-5 h-5" />
                  Telusuri Dokumen
                </button>
              </div>
              <div class="hidden md:block">
                <i-lucide-book-open class="w-24 h-24 text-blue-400 opacity-50" />
              </div>
            </div>
          </div>

          <!-- Downloaded Documents Table -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h3 class="text-lg font-bold text-neutral-900">Dokumen yang Diunduh</h3>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                  <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari dokumen..."
                    class="w-full sm:w-auto px-4 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <select
                    v-model="filterCategory"
                    class="w-full sm:w-auto px-4 py-2 border border-neutral-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Semua Kategori</option>
                    <option value="penelitian">Penelitian</option>
                    <option value="laporan">Laporan</option>
                    <option value="artikel">Artikel</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full">
                <p class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">coming soon..</p>
                <!-- <thead class="bg-neutral-50 border-b border-neutral-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Judul Dokumen
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Kategori
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Tanggal Unduh
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                  <tr
                    v-for="doc in filteredDocuments"
                    :key="doc.id"
                    class="hover:bg-neutral-50 transition-colors"
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                          <i-lucide-file-text class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p class="text-sm font-medium text-neutral-900">{{ doc.title }}</p>
                          <p class="text-xs text-neutral-500">{{ doc.author }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                        {{ doc.category }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-neutral-600">
                      {{ formatDate(doc.downloadDate) }}
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <router-link
                          :to="{ name: 'document-detail', params: { id: doc.id } }"
                          class="text-blue-600 hover:text-blue-800 p-1"
                          title="Lihat Detail"
                        >
                          <i-lucide-eye class="w-4 h-4" />
                        </router-link>
                        <button
                          @click="downloadAgain(doc)"
                          class="text-green-600 hover:text-green-800 p-1"
                          title="Unduh Lagi"
                        >
                          <i-lucide-download class="w-4 h-4" />
                        </button>
                        <button
                          @click="toggleFavorite(doc)"
                          :class="doc.isFavorite ? 'text-amber-600' : 'text-neutral-400'"
                          class="hover:text-amber-800 p-1"
                          title="Favorit"
                        >
                          <i-lucide-star class="w-4 h-4" :class="{ 'fill-current': doc.isFavorite }" />
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredDocuments.length === 0">
                    <td colspan="4" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center justify-center text-neutral-400">
                        <i-lucide-inbox class="w-16 h-16 mb-4" />
                        <p class="text-lg font-medium">Belum ada dokumen yang diunduh</p>
                        <p class="text-sm mt-1">Mulai telusuri dan unduh dokumen dari katalog</p>
                      </div>
                    </td>
                  </tr>
                </tbody> -->
              </table>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Quick Tips -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <h3 class="text-lg font-bold text-neutral-900 mb-4 flex items-center gap-2">
              <i-lucide-lightbulb class="w-5 h-5 text-amber-500" />
              Tips Unduh Dokumen
            </h3>
            <ul class="space-y-3 text-sm text-neutral-700">
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Gunakan fitur pencarian untuk menemukan dokumen</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Tandai dokumen favorit untuk akses cepat</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Filter berdasarkan kategori untuk hasil lebih spesifik</span>
              </li>
              <li class="flex items-start gap-2">
                <i-lucide-check class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                <span>Unduh ulang dokumen kapan saja dari riwayat</span>
              </li>
            </ul>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <h3 class="text-lg font-bold text-neutral-900 mb-4 flex items-center gap-2">
              <i-lucide-activity class="w-5 h-5 text-blue-500" />
              Aktivitas Terbaru
            </h3>
            <div class="space-y-4">
              <div
                v-for="activity in recentActivities"
                :key="activity.id"
                class="flex items-start gap-3 pb-4 border-b border-neutral-100 last:border-0 last:pb-0"
              >
                <div :class="getActivityIconClass(activity.type)" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                  <component :is="getActivityIcon(activity.type)" class="w-4 h-4" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-neutral-900 font-medium">{{ activity.title }}</p>
                  <p class="text-xs text-neutral-500 mt-1">{{ activity.time }}</p>
                </div>
              </div>
              <div v-if="recentActivities.length === 0" class="text-center py-4">
                <p class="text-sm text-neutral-400">Belum ada aktivitas</p>
              </div>
            </div>
          </div>

          <!-- Help Card -->
          <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <i-lucide-help-circle class="w-10 h-10 mb-3 text-purple-200" />
            <h3 class="text-lg font-bold mb-2">Butuh Bantuan?</h3>
            <p class="text-sm text-purple-100 mb-4">
              Pelajari cara menggunakan sistem dengan panduan lengkap kami
            </p>
            <router-link
              to="/faq"
              class="inline-flex items-center gap-2 bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-50 transition-colors"
            >
              Lihat Panduan
              <i-lucide-arrow-right class="w-4 h-4" />
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Become Contributor Wizard Modal -->
    <div
      v-if="showContributorWizard"
      class="fixed inset-0 z-[70] flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
      @click.self="closeContributorWizard"
    >
      <div class="absolute inset-0 bg-black/45 backdrop-blur-[1px]"></div>

      <div class="relative z-10 w-full max-w-3xl rounded-2xl border border-neutral-200 bg-white shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-neutral-200 bg-white/95 px-5 py-4 backdrop-blur-sm">
          <div>
            <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Ajukan Menjadi Kontributor</h2>
            <p class="text-xs sm:text-sm text-neutral-500 mt-0.5">Langkah {{ contributorWizardStep }} dari 2</p>
          </div>
          <button
            type="button"
            class="rounded-lg p-2 text-neutral-500 hover:bg-neutral-100 hover:text-neutral-700 transition"
            @click="closeContributorWizard"
          >
            <i-lucide-x class="w-5 h-5" />
          </button>
        </div>

        <div class="px-5 sm:px-6 py-5 sm:py-6">
          <div class="mb-6 grid grid-cols-2 gap-2">
            <div :class="['h-1.5 rounded-full transition-colors', contributorWizardStep >= 1 ? 'bg-blue-600' : 'bg-neutral-200']"></div>
            <div :class="['h-1.5 rounded-full transition-colors', contributorWizardStep >= 2 ? 'bg-blue-600' : 'bg-neutral-200']"></div>
          </div>

          <div v-if="contributorWizardStep === 1" class="space-y-6">
            <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-5">
              <h3 class="text-2xl font-bold text-neutral-900 mb-5">Mengapa Menjadi Kontributor?</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="flex gap-3">
                  <i-lucide-upload-cloud class="w-7 h-7 text-blue-600 flex-shrink-0" />
                  <div>
                    <p class="font-bold text-neutral-900">Unggah Dokumen</p>
                    <p class="text-sm text-neutral-600 mt-1">Berbagi pengetahuan dan dokumentasi dengan komunitas.</p>
                  </div>
                </div>
                <div class="flex gap-3">
                  <i-lucide-users class="w-7 h-7 text-fuchsia-600 flex-shrink-0" />
                  <div>
                    <p class="font-bold text-neutral-900">Kolaborasi</p>
                    <p class="text-sm text-neutral-600 mt-1">Bekerja sama dengan profesional lainnya.</p>
                  </div>
                </div>
                <div class="flex gap-3">
                  <i-lucide-award class="w-7 h-7 text-green-600 flex-shrink-0" />
                  <div>
                    <p class="font-bold text-neutral-900">Pengakuan</p>
                    <p class="text-sm text-neutral-600 mt-1">Dapatkan pengakuan dan kredit atas kontribusi Anda.</p>
                  </div>
                </div>
                <div class="flex gap-3">
                  <i-lucide-trending-up class="w-7 h-7 text-orange-600 flex-shrink-0" />
                  <div>
                    <p class="font-bold text-neutral-900">Perkembangan</p>
                    <p class="text-sm text-neutral-600 mt-1">Tingkatkan visibilitas dan jangkauan karya Anda.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end">
              <button
                type="button"
                class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
                @click="goToContributorWizardStep(2)"
              >
                Lanjut Isi Form
              </button>
            </div>
          </div>

          <div v-else class="space-y-5">
            <div v-if="contributorSuccessMessage" class="rounded-lg border border-green-200 bg-green-50 p-4">
              <p class="text-sm font-medium text-green-800">{{ contributorSuccessMessage }}</p>
            </div>
            <div v-if="contributorErrorMessage" class="rounded-lg border border-red-200 bg-red-50 p-4">
              <p class="text-sm font-medium text-red-700">{{ contributorErrorMessage }}</p>
            </div>

            <form @submit.prevent="submitContributorRequest" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">Nama Anda</label>
                <input
                  v-model="contributorForm.name"
                  type="text"
                  class="w-full rounded-lg border border-neutral-300 bg-neutral-100 px-4 py-2.5 text-neutral-600"
                  disabled
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">E-mail Anda</label>
                <input
                  v-model="contributorForm.email"
                  type="email"
                  class="w-full rounded-lg border border-neutral-300 bg-neutral-100 px-4 py-2.5 text-neutral-600"
                  disabled
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-1.5">
                  Alasan Ingin Menjadi Kontributor <span class="text-red-500">*</span>
                </label>
                <textarea
                  v-model="contributorForm.message"
                  rows="5"
                  maxlength="1000"
                  :disabled="isSubmittingContributor"
                  class="w-full rounded-lg border border-neutral-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Tulis alasan Anda ingin menjadi kontributor"
                />
                <div class="mt-1 flex items-center justify-between text-xs text-neutral-500">
                  <span>Minimal 10 karakter</span>
                  <span>{{ contributorForm.message.length }} / 1000</span>
                </div>
              </div>

              <div class="pt-2 flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                <button
                  type="button"
                  class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-neutral-200 text-neutral-800 font-semibold hover:bg-neutral-300 transition"
                  @click="goToContributorWizardStep(1)"
                >
                  Kembali
                </button>
                <button
                  type="submit"
                  :disabled="isSubmittingContributor || contributorForm.message.trim().length < 10"
                  class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 disabled:bg-neutral-400 disabled:cursor-not-allowed transition"
                >
                  <span v-if="isSubmittingContributor" class="inline-flex items-center gap-2">
                    <i-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    Mengirim...
                  </span>
                  <span v-else>Ajukan Permintaan</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

// User info
const userName = ref('')
const userEmail = ref('')
const userRole = ref('')

// UI State for navbar
const showProfileMenu = ref(false)
const hasPendingRequest = ref(false)
const showContributorWizard = ref(false)
const contributorWizardStep = ref<1 | 2>(1)
const isSubmittingContributor = ref(false)
const contributorErrorMessage = ref('')
const contributorSuccessMessage = ref('')

const contributorForm = ref({
  name: '',
  email: '',
  message: ''
})

// Computed user initials
const userInitials = computed(() => {
  if (!userName.value) return 'U'
  return userName.value
    .split(' ')
    .map(n => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
})

interface DocumentItem {
  id: number
  title: string
  author: string
  category: string
  downloadDate: string
  isFavorite: boolean
}

interface ActivityItem {
  id: number
  type: string
  title: string
  time: string
}

interface ApiDocumentResponse {
  id: number
  title: string
  author: string
  category_name?: string
  created_at: string
  is_favorite?: boolean
  [key: string]: unknown
}

const stats = ref({
  totalDownloads: 0,
  thisMonth: 0,
  favorites: 0,
  categories: 0,
  recent: 0
})

const documents = ref<DocumentItem[]>([])
const searchQuery = ref('')
const filterCategory = ref('')
const recentActivities = ref<ActivityItem[]>([])

// Filtered documents
// const filteredDocuments = computed(() => {
//   let filtered = documents.value

//   if (searchQuery.value) {
//     filtered = filtered.filter(doc =>
//       doc.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
//       doc.author.toLowerCase().includes(searchQuery.value.toLowerCase())
//     )
//   }

//   if (filterCategory.value) {
//     filtered = filtered.filter(doc => doc.category === filterCategory.value)
//   }

//   return filtered
// })

// Load user data
onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userName.value = user.name || user.username || 'User'
    userEmail.value = user.email || ''
  }

  loadDocuments()
  loadStats()
  loadRecentActivities()
})

const loadDocuments = async () => {
  try {
    const response = await api.documents.search('', 1, 100) as { data: ApiDocumentResponse[] }
    if (response.data) {
      // Filter hanya dokumen yang sudah approved untuk user
      const approvedDocs = response.data.filter((doc: ApiDocumentResponse) => doc.status === 'approved')
      documents.value = approvedDocs.map((doc) => ({
        id: doc.id,
        title: doc.title,
        author: doc.author,
        category: doc.category_name || 'Umum',
        downloadDate: doc.created_at,
        isFavorite: false
      }))
    }
  } catch (error) {
    console.error('Gagal memuat dokumen:', error)
    documents.value = []
  }
}

const loadStats = async () => {
  const sevenDaysAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)
  const thisMonthStart = new Date(new Date().getFullYear(), new Date().getMonth(), 1)

  stats.value = {
    totalDownloads: documents.value.length,
    thisMonth: documents.value.filter(d => new Date(d.downloadDate) >= thisMonthStart).length,
    favorites: documents.value.filter(d => d.isFavorite).length,
    categories: new Set(documents.value.map(d => d.category)).size,
    recent: documents.value.filter(d => new Date(d.downloadDate) >= sevenDaysAgo).length
  }
}

const loadRecentActivities = async () => {
  const latestDocs = documents.value
    .sort((a, b) => new Date(b.downloadDate).getTime() - new Date(a.downloadDate).getTime())
    .slice(0, 5)

  recentActivities.value = latestDocs.map((doc, index) => ({
    id: index + 1,
    type: 'download',
    title: `Mengunduh: ${doc.title}`,
    time: formatTimeAgo(doc.downloadDate)
  }))
}

const formatTimeAgo = (date: string): string => {
  const now = new Date()
  const past = new Date(date)
  const diffMs = now.getTime() - past.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMins / 60)
  const diffDays = Math.floor(diffHours / 24)

  if (diffMins < 60) return `${diffMins} menit yang lalu`
  if (diffHours < 24) return `${diffHours} jam yang lalu`
  if (diffDays < 7) return `${diffDays} hari yang lalu`
  return formatDate(date)
}

// Activity helpers
const getActivityIconClass = (type: string) => {
  const classes: Record<string, string> = {
    download: 'bg-blue-100 text-blue-600',
    favorite: 'bg-amber-100 text-amber-600',
    view: 'bg-green-100 text-green-600'
  }
  return classes[type] || 'bg-neutral-100 text-neutral-600'
}

const getActivityIcon = (type: string) => {
  const icons: Record<string, string> = {
    download: 'i-lucide-download',
    favorite: 'i-lucide-star',
    view: 'i-lucide-eye'
  }
  return icons[type] || 'i-lucide-bell'
}

// Format date
const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Navbar functions
const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value
}

// Navigation functions
const goToLandingPage = () => {
  router.push('/')
}

const openContributorWizard = () => {
  contributorWizardStep.value = 1
  contributorErrorMessage.value = ''
  contributorSuccessMessage.value = ''
  contributorForm.value.name = userName.value || 'User'
  contributorForm.value.email = userEmail.value || ''
  contributorForm.value.message = ''
  showContributorWizard.value = true
}

const closeContributorWizard = () => {
  if (isSubmittingContributor.value) return
  showContributorWizard.value = false
}

const goToContributorWizardStep = (step: 1 | 2) => {
  contributorWizardStep.value = step
}

const submitContributorRequest = async () => {
  if (contributorForm.value.message.trim().length < 10) {
    contributorErrorMessage.value = 'Alasan harus minimal 10 karakter.'
    return
  }

  isSubmittingContributor.value = true
  contributorErrorMessage.value = ''

  try {
    const response = await api.contributorRequests.submit(contributorForm.value.message) as {
      success: boolean
      message?: string
    }

    if (response.success) {
      hasPendingRequest.value = true
      contributorSuccessMessage.value = response.message || 'Permintaan berhasil dikirim. Tim super admin akan meninjau permintaan Anda.'
      contributorForm.value.message = ''

      setTimeout(() => {
        showContributorWizard.value = false
      }, 1000)
    }
  } catch (error) {
    contributorErrorMessage.value = error instanceof Error ? error.message : 'Gagal mengirim permintaan.'
  } finally {
    isSubmittingContributor.value = false
  }
}

const goToContributorDashboard = () => {
  router.push('/contributor-dashboard')
}

const goToCatalog = () => {
  router.push('/catalog')
}

const checkPendingRequest = async () => {
  if (userRole.value === 'guest') {
    try {
      const response = await api.contributorRequests.checkPending() as {
        success: boolean;
        has_pending: boolean;
        has_rejected: boolean;
      }
      if (response.success) {
        if (response.has_pending) {
          hasPendingRequest.value = true
        } else if (response.has_rejected) {
          // User has rejected request, they can submit new request
          hasPendingRequest.value = false
        }
      }
    } catch (error) {
      console.error('Gagal memeriksa status permintaan:', error)
    }
  }
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  localStorage.removeItem('last_known_role')
  router.push('/login')
}

// Close profile menu when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showProfileMenu.value = false
  }
}

onMounted(async () => {
  // Load user data
  const userStr = localStorage.getItem('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      userName.value = user.name || 'User'
      userEmail.value = user.email || ''
      userRole.value = user.role || 'guest'
    } catch {
      userName.value = 'User'
      userEmail.value = ''
      userRole.value = 'guest'
    }
  }

  // Check if user has pending contributor request
  await checkPendingRequest()

  // Load dashboard data
  loadStats()
  loadRecentActivities()

  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// const downloadAgain = async (doc: DocumentItem) => {
//   try {
//     window.open(`${API_BASE_URL}/documents/${doc.id}/download`, '_blank')
//   } catch (error) {
//     console.error('Gagal mengunduh dokumen:', error)
//     alert('Gagal mengunduh dokumen. Silakan coba lagi.')
//   }
// }

// const toggleFavorite = async (doc: DocumentItem) => {
//   const previousState = doc.isFavorite
//   doc.isFavorite = !doc.isFavorite

//   try {
//     await api.documents.update(doc.id, {
//       is_favorite: doc.isFavorite
//     })
//     loadStats()
//   } catch (error) {
//     doc.isFavorite = previousState
//     console.error('Gagal mengubah status favorit:', error)
//   }
// }

// interface ImportMetaEnv {
//   VITE_API_BASE_URL?: string
// }

// interface ImportMeta {
//   env: ImportMetaEnv
// }

// const API_BASE_URL = (import.meta as unknown as ImportMeta).env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api'
</script>
