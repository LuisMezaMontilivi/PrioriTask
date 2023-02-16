<template>
    <div class="divider">
        <h1>PrioriTask</h1>
        <h2>Texto motivacional</h2>
    </div>
    <div class="divider">
        <div class="heroBlock">
            <v-carousel hide-delimiters cycle :show-arrows="false">
                <v-carousel-item v-for="(item, i) in items" :key="i" :src="item.src" cover>
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
export default {
    name: 'CarrouselComp',
    data() {
        return {
            items: [
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/squirrel.jpg',
                    title: "Esquirol"
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/sky.jpg',
                    title: "Cel"
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/bird.jpg',
                    title: "Àguila"
                },
            ],
        }
    },
}
</script>

<style scoped>
    
</style>