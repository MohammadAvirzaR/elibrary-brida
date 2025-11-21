<template>
  <Teleport to="body">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8"
        @click.stop
      >
        <!-- Header -->
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i-lucide-mail class="w-8 h-8 text-blue-600" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi OTP</h2>
          <p class="text-sm text-gray-600">
            Masukkan kode 6 digit yang telah dikirim ke<br />
            <span class="font-semibold text-gray-900">{{ email }}</span>
          </p>
        </div>

        <!-- OTP Input -->
        <div class="mb-6">
          <div class="flex gap-2 justify-center">
            <input
              v-for="(digit, index) in otpDigits"
              :key="index"
              :ref="el => otpInputs[index] = el as HTMLInputElement"
              v-model="otpDigits[index]"
              type="text"
              inputmode="numeric"
              maxlength="1"
              class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none transition"
              @input="handleInput(index, $event)"
              @keydown="handleKeydown(index, $event)"
              @paste="handlePaste"
            />
          </div>
          <p v-if="errorMessage" class="mt-3 text-sm text-red-600 text-center">
            {{ errorMessage }}
          </p>
        </div>

        <!-- Timer and Resend -->
        <div class="text-center mb-6">
          <p v-if="timeRemaining > 0" class="text-sm text-gray-600">
            Kode akan kadaluarsa dalam <span class="font-semibold text-blue-600">{{ formatTime(timeRemaining) }}</span>
          </p>
          <button
            v-else
            @click="$emit('resend')"
            :disabled="isResending"
            class="text-sm text-blue-600 hover:text-blue-700 font-semibold disabled:opacity-50"
          >
            {{ isResending ? 'Mengirim ulang...' : 'Kirim ulang kode OTP' }}
          </button>
        </div>

        <!-- Actions -->
        <div class="flex gap-3">
          <button
            @click="$emit('close')"
            :disabled="isVerifying"
            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition disabled:opacity-50"
          >
            Batal
          </button>
          <button
            @click="handleVerify"
            :disabled="!isOtpComplete || isVerifying"
            class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2"
          >
            <i-lucide-loader-2 v-if="isVerifying" class="w-4 h-4 animate-spin" />
            <span>{{ isVerifying ? 'Memverifikasi...' : 'Verifikasi' }}</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

interface Props {
  email: string
  expiresIn?: number
  isResending?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  expiresIn: 60,
  isResending: false
})

const emit = defineEmits<{
  close: []
  verify: [otp: string]
  resend: []
}>()

const otpDigits = ref<string[]>(['', '', '', '', '', ''])
const otpInputs = ref<HTMLInputElement[]>([])
const isVerifying = ref(false)
const errorMessage = ref('')
const timeRemaining = ref(props.expiresIn)
let timerInterval: number | null = null

const isOtpComplete = computed(() => {
  return otpDigits.value.every(digit => digit !== '')
})

const handleInput = (index: number, event: Event) => {
  const input = event.target as HTMLInputElement
  const value = input.value

  // Only allow numbers
  if (value && !/^\d$/.test(value)) {
    otpDigits.value[index] = ''
    return
  }

  // Move to next input
  if (value && index < 5) {
    otpInputs.value[index + 1]?.focus()
  }

  errorMessage.value = ''
}

const handleKeydown = (index: number, event: KeyboardEvent) => {
  // Handle backspace
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1]?.focus()
  }

  // Handle arrow keys
  if (event.key === 'ArrowLeft' && index > 0) {
    otpInputs.value[index - 1]?.focus()
  }
  if (event.key === 'ArrowRight' && index < 5) {
    otpInputs.value[index + 1]?.focus()
  }
}

const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault()
  const pasteData = event.clipboardData?.getData('text') || ''
  const digits = pasteData.replace(/\D/g, '').slice(0, 6).split('')

  digits.forEach((digit, index) => {
    if (index < 6) {
      otpDigits.value[index] = digit
    }
  })

  // Focus on the first empty input or the last one
  const firstEmpty = otpDigits.value.findIndex(d => d === '')
  const focusIndex = firstEmpty === -1 ? 5 : firstEmpty
  otpInputs.value[focusIndex]?.focus()

  errorMessage.value = ''
}

const handleVerify = () => {
  if (!isOtpComplete.value) {
    errorMessage.value = 'Silakan masukkan semua digit OTP'
    return
  }

  const otp = otpDigits.value.join('')
  isVerifying.value = true
  emit('verify', otp)
}

const formatTime = (seconds: number) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const startTimer = () => {
  timerInterval = window.setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--
    } else {
      if (timerInterval) clearInterval(timerInterval)
    }
  }, 100)
}

watch(() => props.expiresIn, (newValue) => {
  timeRemaining.value = newValue
})

onMounted(() => {
  // Focus first input
  otpInputs.value[0]?.focus()
  startTimer()
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

defineExpose({
  setError: (message: string) => {
    errorMessage.value = message
    isVerifying.value = false
  },
  reset: () => {
    otpDigits.value = ['', '', '', '', '', '']
    errorMessage.value = ''
    isVerifying.value = false
    otpInputs.value[0]?.focus()
  }
})
</script>
