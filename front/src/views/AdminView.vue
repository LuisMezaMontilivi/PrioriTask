<template>
    <v-sheet width="90%" class="mx-auto my-4">
        <h1>Administrador</h1>
    </v-sheet>
    <div class="divider">
        <v-sheet width="90%" class="mx-auto">
            <v-btn class="mt-4" block color="primary" size="x-large" route to='/crear-usuari'>
                Crear usuari
            </v-btn>
            <v-btn class="mt-4" block color="primary" size="x-large" route to='/llista-usuari'>
                Modificar usuari
            </v-btn>
            <v-btn class="mt-4" block color="primary" size="x-large" route to='/crear-tasca'>
                Crear tasca
            </v-btn>
            <v-btn class="mt-4" block color="primary" size="x-large" route to='/modificar-tasca'>
                Modificar tasca
            </v-btn>
        </v-sheet>
    </div>
    <div class="divider">
        <v-sheet width="90%" class="mx-auto">
            <GChart type="ColumnChart" :data="dadesUsuaris" :options="opcionsGraficUsuari" />

            <GChart type="PieChart" :data="dadesTasques" :options="opcionsGraficTasques" />
        </v-sheet>
    </div>

</template>

<script>
import { GChart } from 'vue-google-charts'
import axios from 'axios'

export default {
    props: ["dadesGrafic"],
    data() {
        return {
            cruUsuaris: {},
            cruTasques: {},
            estatsTecnics: [],
            dadesUsuaris: [
                ['Periodicitat', 'Usuaris connectats']
            ],
            opcionsGraficUsuari: {
                vAxis: {
                    ticks: [0, 2, 4, 6, 8, 10, 12, 14]
                },
                title: "Estadístiques de connexions dels usuaris",
                backgroundColor: {
                    fillOpacity: 0
                },
                height: 350
            },
            dadesTasques: [
                ['Estat', 'Quantitat']
            ],
            opcionsGraficTasques: {
                legend: 'none',
                title: "Estadístiques de tasques del darrer mes",
                colors: ['#75FFFF', '#FFFF7D','#0AFFBA','#FF7A90','#696969'],
                pieSliceTextStyle: {
                    color: "black"
                },
                backgroundColor: {
                    fillOpacity: 0
                },
                height: 350
            }
        }
    },
    components: {
        GChart
    },
    methods: {
        //fer les peticions ajax per recuperar les estadístiques
        recuperarEstadistiquesUsuaris() {
            axios.put("http://prioritask.daw.institutmontilivi.cat/api/usuari/data", {}, {
                headers: {
                    'token': sessionStorage.getItem("token")
                }
            })
                .then(resposta => {
                    console.log(resposta.data);
                    this.cruUsuaris = resposta.data[0];
                    this.dadesUsuaris.push(['Dia', parseInt(this.cruUsuaris.ultim_dia)]);
                    this.dadesUsuaris.push(['Setmana', parseInt(this.cruUsuaris.ultima_setmana)]);
                    this.dadesUsuaris.push(['Mes', parseInt(this.cruUsuaris.ultim_mes)]);
                    this.dadesUsuaris.push(['Any', parseInt(this.cruUsuaris.ultim_any)]);
                })
        },
        recuperarEstadistiquesTasques() {
            axios.put("http://prioritask.daw.institutmontilivi.cat/api/tasca/data", {}, {
                headers: {
                    'token': sessionStorage.getItem("token")
                }
            })
                .then(resposta => {
                    this.cruTasques = resposta.data;
                    var formatat = this.formatarTasques(this.cruTasques);
                    this.dadesTasques.push(['Per fer',parseInt(formatat.mes_actual.s)]);
                    this.dadesTasques.push(['En progrés',parseInt(formatat.mes_actual.p)]);
                    this.dadesTasques.push(['Fet',parseInt(formatat.mes_actual.d)]);
                    this.dadesTasques.push(['Incidència',parseInt(formatat.mes_actual.e)]);
                    this.dadesTasques.push(['Arxivat',parseInt(formatat.mes_actual.a)]);
                })
        },
        //classificar per la propietat mes els diferents moments del gràfic, faltaría aprofitar la informació de pendents per informar-ho a la pàgina d'admin
        formatarTasques(data) {
            const result = {};
            for (const obj of data) {
                const mes = obj.mes;
                if (!result[mes]) {
                    result[mes] = [];
                }
                result[mes].push({ [obj.estat]: obj.tasques });
            }
            const formattedResult = {};
            for (const [mes, values] of Object.entries(result)) {
                const estats = {};
                for (const item of values) {
                    const estat = Object.keys(item)[0];
                    const tasques = item[estat];
                    estats[estat] = tasques;
                }
                formattedResult[mes] = estats;
            }
            return formattedResult;
        }
    },
    created() {
        this.recuperarEstadistiquesUsuaris();
        this.recuperarEstadistiquesTasques();
    }
}

</script>