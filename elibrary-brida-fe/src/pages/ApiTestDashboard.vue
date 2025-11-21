<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-8">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">
          🔍 API Test Dashboard
        </h1>
        <p class="text-gray-600">Real-time API endpoint testing and visualization</p>
        <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm">
          <div class="w-3 h-3 rounded-full animate-pulse" :class="isBackendRunning ? 'bg-green-500' : 'bg-red-500'"></div>
          <span class="text-sm font-medium">{{ isBackendRunning ? 'Backend Online' : 'Backend Offline' }}</span>
        </div>
      </div>

      <!-- Overall Status -->
      <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">System Status</h2>
            <p class="text-gray-600 mt-1">Last checked: {{ lastChecked }}</p>
          </div>
          <button
            @click="runAllTests"
            :disabled="isTestingAll"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2"
          >
            <i-lucide-refresh-cw class="w-5 h-5" :class="{ 'animate-spin': isTestingAll }" />
            {{ isTestingAll ? 'Testing...' : 'Test All Endpoints' }}
          </button>
        </div>

        <!-- Progress Bar -->
        <div v-if="isTestingAll" class="mt-4">
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: `${testProgress}%` }"
            ></div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mt-6">
          <div class="text-center p-4 bg-green-50 rounded-xl">
            <div class="text-3xl font-bold text-green-600">{{ passedTests }}</div>
            <div class="text-sm text-gray-600 mt-1">Passed</div>
          </div>
          <div class="text-center p-4 bg-red-50 rounded-xl">
            <div class="text-3xl font-bold text-red-600">{{ failedTests }}</div>
            <div class="text-sm text-gray-600 mt-1">Failed</div>
          </div>
          <div class="text-center p-4 bg-yellow-50 rounded-xl">
            <div class="text-3xl font-bold text-yellow-600">{{ pendingTests }}</div>
            <div class="text-sm text-gray-600 mt-1">Pending</div>
          </div>
          <div class="text-center p-4 bg-blue-50 rounded-xl">
            <div class="text-3xl font-bold text-blue-600">{{ totalTests }}</div>
            <div class="text-sm text-gray-600 mt-1">Total</div>
          </div>
        </div>
      </div>

      <!-- API Endpoints Tests -->
      <div class="grid grid-cols-1 gap-4">
        <!-- Login Test -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <div
                  class="w-12 h-12 rounded-lg flex items-center justify-center"
                  :class="getStatusColor(tests.login.status)"
                >
                  <i-lucide-log-in v-if="tests.login.status === 'idle'" class="w-6 h-6 text-gray-400" />
                  <i-lucide-loader-2 v-else-if="tests.login.status === 'testing'" class="w-6 h-6 text-blue-600 animate-spin" />
                  <i-lucide-check v-else-if="tests.login.status === 'success'" class="w-6 h-6 text-green-600" />
                  <i-lucide-x v-else class="w-6 h-6 text-red-600" />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Login Endpoint</h3>
                  <p class="text-sm text-gray-600">POST /api/login</p>
                </div>
              </div>

              <div v-if="tests.login.response" class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-xs font-mono text-gray-700">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="font-semibold">Status:</span>
                    <span :class="tests.login.status === 'success' ? 'text-green-600' : 'text-red-600'">
                      {{ tests.login.response.status || tests.login.response }}
                    </span>
                  </div>
                  <div v-if="tests.login.response.user" class="space-y-1">
                    <div>👤 User: {{ tests.login.response.user.name }}</div>
                    <div>📧 Email: {{ tests.login.response.user.email }}</div>
                    <div>🔐 Role: {{ tests.login.response.user.role }}</div>
                    <div>🎫 Token: {{ tests.login.response.token.substring(0, 30) }}...</div>
                  </div>
                </div>
              </div>
            </div>

            <button
              @click="testLogin"
              :disabled="tests.login.status === 'testing'"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all text-sm"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Documents List Test -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <div
                  class="w-12 h-12 rounded-lg flex items-center justify-center"
                  :class="getStatusColor(tests.documents.status)"
                >
                  <i-lucide-file-text v-if="tests.documents.status === 'idle'" class="w-6 h-6 text-gray-400" />
                  <i-lucide-loader-2 v-else-if="tests.documents.status === 'testing'" class="w-6 h-6 text-blue-600 animate-spin" />
                  <i-lucide-check v-else-if="tests.documents.status === 'success'" class="w-6 h-6 text-green-600" />
                  <i-lucide-x v-else class="w-6 h-6 text-red-600" />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Get Documents</h3>
                  <p class="text-sm text-gray-600">GET /api/documents</p>
                </div>
              </div>

              <div v-if="tests.documents.response" class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-xs font-mono text-gray-700">
                  <div v-if="tests.documents.response.data" class="space-y-1">
                    <div>📚 Total Documents: {{ tests.documents.response.data.length }}</div>
                    <div>⏳ Pending: {{ tests.documents.response.data.filter(d => d.status === 'pending').length }}</div>
                    <div>✅ Approved: {{ tests.documents.response.data.filter(d => d.status === 'approved').length }}</div>
                    <div v-if="tests.documents.response.data.length > 0" class="mt-2 pt-2 border-t">
                      <div class="font-semibold mb-1">Latest Document:</div>
                      <div>• {{ tests.documents.response.data[0].title }}</div>
                      <div>• Status: {{ tests.documents.response.data[0].status }}</div>
                    </div>
                  </div>
                  <div v-else class="text-red-600">{{ tests.documents.response }}</div>
                </div>
              </div>
            </div>

            <button
              @click="testDocuments"
              :disabled="tests.documents.status === 'testing' || !authToken"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all text-sm"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Upload Test -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <div
                  class="w-12 h-12 rounded-lg flex items-center justify-center"
                  :class="getStatusColor(tests.upload.status)"
                >
                  <i-lucide-upload v-if="tests.upload.status === 'idle'" class="w-6 h-6 text-gray-400" />
                  <i-lucide-loader-2 v-else-if="tests.upload.status === 'testing'" class="w-6 h-6 text-blue-600 animate-spin" />
                  <i-lucide-check v-else-if="tests.upload.status === 'success'" class="w-6 h-6 text-green-600" />
                  <i-lucide-x v-else class="w-6 h-6 text-red-600" />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Upload Endpoint</h3>
                  <p class="text-sm text-gray-600">POST /api/documents/upload</p>
                </div>
              </div>

              <div v-if="tests.upload.response" class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-xs font-mono text-gray-700">
                  <div v-if="tests.upload.response.success !== undefined">
                    <div class="space-y-1">
                      <div>✅ Success: {{ tests.upload.response.success }}</div>
                      <div>💬 Message: {{ tests.upload.response.message }}</div>
                      <div v-if="tests.upload.response.data">
                        <div class="mt-2 pt-2 border-t">
                          <div>📄 Title: {{ tests.upload.response.data.title }}</div>
                          <div>👤 Author: {{ tests.upload.response.data.author }}</div>
                          <div>🆔 ID: {{ tests.upload.response.data.id }}</div>
                          <div>📊 Status: {{ tests.upload.response.data.status }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-red-600">{{ tests.upload.response }}</div>
                </div>
              </div>
            </div>

            <button
              @click="testUpload"
              :disabled="tests.upload.status === 'testing' || !authToken"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all text-sm"
            >
              Test
            </button>
          </div>
        </div>

        <!-- Review Queue Test -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3">
                <div
                  class="w-12 h-12 rounded-lg flex items-center justify-center"
                  :class="getStatusColor(tests.review.status)"
                >
                  <i-lucide-clipboard-check v-if="tests.review.status === 'idle'" class="w-6 h-6 text-gray-400" />
                  <i-lucide-loader-2 v-else-if="tests.review.status === 'testing'" class="w-6 h-6 text-blue-600 animate-spin" />
                  <i-lucide-check v-else-if="tests.review.status === 'success'" class="w-6 h-6 text-green-600" />
                  <i-lucide-x v-else class="w-6 h-6 text-red-600" />
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Review Queue</h3>
                  <p class="text-sm text-gray-600">GET /api/documents/review</p>
                </div>
              </div>

              <div v-if="tests.review.response" class="mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="text-xs font-mono text-gray-700">
                  <div v-if="tests.review.response.data">
                    <div>📋 Documents in Queue: {{ tests.review.response.data.length }}</div>
                  </div>
                  <div v-else class="text-red-600">{{ tests.review.response }}</div>
                </div>
              </div>
            </div>

            <button
              @click="testReview"
              :disabled="tests.review.status === 'testing' || !authToken"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all text-sm"
            >
              Test
            </button>
          </div>
        </div>
      </div>

      <!-- API Base URL Info -->
      <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
        <div class="flex items-center gap-2 text-blue-900">
          <i-lucide-info class="w-5 h-5" />
          <span class="font-semibold">API Base URL:</span>
          <code class="px-2 py-1 bg-white rounded">http://127.0.0.1:8000/api</code>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const API_BASE_URL = 'http://127.0.0.1:8000/api'

interface TestResult {
  status: 'idle' | 'testing' | 'success' | 'error'
  response: any
}

const tests = ref({
  login: { status: 'idle', response: null } as TestResult,
  documents: { status: 'idle', response: null } as TestResult,
  upload: { status: 'idle', response: null } as TestResult,
  review: { status: 'idle', response: null } as TestResult,
})

const authToken = ref<string | null>(null)
const isBackendRunning = ref(false)
const isTestingAll = ref(false)
const lastChecked = ref('')

const totalTests = computed(() => 4)
const passedTests = computed(() => Object.values(tests.value).filter(t => t.status === 'success').length)
const failedTests = computed(() => Object.values(tests.value).filter(t => t.status === 'error').length)
const pendingTests = computed(() => Object.values(tests.value).filter(t => t.status === 'idle').length)
const testProgress = computed(() => {
  const completed = passedTests.value + failedTests.value
  return (completed / totalTests.value) * 100
})

const getStatusColor = (status: string) => {
  switch (status) {
    case 'success': return 'bg-green-100'
    case 'error': return 'bg-red-100'
    case 'testing': return 'bg-blue-100'
    default: return 'bg-gray-100'
  }
}

const updateLastChecked = () => {
  const now = new Date()
  lastChecked.value = now.toLocaleTimeString()
}

const testLogin = async () => {
  tests.value.login.status = 'testing'
  tests.value.login.response = null
  updateLastChecked()

  try {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: 'admin@brida.com',
        password: 'admin123'
      })
    })

    const data = await response.json()

    if (response.ok && data.token) {
      authToken.value = data.token
      tests.value.login.status = 'success'
      tests.value.login.response = data
      isBackendRunning.value = true
    } else {
      tests.value.login.status = 'error'
      tests.value.login.response = data.message || 'Login failed'
    }
  } catch (error) {
    tests.value.login.status = 'error'
    tests.value.login.response = 'Connection failed: ' + (error as Error).message
    isBackendRunning.value = false
  }
}

