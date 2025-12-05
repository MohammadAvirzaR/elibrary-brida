<template>
  <footer class="bg-neutral-100 mt-20 py-12 border-t border-gray-200">
    <div
      class="container mx-auto grid md:grid-cols-4 gap-10 text-sm text-neutral-950 px-6"
    >
      <div class="flex flex-col items-center">
        <img
          src="@/assets/brin-logo-trans.png"
          alt="Logo BRIN"
          class="h-20 mb-4"
        />

        <div class="flex justify-center items-center gap-4">
          <a href="https://www.instagram.com/brida.provsultra/" target="_blank">
            <i-lucide-instagram
              class="w-6 h-6 text-neutral-900 hover:text-pink-500 transition"
            />
          </a>
          <a href="https://x.com/kemenkumsultra" target="_blank">
            <i-simple-icons-x
              class="w-6 h-6 text-neutral-900 hover:text-neutral-500 transition"
            />
          </a>
          <a
            href="https://www.youtube.com/results?search_query=brida+sultra"
            target="_blank"
          >
            <i-lucide-youtube
              class="w-6 h-6 text-neutral-900 hover:text-red-600 transition"
            />
          </a>
          <a href="https://brida.sultraprov.go.id/" target="_blank">
            <i-lucide-globe
              class="w-6 h-6 text-neutral-900 hover:text-blue-600 transition"
            />
          </a>
        </div>
      </div>

      <div>
        <h4 class="font-heading font-semibold text-neutral-950 mb-3 text-sm">
          Alamat & Kontak
        </h4>
        <p class="mb-1">(021) 88888</p>
        <p class="mb-1">elibrarybrida@mail.go.id</p>
        <p>Jl. Pegangsaan Timur No. 56</p>
      </div>

      <div>
        <h4 class="font-heading font-semibold text-neutral-950 mb-3 text-sm">
          Navigasi
        </h4>
        <ul class="space-y-1">
          <li>
            <a href="#" class="hover:text-neutral-800 transition">Tentang Kami</a>
          </li>
          <li>
            <button @click="handleUploadClick" class="hover:text-neutral-800 transition cursor-pointer"
              >Unggah Mandiri</button>
          </li>
          <li><a href="#" class="hover:text-neutral-800 transition">FAQ</a></li>
          <li><router-link to="/login" class="hover:text-neutral-800 transition">Login</router-link></li>
          <li>
            <router-link to="/register" class="hover:text-neutral-800 transition">Register</router-link>
          </li>
        </ul>
      </div>

      <div>
        <h4 class="font-heading font-semibold text-neutral-950 mb-3 text-sm">
          Deskripsi
        </h4>
        <p class="font-body text-justify text-neutral-800 leading-relaxed">
          Kami berkomitmen untuk terus melakukan penelitian sebagai andil ikut
          serta dalam mencerdaskan kehidupan bangsa.
        </p>
        <p class="mt-4 text-xs text-gray-500">
          © E-Library BRIDA Sulawesi Tenggara 2025
        </p>
      </div>
    </div>
  </footer>
</template>
<script setup lang="ts">
import { ref} from 'vue'
import { useRouter} from 'vue-router'

const router = useRouter()


// Authentication state
const isAuthenticated = ref(false)



const handleUploadClick = () => {
  if (!isAuthenticated.value) {
    router.push('/login')
  } else {
    router.push('/upload-document')
  }
}


</script>
