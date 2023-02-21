<template>
    <v-app-bar flat location="bottom"  color="secondary">
      <v-row justify="center" no-gutters>
      <v-col class="text-left mt-4 ml-1 info" min-width="75px" cols="5">
        <div @click="reroute()"><strong>Informació legal i Ús de cookies</strong></div>
       
      </v-col>
      <v-col class="text-right mt-4" min-width="75px"  cols="5">
        {{ new Date().getFullYear() }} — <strong>PrioriTask</strong>
      </v-col>




    </v-row>
  </v-app-bar>
</template>

<script>
export default {
  methods: {
    reroute(){
        this.$router.push('/about');
    }
  }
}
</script>

<style scoped>
  .info{
    cursor: pointer;
  }
</style>
