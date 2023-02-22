<template>
    <v-sheet   class="mx-auto mt-14 centersheet" >
        <v-row justify="center" no-gutters>
            <v-col class="text-left" cols="12">
                <h1>Crea l'usuari</h1>
            </v-col>
        </v-row>
        <v-row  >
            <v-col cols="12" >
        <v-form validate-on="input" @submit.prevent="submit"  v-model="valid" class="mt-4">
            <h2>Email</h2>
            <v-text-field
            v-model="email"
            :rules="emailRules"
            label="Escriu el teu e-mail"
            required
            clearable
            class="mt-2"
          ></v-text-field>
          <h2>Nom i cognoms</h2>
          <v-text-field
            v-model="nom"
            clearable
            label="Escriu el teu nom"
            class="mt-2"
          ></v-text-field>
         
          <h2>Password Temporal</h2>
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

          <v-row>
            <v-col md="5">
                <v-switch
             v-model="rol"
             hide-details
             inset
             true-value="g"
             false-value="t"
             :label="`Rol: ${rol}`"
            color="#232323"
             ></v-switch>
            </v-col>
          </v-row>
         

            <v-btn 
            class="mt-4"
            block
          color="primary"
          type="submit"
          size="x-large"
          @click="altaUsuari()"
          >
          Crea
          </v-btn>
       
          
        </v-form>
    </v-col>
    </v-row>
        

    </v-sheet>



</template>


<script>
import axios from 'axios';
import sha256 from 'crypto-js/sha256';

export default{
    
    
    data: () => ({
        rol: 'g', 
      nom: '',
      valid: false,
      email: '',
      emailRules: [
        value => {
          if (value) return true

          return 'E-mail is requred.'
        },
        value => {
          if (/.+@.+\..+/.test(value)) return true

          return 'E-mail must be valid.'
        },
      ],
      show1: false,
        show2: true,
        password: 'Password',
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || 'Min 8 characters',
        },
    }),
    methods: {

      altaUsuari(){
      axios.put("http://prioritask.daw.institutmontilivi.cat/api/usuari/alta",{},{
        headers: {'token' : sessionStorage.token ,
        'informacioUsuari':'{"nom": "'+ this.nom + '", "contrasenya": "'+ sha256(this.password)  + '", "email": "'+ this.email  + '", "rol": "' + this.rol   +'"}' 
                  
                }
      })
      .then(resposta=>{
        console.log(resposta);
        if(resposta.data){
          this.$router.push('/')
        }
        else{window.alert("Usuari no ha pogut ser creat, torna-ho a intentar")}
      })
    }
  }
    
    

  }






</script>