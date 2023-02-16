<template>
    <v-sheet width="90%" class="mx-auto my-4">
        <h1>Modificar tasca #{{ tasca.id_tasca }}</h1>
    </v-sheet>
    <v-form validate-on="submit" @submit.prevent="submit">
        <div class="divider">
            <v-sheet width="90%" class="mx-auto">

                <h2>Títol</h2>
                <v-text-field v-model="titol" label="Inserta el títol de la tasca" clearable :rules="rules"
                    :disabled="!esGestor"></v-text-field>
                <h2>Descripció</h2>
                <v-textarea v-model="descripcio" label="Inserta la descripció de la tasca" clearable :rules="rules"
                    :disabled="!esGestor"></v-textarea>
                <h2>Prioritat</h2>
                <div class="prior">
                    <v-slider :min="1" :max="9" step="1" show-ticks="always" v-model="prioritat"
                        :disabled="!esGestor"></v-slider>
                </div>
                <div class="txtPrio">
                    <v-text-field :min="1" :max="9" v-model="prioritat" type="number"
                        :disabled="!esGestor"></v-text-field>
                </div>

            </v-sheet>
        </div>
        <div class="divider">
            <v-sheet width="90%" class="mx-auto">
                <h2>Tècnic encarregat</h2>
                <v-autocomplete clearable label="Llistat de tècnics" :items="tecnics" item-title="nom"
                    item-value="id_usuari" :rules="rules" v-model="tecnicAssignat" :disabled="!esGestor">
                </v-autocomplete>
                <v-table>
                    <tbody>
                        <tr>
                            <td>Data creació: </td>
                            <td>{{ tasca.data_alta }}</td>
                        </tr>
                        <tr>
                            <td>Data inici:</td>
                            <td v-if="tasca.data_inici">{{ tasca.data_inici }}</td>
                            <td v-else>Sense</td>
                        </tr>
                        <tr>
                            <td>Data de fi:</td>
                            <td v-if="tasca.data_acabament">{{ tasca.data_acabament }}</td>
                            <td v-else>Sense</td>
                        </tr>
                    </tbody>
                </v-table>
                <v-container>
                    <v-row no-gutters>
                        <v-col>
                            <v-sheet class="pa-2 ma-2">
                                <v-btn v-bind:class="{ 'seleccionat': estat === 'p' }" size="large" block
                                    @click="canviEstat('p')" color="warning">En progrés</v-btn>
                            </v-sheet>
                        </v-col>
                        <v-col>
                            <v-sheet class="pa-2 ma-2">
                                <v-btn v-bind:class="{ 'seleccionat': estat === 'd' }" size="large" block
                                    @click="canviEstat('d')" color="success">Finalitzar</v-btn>
                            </v-sheet>
                        </v-col>

                        <v-responsive width="100%"></v-responsive>

                        <v-col>
                            <v-sheet class="pa-2 ma-2">
                                <v-btn v-bind:class="{ 'seleccionat': estat === 'e' }" block size="large"
                                    @click="canviEstat('e')" color="error">Incidència</v-btn>
                            </v-sheet>
                        </v-col>

                        <v-col>
                            <v-sheet class="pa-2 ma-2">
                                <v-btn v-bind:class="{ 'seleccionat': estat === 'a' }" block size="large"
                                    @click="canviEstat('a')" color="primary">Arxivar</v-btn>
                            </v-sheet>
                        </v-col>
                    </v-row>
                </v-container>
            </v-sheet>
        </div>
        <v-sheet width="90%" class="mx-auto">
            <h2>Comentari</h2>
            <v-textarea v-model="comentari" label="Inserta un comentari sobre la tasca" clearable :rules="rules"
                :disabled="esGestor"></v-textarea>
        </v-sheet>
        <div class="send">
            <v-btn type="submit" block class="mt-5" @click="modificarTasca">Modificar tasca</v-btn>
        </div>
    </v-form>

</template>

<script>
import axios from 'axios';

export default {
    name: 'ModificarTascaView',
    data() {
        return {
            prioritat: 3,
            titol: "",
            descripcio: "",
            comentari: "",
            tecnicAssignat: undefined,
            estat: undefined,
            tecnics: [],
            llistatTecnics: [],
            tasca: {},
            esGestor: undefined,
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
        modificarTasca() {
            if (this.titol != "" && this.descripcio != "" && this.tecnicAssignat !== undefined && this.tecnicAssignat != "") {
                axios.put("http://prioritask.daw.institutmontilivi.cat/api/tasca/modificar", {}, {
                    headers: {
                        'token': sessionStorage.getItem("token"),
                        'tasca': '{"id":' + this.tasca.id_tasca + ',"titol":"' + this.titol + '","descripcio":"' + this.descripcio + '","prioritat":' + this.prioritat + ',"estat":"' + this.estat + '","gestor":"' + sessionStorage.getItem("token") + '","tecnic":' + this.tecnicAssignat + ', "comentari":"' + this.comentari+'"}'
                    }
                })
                    .then(resposta => {
                        var codi = resposta.status;
                        if (codi == '200')
                            this.$router.push('/modificar-tasca');
                    })
            }
        },
        canviEstat(estat) {
            console.log(estat);
            this.estat = estat;
        }
    },
    beforeMount() {
        if (sessionStorage.getItem('tasca')) {
            this.tasca = JSON.parse(sessionStorage.getItem('tasca'));
            console.log(this.tasca);
            this.titol = this.tasca.titol;
            this.descripcio = this.tasca.descripcio;
            this.tecnicAssignat = this.tasca.id_tecnic;
            this.prioritat = this.tasca.prioritat;
            this.estat = this.tasca.estat;
            this.comentari = this.tasca.comentari ? this.tasca.comentari : "";
        }
        var rol = sessionStorage.getItem('rol');
        console.log(rol);
        if (rol) {
            this.esGestor = rol == 'g' || rol == 'a';
            console.log(this.esGestor);
        }
        if (this.esGestor)
            this.recuperarTecnics();
    }
}
</script>

<style scoped>
.seleccionat {
    box-shadow: 0px 0px 44px 9px rgba(0, 0, 0, 0.75);
}

</style>