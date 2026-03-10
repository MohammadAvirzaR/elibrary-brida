<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen bg-gradient-to-b from-blue-700 to-blue-900 text-white shadow-xl transition-all duration-300 ease-in-out z-20',
        isSidebarOpen ? 'w-60' : 'w-20',
        'hidden md:block'
      ]"
    >
      <!-- Toggle Button -->
      <div class="flex items-center justify-center p-6 border-b border-blue-600">
        <button
          @click="toggleSidebar"
          class="p-2 hover:bg-blue-600 rounded-lg transition"
          :title="isSidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
        >
          <i-lucide-panel-left-close v-if="isSidebarOpen" class="w-6 h-6 text-white" />
          <i-lucide-panel-left-open v-else class="w-6 h-6 text-white" />
        </button>
      </div>

      <!-- Menu -->
      <nav class="mt-6">
        <router-link to="/dashboard" class="flex items-center gap-4 px-6 py-3 bg-blue-800 border-l-4 border-white group">
          <i-lucide-layout-dashboard class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Dashboard
          </span>
        </router-link>

        <!-- User Management - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/users"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-users class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            User Management
          </span>
        </router-link>

        <!-- Role Management - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/roles"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-shield-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Role Management
          </span>
        </router-link>

        <!-- Profile Management - Admin & Super Admin -->
        <router-link
          v-if="userRole === 'admin' || userRole === 'super_admin'"
          to="/profile-management"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-user-cog class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Profile Management
          </span>
        </router-link>

        <!-- Contributor Requests - Super Admin Only -->
        <router-link
          v-if="userRole === 'super_admin'"
          to="/contributor-requests"
          class="flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition group"
        >
          <i-lucide-file-check class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Contributor Requests
          </span>
        </router-link>

        <button @click="logout" class="w-full flex items-center gap-4 px-6 py-3 hover:bg-blue-800 transition mt-4 group">
          <i-lucide-log-out class="w-5 h-5 flex-shrink-0" />
          <span
            :class="[
              'font-semibold transition-opacity duration-300',
              isSidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'
            ]"
          >
            Log Out
          </span>
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <div
      :class="[
        'transition-all duration-300 ease-in-out min-h-screen',
        isSidebarOpen ? 'md:ml-60' : 'md:ml-20'
      ]"
    >
      <!-- Top Bar -->
      <header class="bg-gradient-to-r from-blue-500 to-blue-400 shadow-md sticky top-0 z-10">
        <div class="flex items-center justify-between px-4 md:px-6 lg:px-8 py-3 md:py-4">
          <!-- Mobile Menu Button -->
          <button
            @click="toggleSidebar"
            class="md:hidden p-2 hover:bg-blue-600 rounded-lg transition text-white"
          >
            <i-lucide-menu class="w-5 h-5" />
          </button>

          <!-- Search -->
          <div class="relative flex-1 max-w-md mx-4">
            <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 md:w-5 md:h-5 text-gray-500" />
            <input
              type="text"
              placeholder="Search"
              class="w-full pl-9 md:pl-10 pr-4 py-2 text-sm rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-blue-600"
            />
          </div>

          <!-- User Info -->
          <div class="flex items-center gap-2 md:gap-4">
            <div class="text-right hidden sm:block">
              <p class="font-bold text-gray-900 text-sm md:text-base">{{ username || 'Admin BRIDA' }}</p>
              <p class="text-xs md:text-sm text-gray-700 capitalize">{{ userRole || 'Super_admin' }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-blue-700 flex items-center justify-center text-white font-bold text-sm md:text-lg shadow-md">
              {{ username ? username.charAt(0).toUpperCase() : 'A' }}
            </div>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <main class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="mb-6 md:mb-8">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Welcome back, {{ username || 'Admin' }}</h1>
          <p class="text-sm md:text-base text-gray-600 mt-1">Here's what's happening with your library today.</p>
        </div>

        <!-- Summary Section -->
        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">Document Overview</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Queue Review Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow cursor-pointer group">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-clock class="w-6 h-6 text-white" />
                </div>
                <i-lucide-trending-up class="w-5 h-5 text-white opacity-70" />
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-1">Queue Review</h3>
              <p class="text-4xl font-bold text-white">{{ queueCount }}</p>
              <p class="text-white text-opacity-70 text-xs mt-2">Pending approval</p>
            </div>

            <!-- Top Contributor Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow cursor-pointer group">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-trophy class="w-6 h-6 text-white" />
                </div>
                <i-lucide-award class="w-5 h-5 text-white opacity-70" />
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-1">Top Contributor</h3>
              <p class="text-2xl font-bold text-white">{{ topContributor }}</p>
              <p class="text-white text-opacity-70 text-xs mt-2">Most uploads this month</p>
            </div>

            <!-- Top Viewed Card -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow cursor-pointer group">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-eye class="w-6 h-6 text-white" />
                </div>
                <i-lucide-flame class="w-5 h-5 text-white opacity-70" />
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-1">Top Viewed Today</h3>
              <p class="text-2xl font-bold text-white">{{ topArticle }}</p>
              <p class="text-white text-opacity-70 text-xs mt-2">Most popular document</p>
            </div>

            <!-- Total Papers Card -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow cursor-pointer group">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-book-open class="w-6 h-6 text-white" />
                </div>
                <i-lucide-bar-chart class="w-5 h-5 text-white opacity-70" />
              </div>
              <h3 class="text-white text-opacity-90 text-sm font-medium mb-1">Total Papers</h3>
              <p class="text-4xl font-bold text-white">{{ totalPapers }}</p>
              <p class="text-white text-opacity-70 text-xs mt-2">Documents in library</p>
            </div>
          </div>
        </section>

        <!-- Statistics Section -->
        <section class="mb-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i-lucide-bar-chart-3 class="w-6 h-6 text-blue-600" />
            Statistik Penelitian
          </h2>

          <!-- Statistics Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
            <!-- Total Research Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-file-text class="w-6 h-6 text-white" />
                </div>
              </div>
              <p class="text-white text-opacity-90 text-sm font-medium mb-1">JUMLAH KESELURUHAN PENELITIAN</p>
              <p class="text-4xl font-bold text-white">{{ totalResearch }}</p>
            </div>

            <!-- BRIDA Research Card -->
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-award class="w-6 h-6 text-white" />
                </div>
              </div>
              <p class="text-white text-opacity-90 text-sm font-medium mb-1">JUMLAH PENELITIAN BRIDA</p>
              <p class="text-4xl font-bold text-white">{{ bridaResearch }}</p>
            </div>

            <!-- Non-BRIDA Research Card -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
              <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-xl">
                  <i-lucide-book-open class="w-6 h-6 text-white" />
                </div>
              </div>
              <p class="text-white text-opacity-90 text-sm font-medium mb-1">JUMLAH PENELITIAN NON-BRIDA</p>
              <p class="text-4xl font-bold text-white">{{ nonBridaResearch }}</p>
            </div>
          </div>

          <!-- Charts Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pie Chart - Category -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i-lucide-pie-chart class="w-5 h-5 text-blue-600" />
                Jumlah Penelitian per Kategori
              </h3>
              <VueApexCharts
                type="pie"
                :options="categoryChartOptions"
                :series="categoryChartSeries"
                height="350"
              />
            </div>

            <!-- Line Chart - Trend -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i-lucide-trending-up class="w-5 h-5 text-green-600" />
                Tren Penelitian per Bulan dan Tahun
              </h3>
              <VueApexCharts
                type="line"
                :options="trendChartOptions"
                :series="trendChartSeries"
                height="350"
              />
            </div>
          </div>

          <!-- Bar Chart - Institution -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
              <i-lucide-building-2 class="w-5 h-5 text-orange-600" />
              Jumlah Penelitian per Instansi
            </h3>
            <VueApexCharts
              type="bar"
              :options="institutionChartOptions"
              :series="institutionChartSeries"
              height="400"
            />
          </div>
        </section>

        <!-- Queue Review Section -->
        <section class="mb-12">
          <div class="flex items-start md:items-center justify-between mb-6 flex-col md:flex-row gap-4">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i-lucide-inbox class="w-6 h-6 text-blue-600" />
                Queue Review
              </h2>
              <p class="text-gray-500 text-sm mt-1">Review and approve pending submissions</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              <div class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg">
                <span class="text-sm text-gray-600">Selected:</span>
                <span class="font-bold text-blue-600">{{ selectedQueue.length }}</span>
              </div>
              <button
                @click="approveSelected"
                :disabled="selectedQueue.length === 0"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2 shadow-sm hover:shadow"
              >
                <i-lucide-check class="w-4 h-4" />
                Approve
              </button>
              <button
                @click="rejectSelected"
                :disabled="selectedQueue.length === 0"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2 shadow-sm hover:shadow"
              >
                <i-lucide-x class="w-4 h-4" />
                Reject
              </button>
            </div>
          </div>

          <div class="bg-white rounded-xl md:rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="flex items-center gap-2 md:gap-4 p-3 md:p-5 border-b bg-gray-50">
              <button class="p-2 hover:bg-gray-200 rounded-lg transition">
                <i-lucide-filter class="w-4 h-4 md:w-5 md:h-5 text-gray-600" />
              </button>
              <div class="relative flex-1 max-w-md">
                <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="queueSearchQuery"
                  type="text"
                  placeholder="Search documents..."
                  class="w-full pl-9 md:pl-10 pr-4 py-2 md:py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                />
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-4 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAllQueue"
                        :checked="selectedQueue.length === queueReviews.length && queueReviews.length > 0"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Update</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">More</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                  <tr v-for="(item, index) in paginatedQueueReviews" :key="item.id" class="hover:bg-blue-50 transition-colors">
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        :value="item.id"
                        v-model="selectedQueue"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ (queueCurrentPage - 1) * queueRowsPerPage + index + 1 }}</td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                          {{ item.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                        </div>
                        <div>
                          <div class="text-sm font-semibold text-gray-900">{{ item.name }}</div>
                          <div class="text-xs text-gray-500">{{ item.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                      <router-link
                        :to="{ name: 'document-detail', params: { id: item.id } }"
                        class="text-sm text-blue-600 hover:text-blue-800 hover:underline line-clamp-2 font-medium"
                      >
                        {{ item.title }}
                      </router-link>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        <i-lucide-clock class="w-3 h-3" />
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      <div class="font-medium">{{ item.lastUpdate.date }}</div>
                      <div class="text-xs text-gray-500">{{ item.lastUpdate.time }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex gap-2">
                        <button
                          @click="approveItem(item.id)"
                          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm hover:shadow flex items-center gap-1.5"
                        >
                          <i-lucide-check class="w-3.5 h-3.5" />
                          Approve
                        </button>
                        <button
                          @click="rejectItem(item.id)"
                          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm hover:shadow flex items-center gap-1.5"
                        >
                          <i-lucide-x class="w-3.5 h-3.5" />
                          Reject
                        </button>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <button class="p-2 hover:bg-blue-100 rounded-lg transition group">
                        <i-lucide-mail class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 md:px-6 py-3 md:py-4 border-t bg-gray-50">
              <div class="text-xs sm:text-sm text-gray-600">
                <span v-if="filteredQueueReviews.length > 0">{{ queueStartIndex }}-{{ queueEndIndex }} of {{ filteredQueueReviews.length }}</span>
                <span v-else>No items</span>
              </div>
              <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2">
                  <span class="text-xs sm:text-sm text-gray-600">Rows:</span>
                  <select
                    v-model="queueRowsPerPage"
                    @change="queueCurrentPage = 1"
                    class="border border-gray-300 rounded px-2 py-1 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    @click="queuePrevPage"
                    :disabled="queueCurrentPage === 1"
                    class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    <i-lucide-chevron-left class="w-4 h-4 sm:w-5 sm:h-5" />
                  </button>
                  <span class="text-xs sm:text-sm text-gray-600">{{ queueCurrentPage }}/{{ queueTotalPages || 1 }}</span>
                  <button
                    @click="queueNextPage"
                    :disabled="queueCurrentPage >= queueTotalPages"
                    class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    <i-lucide-chevron-right class="w-4 h-4 sm:w-5 sm:h-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- History Section -->
        <section>
          <div class="flex items-start md:items-center justify-between mb-6 flex-col md:flex-row gap-4">
            <div>
              <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i-lucide-history class="w-6 h-6 text-gray-600" />
                Review History
              </h2>
              <p class="text-gray-500 text-sm mt-1">View all approved and rejected submissions</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              <div class="flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-lg">
                <span class="text-sm text-gray-600">Selected:</span>
                <span class="font-bold text-blue-600">{{ selectedHistory.length }}</span>
              </div>
              <button
                @click="deleteSelected"
                :disabled="selectedHistory.length === 0"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2 shadow-sm hover:shadow"
              >
                <i-lucide-trash-2 class="w-4 h-4" />
                Delete
              </button>
            </div>
          </div>

          <div class="bg-white rounded-xl md:rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="flex items-center gap-2 md:gap-4 p-3 md:p-5 border-b bg-gray-50">
              <button class="p-2 hover:bg-gray-200 rounded-lg transition">
                <i-lucide-filter class="w-4 h-4 md:w-5 md:h-5 text-gray-600" />
              </button>
              <div class="relative flex-1 max-w-md">
                <i-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                  v-model="historySearchQuery"
                  type="text"
                  placeholder="Search history..."
                  class="w-full pl-9 md:pl-10 pr-8 md:pr-10 py-2 md:py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                />
                <i-lucide-x class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 cursor-pointer hover:text-gray-600" @click="historySearchQuery = ''" />
              </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-4 text-left">
                      <input
                        type="checkbox"
                        @change="toggleSelectAllHistory"
                        :checked="selectedHistory.length === histories.length && histories.length > 0"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Update</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    <!-- <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">More</th> -->
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                  <tr v-for="(item, index) in paginatedHistories" :key="item.id" class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                      <input
                        type="checkbox"
                        :value="item.id"
                        v-model="selectedHistory"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                      />
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-500">{{ (historyCurrentPage - 1) * historyRowsPerPage + index + 1 }}</td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm">
                          {{ item.name.split(' ').map(n => n[0]).join('').substring(0, 2) }}
                        </div>
                        <div>
                          <div class="text-sm font-semibold text-gray-900">{{ item.name }}</div>
                          <div class="text-xs text-gray-500">{{ item.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 max-w-xs">
                      <router-link
                        :to="{ name: 'document-detail', params: { id: item.id } }"
                        class="text-sm text-blue-600 hover:text-blue-800 hover:underline line-clamp-2 font-medium"
                      >
                        {{ item.title }}
                      </router-link>
                    </td>
                    <td class="px-6 py-4">
                      <span
                        :class="[
                          'inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full border',
                          item.status === 'Accepted'
                            ? 'bg-green-100 text-green-800 border-green-200'
                            : 'bg-red-100 text-red-800 border-red-200'
                        ]"
                      >
                        <i-lucide-check-circle v-if="item.status === 'Accepted'" class="w-3 h-3" />
                        <i-lucide-x-circle v-else class="w-3 h-3" />
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      <div class="font-medium">{{ item.lastUpdate.date }}</div>
                      <div class="text-xs text-gray-500">{{ item.lastUpdate.time }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <button
                        @click="deleteItem(item.id)"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 hover:shadow-md transition-all duration-200"
                      >
                        <i-lucide-trash-2 class="w-4 h-4" />
                        Delete
                      </button>
                    </td>
                    <!-- <td class="px-6 py-4">
                      <button class="group p-2.5 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                        <i-lucide-mail class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" />
                      </button>
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 md:px-6 py-3 md:py-4 border-t bg-gray-50">
              <div class="text-xs sm:text-sm text-gray-600">
                <span v-if="filteredHistories.length > 0">{{ historyStartIndex }}-{{ historyEndIndex }} of {{ filteredHistories.length }}</span>
                <span v-else>No items</span>
              </div>
              <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2">
                  <span class="text-xs sm:text-sm text-gray-600">Rows:</span>
                  <select
                    v-model="historyRowsPerPage"
                    @change="historyCurrentPage = 1"
                    class="border border-gray-300 rounded px-2 py-1 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                  </select>
                </div>
                <div class="flex items-center gap-2">
                  <button
                    @click="historyPrevPage"
                    :disabled="historyCurrentPage === 1"
                    class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    <i-lucide-chevron-left class="w-4 h-4 sm:w-5 sm:h-5" />
                  </button>
                  <span class="text-xs sm:text-sm text-gray-600">{{ historyCurrentPage }}/{{ historyTotalPages || 1 }}</span>
                  <button
                    @click="historyNextPage"
                    :disabled="historyCurrentPage >= historyTotalPages"
                    class="p-1 hover:bg-gray-200 rounded disabled:opacity-50 disabled:cursor-not-allowed transition"
                  >
                    <i-lucide-chevron-right class="w-4 h-4 sm:w-5 sm:h-5" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'
import VueApexCharts from 'vue3-apexcharts'

const router = useRouter()
const { toast } = useToast()
const username = ref('')
const userRole = ref('')
const isSidebarOpen = ref(true)

const queueCount = ref(0)
const topContributor = ref('Memuat...')
const topArticle = ref('Memuat...')
const totalPapers = ref(0)

// Statistics Data
const totalResearch = ref(0)
const bridaResearch = ref(0)
const nonBridaResearch = ref(0)

// Chart Options & Series
const categoryChartOptions = ref({
  chart: {
    type: 'pie' as const,
    height: 350
  },
  labels: ['BRIDA', 'D1', 'D2', 'S1', 'S2'],
  colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
  legend: {
    position: 'bottom' as const,
    fontSize: '12px',
    fontFamily: 'inherit'
  },
  dataLabels: {
    style: {
      fontSize: '12px',
      fontWeight: 500
    }
  },
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        height: 300
      },
      legend: {
        position: 'bottom' as const,
        fontSize: '11px'
      }
    }
  }]
})
const categoryChartSeries = ref([5, 400, 380, 450, 405])

const trendChartOptions = ref({
  chart: {
    type: 'line' as const,
    height: 350,
    toolbar: {
      show: false
    }
  },
  stroke: {
    curve: 'smooth' as const,
    width: 3
  },
  colors: ['#3B82F6', '#F59E0B', '#10B981'],
  xaxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    labels: {
      style: {
        fontSize: '11px'
      }
    }
  },
  yaxis: {
    title: {
      text: 'Jumlah Penelitian',
      style: {
        fontSize: '12px',
        fontWeight: 500
      }
    },
    labels: {
      style: {
        fontSize: '11px'
      }
    }
  },
  legend: {
    position: 'top' as const,
    fontSize: '12px',
    fontFamily: 'inherit'
  },
  grid: {
    borderColor: '#e5e7eb'
  }
})
const trendChartSeries = ref([
  {
    name: 'Tahun 2024',
    data: [12, 18, 25, 30, 45, 55, 60, 72, 80, 85, 90, 95]
  },
  {
    name: 'Tahun 2025',
    data: [8, 15, 22, 35, 50, 65, 75, 88, 95, 100, 105, 110]
  },
  {
    name: 'Tahun 2026',
    data: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60]
  }
])

const institutionChartOptions = ref({
  chart: {
    type: 'bar' as const,
    height: 400,
    toolbar: {
      show: false
    }
  },
  plotOptions: {
    bar: {
      horizontal: true,
      borderRadius: 4
    }
  },
  colors: ['#3B82F6'],
  xaxis: {
    categories: [
      'UNIVERSITAS HALU OLEO',
      'STIKES KARYA KESEHATAN KENDARI',
      'UNIVERSITAS SULAWESI TENGGARA',
      'INSTITUT TEKNOLOGI DAN KESEHATAN AVICENNA',
      'UNIVERSITAS MANDIRI LUWUK BANGGAI',
      'UNIVERSITAS MUHAMMADIYAH KENDARI',
      'UNIVERSITAS LAKIDENDE',
      'UNIVERSITAS ISLAM NEGERI ALAUDDIN MAKASSAR'
    ],
    title: {
      text: 'Instansi',
      style: {
        fontSize: '12px',
        fontWeight: 500
      }
    },
    labels: {
      style: {
        fontSize: '10px'
      }
    }
  },
  yaxis: {
    title: {
      text: 'Jumlah Penelitian',
      style: {
        fontSize: '12px',
        fontWeight: 500
      }
    },
    labels: {
      style: {
        fontSize: '11px'
      }
    }
  },
  grid: {
    borderColor: '#e5e7eb'
  },
  dataLabels: {
    enabled: true,
    style: {
      fontSize: '11px'
    }
  }
})
const institutionChartSeries = ref([{
  name: 'Jumlah Penelitian',
  data: [1200, 800, 600, 400, 350, 300, 250, 200]
}])

const selectedQueue = ref<number[]>([])
const queueSearchQuery = ref('')
const queueRowsPerPage = ref(5)
const queueCurrentPage = ref(1)

interface QueueItem {
  id: number
  name: string
  email: string
  title: string
  status: string
  lastUpdate: {
    date: string
    time: string
  }
}

const queueReviews = ref<QueueItem[]>([])

const loadPendingDocuments = async () => {
  try {
    console.log('Loading pending documents...')
    console.log('Auth token exists:', !!localStorage.getItem('auth_token'))
    console.log('User role:', userRole.value)

    const response = await api.documents.review() as { success: boolean; data: DocumentResponse[] }
    console.log('Response from /documents/review:', response)

    if (response.success && response.data) {
      console.log('Found', response.data.length, 'pending documents')
      queueReviews.value = response.data.map((doc) => ({
        id: doc.id,
        name: doc.user?.name || 'Unknown',
        email: doc.user?.email || '',
        title: doc.title,
        status: 'Waiting',
        lastUpdate: {
          date: new Date(doc.created_at).toLocaleDateString('id-ID'),
          time: new Date(doc.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
        }
      }))
      queueCount.value = queueReviews.value.length
      console.log('Queue reviews updated:', queueReviews.value)
    } else {
      console.warn('No pending documents or invalid response structure')
      queueReviews.value = []
      queueCount.value = 0
    }
  } catch (error) {
    console.error('Gagal memuat dokumen pending:', error)
    if (error instanceof Error) {
      console.error('Error message:', error.message)

      if (error.message.includes('401') || error.message.includes('Unauthorized')) {
        toast.error('Sesi Berakhir', 'Silakan login kembali')
        router.push('/login')
      } else if (error.message.includes('403') || error.message.includes('Forbidden')) {
        toast.error('Akses Ditolak', 'Anda tidak memiliki izin untuk melihat queue review')
      } else if (error.message.includes('404') || error.message.includes('not found')) {
        console.warn('Review endpoint returned 404 - this might be a route matching issue')
        toast.warning('Endpoint Tidak Ditemukan', 'Pastikan backend berjalan dengan benar')
      }
    }
    queueReviews.value = []
    queueCount.value = 0
  }
}

const filteredQueueReviews = computed(() => {
  if (!queueSearchQuery.value) return queueReviews.value
  const query = queueSearchQuery.value.toLowerCase()
  return queueReviews.value.filter(item =>
    item.name.toLowerCase().includes(query) ||
    item.email.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query)
  )
})

const paginatedQueueReviews = computed(() => {
  const start = (queueCurrentPage.value - 1) * queueRowsPerPage.value
  const end = start + queueRowsPerPage.value
  return filteredQueueReviews.value.slice(start, end)
})

const queueTotalPages = computed(() => {
  return Math.ceil(filteredQueueReviews.value.length / queueRowsPerPage.value)
})

const queueStartIndex = computed(() => {
  return (queueCurrentPage.value - 1) * queueRowsPerPage.value + 1
})

const queueEndIndex = computed(() => {
  return Math.min(queueCurrentPage.value * queueRowsPerPage.value, filteredQueueReviews.value.length)
})

const queuePrevPage = () => {
  if (queueCurrentPage.value > 1) {
    queueCurrentPage.value--
  }
}

const queueNextPage = () => {
  if (queueCurrentPage.value < queueTotalPages.value) {
    queueCurrentPage.value++
  }
}

const selectedHistory = ref<number[]>([])
const historySearchQuery = ref('')
const historyRowsPerPage = ref(5)
const historyCurrentPage = ref(1)


interface HistoryItem {
  id: number
  name: string
  email: string
  title: string
  status: 'Accepted' | 'Rejected'
  lastUpdate: {
    date: string
    time: string
  }
}

interface DocumentResponse {
  id: number
  title: string
  status?: string
  created_at: string
  updated_at?: string
  user?: {
    name: string
    email: string
  }
  [key: string]: unknown
}

const histories = ref<HistoryItem[]>([])

const loadHistory = async () => {
  try {
    const response = await api.documents.getAll() as { success: boolean; data: DocumentResponse[] }
    if (response.success && response.data) {
      histories.value = response.data
        .filter(doc => doc.status === 'approved' || doc.status === 'rejected')
        .map((doc) => ({
          id: doc.id,
          name: doc.user?.name || 'Unknown',
          email: doc.user?.email || '',
          title: doc.title,
          status: doc.status === 'approved' ? 'Accepted' as const : 'Rejected' as const,
          lastUpdate: {
            date: new Date(doc.updated_at || doc.created_at).toLocaleDateString('id-ID'),
            time: new Date(doc.updated_at || doc.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
          }
        }))
    }
  } catch (error) {
    console.error('Gagal memuat riwayat:', error)
    histories.value = []
  }
}

const loadStats = async () => {
  try {
    const response = await api.documents.getAll() as { success: boolean; data: DocumentResponse[] }
    if (response.success && response.data) {
      const allDocs = response.data
      queueCount.value = allDocs.filter(doc => doc.status === 'pending').length
      totalPapers.value = allDocs.filter(doc => doc.status === 'approved').length

      const userCounts: Record<string, number> = {}
      allDocs.forEach(doc => {
        if (doc.user?.name) {
          userCounts[doc.user.name] = (userCounts[doc.user.name] || 0) + 1
        }
      })
      const topUser = Object.entries(userCounts).sort((a, b) => b[1] - a[1])[0]
      topContributor.value = topUser ? topUser[0] : 'Belum ada'

      const approvedDocs = allDocs.filter(doc => doc.status === 'approved')
      topArticle.value = approvedDocs[0]?.title || 'Belum ada'
    }
  } catch (error) {
    console.error('Gagal memuat statistik:', error)
  }
}

// TODO: Backend API - Create endpoint for statistics data
// Endpoint: GET /api/statistics
// Response structure:
// {
//   success: true,
//   data: {
//     total_research: 1640,
//     brida_research: 5,
//     non_brida_research: 1635,
//     category_data: { labels: [...], values: [...] },
//     trend_data: { years: [...], months: [...], values: [...] },
//     institution_data: { institutions: [...], counts: [...] }
//   }
// }
const loadStatistics = async () => {
  try {
    // TODO: Replace with actual API call
    // const response = await api.statistics.getAll()
    // if (response.success && response.data) {
    //   totalResearch.value = response.data.total_research
    //   bridaResearch.value = response.data.brida_research
    //   nonBridaResearch.value = response.data.non_brida_research
    //   categoryChartSeries.value = response.data.category_data.values
    //   trendChartSeries.value = response.data.trend_data.series
    //   institutionChartSeries.value = response.data.institution_data.series
    // }

    // MOCK DATA - Remove this when backend is ready
    totalResearch.value = 1640
    bridaResearch.value = 5
    nonBridaResearch.value = 1635

    // Mock data for charts (already set in initial ref values)
    console.log('Statistics loaded (mock data)')
  } catch (error) {
    console.error('Gagal memuat statistik:', error)
  }
}

const filteredHistories = computed(() => {
  if (!historySearchQuery.value) return histories.value
  const query = historySearchQuery.value.toLowerCase()
  return histories.value.filter(item =>
    item.name.toLowerCase().includes(query) ||
    item.email.toLowerCase().includes(query) ||
    item.title.toLowerCase().includes(query)
  )
})

const paginatedHistories = computed(() => {
  const start = (historyCurrentPage.value - 1) * historyRowsPerPage.value
  const end = start + historyRowsPerPage.value
  return filteredHistories.value.slice(start, end)
})

const historyTotalPages = computed(() => {
  return Math.ceil(filteredHistories.value.length / historyRowsPerPage.value)
})

const historyStartIndex = computed(() => {
  return (historyCurrentPage.value - 1) * historyRowsPerPage.value + 1
})

const historyEndIndex = computed(() => {
  return Math.min(historyCurrentPage.value * historyRowsPerPage.value, filteredHistories.value.length)
})

const historyPrevPage = () => {
  if (historyCurrentPage.value > 1) {
    historyCurrentPage.value--
  }
}

const historyNextPage = () => {
  if (historyCurrentPage.value < historyTotalPages.value) {
    historyCurrentPage.value++
  }
}

// Auto refresh data
let refreshInterval: ReturnType<typeof setInterval> | null = null

// Reset page when search query changes
watch(queueSearchQuery, () => {
  queueCurrentPage.value = 1
})

watch(historySearchQuery, () => {
  historyCurrentPage.value = 1
})

// Functions
onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      const user = JSON.parse(storedUser)
      username.value = user.name || user.username || 'Admin'
      userRole.value = user.role || ''
      console.log('User role:', userRole.value)
      console.log('User data:', user)
    } catch {
      username.value = 'Admin'
      userRole.value = ''
    }
  } else {
    router.push('/login')
    return
  }


  const allowedRoles = ['reviewer', 'super_admin']
  if (!allowedRoles.includes(userRole.value.toLowerCase())) {
    console.warn('User does not have permission to view dashboard. Role:', userRole.value)

    // Redirect admin to profile management
    if (userRole.value.toLowerCase() === 'admin') {
      router.push('/profile-management')
      return
    }

    toast.error('Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses dashboard admin')
    router.push('/')
    return
  }

  loadPendingDocuments()
  loadHistory()
  loadStats()
  loadStatistics()

  refreshInterval = setInterval(() => {
    loadPendingDocuments()
    loadHistory()
    loadStats()
    loadStatistics()
  }, 30000)
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push('/login')
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

