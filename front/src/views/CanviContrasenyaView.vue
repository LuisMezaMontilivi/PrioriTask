<template>
    <v-sheet   class="mx-auto mt-14 centersheet" >
        <v-row justify="center" no-gutters>
            <v-col class="text-left" cols="12">
                <h1>Canvia la contrasenya</h1>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12">
                <v-alert
      variant="outlined"
      type="warning"
      prominent
      border="top"
      color="error"
    >
    És necessari un canvi de contrasenya, en cas de perdua caldrà contactar a l'administrador
    </v-alert>
            </v-col>

        </v-row>

        <v-row  >
            <v-col cols="12" >
        <v-form validate-on="input" @submit.prevent="submit"  v-model="valid" class="mt-4">
           
         
          <h2>Password </h2>
          <v-text-field
            v-model="password"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.min]"
            :type="show1 ? 'text' : 'password'"
            name="input-10-1"
            label="Escriu la teva contrasenya"
            hint="At least 8 characters"
            counter
            required
            clearable
            class="mt-2"
            @click:append="show1 = !show1"
          ></v-text-field>
          

       
         

            <v-btn 
            class="mt-4"
            block
          color="primary"
          type="submit"
          size="x-large"
          @click="canviarContrasenya()"
          >
          Canvia la contrasenya
          </v-btn>
       
          
        </v-form>
    </v-col>
    </v-row>
        

    </v-sheet>



</template>


<script a>
import axios from 'axios'

export default{
    
    
    data: () => ({

      show1: false,
        show2: true,
        password: '',
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || 'Min 8 characters',
        },
       

    }),

    methods: {
           canviarContrasenya(){
      axios.put("http://prioritask.daw.institutmontilivi.cat/api/usuari/contrasenya",{},{
        headers: {'token' : sessionStorage.token,
         'maininfo':'{"contrasenyaEncriptada": "'+ this.password +'"}' }
      })
      .then(resposta=>{
        if(resposta.data){
            this.$router.push('/')
        }
        else{window.alert("Contrasenya no canviada, torna-ho a intentar")}
      })
    }
    }
    
    

  }






</script>