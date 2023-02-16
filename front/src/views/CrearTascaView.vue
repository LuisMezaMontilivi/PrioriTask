<template>
    <v-sheet width="90%" class="mx-auto my-4">
        <h1>Tasca nova</h1>
    </v-sheet>
    <v-form validate-on="submit" @submit.prevent="submit">
        <div class="divider">
            <v-sheet width="90%" class="mx-auto">

                <h2>Títol</h2>
                <v-text-field v-model="titol" label="Inserta el títol de la tasca" clearable
                    :rules="rules" autofocus></v-text-field>
                <h2>Descripció</h2>
                <v-textarea v-model="descripcio" label="Inserta la descripció de la tasca" clearable
                    :rules="rules"></v-textarea>
                <h2>Prioritat</h2>
                <div class="prior">
                    <v-slider :min="1" :max="9" step="1" show-ticks="always" v-model="prioritat"></v-slider>
                </div>
                <div class="txtPrio">
                    <v-text-field :min="1" :max="9" v-model="prioritat" type="number"></v-text-field>
                </div>

            </v-sheet>
        </div>
        <div class="divider">
            <v-sheet width="90%" class="mx-auto">
                <h2>Tècnic encarregat</h2>
                <v-autocomplete clearable label="Llistat de tècnics" :items="tecnics" item-title="nom"
                    item-value="id_usuari" :rules="rules" v-model="tecnicAssignat">
                </v-autocomplete>
            </v-sheet>
        </div>
        <div class="send">
            <v-btn type="submit" block class="mt-5" @click="crearTasca">Crear tasca</v-btn>
        </div>
    </v-form>
    
</template>

<script>
import axios from 'axios';

export default {
    name: 'CrearTascaView',
    data() {
        return {
            prioritat: 3,
            titol: "",
            descripcio: "",
            tecnicAssignat: undefined,
            tecnics: [],
            llistatTecnics: [],
            rules: [
                value => {
                    if (value) return true
                    return 'Camp requerit'
                },
            ],
        }
    },
    methods: {
        recuperarTecnics() {
            axios.put("http://prioritask.daw.institutmontilivi.cat/api/usuari/llistat_tecnics", {}, {
                headers: { 'token': sessionStorage.getItem("token") }
            })
                .then(resposta => {
                    this.tecnics = resposta.data;
                })
        },
        crearTasca() {
            if(this.titol != "" && this.descripcio != "" && this.tecnicAssignat !== undefined && this.tecnicAssignat != ""){
                axios.put("http://prioritask.daw.institutmontilivi.cat/api/tasca/crear", {}, {
                headers: {
                    'token': sessionStorage.getItem("token"),
                    'tasca': '{"titol":"'+this.titol+'","descripcio":"'+this.descripcio+'","prioritat":'+this.prioritat+',"estat":"s","gestor":"'+sessionStorage.getItem("token")+'","tecnic":'+this.tecnicAssignat+'}'
                }
                })
                    .then(resposta => {
                        var codi = resposta.status;
                        if(codi=='201')
                            this.$router.push('/modificar-tasca');
                    })
            }
        }
    },
    beforeMount() {
        this.recuperarTecnics();
    }
}
</script>
