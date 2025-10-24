<template>
  <div>
    <NavigationBar />
    <HeroSearch />
    <BooksTable />
    <FooterSection />
  </div>
</template>

<script lang="ts">
import NavigationBar from './components/NavigationBar.vue'
import HeroSearch from './components/HeroSearch.vue'
import BooksTable from './components/BooksTable.vue'
import FooterSection from './components/FooterSection.vue'

export default {
  components: {
    NavigationBar,
    HeroSearch,
    BooksTable,
    FooterSection,
  },
}
</script>