// Queue Review Actions
const toggleSelectAllQueue = (e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  selectedQueue.value = checked ? queueReviews.value.map(item => item.id) : []
}

const approveItem = async (id: number) => {
  try {
    await api.documents.update(id, { status: 'approved' })
    const item = queueReviews.value.find(q => q.id === id)
    if (item) {
      histories.value.unshift({
        ...item,
        status: 'Accepted'
      })
      queueReviews.value = queueReviews.value.filter(q => q.id !== id)
      queueCount.value--
      toast.success('Dokumen Disetujui', `"${item.title}" telah disetujui`)
    }
  } catch (error) {
    console.error('Gagal menyetujui dokumen:', error)
    toast.error('Gagal Menyetujui', 'Terjadi kesalahan saat menyetujui dokumen')
  }
}

const rejectItem = async (id: number) => {
  try {
    await api.documents.update(id, { status: 'rejected' })
    const item = queueReviews.value.find(q => q.id === id)
    if (item) {
      histories.value.unshift({
        ...item,
        status: 'Rejected'
      })
      queueReviews.value = queueReviews.value.filter(q => q.id !== id)
      queueCount.value--
      toast.warning('Dokumen Ditolak', `"${item.title}" telah ditolak`)
    }
  } catch (error) {
    console.error('Gagal menolak dokumen:', error)
    toast.error('Gagal Menolak', 'Terjadi kesalahan saat menolak dokumen')
  }
}

