<template>
  <v-sheet width="90%" class="mx-auto my-4">
    <h1>Llistat de tasques</h1>
    <v-btn color="primary" v-on:click="historic = !historic" class="mr-1">Mostrar tasques {{ txtBtnHistoric }}</v-btn>
  </v-sheet>
  
  <div v-if="!historic">
    <LlistatComp titol="Per fer" :tasques="perFer" color="#75FFFF"></LlistatComp>
    <LlistatComp titol="En progrés" :tasques="enCurs" color="#FFFF7D"></LlistatComp>
    <LlistatComp titol="Incidències" :tasques="incidencies" color="#FF7A90"></LlistatComp>
  </div>
  <div v-else>
    <LlistatComp titol="Finalitzades" :tasques="finalitzades" color="#0AFFBA"></LlistatComp>
    <LlistatComp titol="Arxivades" :tasques="arxiu" color="#696969"></LlistatComp>
</div>
</template>

<script>

import axios from 'axios';
import LlistatComp from '@/components/LlistatComp.vue';

export default {
  name: 'LlistatTascaVue',
  data: () => ({
    tasques: "",
    perFer: [],
    enCurs: [],
    incidencies: [],
    finalitzades: [],
    arxiu: [],
    historic: false,
    txtBtnHistoric: "fetes"
  }),
  components: {
    LlistatComp
  },
  methods: {
    recuperarTasques() {
      axios.put("http://prioritask.daw.institutmontilivi.cat/api/tasca/llistat", {}, {
        headers: { 'token': sessionStorage.getItem("token") }
      })
        .then(resposta => {
          this.organitzarTasques(resposta.data);
        })
    },
    organitzarTasques(tasquesOrganitzar) {
      for (var i = 0; i < tasquesOrganitzar.length; i++) {
        if (tasquesOrganitzar[i].data_acabament != null) {//si s'ha acabat
          this.finalitzades.push(tasquesOrganitzar[i]);
        }
        else if (tasquesOrganitzar[i].estat == "e") {//si hi ha inicidència
          this.incidencies.push(tasquesOrganitzar[i]);
        }
        else if (tasquesOrganitzar[i].estat == "a") {//si s'ha arxivat
          this.arxiu.push(tasquesOrganitzar[i]);
        }
        else if (tasquesOrganitzar[i].data_inici != null) {//si s'ha començat
          this.enCurs.push(tasquesOrganitzar[i]);
        }
        else {
          this.perFer.push(tasquesOrganitzar[i]);//si està per fer
        }
      }
    }
  },
  watch: {
    historic() {
      if (this.historic)
        this.txtBtnHistoric = "en procés";
      else
        this.txtBtnHistoric = "fetes"
    }
  },
  beforeMount() {
    this.recuperarTasques();
  }
}

</script>
