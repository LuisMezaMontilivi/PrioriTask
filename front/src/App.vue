<template>
  <v-app>
    <header-comp :infoHeader="infoHeader"/>
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
    data(){
      return{
        infoHeader : {colorHeader : "primary", rol : null, logejat : false }
        
      }
     
    },
    components: {
      HeaderComp,
      FooterComp
    },
    watch:{
       $route(from, to){
        console.log(from + " " + to);
        if(sessionStorage.colorHeader != undefined){this.infoHeader.colorHeader = sessionStorage.colorHeader }
        this.infoHeader.rol = sessionStorage.rol;
        this.infoHeader.logejat = sessionStorage.logejat;
        console.log(this.infoHeader);

       }
    }

  }
</script>

<style>
  v-main{
    height: 80%;
  }
</style>