const approveSelected = async () => {
  const count = selectedQueue.value.length
  for (const id of selectedQueue.value) {
    await approveItem(id)
  }
  selectedQueue.value = []
  if (count > 0) {
    toast.success('Dokumen Disetujui', `${count} dokumen berhasil disetujui`)
  }
}

const rejectSelected = async () => {
  const count = selectedQueue.value.length
  for (const id of selectedQueue.value) {
    await rejectItem(id)
  }
  selectedQueue.value = []
  if (count > 0) {
    toast.warning('Dokumen Ditolak', `${count} dokumen telah ditolak`)
  }
}

// History Actions
const toggleSelectAllHistory = (e: Event) => {
  const checked = (e.target as HTMLInputElement).checked
  selectedHistory.value = checked ? histories.value.map(item => item.id) : []
}

const deleteItem = async (id: number) => {
  const item = histories.value.find(h => h.id === id)

  if (!item) {
    return
  }

  if (!confirm(`Apakah Anda yakin ingin menghapus dokumen "${item.title}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
    return
  }

  try {
    const response = await api.documents.delete(id) as { success: boolean; message?: string }

    if (response.success) {
      histories.value = histories.value.filter(h => h.id !== id)
      toast.success('Dokumen Berhasil Dihapus', `"${item.title}" telah dihapus secara permanen`)
    } else {
      throw new Error(response.message || 'Gagal menghapus dokumen')
    }
  } catch (error) {
    console.error('Error deleting document:', error)

    let errorMessage = 'Gagal menghapus dokumen. Silakan coba lagi.'
    if (error instanceof Error) {
      if (error.message.includes('403') || error.message.includes('Forbidden')) {
        errorMessage = 'Anda tidak memiliki izin untuk menghapus dokumen ini.'
      } else if (error.message.includes('404')) {
        errorMessage = 'Dokumen tidak ditemukan.'
      } else if (error.message.includes('401') || error.message.includes('Unauthenticated')) {
        errorMessage = 'Sesi Anda telah berakhir. Silakan login kembali.'
      } else {
        errorMessage = `Gagal menghapus dokumen: ${error.message}`
      }
    }

    toast.error('Gagal Menghapus', errorMessage)
  }
}

const deleteSelected = async () => {
  const count = selectedHistory.value.length

  if (count === 0) {
    return
  }

  if (!confirm(`Apakah Anda yakin ingin menghapus ${count} dokumen?\n\nTindakan ini tidak dapat dibatalkan.`)) {
    return
  }

  let successCount = 0
  let failCount = 0

  for (const id of selectedHistory.value) {
    try {
      const response = await api.documents.delete(id) as { success: boolean; message?: string }
      if (response.success) {
        successCount++
      } else {
        failCount++
      }
    } catch (error) {
      console.error(`Error deleting document ${id}:`, error)
      failCount++
    }
  }

  // Reload histories to ensure sync with server
  await loadHistory()

  selectedHistory.value = []

  if (successCount > 0 && failCount === 0) {
    toast.success('Berhasil Dihapus', `${successCount} dokumen berhasil dihapus`)
  } else if (successCount > 0 && failCount > 0) {
    toast.warning('Sebagian Berhasil', `${successCount} dokumen berhasil dihapus, ${failCount} gagal`)
  } else {
    toast.error('Gagal Menghapus', `Semua dokumen gagal dihapus`)
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
