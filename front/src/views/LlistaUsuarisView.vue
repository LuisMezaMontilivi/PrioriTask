<template>
<v-card  class="mx-auto mt-14 centersheet">
    <v-card-title :style="style" >
                <h3 class="titl">Llistat d'Usuaris existents</h3>
            </v-card-title>
            <v-divider inset></v-divider>
            <v-card-text>
      <v-text-field
        :loading="loading"
        density="compact"
        variant="solo"
        label="Busca Usuaris"
        append-inner-icon="mdi-magnify"
        single-line
        hide-details
        v-model="searchTerm"
      ></v-text-field>
      
    </v-card-text>
    <v-list-item 
    v-for="usuari in llistaUsuarisFiltrada"
    :key="usuari.id"
    :title="usuari.nom"
    :subtitle="usuari.email"
    @click="reroute(usuari)"
    ></v-list-item>
      


</v-card>


</template>

<script>
import axios from 'axios';

export default{
    name: 'LlistatUsuaris',
    data: () => ({
        searchTerm:'',
        llistaUsuaris: []
  }),

  methods: {


    reroute(usuari){
            sessionStorage.setItem("usuariAModificar", JSON.stringify(usuari));
            this.$router.push("/modificar-usuari/" + usuari.id_usuari);
        },
    llistatUsuaris(){
      axios.put("http://localhost/api/usuari/llistat",{},{
        headers: {'token' : sessionStorage.token}
      })
      .then(resposta=>{
        console.log(resposta.data);
        this.llistaUsuaris = resposta.data;
      })
    }

    },
    computed:{
        llistaUsuarisFiltrada(){
            return this.searchTerm =="" ? this.llistaUsuaris :
            this.llistaUsuaris.filter((usuari)=> usuari.nom.toLowerCase().indexOf(this.searchTerm.toLowerCase()) >= 0)
        }
    },

    beforeMount(){
        this.llistatUsuaris();
    }

}

</script>

<style scoped>
.titl{
    text-align: center;
    padding: 0px;
    color: #232323;
}
</style>