const testDocuments = async () => {
  if (!authToken.value) {
    tests.value.documents.response = 'Please login first'
    return
  }

  tests.value.documents.status = 'testing'
  tests.value.documents.response = null
  updateLastChecked()

  try {
    const response = await fetch(`${API_BASE_URL}/documents`, {
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Accept': 'application/json'
      }
    })

    const data = await response.json()

    if (response.ok) {
      tests.value.documents.status = 'success'
      tests.value.documents.response = data
    } else {
      tests.value.documents.status = 'error'
      tests.value.documents.response = data.message || 'Failed to fetch documents'
    }
  } catch (error) {
    tests.value.documents.status = 'error'
    tests.value.documents.response = 'Connection failed: ' + (error as Error).message
  }
}

const testUpload = async () => {
  if (!authToken.value) {
    tests.value.upload.response = 'Please login first'
    return
  }

  tests.value.upload.status = 'testing'
  tests.value.upload.response = null
  updateLastChecked()

  try {
    // Create a simple test PDF
    const pdfContent = '%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\nxref\n0 1\ntrailer<</Size 1/Root 1 0 R>>\nstartxref\n50\n%%EOF'
    const blob = new Blob([pdfContent], { type: 'application/pdf' })
    const file = new File([blob], 'test-document.pdf', { type: 'application/pdf' })

    const formData = new FormData()
    formData.append('file', file)
    formData.append('title', 'API Test Document')
    formData.append('description', 'This is an automated test document created by the API test dashboard')
    formData.append('category', 'penelitian')
    formData.append('year', '2024')
    formData.append('author', 'API Test System')
    formData.append('publisher', 'BRIDA Test')
    formData.append('keywords', 'test, api, automated')

    const response = await fetch(`${API_BASE_URL}/documents/upload`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Accept': 'application/json'
      },
      body: formData
    })

    const data = await response.json()

    if (response.ok) {
      tests.value.upload.status = 'success'
      tests.value.upload.response = data
    } else {
      tests.value.upload.status = 'error'
      tests.value.upload.response = data.message || 'Upload failed'
    }
  } catch (error) {
    tests.value.upload.status = 'error'
    tests.value.upload.response = 'Connection failed: ' + (error as Error).message
  }
}

