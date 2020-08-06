<template>
  <div>
    <h2>News</h2>
    <span v-show="loading" class="spinner-border spinner-border-sm"></span>
  </div>
</template>

<script lang="ts">
export default {
  name: 'News',
  data() {
    return {
      loading: false,
      news: [],
    }
  },
  computed: {},
  created() {
    this.loadNews()
  },
  methods: {
    loadNews() {
      this.loading = true
      this.$store
        .dispatch('news/load')
        .then(news => {
          this.news = news
        })
        .finally(() => {
          this.loading = false
        })
    },
  },
}
</script>

<style scoped></style>
