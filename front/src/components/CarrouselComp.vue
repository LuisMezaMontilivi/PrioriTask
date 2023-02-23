<template>
    <div class="divider">
        <div class="textMain">

            <h1>Planifica, Prioritza, Aconsegueix.</h1>

            <h1 class="prioritaskNom">Prioritask</h1>
            <div class="fons-Primari">
                <h2>Prioritask és el sistema de gestió de tasques que respón a les necessitats de la teva empresa.</h2>
                <h2>Web, Intuitiu i Escalable a la mida de la teva empresa. Organitza, gestiona i assigna tasques de forma ràpida i dinàmica. 
                    Amb un sistema de colors intuitiu que permet identificar visualment l'estat de les tasques.
                </h2>
            </div>

        </div>




    </div>
    <div class="divider margintop">
        <div class="heroBlock">
            <v-carousel hide-delimiters cycle :show-arrows="false">
                <v-carousel-item v-for="(item, i) in items" :key="i" :src="item.src" >
                    <v-row class="fill-height title" align="center" justify="center">
                        <div>
                            {{ item.title }}
                        </div>
                    </v-row>
                </v-carousel-item>
            </v-carousel>
        </div>
    </div>
</template>

<script>
import llistaTascaMobil from '@/assets/llistaTasquesMobil.png'
import creaTascaMobil from '@/assets/creaTascaMobil.png'
import paginaAdminMobil from '@/assets/paginaAdminMobil.png'
import modificaTascaMobil from '@/assets/modificaTascaMobil.png'
export default {
    name: 'CarrouselComp',
    data() {
        return {
            items: [
                {
                    src:  llistaTascaMobil,
                   
                },
                {
                    src:  creaTascaMobil,
                   
                },
                {
                    src: modificaTascaMobil,
                  
                },
                {
                    src: paginaAdminMobil,
                    
                },
            ],
        }
    },
}
</script>

<style scoped>
.textMain {
    margin-left: 12%;
    margin-right: 7%;
    margin-top: 7%;
}

.textMain h1 {
    color: primary;
    font-weight: bold;
}


.prioritaskNom {
    color: #0AFFBA;
    justify-content: center;
    width: 100%;
}

.fons-Primari {
    background-color: primary;
}

.margintop{
    margin-top: 5%;
    
}
</style>