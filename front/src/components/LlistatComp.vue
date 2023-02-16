<template>
    
    <div class="divider3">
        <v-card
        class="mx-auto"
        max-width="500">
            <v-card-title :style="style" class="">
                <h3 class="titl">{{ titol }}</h3>
            </v-card-title>
                <v-list>
                    <v-list-item v-for="tasca in tasques" :key="tasca" @click="reroute(tasca)">
                        <v-list-item-title>{{ tasca.titol }}</v-list-item-title>
                    </v-list-item>
                </v-list>
        </v-card>
    </div>
</template>

<script>
export default {
    props: [ 
        "titol",
        "tasques",
        "color" 
    ],
    computed:{
        style(){
            return 'background-color: ' + this.color;
        }
    },
    methods: {
        reroute(tasca){
            sessionStorage.setItem('tasca',JSON.stringify(tasca));
            this.$router.push('/modificar-tasca/' + tasca.id_tasca);
        }
    }
  }
  
</script>

<style scoped>
.titl{
    text-align: center;
    padding: 0px;
}
</style>