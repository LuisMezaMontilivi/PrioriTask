<template>
    <v-app-bar color="primary">
        <v-row>
            <v-col cols="12" sm="1">
                <router-link :to="'/'">
                <v-img src="../assets/logo.svg" contain max-height="35px" />
                </router-link>
            </v-col>
            <v-col cols="12" sm="2">
                <v-toolbar-title class="d-flex justify-left">PrioriTask</v-toolbar-title>
            </v-col>
        </v-row>
        <v-toolbar-items>
            
            <v-btn v-for="btn in botons" :key="btn" @click="reroute(btn.ruta)" class="text-decoration-underline">
                    {{btn.txt}}
            </v-btn>
            <v-spacer></v-spacer>
           
                <v-btn color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
           
        </v-toolbar-items>

    </v-app-bar>

</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            // Array que guardarà els diferents botons amb les rutes que executaran
            botons: [
                {   txt: "Crear Usuari",
                    ruta: "/crear-usuari" },
                {   txt: "Modificar Usuari",
                    ruta: "/modificar-usuari" },
                {   txt: "Crear Tasca",
                    ruta: "/crear-tasca" },
                {   txt: "Modificar Tasca",
                    ruta: "/modificar-tasca" },
            ]
        }
    },
    methods: {
        reroute(ruta){
            this.$router.push(ruta);
        }
    }
}
</script>