<template>
    <v-app-bar :color="infoHeader.colorHeader" :style="style" v-if="midaGran">
        <v-row>
            <v-col cols="12" sm="1">
                <router-link :to="'/'">
                <v-img src="../assets/logo.png" contain max-height="35px" />
                </router-link>
            </v-col>
            <v-col cols="12" sm="2">
                <v-toolbar-title class="d-flex justify-left">
                    <div v-if="infoHeader.rol == 'a'" class="direccio" @click="reroute('/principal-admin')">PrioriTask</div>
                    <div v-else>PrioriTask</div>
                </v-toolbar-title>
            </v-col>
        </v-row>
        <v-toolbar-items v-if="infoHeader.rol == 'a'">
            <v-btn v-for="btn in botons" :key="btn" @click="reroute(btn.ruta)" class="text-decoration-underline">
                    {{btn.txt}}
            </v-btn>
            <v-spacer></v-spacer>
                <v-btn v-if="!infoHeader.logejat" color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
                <v-btn v-if="infoHeader.logejat" color="pink" @click="logout()">Logout</v-btn>
        </v-toolbar-items>

        <v-toolbar-items v-if="infoHeader.rol == undefined">
           
            <v-spacer></v-spacer>
                <v-btn v-if="!infoHeader.logejat" color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
                <v-btn v-if="infoHeader.logejat" color="pink" @click="logout()">Logout</v-btn>
        </v-toolbar-items>

        <v-toolbar-items v-if="infoHeader.rol == 't'">
            <v-btn v-for="btn in botonsTecnic" :key="btn" @click="reroute(btn.ruta)" class="text-decoration-underline">
                    {{btn.txt}}
            </v-btn>
           <v-spacer></v-spacer>
               <v-btn v-if="!infoHeader.logejat" color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
               <v-btn v-if="infoHeader.logejat" color="pink" @click="logout()">Logout</v-btn>
       </v-toolbar-items>

       <v-toolbar-items v-if="infoHeader.rol == 'g'">
            <v-btn v-for="btn in botonsGestor" :key="btn" @click="reroute(btn.ruta)" class="text-decoration-underline">
                    {{btn.txt}}
            </v-btn>
           <v-spacer></v-spacer>
               <v-btn v-if="!infoHeader.logejat" color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
               <v-btn v-if="infoHeader.logejat" color="pink" @click="logout()">Logout</v-btn>
       </v-toolbar-items>

        

    </v-app-bar>

    
    <v-app-bar
        color="primary"
        prominent
        v-if="!midaGran"
      >
        <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>

        <v-toolbar-title>
            <div v-if="infoHeader.rol == 'a'" class="direccio" @click="reroute('/principal-admin')">PrioriTask</div>
            <div v-else>PrioriTask</div>
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn v-if="!infoHeader.logejat" color="pink" @click="reroute('/login')">Iniciar Sessió</v-btn>
        <v-btn v-if="infoHeader.logejat" color="pink" @click="logout()">Logout</v-btn>
        
      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        location="bottom"
        temporary
        v-if="infoHeader.rol != '' && infoHeader.rol !== undefined"
      >
        <v-list v-if="infoHeader.rol == 'a'">
            <v-list-item v-for="boto in botons" :key="boto" @click="reroute(boto.ruta)">
                <v-list-item-title>{{ boto.txt }}</v-list-item-title>
            </v-list-item>
        </v-list>

        <v-list v-if="infoHeader.rol == 'g'">
            <v-list-item v-for="boto in botonsGestor" :key="boto" @click="reroute(boto.ruta)">
                <v-list-item-title>{{ boto.txt }}</v-list-item-title>
            </v-list-item>
        </v-list>

        <v-list v-if="infoHeader.rol == 't'">
            <v-list-item v-for="boto in botonsTecnic" :key="boto" @click="reroute(boto.ruta)">
                <v-list-item-title>{{ boto.txt }}</v-list-item-title>
            </v-list-item>
        </v-list>
        
      </v-navigation-drawer>

</template>

<script>
export default {
    name: 'App',
    props: ["infoHeader"],
    data() {
        return {
            drawer: false,
            group: null,
            windowWidth: window.innerWidth,
            // Array que guardarà els diferents botons amb les rutes que executaran
            botons: [
                {   txt: "Crear Usuari",
                    ruta: "/crear-usuari" },
                {   txt: "Modificar Usuari",
                    ruta: "/llista-usuari" },
                {   txt: "Crear Tasca",
                    ruta: "/crear-tasca" },
                {   txt: "Modificar Tasca",
                    ruta: "/modificar-tasca" },
            ],
            botonsGestor: [
            {   txt: "Crear Tasca",
                    ruta: "/crear-tasca" },
                {   txt: "Modificar Tasca",
                    ruta: "/modificar-tasca" },
            ],
            botonsTecnic: [
            {   txt: "Modificar Tasca",
                    ruta: "/modificar-tasca" },

            ],
            }
    },
    methods: {
        reroute(ruta){
            this.$router.push(ruta);
        },
        logout(){
            sessionStorage.clear();
            this.$router.replace('/login');
        }

    },
    computed:{
        style(){
            return 'color: ' + this.infoHeader.headerColor;
        },
        midaGran(){
            return this.windowWidth > 802;
        }
    },
    mounted(){
        window.addEventListener('resize',()=>{
            this.windowWidth = window.innerWidth;
        })
    },
    watch: {
      group () {
        this.drawer = false
      },
    },
}
</script>

<style scoped>
    .direccio{
        cursor: pointer;
    }
</style>


