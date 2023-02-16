<template>
  <v-app>
    <header-comp/>
    <v-main>
      <router-view/>
    </v-main>
    <footer-comp></footer-comp>
  </v-app>
</template>

<script>
  import HeaderComp from './components/HeaderComp.vue';
  import FooterComp from './components/FooterComp.vue';

  export default {
    name: 'App',
    components: {
      HeaderComp,
      FooterComp
    }
  }
</script>

<style>
  v-main{
    height: 80%;
  }
</style>