const testReview = async () => {
  if (!authToken.value) {
    tests.value.review.response = 'Please login first'
    return
  }

  tests.value.review.status = 'testing'
  tests.value.review.response = null
  updateLastChecked()

  try {
    const response = await fetch(`${API_BASE_URL}/documents/review`, {
      headers: {
        'Authorization': `Bearer ${authToken.value}`,
        'Accept': 'application/json'
      }
    })

    const data = await response.json()

    if (response.ok) {
      tests.value.review.status = 'success'
      tests.value.review.response = data
    } else {
      tests.value.review.status = 'error'
      tests.value.review.response = data.message || 'Failed to fetch review queue'
    }
  } catch (error) {
    tests.value.review.status = 'error'
    tests.value.review.response = 'Connection failed: ' + (error as Error).message
  }
}

const runAllTests = async () => {
  isTestingAll.value = true

  await testLogin()
  await new Promise(resolve => setTimeout(resolve, 500))

  if (authToken.value) {
    await testDocuments()
    await new Promise(resolve => setTimeout(resolve, 500))

    await testUpload()
    await new Promise(resolve => setTimeout(resolve, 500))

    await testReview()
  }

  isTestingAll.value = false
}

onMounted(() => {
  updateLastChecked()
  // Auto-run tests on mount
  runAllTests()
})
</script>
