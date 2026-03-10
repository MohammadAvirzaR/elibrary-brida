<template>
  <div>
    <NavigationBar />
    <HeroSearch />
    <!-- <BooksTable /> -->
    <WebsiteProfile />
    <!-- <ImageSlider /> -->
    <FooterSection />
  </div>
</template>

<script lang="ts">
import NavigationBar from "@/components/NavigationBar.vue";
import HeroSearch from "@/components/HeroSearch.vue";
// import BooksTable from "@/components/BooksTable.vue";
import FooterSection from "@/components/FooterSection.vue";
import WebsiteProfile from "@/components/WebsiteProfile.vue";
// import ImageSlider from "@/components/ImageSlider.vue";

export default {
  components: {
    NavigationBar,
    HeroSearch,
    WebsiteProfile,
    // BooksTable,
    // ImageSlider,
    FooterSection,

  },
};
</script>
