<template>
    <h1>Tasca nova</h1>
    <div class="divider">
        <v-sheet width="800" class="mx-auto">
            <v-form validate-on="submit" @submit.prevent="submit">
            <h2>Títol</h2>
            <v-text-field
                v-model="userName"
                :rules="rules"
                label="Inserta el títol de la tasca"
            ></v-text-field>
            <h2>Descripció</h2>
            <v-textarea
                v-model="userName"
                :rules="rules"
                label="Inserta la descripció de la tasca"
            ></v-textarea>
            <h2>Prioritat</h2>
            <v-slider
                :min="1"
                :max="9"
                step="1"
                show-ticks="always"
                v-model="prioritat"
            ></v-slider>
            <v-text-field
                :min="1"
                :max="9"
                v-model="prioritat"
                type="number">

            </v-text-field>
            <v-btn type="submit" block class="mt-2">Submit</v-btn>
            </v-form>
        </v-sheet>
    </div>
    <div class="divider">
        <v-autocomplete
            clearable
            label="Llistat de tècnics"
            :items="tecnics"
            >
        </v-autocomplete>
    </div>
</template>

<script>
export default {
    name: 'CrearTascaView',
    data() {
        return {
            prioritat: 3,
            tecnics: ["Guillem","Ferran","Aba"]
        }
    }
}
</script>
